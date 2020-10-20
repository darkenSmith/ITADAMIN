<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use App\Models\RS\CurlStatuschange;

/**
 * Class ApprovData
 * @package App\Models\RS
 */
class IsDone extends AbstractModel
{
    public $response;
    public $id;

    /**
     * ApprovData constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function tocollected()
    { 
     

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);


        $stuff = $_POST['stuff'];




foreach ($stuff as $value) {

  if(!empty($value)){

  
  $colupdate = "SELECT
  replace(OrD, 'ORD-', '') AS ORD,
    r.request_ID as id,
    r.Customer_name as customer,
    customer_contact as contact,
    customer_email as email,
    contact_tel as tel,
    customer_contact_positon as position,
    add1 + add2 + add3 as address,
    add1 as address1,
    add2 as address2,
    add3 as address3,
    postcode as postcode,
    r.bio_pass as BIOS_Password,
    r.req_col_instrct as CollectionInstruction,
    r.request_col_date as CollectionDate,
    r.collection_date as ProposedDate,
    r.Update_Note as Updatenote,
    r.updatedBy as RequestUpdatedBy,
    r.owner
  FROM
    request AS r with(nolock)
  WHERE
    Request_ID = '".$value."'";

// $fw = fopen($_SERVER["DOCUMENT_ROOT"]."step1is_done_query_data1.txt","a+");
// fwrite($fw,$colupdate);
// fclose($fw);

  $stmtu = $this->sdb->prepare($colupdate);
  $stmtu->execute();
  $ro = $stmtu->fetch(\PDO::FETCH_ASSOC);

//   $fw = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_data1.txt","a+");
//   fwrite($fw,$colupdate);
//   fclose($fw);

  $pos = $ro['postcode'];

  $apicall = new CurlStatuschange();

  $who =  str_replace( '@stonegroup.co.uk', '', $_SESSION['user']['username']);

  $update_req = "
  update request 
  set done = 1,
  laststatus = 'Done',
  modifedby = '".$who."',
  modifydate = getdate()
  where request_id = '".$value."' 
  ";
  $stmtreq = $this->sdb->prepare($update_req);
  $stmtreq->execute();
// out for now cant connect while vpn
 // $apicall->updateAPI($value, 'Done');


  

$newsql = " 



set nocount on
declare @t table(


  customername varchar(100),
  postcode varchar(20),
  ordernum varchar(20),
      TFT  int,
      PC int,
	   PCOther int,
	     PCAMD int,
      [All in One] int,
	      [All in OneOther] int,
		   [All in OneAMD] int,
      LAPTOP int,
	   LAPTOPOther int,
	    LAPTOPAMD int,
      SERVER int,
      [Printers] int,
      [MFD Printers] int,
	    Laptop_Trolly int,
    [TV CRT Monitors] int,
      [Switch] int,
      Projector int,
      Smartboard int,
      Smartphone int,
   ApplePhone int,
 AppleTablet int,
 Tablet int,
 TV int, 
      Other int,
  totalunit int,
  totalweight decimal(8, 2)
  
  
  )
  
  insert into @t
  
  select 

  rt.Customer_name,
  postcode,
  ord,
  isnull((select qty from Req_Detail where prod_id = 11 and req_id = ".$value."), 0) as TFT,
  isnull((select qty from Req_Detail where prod_id = 3 and req_id = ".$value."), 0) as PC,
    isnull((select qty from Req_Detail where prod_id = 55 and req_id = ".$value."), 0) as [PCother],
	    isnull((select qty from Req_Detail where prod_id = 53 and req_id = ".$value."), 0) as [PCAMD],
  isnull((select qty from Req_Detail where prod_id = 31 and req_id = ".$value."), 0) as Allinone_PC,
    isnull((select qty from Req_Detail where prod_id = 61 and req_id = ".$value."), 0) as [Allinone_PCother],
	   isnull((select qty from Req_Detail where prod_id = 63 and req_id = ".$value."), 0) as [Allinone_PCAMD],
  isnull((select qty from Req_Detail where prod_id = 5 and req_id = ".$value."), 0) as Laptop,
    isnull((select qty from Req_Detail where prod_id = 57 and req_id = ".$value."), 0) as LaptopOther,
	  isnull((select qty from Req_Detail where prod_id = 59 and req_id = ".$value."), 0) as LaptopAMD,
  isnull((select qty from Req_Detail where prod_id = 7 and req_id = ".$value."), 0) as Server,
  isnull((select qty from Req_Detail where prod_id = 25 and req_id = ".$value."),0) as DesktopPrinter,
  isnull((select qty from Req_Detail where prod_id = 27 and req_id = ".$value."),0) as Standalone_Printer,
  isnull((select qty from Req_Detail where prod_id = 41 and req_id = ".$value."),0) as Lap_trolly,
  isnull((select qty from Req_Detail where prod_id = 13 and req_id = ".$value."), 0) as CRT,
  isnull((select qty from Req_Detail where prod_id = 43 and req_id = ".$value."), 0) as Switches,
  isnull((select qty from Req_Detail where prod_id = 9 and req_id = ".$value."), 0) as Projector,
  isnull((select qty from Req_Detail where prod_id = 29 and req_id = ".$value."),0) as Smartboard,
    isnull((select qty from Req_Detail where prod_id = 47 and req_id = ".$value."),0) as Smartphone,
  isnull((select qty from Req_Detail where prod_id = 45 and req_id = ".$value."),0) as ApplePhone,
  isnull((select qty from Req_Detail where prod_id = 49 and req_id = ".$value."),0) as AppleTablet,
  isnull((select qty from Req_Detail where prod_id = 51 and req_id = ".$value."),0) as Tablet,
  isnull((select qty from Req_Detail where prod_id = 15 and req_id = ".$value."),0) as TV,

  isnull((select qty from Req_Detail where prod_id IN (19) and req_id = ".$value.")  +
  (select qty from Req_Detail where prod_id IN (21) and req_id = ".$value.") +
  (select qty from Req_Detail where prod_id IN (23) and req_id = ".$value."), 0) as other,
  total_units,
  total_weight
  --p.Product, 
  --(working+ not_working) 
  from request as rt 
  join Req_Detail as r
  on rt.Request_id = r.req_id 
  join productlist as p on
  p.product_ID = r.prod_id
  where Request_id = '".$value."'
  group by Request_id,
  customer_contact,
  postcode,
  total_units,
  total_weight,
  rt.Customer_name,
  ord,
  r.req_id
  
  
  
  
  select 

     customername as custname,
     postcode,
      ordernum as ord, 
  TFT as tftq, 
  PC as pcq,
  PCOther as pco,
    PCAMD as pcamd,
    [All in One] as aioq, 
   [All in OneOther] as aioother, 
   [All in OneAMD] as aioAMD, 
   LAPTOP as lapq,
      LAPTOPOther as lapoth,
	   LAPTOPAmd as lapamd,
    SERVER as servq,
    Printers as printq,
  [MFD Printers] as mfdprq,
  Laptop_Trolly as laptrol,
    Switch as swi,
       [TV CRT Monitors] as crtq,
     Projector as projq, 
      Smartboard as smbq,
      [Smartphone] as sphq,
    [ApplePhone] as aspq,
  [AppleTablet] as atq,
  [Tablet] as tabq,
 [TV] as tvq, 

     Other as oth,
      totalunit as totalu, 
      totalweight as totalw 
      from @t

";


$stmtreqn = $this->sdb->prepare($newsql);
$stmtreqn->execute();
$rw = $stmtreqn->fetch(\PDO::FETCH_ASSOC);


$fy = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_new2.txt","a+");
fwrite($fy,$newsql);
fclose($fy);

  // PHP Date
  $week   = date("W")-1;
  $q = "Q".ceil(date('n', time())/3);
  $date = date("Y");
  $day = date("l");



  //$week = $r['week'];
  //$q = $r['Quarter'];

  $timeconvert = $ro['ProposedDate'];
  $finaltime = date("Y-m-d H:i:s",strtotime($timeconvert));


  $f2 = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_part3","a+");
fwrite($f2,"\n".$timeconvert."\n");
fclose($f2);



  $totals_sql = "

  select
  isnull(rt.owner, '') as owner,
SUM(qty) as totalunits,
sum(qty * convert(DECIMAL(9 , 2),typicalweight)) as totalweight

 from Req_Detail as rq with(nolock) 
join productlist as p on 
p.product_ID = prod_id 
join request as rt with(nolock) on
rt.Request_id = rq.req_id



where rq.req_id =".$value."
group by rq.req_id,   rt.owner";


// $f = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_totals.txt","a+");
// fwrite($f,"\n".print_r($totals_sql,true)."\n");
// fclose($f);

$totalst = $this->sdb->prepare($totals_sql);
$totalst->execute();
$t = $totalst->fetch(\PDO::FETCH_ASSOC);



$f = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_totals.txt","a+");
fwrite($f,"\n".$totals_sql."\n");
fclose($f);





$commissql = "
set nocount on
exec commisionable ".$value."
";
 $commisstmt = $this->sdb->prepare($commissql);
 $commisstmt->execute();
 $commis = $commisstmt->fetch(\PDO::FETCH_ASSOC);


/// needs to be in sync with request_id - ORD


 $ordsql = "
 select replace(SalesOrderNumber, 'ord-', '') as ord from [greenoak].[we3recycler].[dbo].SalesOrders where CustomerPONumber like '%".$value."'

";
 $ordstmt = $this->sdb->prepare($ordsql);
 $ordstmt->execute();
 $orddata = $ordstmt->fetch(\PDO::FETCH_ASSOC);

 $fw = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_dataord.txt","a+");
 fwrite($fw,"\n".isset($orddata['ord'])."\n");
 fclose($fw);


 if(isset($orddata['ord'])){
 // var_dump($orddata['ord']);
$trimord = $orddata['ord']; // returns "d"
}else{

$trimord = '00000';

}



$colcheck = "
select count(*) as c from Collections_Log where Customer like '".$rw['custname']."' and OrderNum ='".$trimord."'";


$fw = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_check.txt","a+");
fwrite($fw,"\n".$colcheck."\n");
fclose($fw);


 $checkstmt = $this->sdb->prepare($colcheck);
 $checkstmt->execute();
 $checkdata = $checkstmt->fetch(\PDO::FETCH_ASSOC);

 $fw = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_check.txt","a+");
 fwrite($fw,"\n".$checkdata['c']."\n");
 fclose($fw);





$user = $_SESSION['user']['username'];

if(!isset($trimord) == false){
$bercheck = "select count(*) from berresults where ordernum =".$trimord;
$berexs = $this->sdb->prepare($bercheck);
$berexs->execute();
$exs = $berexs->fetch(\PDO::FETCH_ASSOC);
if(!isset($exs)){


$bersql = "
insert into berresults(ordernum)
values(".$trimord.")

";
 $berstmt = $this->sdb->prepare($bersql);
 $berstmt->execute();
 $fw = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_berlist.txt","a+");
 fwrite($fw,"\n"."ber"."\n");
 fclose($fw);
  }

}


$sqlgreen = "SELECT
ConsignmentNumber AS consign,
d.WasteTransferNumber as wtn,
SICCode,
c.SiteCode as SiteCode
FROM
[dbo].Delivery AS d
JOIN
SalesOrders AS s ON d.SalesOrderID = s.SalesOrderID
left join company as c with(nolock) on
c.CompanyID = s.CompanyID
WHERE
REPLACE(SalesOrderNumber, 'ORD-', '') LIKE  '".$trimord."'";

$stmtgreen = $this->gdb->prepare($sqlgreen);

$stmtgreen->execute();



$rg = $stmtgreen->fetch(\PDO::FETCH_ASSOC);


//$user = substr(ucwords($name[0]),0, 1).substr(ucwords($name[1]),0, 1);
  /// convert time so its the correct format in sql


  $sqlcompany = " select distinct * from [dbo].[getCompanyinfo]('".$trimord."')";
  $compdata = $this->gdb->prepare($sqlcompany);
  $compdata->execute();
  $compstuff = $compdata->fetch(\PDO::FETCH_ASSOC);

  $compowner =  $compstuff['Owner'];
  $dept = $compstuff['dept'];
  $rpt = $compstuff['rpt'];
  $sharedwith = $compstuff['sharedWith'];

  if($checkdata['c'] == 0){

  $updatelog = "INSERT INTO
    Collections_Log (
    dept, 
    SharedWith,
    Week ,
    Quarter ,
    Year  ,
    [Booking In Date] ,
    DateCollected,
    Day ,
    Customer ,
    Postcode,
    OrderNum ,
    CommissUnits,
    NonCommissUnits,
    ITADAccountManager,
    TFT,
    PC,
    PC_OTHER ,
    PC_AMD ,
    [PC_app],
    AllInOne ,
    AIO_OTHER,
    AIO_AMD,
    LAPTOP ,
    [lap_App],
    LAPTOP_Other ,
    LAPTOP_AMD ,
    SERVER ,
    Printers ,
    MFD_Printer,
    [CRT_Monitor],
    [Switch],
    Projector,
    Smartboard,
    SmartPhone,
    ApplePhone ,
    AppleTablet,
    TABLET, 
    [TFT TV] ,
    Other ,
    TotalUnit ,
    Total_Kg,
    create_date,
    CreatedBy,
    ConsignmentNoteCode
    
 )
  VALUES (
    '".$dept."',
    '".$sharedwith."',
    ".$week.",
    '".$q."',
    '".$date."',
    '".$finaltime."',
    '".$finaltime."',
    '".$day."',
    '".$rw['custname']."',
    '".$pos."',
    ".$trimord.",
    0,
    0,
    '".$compowner."',
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    0,
    getdate(),
    '".$user."',
    '".$rg['SiteCode']."/".$rg['SICCode']."'
  )
  ";

  $fw = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_updatecol.txt","a+");
  fwrite($fw,$updatelog);
  fclose($fw);


   $stmtupdatecol = $this->sdb->prepare($updatelog);
  $stmtupdatecol->execute();


  $fw = fopen($_SERVER["DOCUMENT_ROOT"]."is_done_query_updatecol.txt","a+");
  fwrite($fw,$updatelog);
  fclose($fw);


  $sqlii = "UPDATE request SET done = 1 WHERE request_id =".$value;
  $stmt = $this->sdb->prepare($sqlii);
  $stmt->execute();

    }
  }

}
 
    }

}