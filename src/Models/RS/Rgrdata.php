<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use Office365\Runtime\Http\Response;

/**
 * Class Rgrdata
 * @package App\Models\RS
 * * alex 
 */
class Rgrdata extends AbstractModel
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

        if (isset($_GET['ye'])){

              strip_tags($_GET['ye']);
          
          
                   $ord = filter_var ($_GET['ye'], FILTER_SANITIZE_STRING); 
        
        $sqlcust = "select Customer from Collections_Log as c where OrderNum =".$ord;
        $stmtc = $this->sdb->prepare($sqlcust);
        if (!$stmtc) {
          //echo "\nPDO::errorInfo():\n";
          //print_r($conn->errorInfo());
          die();
        }
        $stmtc->execute();
        $data2 = $stmtc->fetch(\PDO::FETCH_ASSOC);
        $_SESSION['customer'] = $data2['Customer'];

        $t = $ord;

        $sqls = "SELECT
[Printers] as pri,
MFD_Printer as mfd,
[TV_CRT_Monitor] as tvcrt,
[Scanner] as scan,
[Batteries] as bat,
[PDA] AS PDA,
SkyBoxes AS skybox,
[Projector] as pro,
[ThinClient] as thin,
[Switch] as sw,
[Smartboard] as smab,
[Other] as oth,
[Batts_Haz] as bathaz,
Batts_NonHaz as batnon,
[applephone] as appphne,
[appletablet] as appptab,
TFT as tftqty,
[TFT TV] as tfttvwty,
[PC] as pcqty,
[PC_APP] as pcappqty,
--[PC_OTHER] AS PCO,
--[PC_AMD] AS PCAMD,
[ALLINONE] AS aioqty,
[Aio_app] AS aioappqty,
--AIO_OTHER AS AIOOTHER,
--AIO_AMD AS AIOAMD,
[laptop] as lapqty,
lap_app as lapappqty,
--[laptop_OTHER] as LAPOTHER,
--[laptop_AMD] as LAPamd,
[server] as serverqty,
[tablet] as tabqty,
[NonSmart] asnonsphoneqty,
[SmartPhone] as sphoneqty,
MFD_Printer as mfdqty,
[thinclient] as thinc,
AIO_Kg as AIO_wgt,
LAPTOP_APP_kg as lapappkg,
[AIO_APP_kg] as AIO_APP_wgt,
[AIO_Other_kg] as AIO_Other_wgt,
[AIO_AMD_kg] as AIO_AMD_wgt,
[applePhone_kg] AS Appphnewgt,
AppleTablet_Kg AS apptabwgt,
BackUpDevs_Kg AS Back_Up_dev_wgt,
ExHDD_Kg as ex_wgt,
[Batteries_kg] as batts_wgt,
[Batts_Haz_kg] as batt_haz_wgt,
Batts_NonHaz_Kg as batt_non_haz_wgt,
CRT_Monitor_Kg as CRT_Mon_wgt,
[DataTape _Kg] as Datatape_wgt,
[GamesConsole _Kg] as game_console_wgt,
GeneralWEEE_Kg as Gen_WEE_wgt,
[HDDs_kg] AS HDD_wgt,
LooseHDD_Kg as loose_wgt,
[LAPTOP_kg] Laptop_wgt,
[LAPTOP_Other_kg] Laptop_other_wgt,
[LAPTOP_AMD_kg] Laptop_amd_wgt,
isnull(Laptop_Kg, 0) as Lapappwgt,
MFD_Printer_Kg as MFD_printer_wgt,
NonSmartPhone_kg as nonphone_wgt,
[Other_kg] as Other_wgt,
[PC_kg] as PC_wgt,
[PC_AMD_kg] AS PC_AMD_wgt,
PC_Other as PC_Other_wgt,
[PC_APP_kg] as PC_app_wgt,
[PDAs_kg] as PDA_wgt,
Printer_kg as Printers_wgt,
[Projector_kg] as	Projector_wgt,
[Scanner_kg] as Scanner_wgt,
[ScrapCable _Kg] as Scrap_Cab_wgt,
[SERVER_kg] as Server_wgt,
[SkyBoxes _Kg] as Sky_Box_wgt,
[GamesConsole _Kg] AS game_wgt,
[DataTape _Kg]  as dtape_wgt,
[SmartPhone_kg] as Smart_Phone_wgt,
[Smartboard_kg] as Smart_Board_wgt,
[Switch_kg] as Switch_wgt,
[TABLET_kg] as Tablet_wgt,
[TFT_TV_kg] as TFT_TV_wgt,
[TFT_kg] as TFT_wgt,
[ThinClient_kg] as Thin_wgt,
TV_CRT_Monitor_kg as tvcrtmonwgt,
AMR_Comp as AMR,
 Rebate as reabate,
 wipe as Enchancedwipe,
 convert(varchar(50), [Booking In Date], 103) as coldate,
Total_Kg as totalkg

  FROM Collections_Log WHERE ordernum LIKE ".$t;



$stmt = $this->sdb->prepare($sqls);
$stmt->execute();
$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);




$compquery = "

set language british; 

select
'AMR:' + c.is_AMR + ' ' + 'Rebate:'+ c.is_Rebate as RPT,
department as dept,
c.is_AMR AS AMR,
--[AccountManager],
COD_pwork_Checked as cod, 
[Booking In Date] as coldate, 
[ConsignmentNoteCode], 
[dbo].[fn_WorkDays](cl.[Booking In Date], getdate()) as nodays,
[Booking In Date] as coldate,
 c.is_Rebate as reb,
 c.sharedWith as sw,
 cl.wipe as wipeecnchan,
 c.Owner as [AccountManager]
 from Collections_Log as cl with(nolock)
 left join Companies as c with(nolock) on
 c.Location = cl.Postcode and
 c.Location+c.[CompanyName] = cl.Postcode+cl.Customer
 where OrderNum = ".$t."  
 --and c.[Date Added] <> '' 
 ";
////this needs to be set to use CMP numbers
$collstmt = $this->sdb->prepare($compquery);
$collstmt->execute();
$coldata = $collstmt->fetch(\PDO::FETCH_ASSOC);


/*

select 
isnull([TFT], 0) as tftber
,isnull([TFT TV], 0) as tfttvber
,isnull([PC], 0) as berpc
,isnull([All in One], 0) as aiober
,isnull([LAPTOP], 0) as lapber
,isnull([SERVER], 0) as serber
,isnull([TABLET], 0) as bertab
,isnull([Smart Phone], 0) as bersp
,isnull([Non Smart], 0) as bernonpne
,isnull([Printers], 0) as berpri
,isnull([MFD Printers], 0) as bermfd
,isnull([CRT Monitors], 0) as crtmon
,isnull([TV CRT Monitors], 0) [TV CRT Monitors]
,isnull([Scanner], 0) as berscaner
,isnull([Batteries], 0) as [Batteries]
,isnull([Projector], 0) as berproj
,isnull([Thin Client], 0) as berthin
,isnull([Switch], 0) as berswi
,isnull([Smartboard], 0) as bersmb
,isnull([batt_haz], 0)  as berbathaz
,isnull([Other], 0) as berother
,isnull([Batts_non Haz], 0) as bernonhaz
,[gen WEE%]
,isnull([HDD], 0) as [HDD]
,isnull([ApplePhone], 0) as beraphne
,isnull([Appletab], 0)  as berrapptab
,isnull([PDA], 0) as berpda
,isnull([Sky Boxes], 0) as bersky
,isnull([Games Consoles], 0) as bergame
,isnull([Data Tapes], 0) as berdatatape
,isnull([Loose Hard Drives], 0) as berloose
,isnull([Back Up Devices], 0) as berbackup
,isnull([Ext Hard Drives], 0) as berexhard
,isnull([Total], 0) as [Total] 
,isnull([AIO_APP], 0) as AIOappber
,isnull(pc_app, 0) as pc_app
,isnull(lap_app, 0) as lapappber

*/


$berquery = "

select 
[TFT] as tftber
,[TFT TV] as tfttvber
,[PC] as berpc
,[All in One] as aiober
,[LAPTOP] as lapber
,[SERVER] as serber
,[TABLET] as bertab
,[SmartPhone] as bersp
,[NonSmart] as bernonpne
,[Printers] as berpri
,[MFD_Printer] as bermfd
,CRT_Monitor as crtmon
,TV_CRT_Monitor tvcrtber
,[Scanner] as berscaner
,[Batteries] as [Batteries]
,[Projector] as berproj
,[Thin Client] as berthin
,[Switch] as berswi
,[Smartboard] as bersmb
,[Batts_Haz]  as berbathaz
,[Other] as berother
,[Batts_NonHaz] as bernonhaz
,GeneralWEEE_percent
,[HDD] as [HDD]
,[ApplePhone] as beraphne
,[Appletab]  as berrapptab
,[PDA] as berpda
,[SkyBoxes] as bersky
,GamesConsole as bergame
,DataTape as berdatatape
,LooseHDD as berloose
,BackUpDevs as berbackup
,ExtHDD as berexhard
,[Total] as [Total] 
,AIO_App as AIOappber
,pc_app as pc_app
,lap_app as lapappber


 from berresults AS b where ordernum =".$t;

$berst = $this->sdb->prepare($berquery);
$berst->execute();
$berda = $berst->fetch(\PDO::FETCH_ASSOC);





$table = "";

foreach($data as $row) {




  $table .=  "
   <table id='tb' class='table table-striped'>
    <thead>
   <tr>
   
     <th> Product </th>
    <th style='background-color:#ADFF2F'> BL </th>
    <th style='background-color:#00FFFF'> BER </th>
    <th> Weight </th>
  </tr>
  </thead>
  <tbody>

  <tr>
  <th>TFT</th>
  <td class='keyvalue'><input type='number' id='colq' value= '".$row['tftqty']."' class='qty' ></td>
  <td class='keyvalue'> <input type='number' id='tftber2col' value='".$berda['tftber']."' class='ber' ></td>
  <td class='keyvalue'><input type='number' id='col' value='".$row['TFT_wgt']."'  class='wgt' ></td>
  </tr>

  <tr>
  <th> TFT TV </th>
  <td class='keyvalue'style='background-color:black;'></td>
  <td class='keyvalue'> <input type='number' id='ber1col' value='".$berda['tfttvber']."' class='ber'></td>
  <td class='keyvalue'><input type='number' id='col1' value='".$row['TFT_TV_wgt']."' class='wgt'></td>
  </tr>


  <tr>
  <th> PC </th>
  <td class='keyvalue'><input type='number' id='col2q' value='".$row['pcqty']."' class='qty'></td>
  <td class='keyvalue'> <input type='number' id='ber2col' value='".$berda['berpc']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col2' value='".$row['PC_wgt']."' class='wgt' ></td>
  </tr>

  <tr>
  <th> PC - Apple </th>
  <td class='keyvalue'><input type='number' id='col2qother' value='".$row['pcappqty']."' class='qty'></td>
  <td class='keyvalue'> <input type='number' id='ber2colother' value='".$berda['pc_app']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col2other'  value='".$row['PC_app_wgt']."' class='wgt' ></td>
  </tr>

 

  <tr>
  <th> All in One </th>
  <td class='keyvalue'> <input type='number' id='col3' value='".$row['aioqty']."'  class='qty' ></td>
  <td class='keyvalue'> <input type='number' id='ber3col' value='".$berda['aiober']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col3q' value='".$row['AIO_wgt']."' class='wgt'></td>
  </tr>


  <tr>
  <th> All in One - Apple </th>
  <td class='keyvalue'> <input type='number' id='appaio' value='".$row['aioappqty']."' class='qty' ></td>
  <td class='keyvalue'> <input type='number' id='berappaio' value='".$berda['AIOappber']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='wgtaio'  value='".$row['AIO_APP_wgt']."' class='wgt'></td>
  </tr>

  <tr>
  <th> LAPTOP </th>
  <td class='keyvalue'> <input type='number' id='col4q' value='".$row['lapqty']."' class='qty'> </td>
  <td class='keyvalue'> <input type='number' id='ber4col' value='".$berda['lapber']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col4' VALUE='".$row['Laptop_wgt']."' class='wgt' > </td>
  </tr>


  <tr>
  <th> LAPTOP - Apple </th>
  <td class='keyvalue'> <input type='number' id='col4qother' value='".$row['lapappqty']."' class='qty'> </td>
  <td class='keyvalue'> <input type='number' id='ber4colother' value='".$berda['lapappber']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col4other' value='".$row['lapappkg']."'  class='wgt' > </td>
  </tr>



  <tr> 
  <th> SERVER </th>
  <td class='keyvalue'><input type='number' id='col5q' value='".$row['serverqty']."' class='qty' > </td>
  <td class='keyvalue'> <input type='number' id='berservcol' value='".$berda['serber']."' class='ber' ></td>
   <td class='keyvalue'><input type='number' id='col5' value='".$row['Server_wgt']."' class='wgt'> </td>
  </tr>




    
  <tr>
  <th> Tablet </th>
  <td class='keyvalue'><input type='number' id='col6q' value='".$row['tabqty']."' class='qty' ></td>
  <td class='keyvalue'> <input type='number' id='bertab2col' value='".$berda['bertab']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col6' value='".$row['Tablet_wgt']."' class='wgt' ></td>
  </tr>

  <tr>
  <th> Tablet Apple </th>
  <td class='keyvalue'><input type='number' id='colapptab2q' value='".$row['appptab']."' class='qty' ></td>
  <td class='keyvalue'> <input type='number' id='berapptab2col' value='".$berda['berrapptab']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='colapptab2' value='".$row['apptabwgt']."' class='wgt'></td>
  </tr>

  <tr>
  <th> Smart Phones </th>
  <td class='keyvalue'><input type='number' id='col7q' value='".$row['sphoneqty']."' class='qty' ></td>
  <td class='keyvalue'> <input type='number' id='berspcol' value='".$berda['bersp']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col7' value='".$row['Smart_Phone_wgt']."' class='wgt' ></td>
  </tr>

  <tr>
  <th>  Phone Apple </th>
  <td class='keyvalue'><input type='number' id='colapp2q' value='".$row['appphne']."' class='qty' ></td>
  <td class='keyvalue'> <input type='number' id='berapp2col' value='".$berda['beraphne']."' class='ber'></td>
  <td class='keyvalue'> <input type='number' id='colapp2' value='".$row['Appphnewgt']."' class='wgt' ></td>
  </tr>

  <tr>
  <th> Non Smart Phones </th>
  <td class='keyvalue'><input type='number' id='colnonq' value='".$row['asnonsphoneqty']."' class='qty' ></td>
  <td class='keyvalue'> <input type='number' id='bernonspcol' value='".$berda['bernonpne']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='colnon7' value='".$row['nonphone_wgt']."' class='wgt' ></td>
  </tr>


  <tr>
  <th> Printers </th>
  <td class='keyvalue'style='background-color:black;'></td>
  <td class='keyvalue'> <input type='number' id='ber9col' value='".$berda['berpri']."' class='ber'></td>
  <td class='keyvalue'> <input type='number' id='col9' value='".$row['Printers_wgt']."' class='wgt'></td>
  </tr>

  
  <tr>
  <th> MFD Printers </th>
  <td class='keyvalue'style='background-color:black;'></td>
  <td class='keyvalue'> <input type='number' value='".$berda['bermfd']."' id='ber10col' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col10' value='".$row['MFD_printer_wgt']."' class='wgt'></td>
  </tr>

  
  <tr>
  <th> CRT Monitors </th>
  <td class='keyvalue'style='background-color:black;'></td>
  <td class='keyvalue'> <input type='number' id='ber20col' value='".$berda['crtmon']."' class='ber' ></td>
  <td class='keyvalue'> <input type='number' id='col11' value='".$row['CRT_Mon_wgt']."' class='wgt' ></td>
  </tr>

  <tr> 
  <th> Scanner</th>  
   <td class='keyvalue'style='background-color:black;'></td>
   <td class='keyvalue'> <input type='number' id='ber13col' value='".$berda['berscaner']."' class='ber' > </td>
  <td class='keyvalue'><input type='number' id='col13' value='".$row['Scanner_wgt']."' class='wgt'> </td>
 </tr>


 <tr>
 <th> Batteries - Haz </th>
 <td class='keyvalue'style='background-color:black;'></td>
 <td class='keyvalue'> <input type='number' value='".$berda['berbathaz']."' id='ber23col' class='ber' ></td>
 <td class='keyvalue'> <input type='number' value='".$row['batt_haz_wgt']."'  id='col23' class='wgt' ></td>
 </tr>


 
 <tr>
 <th> Batteries - Non Haz </th>
 <td class='keyvalue'style='background-color:black;'></td>
 <td class='keyvalue'> <input type='number' value='".$berda['bernonhaz']."' id='ber19col' class='ber'></td>
 <td class='keyvalue'> <input type='number' id='col19' value='".$row['batt_non_haz_wgt']."' class='wgt'></td>
 </tr>

 
 <tr>
 <th> Projector </th>
 <td class='keyvalue'style='background-color:black;'></td>
 <td class='keyvalue'> <input type='number' id='ber15col' value='".$berda['berproj']."' class='ber' ></td>
 <td class='keyvalue'> <input type='number' id='col15' value='".$row['Projector_wgt']."' class='wgt'></td>
 </tr>

 

 <tr>
 <th> Thin Client </th>
 <td class='keyvalue'style='background-color:black;'></td>
 <td class='keyvalue'> <input type='number' id='ber16col' value='".$berda['berthin']."' class='ber' ></td>
 <td class='keyvalue'> <input type='number' id='col1thin' value='".$row['Thin_wgt']."' class='wgt'></td>
 </tr>


  <tr>
  <th> Switches </th>
  <td class='keyvalue'style='background-color:black;'></td>
  <td class='keyvalue'> <input type='number' id='ber17col' value='".$berda['berswi']."' class='ber' ></td>
  <td class='keyvalue'><input type='number' id='col17' value='".$row['Switch_wgt']."' class='wgt'></td>
  </tr>


  <tr>
  <th> SmartBoard </th>
  <td class='keyvalue'style='background-color:black;'></td>
  <td class='keyvalue'> <input type='number' id='ber8col' value='".$berda['bersmb']."' class='ber'></td>
  <td class='keyvalue'> <input type='number' id='col8' value='".$row['Smart_Board_wgt']."' class='wgt'></td>
  </tr>









  <tr>
  <th class='keyvalue'style='background-color:black;'></th>
  <td class='keyvalue'style='background-color:black;color:white;'><strong>Miscellaneous</strong></td>
  <td class='keyvalue'style='background-color:black;'></td>
  <td class='keyvalue'style='background-color:black;'></td>
  </tr>

  <tr>
   
  <th>  </th>
 <th style='background-color:#ADFF2F'> BL </th>
 <th style='background-color:#00FFFF'> BER </th>
 <th> Weight </th>
</tr>


  <tr> 
  <th> PDA</th>  
   <td class='keyvalue'style='background-color:black;'></td>
   <td class='keyvalue'> <input type='number' id='col30ber' value='".$berda['berpda']."' class='ber' > </td>
  <td class='keyvalue'><input type='number' value='".$row['PDA_wgt']."'  id='col30' class='wgt' > </td>
 </tr>

 <tr> 
 <th> Sky Boxes</th>  
  <td class='keyvalue'style='background-color:black;'></td>
  <td class='keyvalue'> <input type='number' id='col29ber' value='".$berda['bersky']."' class='ber' > </td>
 <td class='keyvalue'><input type='number' id='col29' value='".$row['Sky_Box_wgt']."' class='wgt' > </td>
</tr>

<tr> 
<th> Games Consoles</th>  
 <td class='keyvalue'style='background-color:black;'></td>
 <td class='keyvalue'> <input type='number' id='col28ber' value='".$berda['bergame']."' class='ber' > </td>
<td class='keyvalue'><input type='number' id='col28' value='".$row['game_console_wgt']."' class='wgt' > </td>
</tr>

<tr>
<th class='keyvalue'style='background-color:black;'></th>
<td class='keyvalue'style='background-color:black;color:white;'><strong>Data Bearing items(Non PC)</strong></td>
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'style='background-color:black;'></td>
</tr>

<tr>
 
<th>  </th>
<th style='background-color:#ADFF2F'> BL </th>
<th style='background-color:#00FFFF'> BER </th>
<th> Weight </th>
</tr>


<tr> 
<th> Data Tapes</th>  
 <td class='keyvalue'style='background-color:black;'></td>
 <td class='keyvalue'> <input type='number' id='col27ber' value='".$berda['berdatatape']."' class='ber' > </td>
<td class='keyvalue'><input type='number' value='".$row['Datatape_wgt']."' id='col27' class='wgt' > </td>
</tr>

<tr> 
<th> Loose Hard Drives</th>  
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'> <input type='number' id='col13q' value='".$berda['berloose']."' class='ber' > </td>
<td class='keyvalue'><input type='number' id='col1hd' value='".$row['loose_wgt']."' class='wgt' > </td>
</tr>

<tr> 
<th> Back Up Devices</th>  
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'> <input type='number' id='col31ber' value='".$berda['berbackup']."' class='ber' > </td>
<td class='keyvalue'><input type='number' id='col31' value='".$row['Back_Up_dev_wgt']."' class='wgt' > </td>
</tr>

<tr> 
<th> Ext Hard Drives</th>  
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'> <input type='number' id='col32ber' value='".$berda['berexhard']."' class='ber' > </td>
<td class='keyvalue'><input type='number' id='col32' value='".$row['ex_wgt']."' class='wgt' > </td>
</tr>

<tr> 
<th> Other:</th>  
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'> <input type='number' id='col19ber' value='".$berda['berother']."' class='ber'> </td>
<td class='keyvalue'><input type='number' id='col1other' value='".$row['Other_wgt']."' class='wgt'> </td>
</tr>


<tr>
<th class='keyvalue'style='background-color:black;'></th>
<td class='keyvalue'style='background-color:black;color:white;'><strong>Other:</strong></td>
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'style='background-color:black;'></td>
</tr>

<tr>
 
<th>  </th>
<th style='background-color:#ADFF2F'> BL </th>
<th style='background-color:#00FFFF'> BER </th>
<th> Weight </th>
</tr>


<tr> 
<th> Scrap Cable</th>  
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'><input type='number' id='col24' value='".$row['Scrap_Cab_wgt']."' class='wgt' > </td>
</tr>

<tr> 
<th> Genreal WEE</th>  
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'><input type='number' id='col20' value='".$row['Gen_WEE_wgt']."' class='wgt' > </td>
</tr>
<tr>
<th class='keyvalue'style='background-color:black;'></th>
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'style='background-color:black;'></td>
</tr>
<TR>
<th> Total </th>
<th style='background-color:#ADFF2F'> BL </th>
<th style='background-color:#00FFFF'> BER </th>
<th> Weight </th>
</tr>

<tr> 
<th class='keyvalue'style='background-color:black;'></th> 
<td class='keyvalue'> <input type='number' id='totalbl' class='group1' value= 0 > </td>
<td class='keyvalue'> <input type='number' id='totalbr' class='group1' value= 0 > </td>
<td class='keyvalue'><input type='number' id='totalwe' class='group1' value=0 > </td>
</tr>
<tr>
<th> Overrall total </th>
<td class='keyvalue'> <input type='number' id='overtotal' class='group1' value= 0 > </td>
<td class='keyvalue'style='background-color:black;'></td>
<td class='keyvalue'style='background-color:black;'></td>
</tr>

  </tbody>
  </table>

<div id='cldata' class='form-group'>
  <form id = 'collectiondata' method='POST'>

  <label hidden>Dept </label>
  <input hidden id='dept' class='form-control form-control-lg' type='hidden' placeholder='Department' value = '".$coldata['dept']."'>
 
<input hidden id='rpt' class='form-control' type='hidden' placeholder='RPT' value = '".$coldata['RPT']."'>
<label hidden>Account Manager </label>
<input hidden id='accman' class='form-control' type='hidden' placeholder='Account Manager' value = '".$coldata['AccountManager']."'>

<input hidden id='codpap' class='form-control form-control-lg' type='hidden' placeholder='COD Paperwork sent' value = '".$coldata['cod']."'>


<label hidden>Date Collected </label>
<input hidden id='datec' class='form-control' type='hidden' value = '".date("d-m-y",strtotime($coldata['coldate']))."'>
<label hidden>Consignment Note </label>
<input hidden id='conignnum' class='form-control' type='hidden' placeholder='Consignment Note' value = '".$coldata['ConsignmentNoteCode']."'>




<label hidden>shared With </label>
<input hidden id='swith' class='form-control' type='hidden' placeholder='shared With' value = '".$coldata['sw']."' disabled>


<label hidden>No. of Days Old </label>
<input hidden id='nodays' class='form-control' type='hidden' placeholder='No. of Days Old' value = '".$coldata['nodays']."' disabled>


<input id='amr' class='form-control' type='hidden' placeholder='AMR?' value = '".$coldata['AMR']."'  disabled>

<input id='rebh' class='form-control' type='hidden' placeholder='Rebate' value = '".$coldata['reb']."' disabled>

<label >Enchanced Wipe</label>
<input id='enchan' class='form-control' type='text' placeholder='Enhanced wipe?' value = '".$coldata['wipeecnchan']."'>
  </form>
  </div>
  <button id='updatewgt'  class='btn btn-warning'>Update</button>";


}

$this->response = $table;
return $this->response;

        
     }
       // return [];
     
    }

    public function qualifying($id){

      $sql = "exec commisionable ".$id;
      $stmt = $this->sdb->prepare($sql);

      echo $id;
      try{
        $stmt->execute();


      $data = $stmt->fetch(\PDO::FETCH_ASSOC);
      $this->response = $data;
      return $this->response;
      }catch (\Exception $e) {
        var_dump($e->getmessage());
       }

      
 
    }


    
    public function getareas(){
  
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


    public function clean($string) {
      $string = str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
       
      return str_replace("-"," ",preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
    }


}

