<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;

/**
 * Class Booking
 * @package App\Models\RS
 * * alex
 */
class Bookingdata extends AbstractModel
{
    public $response;
    public $id;

    /**
     * Booking constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }
    


    

    public function getdata()
    {
    
        if (isset($_POST["postcode"])) {
            $postcode = $this->clean($_POST["postcode"]);
            $id= $this->clean($_POST["id"]);
            $ord = $this->clean($_POST["ent"]);
            $notes = $this->clean($_POST["notesja"]);
        }
              
        if (isset($_SESSION['stat'])) {
            $_SESSION['stat'] = isset($_POST["filterstatus"]) ? $_SESSION['stat'] : "AND laststatus not like 'On-Hold'";
        }

             

             
              
              
              $sql = "




              SET LANGUAGE British;

              set nocount on
              
              select 
              Request_id as id,
                    SUM(qty) as totalunits,
                    sum(qty * convert(DECIMAL(9 , 2),typicalweight)) as totalweight,
                    (select sum(qty) from Req_Detail join productlist as p on product_ID = prod_id where req_id =Request_id  and commisionable = 1) as commisionable,
                    (select sum(qty) from Req_Detail join productlist as p on product_ID = prod_id where req_id =Request_id  and commisionable = 0) as noncommisionable,
              prev_orders as prev,
              [SurveyComplete] as scomp,
              ISNULL(RT.Owner, BC.Owner) as Owner,
              approved,
              process,
              rt.ord ordern,
              case when been_collected = 1 AND  isnull(cast(collection_date as varchar(50)),'not set') IS NOT NULL and (confirmed = 0  or confirmed is null) 
              and laststatus like 'booked' then 'booked' When  isnull(cast(collection_date as varchar(50)),'not set') is not null and confirmed = 1 then 'confirmed' when been_collected = 1 AND  isnull(cast(collection_date as varchar(50)),'not set') IS NOT NULL and (confirmed = 1  and laststatus like 'confirmed') then 'Confirmed' when laststatus like 'On-Hold' then 'Hold'
               when laststatus like 'cancelled' then  'cancelled'  when (rt.Owner is null or rt.Owner like '') then 'New' when isnull(deleted, 0) = 1 then 'deleted'  else 'Request' end as Stat,
              isnull(Cmp_number, 'N/A') as crm,
              request_date_added as requestdate,
              isnull(cast(collection_date as varchar(50)),'not set') AS coldate,
                Customer_name AS name,
                customer_email AS email,
                customer_contact AS contact ,
                customer_phone AS tel,
               rt.town AS town,
               ISNULL([TYPE], '') as typ,
               req_note as upnotes ,
                county AS county,
                isnull(charge, 0)  as charge,
                bc.survey_deadline,
                req_col_instrct as instructions, 
                convert(varchar(100), request_col_date) request_col_date,
                case when isnull(GDPRconf, 0) = 1 then 'Yes' else 'No' end as gdpr,
                rt.postcode AS postcode, 
                bc.emailsentdate
              
              
              FROM
               request as rt with(nolock)
              LEFT join Booked_Collections as bc with(nolock) on
              (case when ISNUMERIC(bc.RequestID) = 1 then bc.RequestID else 0 end)  = rt.Request_id
              join Req_Detail as rd on 
              rd.req_id = Request_id
              join productlist as p with(nolock) on 
              p.product_ID = prod_id 
              
              WHERE
              Request_date_added  > dateadd(YY, DATEDIFF(YY, 0, getdate()), -30) 
                AND Request_ID  LIKE '".(isset($id) && $id != "" ? $id :"%")."'
                ";
        if (isset($ord) &&  $ord != "") {
            $sql .= "AND rt.ORD LIKE '%".$_POST["ent"]."' ";
        } else {
            $sql .= "AND (rt.ORD LIKE '%%' OR rt.ORD IS NULL) ";
        }
              
        if (isset($notes) &&  $notes != "") {
            $sql .= "AND  bc.[Job_notes] + bc.[Access_Notes] LIKE '%".$notes."%' ";
        } else {
            $sql .= "AND (bc.[Job_notes] + bc.[Access_Notes] LIKE '%%' OR bc.[Job_notes] + bc.[Access_Notes] IS NULL) ";
        }
                
              
              
        if (isset($_POST['filterstatus']) && $_POST['filterstatus'] == 'deleted') {
            $sql .= "
              AND isnull(deleted, 0) = 1  AND  isnull(DONE, 0) <> 1 
                AND rt.postcode LIKE '%".(isset($postcode) && $postcode != "" ? '%'.$postcode.'%' : '%')."%'
                And (rt.area1 like '".(isset($_POST["areafilter"]) && $_POST["areafilter"] != "" ? $_POST["areafilter"] : '%' )."' )";
        } else {
            $sql .= "
              AND isnull(deleted, 0) <> 1  AND  isnull(DONE, 0) <> 1 
               ".(isset($_POST["filterstatus"]) ? $_POST["filterstatus"] : "AND laststatus not like 'On-Hold' ")."
                AND rt.postcode LIKE '%".(isset($postcode) && $postcode != "" ? '%'.$postcode.'%' : '%')."%'
                And (rt.area1 like '".(isset($_POST["areafilter"]) && $_POST["areafilter"] != "" ? $_POST["areafilter"] : '%' )."' )";
        }
              
        if (isset($_POST["collectdate"]) &&  $_POST["collectdate"] != "") {
            $fu = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/collectdatesearch.txt", "a+");
            fwrite($fu, date("d-m-y", strtotime($_POST["collectdate"]))."\n");
            fclose($fu);
            $sql .= "and isnull(cast(rt.collection_date as varchar(50)),'not set') ='".date("d-m-y", strtotime($_POST["collectdate"]))."'";
        }
                
              
              
                $sql .= "

                group by 
                Request_id,

                prev_orders ,
                [SurveyComplete],
                ISNULL(RT.Owner, BC.Owner) ,
                approved,
                process,
                rt.ord ,
                case when been_collected = 1 AND  isnull(cast(collection_date as varchar(50)),'not set') IS NOT NULL and (confirmed = 0  or confirmed is null) 
                and laststatus like 'booked' then 'booked' When  isnull(cast(collection_date as varchar(50)),'not set') is not null and confirmed = 1 then 'confirmed' when been_collected = 1 AND  isnull(cast(collection_date as varchar(50)),'not set') IS NOT NULL and (confirmed = 1  and laststatus like 'confirmed') then 'Confirmed' when laststatus like 'On-Hold' then 'Hold'
                 when laststatus like 'cancelled' then  'cancelled'  when (rt.Owner is null or rt.Owner like '') then 'New' when isnull(deleted, 0) = 1 then 'deleted'  else 'Request' end,
                isnull(Cmp_number, 'N/A') ,
                request_date_added,
                isnull(cast(collection_date as varchar(50)),'not set') ,
                  Customer_name, 
                  customer_email ,
                  customer_contact ,
                  customer_phone ,
                 rt.town ,
                 ISNULL([TYPE], '') ,
                 req_note  ,
                  county,
                  isnull(charge, 0),
                  bc.survey_deadline,
                  req_col_instrct , 
                convert(varchar(100), request_col_date),
                  case when isnull(GDPRconf, 0) = 1 then 'Yes' else 'No' end ,
                  rt.postcode , 
                  bc.emailsentdate
              ORDER BY
                ".(isset($_POST["filter"]) && $_POST["filter"] != "" ? $_POST["filter"] : " isnull(cast(collection_date as varchar(50)),'not set') ")." ".(isset($_POST["filter2"]) && $_POST["filter2"] != "" ? $_POST["filter2"] : "  DESC")." 
                ";
              

                $stmt = $this->sdb->prepare($sql);
        if (!$stmt) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->sdb->errorInfo());
        }




        try {
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            $dataarray = array();
            $dataarray = $data;

            




            


         
            $this->response = $dataarray;
            return $this->response;
        } catch (\Exception $e) {
            var_dump($e);
        }

       // return [];
    }

    public function qualifying($id)
    {

        $sql = "exec commisionable ".$id;
        $stmt = $this->sdb->prepare($sql);

        echo $id;
        try {
            $stmt->execute();


            $data = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->response = $data;
            return $this->response;
        } catch (\Exception $e) {
            var_dump($e->getmessage());
        }
    }


    
    public function getareas()
    {
  
        $areaquery = "select distinct area1 from Area";
        $stmt3 = $this->sdb->prepare($areaquery);



       
        try {
            $stmt3->execute();
            $datarea = $stmt3->fetchall(\PDO::FETCH_ASSOC);

            $dataArray2 = $datarea;
            $this->response = $dataArray2;
            return $this->response;
        } catch (\JsonException $e) {
            var_dump($e->getmessage());
        }
    }


    public function clean($string)
    {
        $string = str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
       
        return str_replace("-", " ", preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
    }
}
