<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;

/**
 * Class UpdateDataDoc
 * @package App\Models\RS
 */
class UpdateDataDoc extends AbstractModel
{
    public $response;
    public $id;

    /**
     * UpdateDataDoc constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        parent::__construct();
    }

    public function update()
    {
        ///go

        if (!isset($_POST['other1name'])) {
            $other1name = ' ';
        } else {
            $other1name = $this->clean($_POST['other1name']);
        }

        if (!isset($_POST['other2name'])) {
            $other2name = ' ';
        } else {
            $other2name = $this->clean($_POST['other2name']);
        }

        if (!isset($_POST['other3name'])) {
            $other3name = ' ';
        } else {
            $other3name = $this->clean($_POST['other3name']);
        }
        if (!isset($_POST['other4name'])) {
            $other4name = ' ';
        } else {
            $other4name = $this->clean($_POST['other4name']);
        }
        if (!isset($_POST['other5name'])) {
            $other5name = ' ';
        } else {
            $other5name = $this->clean($_POST['other5name']);
        }
        if (!isset($_POST['other6name'])) {
            $other6name = ' ';
        } else {
            $other6name = $this->clean($_POST['other6name']);
        }

        $who = str_replace('@stonegroup.co.uk', '', $_SESSION['user']['username']);

        $reqid = $this->clean($_POST['reqid']);
        $email = ltrim($_POST['email'], " ");
        $name = $this->clean($_POST['name']);
        $address1 = $this->clean($_POST['address1']);
        $address2 = $this->clean($_POST['address2']);
        $address3 = $this->clean($_POST['address3']);
        $tel = $this->clean($_POST['tel']);
        $ordnum = $this->clean($_POST['ordnum']) ?? '';
        $twn = $this->clean($_POST['twn']);
        $postcode = $this->clean($_POST['postcode']);
        $statusnote = $this->clean($_POST['reqstat']);
        $gdprselect = $_POST['gdprselect'];
        $avoid = $this->clean($_POST['avoid']);

        if (isset($_POST['deadline'])) {
            $deadline = $_POST['deadline'];
        } else {
            $deadline = '';
        }

        $manordd = $_POST['manord'];


        $pos = $this->clean($_POST['pos']);
        $biopass = $this->clean($_POST['biopass']);
        $colinstruct = $this->clean($_POST['colinstruct']);
        $colldate = $this->clean($_POST['colldate']);
        $upnotes = $this->clean($_POST['upnotes']);
        $reqby = $this->clean($_POST['reqby']);
        $custphone = $this->clean($_POST['custphone']);
        $reqowner = $_POST['reqowner'];
        $previousorders = $this->clean($_POST['previousorders']);
        $dtype = $this->clean($_POST['dtype']);
        $approved = $this->clean($_POST['approved']);
        $process = $this->clean($_POST['process']);
        $who = $_SESSION['user']['username'];

        $emailsent = $_POST['emailsent'];

        if (isset($_POST['emailsent'])) {
            $emailsent = $_POST['emailsent'];
        } else {
            $emailsent = '';
        }

        if (empty($ordnum) || $ordnum == ' ') {
            $ordnum = $manordd;
        }

        $sqlbookcheck = "
          select count(*) as cn from Booked_Collections where RequestID = '" . $reqid . "'
          ";

        $stmtcheck = $this->sdb->prepare($sqlbookcheck);
        $stmtcheck->execute();
        $bookdata = $stmtcheck->fetch(\PDO::FETCH_ASSOC);


        if (!$bookdata['cn'] == 0) {
            $updead = "
                          set language BRITISH
                              declare @date datetime 
          
                        set @date = '" . $deadline . "'
                              
                
                          update Booked_Collections
                            set  [survey_deadline] = (select  format(@date, 'dd-MM-yyyy hh:mm:ss') ),
                            [email_sent] = '" . $emailsent . "',
                            [EstWeight] =(select sum(qty * convert(DECIMAL(9 , 2),typicalweight)) as totalweight from request r join Req_Detail as rr on r.request_id = rr.req_id join productlist as p on p.product_ID = rr.prod_id where request_id = " . $reqid . "),
                           [Est Total Units] = (select SUM(qty) as totalunits from request r join Req_Detail as rr on r.request_id = rr.req_id join productlist as p on p.product_ID = rr.prod_id where request_id = " . $reqid . "),
                            ORD = '" . $ordnum . "'
                            where RequestID = '" . $reqid . "'
          
          
                            update request
                            set survey_deadline = (select  format(@date, 'dd-MM-yyyy hh:mm:ss') ), 
                            modifedby = '" . str_replace("@stonegroup.co.uk", "", $who) . "',
                            modifydate = getdate()
                            where Request_ID = '" . $reqid . "'
                            ";

            Logger::getInstance("updateDataDoc.log")->debug(
                'bookcheck',
                [$updead]
            );
            $bookupdate = $this->sdb->prepare($updead);
            $bookupdate->execute();
        }

        // var_dump($name);

        $ord = $_SESSION['ordnum'];
        $is_other = 0;

        $arr = json_decode(json_encode($_POST['reqlines']), true);
        // $ItemArray = json_decode($_POST['json']);
        $arrfinal = json_decode($arr, true);

        Logger::getInstance("updateDataDoc.log")->debug(
            'updatearraydet',
            [$arrfinal]
        );

        $sqlhead = "
          update request
          set confirmed = 1,
          laststatus = 'Confirmed',
          modifydate = getdate(),
          updatedBy = '" . $who . "'
          
          where request_id in (select requestid from Booked_Collections 
          where [SurveyComplete] like 'yes%')
          and laststatus  <> 'on-hold'
          
           update request
           set Customer_name = '" . $name . "' ,
           town  = '" . $twn . "',
           customer_email  = '" . $email . "',
           contact_tel = '" . $tel . "',
           customer_phone = '" . $custphone . "',
           customer_contact_positon = '" . $pos . "',
           prev_orders = '" . $previousorders . "',
           add1 = '" . $address1 . "',
          add2 = '" . $address2 . "',
          add3 = '" . $address3 . "',
          postcode  = '" . $postcode . "',
          bio_pass  = '" . $biopass . "',
          req_col_instrct = '" . $colinstruct . "',
          request_col_date = '" . $colldate . "',
          Update_Note = '" . $upnotes . "',
          updatedBy = '" . $reqby . "',
          ORD  = '" . $ordnum . "',
          req_note = '" . $statusnote . "',
          owner = replace('" . $reqowner . "', ' ', '.') ,
          [TYPE] = '" . $dtype . "',
          approved = '" . $approved . "',
          avoidtimes = '" . $avoid . "',
          process = '" . $process . "',
          GDPRconf = '" . $gdprselect . "'
          
          from request 
          where Request_id = " . $reqid . "
          
          
          ";

        Logger::getInstance("updateDataDoc.log")->debug(
            'requpdatesqloutput',
            [$sqlhead]
        );

        $restmt = $this->sdb->prepare($sqlhead);
        $restmt->execute();

        ///do a update for anddress and customer details

        ///Request_SERVER_ASSET_REQ =". $con7 . ",
        foreach ($arrfinal as $output) {
            $det = implode(",", $output);
            $id = array_slice($output, 0, 1);
            $Rid = implode(",", $id);
            $w = array_slice($output, 1, 1);
            $wkn = implode(",", $w);
            // $asset = array_slice($output, 2, 1);
            $ass = 0;
            //$wipe = array_slice($output, 3, 1);
            $wipest = 0;


            $sqlii = "update Req_Detail";


            //push to server using array same as testform
            if ($Rid == 19) {
                $is_other = 1;

                $sqlii .= " 
          set qty =" . $wkn . ",
          other1_name = '" . $other1name . "',
          Asset_req =" . $ass . ",
          Wipe = " . $wipest . "
          from req_detail
          where req_id =" . $reqid . " and 
          prod_id =" . $Rid;
            }

            if ($Rid == 21) {
                $sqlii .= "
          
              set qty =" . $wkn . ",
              other2_name = '" . $other2name . "',
              Asset_req =" . $ass . ",
              Wipe = " . $wipest . "
              from req_detail
              where req_id =" . $reqid . " and 
              prod_id =" . $Rid;
            }

            if ($Rid == 23) {
                $sqlii .= "
          
              set qty =" . $wkn . ",
              other3_name = '" . $other3name . "',
              Asset_req =" . $ass . ",
              Wipe = " . $wipest . "
              from req_detail
              where req_id =" . $reqid . " and 
              prod_id =" . $Rid;
            }
            if ($Rid == 65) {
                $sqlii .= "
            
                set qty =" . $wkn . ",
                other4_name = '" . $other4name . "',
                Asset_req =" . $ass . ",
                Wipe = " . $wipest . "
                from req_detail
                where req_id =" . $reqid . " and 
                prod_id =" . $Rid;
            }
            if ($Rid == 67) {
                $sqlii .= "
              
                  set qty =" . $wkn . ",
                  other5_name = '" . $other5name . "',
                  Asset_req =" . $ass . ",
                  Wipe = " . $wipest . "
                  from req_detail
                  where req_id =" . $reqid . " and 
                  prod_id =" . $Rid;
            }
            if ($Rid == 69) {
                $sqlii .= "
                
                    set qty =" . $wkn . ",
                    other6_name = '" . $other6name . "',
                    Asset_req =" . $ass . ",
                    Wipe = " . $wipest . "
                    from req_detail
                    where req_id =" . $reqid . " and 
                    prod_id =" . $Rid;
            }
            if ($Rid != 19 && $Rid != 21 && $Rid != 23 && $Rid != 65 && $Rid != 67 && $Rid != 69) {
                $sqlii .= " 
          
              set qty =" . $wkn . ",
              Asset_req =" . $ass . ",
              Wipe = " . $wipest . "
              from req_detail
              where req_id =" . $reqid . " and 
              prod_id =" . $Rid;
            }

            Logger::getInstance("updateDataDoc.log")->debug(
                'newreqsqlout',
                [$sqlii]
            );
            $stmt = $this->sdb->prepare($sqlii);
            $stmt->execute();
        }
    }

    function clean($string)
    {
        $string = str_replace(array("'", "\"", "&quot;"), "", htmlspecialchars($string));
        $string = str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
        $string = str_replace('&', 'and', $string);
        return str_replace("-", " ", preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
    }
}
