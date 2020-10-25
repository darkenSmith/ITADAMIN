<?php

namespace App\Models\RS;

use App\Helpers\Config;
use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use Office365\Runtime\Auth\AuthenticationContext;
use Office365\SharePoint\ClientContext;
use Office365\SharePoint\FileCreationInformation;
use ZipArchive;

/**
 * Class Pdfmaker
 * @package App\Models\RS
 */
class Pdfmaker extends AbstractModel
{
    public $response;
    public $id;
    private $sharePointConfig;

    /**
     * Pdfmaker constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        $this->sharePointConfig = Config::getInstance()->get('sharepoint');

        parent::__construct();
    }

    public function printdoc()
    {
        $_SESSION['err'] = '';
        $id = $_POST['stuff'];

        try {
            $authCtx = new AuthenticationContext($this->sharePointConfig['url']);

            $authCtx->acquireTokenForUser(
                $this->sharePointConfig['user'],
                $this->sharePointConfig['pass']
            );

            $ctx = new ClientContext(
                $this->sharePointConfig['url'] . $this->sharePointConfig['remote']['path'],
                $authCtx
            );

            $web = $ctx->getWeb();
            $list = $web->getLists()->getByTitle($this->sharePointConfig['remote']['title']);
            $items = $list->getItems();
            $ctx->load($items);
            $ctx->executeQuery();
            Logger::getInstance("pdfMaker.log")->info(
                'printdoc',
                ['line' => __LINE__]
            );
        } catch (\Exception $e) {
            Logger::getInstance("pdfMaker.log")->error(
                'printdoc-try-catch-error',
                [$e->getLine(), $e->getMessage()]
            );
            exit('Can not create connection to Sharepoint!');
        }

        $template_file_name = sprintf(
            '%s%s/%s',
            PROJECT_DIR,
            $this->sharePointConfig['local']['path'],
            $this->sharePointConfig['local']['template']
        );

        $folder = PROJECT_DIR . $this->sharePointConfig['local']['path'];

        try {
            if (!file_exists($folder) && !mkdir($folder) && !is_dir($folder)) {
                Logger::getInstance("pdfMaker.log")->warning(
                    'printdoc',
                    ['line' => __LINE__, 'throw' => 'Directory "%s" was not created '. $folder]
                );
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $folder));
            }

            $zip_val = new ZipArchive;
            Logger::getInstance("pdfMaker.log")->debug(
                'printdoc',
                ['line' => __LINE__, 'foreach-start' => count($id)]
            );
            foreach ($id as $val) {
                $ord_sql = "select replace(SalesOrderNumber, 'ORD-', '') as ord from [greenoak].[we3recycler].[dbo].SalesOrders  where CustomerPONumber like '%" . $val . "'";
                $ord_stmt = $this->gdb->prepare($ord_sql);
                $ord_stmt->execute();
                $orddata = $ord_stmt->fetch(\PDO::FETCH_ASSOC);

                Logger::getInstance("pdfMaker.log")->debug(
                    'printdoc',
                    [
                        'line' => __LINE__,
                        'val' => $val,
                        'orddata' => $orddata
                    ]
                );

                $wtnquery = "SELECT d.WasteTransferNumber as WTN FROM [greenoak].[we3recycler].[dbo].Delivery AS d  JOIN [greenoak].[we3recycler].[dbo].SalesOrders AS s ON d.SalesOrderID = s.SalesOrderID WHERE CustomerPONumber LIKE ".$val;
                $stmtwtn = $this->gdb->prepare($wtnquery);
                $wtndata = $stmtwtn->fetch(\PDO::FETCH_ASSOC);

                Logger::getInstance("pdfMaker.log")->debug(
                    'printdoc',
                    ['line' => __LINE__, 'wtndata' => $wtndata]
                );

                $ord = 'ORD-' . $orddata['ord'];

                Logger::getInstance("pdfMaker.log")->debug(
                    'printdoc',
                    ['line' => __LINE__, 'ord' => $ord]
                );

                $sql = "
                SET Language British;
                select * from dbo.reqOrderline(" . $val . ")";
                $stmt = $this->sdb->prepare($sql);
                $stmt->execute();

                $data = $stmt->fetch(\PDO::FETCH_ASSOC);

                Logger::getInstance("pdfMaker.log")->debug(
                    'printdoc',
                    ['line' => __LINE__, 'data' => $data]
                );

                $sqlw = "
          select
          SUM(qty) as totalunits,
          sum(qty * convert(DECIMAL(9 , 2),typicalweight)) as totalweight,
          SUM(qty) * max(commisionable) as commisionable
          
           from Req_Detail with(nolock)
          join productlist as p with(nolock) on 
          p.product_ID = prod_id 
          
          where req_id =" . $val . "
          group by req_id";
                $stmtw = $this->sdb->prepare($sqlw);
                $stmtw->execute();

                $dataw = $stmtw->fetch(\PDO::FETCH_ASSOC);

                Logger::getInstance("pdfMaker.log")->debug(
                    'printdoc',
                    ['line' => __LINE__, 'dataw' => $dataw]
                );

                $fileName = $data['Customer_name'] . "-RC-000" . $val . ".docx";
                $full_path = $folder . '/' . $fileName;

                Logger::getInstance("pdfMaker.log")->info(
                    'printdoc',
                    ['line' => __LINE__, 'fileName' => $fileName, 'fullpath' => $full_path]
                );

                if (!copy($template_file_name, $full_path)) {
                    Logger::getInstance("pdfMaker.log")->error(
                        'printdoc',
                        ['line' => __LINE__, 'failed_to_copy' => [$template_file_name, $full_path]]
                    );
                } else {
                    Logger::getInstance("pdfMaker.log")->info(
                        'printdoc',
                        ['line' => __LINE__, 'copied' => [$template_file_name, $full_path]]
                    );
                }

                //Docx file is nothing but a zip file. Open this Zip File
                if ($zip_val->open($full_path) == true) {
                    // In the Open XML Wordprocessing format content is stored.
                    // In the document.xml file located in the word directory.

                    $key_file_name = 'word/document.xml';
                    $message = $zip_val->getFromName($key_file_name);

                    Logger::getInstance("pdfMaker.log")->debug(
                        'zip_val->open',
                        [
                            'key_file_name' => $key_file_name,
                            'message' => $message,
                        ]
                    );

                    // this data Replace the placeholders with actual values
                    $message = str_replace(
                        [
                            "{ordernum}",
                            "{Requestid}",
                            "{WTN}",
                            "{Organisation}",
                            "{ProposedCollectionDate}",
                            "{Address1}",
                            "{Address2}",
                            "{Address3}",
                            "{Town}",
                            "{County}",
                            "{Postcode}",
                            "{SiteContact}",
                            "{RequestedBy}",
                            "{TelePhone}",
                            "{Weight}",
                            "{sitecontactphone}",
                            "{EmailAddress}",
                            "{CollectionInstruction}",
                            "{AccessTime}",
                            "{avoid}",
                            "{helponsite}",
                            "{Parking}",
                            "{PC Working}",
                            "{pcwipe}",
                            "{aiopcw}",
                            "{aiopcwipe}",
                            "{lapw}",
                            "{lapwipe}",
                            "{mps_w}",
                            "{aphne_w}",
                            "{apptab_w}",
                            "{tab_w}",
                            "{serw}",
                            "{swi_w}",
                            "{boardw}",
                            "{spw}",
                            "{twoman}",
                            "{steps}",
                            "{lift}",
                            "{ground}",
                            "{tftw}",
                            "{tvw}",
                            "{smartboardw}",
                            "{dpw}",
                            "{prow}",
                            "{crtw}",
                            "{other1w}",
                            "{other2w}",
                            "{other3w}",
                            "{other1}",
                            "{other2}",
                            "{other3}",
                            "{Position}",
                            "{totalUnits}"
                        ],
                        [
                            $ord,
                            $data['Request_id'],
                            $wtndata['WTN'],
                            $data['Customer_name'],
                            $data['collection_date'],
                            $data['add1'],
                            $data['add2'],
                            $data['add3'],
                            $data['town'],
                            $data['county'],
                            $data['PostCode'],
                            $data['customer_contact'],
                            $data['contact_name'],
                            $data['customer_phone'],
                            $dataw['totalweight'],
                            $data['contact_tel'],
                            $data['customer_email'],
                            $data['req_col_instrct'],
                            $data['Early_Access'],
                            $data['Avoid'],
                            $data['help_onsite'],
                            $data['parking_notes'],
                            $data['PC_w'],
                            $data['pc_wipe'],
                            $data['Allinone_PC_w'],
                            $data['Allinone_PC_wipe'],
                            $data['Laptop_w'],
                            $data['Laptop_wipe'],
                            $data['Mobile_Phone(smart)_w'],
                            $data['Apple_Phone_w'],
                            $data['AppleTab_Phone_w'],
                            $data['Tab_Phone_w'],
                            $data['Server_w'],
                            $data['Switches_w'],
                            $data['Harddrive_w'],
                            $data['Standalone_Printer_w'],
                            $data['twoman'],
                            $data['steps'],
                            $data['lift'],
                            $data['ground'],
                            $data['TFT_w'],
                            $data['TV_w'],
                            $data['SmartBoard_w'],
                            $data['DesktopPrinter_w'],
                            $data['Projector_w'],
                            $data['CRT_w'],
                            $data['Other1_w'],
                            $data['Other2_w'],
                            $data['Other3_w'],
                            $data['other1name'],
                            $data['other2name'],
                            $data['other3name'],
                            $data['customer_contact_positon'],
                            $data['totaldataunit']
                        ],
                        $message
                    );

                    Logger::getInstance("pdfMaker.log")->debug(
                        'str-replace-message',
                        [
                            'key_file_name' => $key_file_name,
                            'message' => $message,
                        ]
                    );

                    //Replace the content with the new content created above.
                    $zip_val->addFromString($key_file_name, $message);
                    $zip_val->close();

                    $this->uploadFileIntoFolder($ctx, $full_path, $this->sharePointConfig['remote']['folder']);
                }
                unlink($full_path);
            }

            Logger::getInstance("pdfMaker.log")->debug(
                'printdoc',
                ['line' => __LINE__, 'foreach-end']
            );
        } catch (\Exception $exc) {
            Logger::getInstance("pdfMaker.log")->error(
                'faildocgen',
                [$exc->getMessage()]
            );
            header("Content-type: application/json");

            return json_encode($exc);
        }

        return json_encode(['success' => 1]);
    }

    public function uploadFileIntoFolder(ClientContext $ctx, $localPath, $targetFolderUrl)
    {
        try {
            $fileName = basename($localPath);
            $fileCreationInformation = new FileCreationInformation();
            $fileCreationInformation->Content = file_get_contents($localPath);
            $fileCreationInformation->Url = $fileName;

            $ctx->getWeb()->getFolderByServerRelativeUrl($targetFolderUrl)->getFiles()->add($fileCreationInformation);
            $ctx->executeQuery();
        } catch (\Exception $e) {
            Logger::getInstance("pdfMaker.log")->error(
                'uploadFileIntoFolder',
                [$e->getLine(), $e->getMessage()]
            );
        }
    }
}
