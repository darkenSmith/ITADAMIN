<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

/**
 * Class BdView
 * @package App\Models
 */
class BdView extends AbstractModel
{
    public $datastuff;
    public $heading;

    /**
     * BdView constructor.
     */
    public function __construct()
    {
        $this->heading = 'datastuff';
        $this->sdb =  Database::getInstance('sql01');

        parent::__construct();
    }

    public function getowners()
    {

        include('./RECbooking/db.php');

        $sql= "select id, name, dept from owners";



        $fh = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/owners.txt", "a+");
        fwrite($fh, $sql ."\n");
        fclose($fh);

              $stmt = $this->sdb->prepare($sql);
              $stmt->execute();
              $this->datastuff = $stmt->fetchall(\PDO::FETCH_ASSOC);
             return $this->datastuff;
    }


    public function getdata($own, $filter)
    {

            include('./RECbooking/db.php');
            $own = $_POST['own'] ?? '';
        if (!isset($own)) {
            $own = '%';
        }

        if ($own == 'Please select owners') {
            $own = '%';
        }
        $filter->$_POST['filterstatus'] ?? '';

        $sql= "

        SET LANGUAGE British;
        
        set nocount on
        
        
        declare @owner varchar(200)
        set @owner = '".$own."'
        
        select 
        convert(varchar(50), request_date_added, 103) as requestdate,
        Request_id as id,
        rt.ord ordern,
        prev_orders as prev,
        [SurveyComplete] as scomp,
        ISNULL(RT.Owner, BC.Owner) as Owner,
        (case when BC.Owner = 'Sales' then 'Sales' else 'ITAD' end) as dept,
        wgt.totalunits,
        wgt.totalweight,
        wgt.commisionable,
        approved,
        process,
        case when been_collected = 1 AND collection_date IS NOT NULL and (confirmed = 0  or confirmed is null) 
        and laststatus like 'booked' then 'booked' When collection_date is not null and confirmed = 1 then 'confirmed' when been_collected = 1 AND collection_date IS NOT NULL and (confirmed = 1  and laststatus like 'confirmed') then 'Confirmed' when laststatus like 'On-Hold' then 'Hold'
         when laststatus like 'cancelled' then  'cancelled'  when rt.Owner is null then 'New'  else 'Request' end as Stat,
        isnull(Cmp_number, 'N/A') as crm,
        ---[dbo].[greencrmn](Request_id) crm,
        isnull(cast(convert(varchar(50), collection_date, 103) as varchar(50)),'not set') AS coldate,
          Customer_name AS name,
          customer_email AS email,
          customer_contact AS contact ,
          customer_phone AS tel,
         rt.town AS town,
         ISNULL([TYPE], '') as typ,
         req_note as upnotes ,
          county AS county,
          isnull(charge, 0)  as charge,
          convert(varchar(50),bc.survey_deadline, 103) survey_deadline,
          req_col_instrct as instructions, 
          request_col_date,
          case when isnull(GDPRconf, 0) = 1 then 'Yes' else 'No' end as gdpr,
          rt.postcode AS postcode, 
            prev_date
        
        FROM
         request as rt with(nolock)
        LEFT join Booked_Collections as bc with(nolock) on
        (case when ISNUMERIC(bc.RequestID) = 1 then bc.RequestID else 0 end)  = rt.Request_id
        
        
        join
        
          (select
          req_id,
        SUM(qty) as totalunits,
        sum(qty * convert(DECIMAL(9 , 2),typicalweight)) as totalweight,
        SUM(qty) * max(commisionable) as commisionable
        
         from Req_Detail as rd with(nolock)
        join productlist as p with(nolock) on 
        p.product_ID = prod_id 
        group by req_id) as wgt on
        wgt.req_id = rt.Request_id
        
        
        
        
        WHERE
        Request_date_added  > dateadd(YY, DATEDIFF(YY, 0, getdate()), -30) 
          AND Request_ID  LIKE '%'
          AND (rt.ORD LIKE '%%' OR rt.ORD IS NULL) AND (bc.[Job_notes] + bc.[Access_Notes] LIKE '%%' OR bc.[Job_notes] + bc.[Access_Notes] IS NULL) 
        AND isnull(deleted, 0) <> 1  AND  isnull(DONE, 0) <> 1 "
        .(isset($filter) ? $filter : "AND laststatus not like 'On-Hold' ")."
          AND rt.postcode LIKE '%%%' and rt.postcode <> '%test'
          And (rt.area1 like '%') and ISNULL(RT.Owner, BC.Owner) like '%'+@owner+'%' and Customer_name  not like 'test%'
        ORDER BY
        collection_date desc";



        $fh = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/stage1bdm.txt", "a+");
        fwrite($fh, $sql ."\n");
        fclose($fh);

                $stmt = $this->sdb->prepare($sql);
                $stmt->execute();
                $data = $stmt->fetchall(\PDO::FETCH_ASSOC);

                $dataarry = array();

        foreach ($data as $val) {
            array_push($dataarry, $val);
        }

                $this->datacheck = $dataarry;


                $fh = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/bdmreport.txt", "a+");
                fwrite($fh, $sql ."\n");
                fclose($fh);



        return $this->datacheck;
    }
}
