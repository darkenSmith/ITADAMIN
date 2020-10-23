<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use Office365\Runtime\Auth\AuthenticationContext;
use Office365\SharePoint\ClientContext;
use ZipArchive;

/**
 * Class ApprovData
 * @package App\Models\RS
 */
class Pdfmaker extends AbstractModel
{
    public $response;
    public $id;

    /**
     * ApprovData constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function printdoc()
    {
        $pass = '1t@D458!';
        $Usershare = 'itad.365@stonegroup.co.uk';

        $_SESSION['err'] = '';
        $id = $_POST['stuff'];

        $UserName = $Usershare;
        $Password = $pass;
        $Url = 'https://stonegroupltd.sharepoint.com';
        $Url2 = 'https://stonegroupltd.sharepoint.com/sites/Recycling';

        $localPath = "./files/copy.docx";
        $targetLibraryTitle = "Documents";
        $targetFolderUrl = "Shared Documents/Request - COD";
        $fileUrl = "/sites/Recycling/Shared Documents/templatetest.docx";
        $listTitle = "";
        $delete = 0;

        try {
            $authCtx = new AuthenticationContext($Url);
            $authCtx->acquireTokenForUser($UserName, $Password); //authenticate
            $ctx = new ClientContext($Url2, $authCtx); //initialize REST client
            $web = $ctx->getWeb();
            $list = $web->getLists()->getByTitle($targetLibraryTitle); //init List resource
            $items = $list->getItems();  //prepare a query to retrieve from the
            $ctx->load($items);  //save a query to retrieve list items from the server
            $ctx->executeQuery(); //submit query to SharePoint Online REST service

        } catch (\Exception $e) {
            Logger::getInstance("pdfMaker.log")->debug(
                'printdoc',
                [$e->getMessage()]
            );
        }

        $template_file_name = PROJECT_DIR  . 'assets/files/copy.docx';
        $folder = "files";

        try {
            if (!file_exists($folder)) {
                if (!mkdir($folder) && !is_dir($folder)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $folder));
                }
            }

            // add calss Zip Archive
            $zip_val = new ZipArchive;

            foreach ($id as $val) {

                $ord_sql = "
                          select [dbo].[zzfnRemoveNonNumericCharacters](isnull(SalesOrderNumber, '0000000')) as ord from [greenoak].[we3recycler].[dbo].SalesOrders  where CustomerPONumber like '%" . $val . "'";
                $ord_stmt = $this->gdb->prepare($ord_sql);
                $ord_stmt->execute();
                $orddata = $ord_stmt->fetch(\PDO::FETCH_ASSOC);

                Logger::getInstance("pdfMaker.log")->debug(
                    'printdoc',
                    [$orddata]
                );

                $wtnquery = "SELECT d.WasteTransferNumber as WTN FROM [greenoak].[we3recycler].[dbo].Delivery AS d  JOIN [greenoak].[we3recycler].[dbo].SalesOrders AS s ON d.SalesOrderID = s.SalesOrderID WHERE CustomerPONumber LIKE ".$val;
                $stmtwtn = $this->gdb->prepare($wtnquery);
                $wtndata = $stmtwtn->fetch(\PDO::FETCH_ASSOC);

                $ord = 'ORD-' . $orddata['ord'];

                $sql = "
                SET Language British;
                select * from dbo.reqOrderline(" . $val . ")";
                $stmt = $this->sdb->prepare($sql);
                $stmt->execute();

                $data = $stmt->fetch(\PDO::FETCH_ASSOC);

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

                $fileName = $data['Customer_name'] . "-RC-000" . $val . ".docx";

                $full_path = $folder . '/' . $fileName;
                copy($template_file_name, $full_path);


                //Docx file is nothing but a zip file. Open this Zip File
                if ($zip_val->open($full_path) == true) {
                    // In the Open XML Wordprocessing format content is stored.
                    // In the document.xml file located in the word directory.

                    $key_file_name = 'word/document.xml';
                    $message = $zip_val->getFromName($key_file_name);

                    Logger::getInstance("pdfMaker.log")->debug(
                        'worddocdeit',
                        [$message]
                    );
                    //$timestamp = date('d-M-Y H:i:s');

                    // this data Replace the placeholders with actual values
                    $message = str_replace("{ordernum}", $ord, $message);
                    $message = str_replace("{Requestid}", $data['Request_id'], $message);
                    $message = str_replace("{WTN}", $wtndata['WTN'], $message);
                    $message = str_replace("{Organisation}", $data['Customer_name'], $message);
                    $message = str_replace("{ProposedCollectionDate}", $data['collection_date'], $message);
                    $message = str_replace("{Address1}", $data['add1'], $message);
                    $message = str_replace("{Address2}", $data['add2'], $message);
                    $message = str_replace("{Address3}", $data['add3'], $message);
                    $message = str_replace("{Town}", $data['town'], $message);
                    $message = str_replace("{County}", $data['county'], $message);
                    $message = str_replace("{Postcode}", $data['PostCode'], $message);
                    $message = str_replace("{SiteContact}", $data['customer_contact'], $message);
                    $message = str_replace("{RequestedBy}", $data['contact_name'], $message);
                    $message = str_replace("{TelePhone}", $data['customer_phone'], $message);
                    $message = str_replace("{Weight}", $dataw['totalweight'], $message);
                    $message = str_replace("{sitecontactphone}", $data['contact_tel'], $message);
                    $message = str_replace("{EmailAddress}", $data['customer_email'], $message);
                    $message = str_replace("{CollectionInstruction}", $data['req_col_instrct'], $message);
                    $message = str_replace("{AccessTime}", $data['Early_Access'], $message);
                    $message = str_replace("{avoid}", $data['Avoid'], $message);
                    $message = str_replace("{helponsite}", $data['help_onsite'], $message);
                    $message = str_replace("{Parking}", $data['parking_notes'], $message);
                    $message = str_replace("{PC Working}", $data['PC_w'], $message);
                    $message = str_replace("{pcwipe}", $data['pc_wipe'], $message);
                    $message = str_replace("{aiopcw}", $data['Allinone_PC_w'], $message);
                    $message = str_replace("{aiopcwipe}", $data['Allinone_PC_wipe'], $message);
                    $message = str_replace("{lapw}", $data['Laptop_w'], $message);
                    $message = str_replace("{lapwipe}", $data['Laptop_wipe'], $message);
                    $message = str_replace("{mps_w}", $data['Mobile_Phone(smart)_w'], $message);
                    $message = str_replace("{aphne_w}", $data['Apple_Phone_w'], $message);
                    $message = str_replace("{apptab_w}", $data['AppleTab_Phone_w'], $message);
                    $message = str_replace("{tab_w}", $data['Tab_Phone_w'], $message);
                    $message = str_replace("{serw}", $data['Server_w'], $message);
                    $message = str_replace("{swi_w}", $data['Switches_w'], $message);
                    $message = str_replace("{boardw}", $data['Harddrive_w'], $message);
                    $message = str_replace("{spw}", $data['Standalone_Printer_w'], $message);
                    $message = str_replace("{twoman}", $data['twoman'], $message);
                    $message = str_replace("{steps}", $data['steps'], $message);
                    $message = str_replace("{lift}", $data['lift'], $message);
                    $message = str_replace("{ground}", $data['ground'], $message);
                    $message = str_replace("{tftw}", $data['TFT_w'], $message);
                    $message = str_replace("{tvw}", $data['TV_w'], $message);
                    $message = str_replace("{smartboardw}", $data['SmartBoard_w'], $message);
                    $message = str_replace("{dpw}", $data['DesktopPrinter_w'], $message);
                    $message = str_replace("{prow}", $data['Projector_w'], $message);
                    $message = str_replace("{crtw}", $data['CRT_w'], $message);
                    $message = str_replace("{other1w}", $data['Other1_w'], $message);
                    $message = str_replace("{other2w}", $data['Other2_w'], $message);
                    $message = str_replace("{other3w}", $data['Other3_w'], $message);
                    $message = str_replace("{other1}", $data['other1name'], $message);
                    $message = str_replace("{other2}", $data['other2name'], $message);
                    $message = str_replace("{other3}", $data['other3name'], $message);
                    $message = str_replace("{Position}", $data['customer_contact_positon'], $message);
                    $message = str_replace("{totalUnits}", $data['totaldataunit'], $message);


                    //Replace the content with the new content created above.
                    $zip_val->addFromString($key_file_name, $message);
                    $zip_val->close();
                    Logger::getInstance("pdfMaker.log")->debug(
                        'calling uploadFileIntoFolder',
                        [$ctx, $full_path, $targetFolderUrl]
                    );

                    $this->uploadFileIntoFolder($ctx, $full_path, $targetFolderUrl);
                }

                unlink($full_path);
            }
        } catch (\Exception $exc) {
            $error_message = "Error creating the Word Document";
            Logger::getInstance("pdfMaker.log")->error(
                'faildocgen',
                [$exc->getMessage()]
            );
            //var_dump($exc);
            header("Content-type: application/json");

            echo json_encode($exc);
        }
    }

    public function uploadFileIntoFolder(ClientContext $ctx, $localPath, $targetFolderUrl)
    {
        $fileName = basename($localPath);
        $fileCreationInformation = new \Office365\SharePoint\FileCreationInformation();
        $fileCreationInformation->Content = file_get_contents($localPath);
        $fileCreationInformation->Url = $fileName;

        $uploadFile = $ctx->getWeb()->getFolderByServerRelativeUrl($targetFolderUrl)->getFiles()->add($fileCreationInformation);
        $ctx->executeQuery();
    }

    public function uploadFiles($localPath, \Office365\PHP\Client\SharePoint\SPList $targetList)
    {
        $ctx = $targetList->getContext();
        $searchPrefix = $localPath . '.';
        try {
            foreach (glob($searchPrefix) as $filename) {
                $fileCreationInformation = new \Office365\PHP\Client\SharePoint\FileCreationInformation();
                $fileCreationInformation->Content = file_get_contents($filename);
                $fileCreationInformation->Url = basename($filename);
                $uploadFile = $targetList->getRootFolder()->getFiles()->add($fileCreationInformation);
                $ctx->executeQuery();//upload the file
                $listEntity = $uploadFile->getListItemAllFields(); //now update associated list item entity
                $listEntity->setProperty('Title', "it is a test");
                $listEntity->update(); //tell query to update entity
                $ctx->executeQuery();
                print "File {$uploadFile->getProperty('Name')} has been uploaded\r\n";
            }
        } catch (\Exception $e) {
            Logger::getInstance("pdfMaker.log")->error(
                'uploadFiles',
                [$e->getMessage()]
            );
            echo 'Error : ' . $e->getMessage();
        }
    }
}
