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
class Arcdata extends AbstractModel
{
    public $response;
    public $id;

    /**
     * Booking constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        parent::__construct();
    }

    public function getdata()
    {
        if (isset($_POST["postcode"])) {
            $postcode = $this->clean($_POST["postcode"]);
            $id = $this->clean($_POST["id"]);
            $ord = $this->clean($_POST["ent"]);
        }

        $sql = "
SET LANGUAGE British;
select distinct
Request_id as id,
cl.[COD_pwork_Checked] as cod,
cl.AMR_Comp,
cl.Rebate,
cl.is_Rebate,
cl.[ITADAccountManager] as owner,
ord ordern,
(select case when max(rpt) is null then isnull(max(rpt), 'AMR') else 'AMR - '+max(rpt) end  from Companies where cmp = cl.cmp_num) as   rpt,
isnull(Cmp_number, 'N/A') as crm,
request_date_added as requestdate,
  isnull(collection_date,'01/01/1900') AS coldate,
  Customer_name AS name,
  customer_email AS email,
  customer_contact AS contact ,
  customer_phone AS tel,
 town AS town,
 req_note as upnotes ,
  county AS county,
  rt.postcode AS postcod,
  invoicedate,
  area1 as area,
  isnull(cl.TFT+isnull(br.TFT, 0), 0) as tft, 
  isnull(cl.[TFT TV], 0) as tfttv,
  isnull(cl.PC+isnull(br.PC, 0), 0) as pc,
  isnull(cl.PC_APP+isnull(br.pc_APP, 0), 0) as pcapp,
  isnull(cl.[AllInOne]+isnull(br.[All in One], 0), 0) as aio,
  isnull(cl.[AIO_App]+isnull(br.[AIO_App], 0), 0) as aioapp,
  isnull(cl.LAPTOP+isnull(br.LAPTOP, 0), 0) as lap, 
  isnull(cl.Lap_APP+isnull(br.LAP_APP, 0), 0) as lapapp,
  isnull(cl.[SERVER]+isnull(br.[SERVER], 0), 0) as serv,
  isnull(cl.TABLET+isnull(br.TABLET, 0), 0) as tab,
  isnull(cl.[AppleTablet]+isnull(br.Appletab, 0), 0) as apptab,
  isnull(cl.[SmartPhone]+isnull(br.[SmartPhone], 0), 0) as sphone,
  isnull(cl.[ApplePhone]+isnull(br.ApplePhone, 0), 0) as appphone,
  isnull(cl.[NonSmart]+isnull(br.[NonSmart], 0), 0) as nonsmart,
  isnull(cl.Printers, 0) as Printers,
  isnull(cl.[MFD_Printer], 0) as mfdp,
  isnull(cl.[CRT_Monitor], 0) as crtmon,
  isnull(CL.[TV_CRT_Monitor] , 0) as crttv,
  isnull(cl.Scanner, 0) as scanner,
  isnull(cl.[Batts_HAZ], 0) as bathaz,
  isnull(cl.[Batts_NonHaz], 0) as nonbathaz,
  isnull(cl.Projector, 0) as proj,
  isnull(cl.[ThinClient], 0) as thin,
  isnull(cl.Switch, 0) as switch ,
  isnull(cl.Smartboard, 0) as Smartboard,
  isnull(cl.HDD, 0) as HDD,
  cl.[CommissUnits] as commis,
  cl.[NONCommissUnits] as noncommis,
  total_units as Totalunits,
  total_weight as totalweights,
  cl.Total_Kg as coltotalwgt,
  cl.[TotalUnit] as coltotalunits,
  cl.invoiceAmt,
  cast(modifed_date as varchar(50)) as modifed_date,
  (CASE WHEN  ISNULL( collection_date, '1 JAN 1991') IS NOT NULL THEN 'BOOKED' ELSE 'Not Booked' END)  AS Status
FROM
Collections_Log as cl  WITH(nolock)
 join request as rt with(nolock)  on
    cl.OrderNum =   [dbo].[zzfnRemoveNonNumericCharacters](rt.ord)
    left join berresults as br on
    br.OrderNum = cl.OrderNum

WHERE
Request_date_added >= '1 jan 2019' 
  AND Request_ID  LIKE '" . (isset($id) && $id != "" ? $id : "%") . "'
  ";

        if (isset($_POST["collectdate"]) && $_POST["collectdate"] != "") {
            $sql .= "and collection_date ='" . date("d-m-y", strtotime($_POST["collectdate"])) . "'";
        }

        if (isset($ord) && $ord != "") {
            $sql .= "AND ORD LIKE '%" . $_POST["ent"] . "' ";
        } else {
            $sql .= "AND (ORD LIKE '%%' OR ORD IS NULL) ";
        }

        $sql .= "
AND ISNULL(deleted, 0) <> 1 
  AND collection_date IS not NULL
  AND done = 1 AND been_collected = 1
  AND rt.postcode LIKE '%" . (isset($postcode) && $postcode != "" ? '%' . $postcode . '%' : '%') . "%'
  And area1 like '%" . (isset($_POST["areafilter"]) && $_POST["areafilter"] != "" ? $_POST["areafilter"] : '%') . "%' 
ORDER BY
  " . (isset($_POST["filter"]) && $_POST["filter"] != "" ? $_POST["filter"] : "isnull(collection_date,'01/01/1900')  ") . " " . (isset($_POST["filter2"]) && $_POST["filter2"] != "" ? $_POST["filter2"] : "  DESC") . "
  ";

        $stmt = $this->sdb->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string);
        return str_replace("-", " ", preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
    }

    public function getareas()
    {
        $areaquery = "      select distinct area1 as name, area1 as val from request
        WHERE
        Request_date_added BETWEEN DATEADD(yy, DATEDIFF(yy, 0, GETDATE()), 0) AND DATEADD(yy, DATEDIFF(yy, 0, GETDATE()) + 1, -1)
        AND Request_ID  LIKE '%'
        AND (ORD LIKE '%%' OR ORD IS NULL) 
         AND deleted <> 1
        AND collection_date IS not NULL
        AND done = 1 AND been_collected = 1";
        $stmt3 = $this->sdb->prepare($areaquery);

        try {
            $stmt3->execute();
            $datarea = $stmt3->fetchall(\PDO::FETCH_ASSOC);

            $dataArray2 = $datarea;
            $this->response = $dataArray2;
            return $this->response;
        } catch (\Exception $e) {
            Logger::getInstance("ArcData.log")->warning(
                "getareas",
                [$e->getMessage()]
            );
        }
        return [];
    }
}
