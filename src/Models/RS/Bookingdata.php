<?php

namespace App\Models\RS;

use App\Helpers\Database;
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
    
              if(isset($_POST["postcode"]))
              {
                  $postcode = $this->clean($_POST["postcode"]);
                  $id= $this->clean($_POST["id"]);
                  $ord = $this->clean($_POST["ent"]);
                  $notes = $this->clean($_POST["notesja"]);
              }
              
              
              $sql = "

              SET LANGUAGE British;
              
              set nocount on
              
              select 
              Request_id as id,
              prev_orders as prev,
              [SurveyComplete] as scomp,
              ISNULL(RT.Owner, BC.Owner) as Owner,
              approved,
              process,
              rt.ord ordern,
              case when been_collected = 1 AND collection_date IS NOT NULL and (confirmed = 0  or confirmed is null) 
              and laststatus like 'booked' then 'booked' When collection_date is not null and confirmed = 1 then 'confirmed' when been_collected = 1 AND collection_date IS NOT NULL and (confirmed = 1  and laststatus like 'confirmed') then 'Confirmed' when laststatus like 'On-Hold' then 'Hold'
               when laststatus like 'cancelled' then  'cancelled'  when (rt.Owner is null or rt.Owner like '') then 'New' when isnull(deleted, 0) = 1 then 'deleted'  else 'Request' end as Stat,
              isnull(Cmp_number, 'N/A') as crm,
              ---[dbo].[greencrmn](Request_id) crm,
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
                request_col_date,
                case when isnull(GDPRconf, 0) = 1 then 'Yes' else 'No' end as gdpr,
                rt.postcode AS postcode, 
                bc.emailsentdate
              
              FROM
               request as rt with(nolock)
              LEFT join Booked_Collections as bc with(nolock) on
              (case when ISNUMERIC(bc.RequestID) = 1 then bc.RequestID else 0 end)  = rt.Request_id
              
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
                }else{
                  $sql .= "
              AND isnull(deleted, 0) <> 1  AND  isnull(DONE, 0) <> 1 
               ".(isset($_POST["filterstatus"]) ? $_POST["filterstatus"] : "AND laststatus not like 'On-Hold' ")."
                AND rt.postcode LIKE '%".(isset($postcode) && $postcode != "" ? '%'.$postcode.'%' : '%')."%'
                And (rt.area1 like '".(isset($_POST["areafilter"]) && $_POST["areafilter"] != "" ? $_POST["areafilter"] : '%' )."' )";
              
                }
              
                if (isset($_POST["collectdate"]) &&  $_POST["collectdate"] != "") {
              
                  $fu = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/collectdatesearch.txt","a+");
                  fwrite($fu,date("d-m-y",strtotime($_POST["collectdate"]))."\n");
                  fclose($fu);
                  $sql .= "and rt.collection_date ='".date("d-m-y",strtotime($_POST["collectdate"]))."'"; 
                
                }
                
              
              
                $sql .= "
              ORDER BY
                ".(isset($_POST["filter"]) && $_POST["filter"] != "" ? $_POST["filter"] : "collection_date")." ".(isset($_POST["filter2"]) && $_POST["filter2"] != "" ? $_POST["filter2"] : "  DESC")." 
                ";
              
              
                $stmt = $this->sdb->prepare($sql);

        try {
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
           echo $sql;
            $this->response = $data;
            return $this->response;
        } catch (\Exception $e) {
          var_dump($e);
        }

        return [];
     
    }


    
    public function getareas(){
  
        $areaquery = "select distinct area1 from Area";
        $stmt = $this->sdb->prepare($areaquery);
        $stmt->execute();
        $datarea = $stmt->fetchall(PDO::FETCH_ASSOC);


        foreach($datarea as $area){
          echo "<option  value='".$area['area1']."'> ".$area['area1']." </option>";
        }
        
        $dataArray2 = array();
        foreach ($datarea as $val) {
            $dataArray2[] = $val;
        }
       
        try {
            $this->response = json_encode($dataArray2);
            return $this->response;
        } catch (\JsonException $e) {
          
        }

        return [];
        


    }


    public function clean($string) {
      $string = str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
       
      return str_replace("-"," ",preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
    }


}

