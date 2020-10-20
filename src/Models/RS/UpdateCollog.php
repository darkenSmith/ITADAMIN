<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

/**
 * Class UpdateCollog
 * @package App\Models\RS
 */
class UpdateCollog extends AbstractModel
{
    public $response;
    public $id;

    /**
     * ApprovData constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }

    public function update()
    {
        function nanremove($value)
        {

            if ($value == 'NaN') {
                $value  = 0;
            }
            return $value;
        }
        
        $pcqty = nanremove($_POST['pcqty']) ?? 0;
        $pcotherqty = nanremove($_POST['pcotherqty']) ?? 0;
        
        $pcber = nanremove($_POST['pcber']) ?? 0;
        $pcotherber = nanremove($_POST['pcotherber']) ?? 0;
        $pcwgt = nanremove($_POST['totalpcwgt']) ?? 0;
        $iserespc = nanremove($_POST['pcwgt']) ?? 0;
        
        $pctotal = nanremove($_POST['pctotal']) ?? 0;
        $pcothertotal = nanremove($_POST['pcothertotal']) ?? 0;
        
        $pcotherwgt = nanremove($_POST['pcotherwgt']) ?? 0;
        
        $aioqty = nanremove($_POST['aioqty']) ?? 0;
        $aiootherqty = nanremove($_POST['aiootherqty']) ?? 0;
        
        $aiootherwgt = nanremove($_POST['aiootherwgt']) ?? 0;
        
        $aiober = nanremove($_POST['aiober']) ?? 0;
        $aiootherber = nanremove($_POST['aiootherber']) ?? 0;
        $iseriesaiowgt = nanremove($_POST['aiowgt']) ?? 0;
        $aiowgt = nanremove($_POST['totalaiowgt']) ?? 0;
        
        $aiototal = nanremove($_POST['aiototal']) ?? 0;
        $aioothertotal = nanremove($_POST['aioothertotal']) ?? 0;
        
        $aioappqty = nanremove($_POST['aioappqty']) ?? 0;
        $aioappber = nanremove($_POST['aioappber']) ?? 0;
        $aioappwgt = nanremove($_POST['aioappwgt']) ?? 0;
        $aioapptotal = nanremove($_POST['aioapptotal']) ?? 0;
        
        
        $lapotherwgt = nanremove($_POST['lapotherwgt']) ?? 0;
        $lapqty = nanremove($_POST['lapqty']) ?? 0;
        $lapotherqty = nanremove($_POST['lapotherqty']) ?? 0;
        $lapber = nanremove($_POST['lapber']) ?? 0;
        $lapotherber = nanremove($_POST['lapotherber']) ?? 0;
        $isereslapwgt = nanremove($_POST['lapwgt']) ?? 0;
        $lapwgt = nanremove($_POST['totallapwgt']) ?? 0;
        $laptotal = nanremove($_POST['laptotal']) ?? 0;
        $lapothertotal = nanremove($_POST['lapothertotal']) ?? 0;
        $nspqty = nanremove($_POST['nspqty']) ?? 0;
        $nspber = nanremove($_POST['nspber']) ?? 0;
        $nspwgt = nanremove($_POST['nspwgt']) ?? 0;
        $nsptotal = nanremove($_POST['nsptotal']) ?? 00;
        
        $spqty = nanremove($_POST['spqty']) ?? 0;
        $spber = nanremove($_POST['spber']) ?? 0;
        $spwgt = nanremove($_POST['spwgt']) ?? 0;
        $sptotal = nanremove($_POST['sptotal']) ?? 0;
        
        $appspqty = nanremove($_POST['appspqty']) ?? 0;
        $appspber = nanremove($_POST['appspber']) ?? 0;
        $appspwgt = nanremove($_POST['appspwgt']) ?? 0;
        $appsptotal = nanremove($_POST['appsptotal']) ?? 0;
        
        $apptabqty = nanremove($_POST['apptabqty']) ?? 0;
        $apptabber = nanremove($_POST['apptabber']) ?? 0;
        $apptabwgt = nanremove($_POST['apptabwgt']) ?? 0;
        $apptabtotal = nanremove($_POST['apptabtotal']) ?? 0;
        
        
        $tabqty = nanremove($_POST['tabqty']) ?? 0;
        $tabber = nanremove($_POST['tabber']) ?? 0;
        $tabwgt = nanremove($_POST['tabwgt']) ?? 0;
        $tabtotal = nanremove($_POST['tabtotal']) ?? 0;
        
        
        $tftqty = nanremove($_POST['tftqty']) ?? 0;
        $tftber = nanremove($_POST['tftber']) ?? 0;
        $tftwgt = nanremove($_POST['tftwgt']) ?? 0;
        $tfttotal = nanremove($_POST['tfttotal']) ?? 0;
        
        
        $tfttvber = nanremove($_POST['tfttvber']) ?? 0;
        $tfttvwgt = nanremove($_POST['tfttvwgt']) ?? 0;
        
        
        $srvqty = nanremove($_POST['srvqty']) ?? 0;
        $srvber = nanremove($_POST['srvber']) ?? 0;
        $srvwgt = nanremove($_POST['srvwgt']) ?? 0;
        $srvtotal = nanremove($_POST['srvtotal']) ?? 0;
        
        
        
        $swber = nanremove($_POST['swber']) ?? 0;
        $swwgt = nanremove($_POST['swwgt']) ?? 0;
        
        $smber = nanremove($_POST['smber']) ?? 0;
        $smwgt = nanremove($_POST['smwgt']) ?? 0;
        
        $priber = nanremove($_POST['priber']) ?? 0;
        $priwgt = nanremove($_POST['priwgt']) ?? 0;
        
        $mfdber = nanremove($_POST['mfdber']) ?? 0;
        $mfdwgt = nanremove($_POST['mfdwgt']) ?? 0;
        
        $bathazber = nanremove($_POST['bathazber']) ?? 0;
        $bathazwgt = nanremove($_POST['bathazwgt']) ?? 0;
        
        $batnonhazber = nanremove($_POST['batnonhazber']) ?? 0;
        $batnonhazwgt = nanremove($_POST['batnonhazwgt']) ?? 0;
        
        
        $projber = nanremove($_POST['projber']) ?? 0;
        $projwgt = nanremove($_POST['projwgt']) ?? 0;
        
        $crtber = nanremove($_POST['crtber']) ?? 0;
        $crtwgt = nanremove($_POST['crtwgt']) ?? 0;
        
        $thinber = nanremove($_POST['thinber']) ?? 0;
        $thinwgt = nanremove($_POST['thinwgt']) ?? 0;
        
        $scanber = nanremove($_POST['scanber']) ?? 0;
        $scanwgt = nanremove($_POST['scanwgt']) ?? 0;
        
        $pdaber = nanremove($_POST['pdaber']) ?? 0;
        $pdawgt = nanremove($_POST['pdawgt']) ?? 0;
        
        $skyber = nanremove($_POST['skyber']) ?? 0;
        $skywgt = nanremove($_POST['skywgt']) ?? 0;
        
        $gameber = nanremove($_POST['gameber']) ?? 0;
        $gamewgt = nanremove($_POST['gamewgt']) ?? 0;
        
        
        $databer = nanremove($_POST['databer']) ?? 0;
        $datawgt = nanremove($_POST['datawgt']) ?? 0;
        
        $looseber = nanremove($_POST['looseber']) ?? 0;
        $loosewgt = nanremove($_POST['loosewgt']) ?? 0;
        
        $backupber = nanremove($_POST['backupber']) ?? 0;
        $backupwgt = nanremove($_POST['backupwgt']) ?? 0;
        
        $exhardber = nanremove($_POST['exhardber']) ?? 0;
        $exhardwgt = nanremove($_POST['exhardwgt']) ?? 0;
        
        $otherber = nanremove($_POST['otherber']) ?? 0;
        $otherwgt = nanremove($_POST['otherwgt']) ?? 0;
        
        $scrapwgt = nanremove($_POST['scrapwgt']) ?? 0;
        $sweewgt = nanremove($_POST['sweewgt']) ?? 0;
        
        $totalwe = $_POST['totalwe'];
        
        
        
        $ord = $_POST['ord'];
        $totalber = $_POST['totalBER'];
        
        
        
        $dept = $_POST['dept'];
        //$rpt = $_POST['rpt'];
        $accmanager = $_POST['accmanager'];
        $codpaper = $_POST['codpaper'];
        //$datecoll = $_POST['datecoll'];
        $Consign = $_POST['Consign'];
        $shared = $_POST['shared'];
        $nodays = $_POST['nodays'];
        
        $shared = $_POST['shared'];
        $enchanced = $_POST['enchanced'];
        $totaluni = $_POST['totalunits'];
        
        
        
        
        
        
        
        
        
        
        $commis = $pctotal + $pcothertotal + $laptotal +$lapothertotal + $sptotal + $appsptotal + $apptabtotal +
                    $tabtotal  +  $tfttotal + $srvtotal + $swber + $aiototal + $tfttvber + $aioapptotal + $aioothertotal;
        
        $noncommis = $projber + $crtber + $nsptotal + $gameber + $smber + $priber + $mfdber + $batnonhazber + $bathazber
                      + $thinber + $scanber + $pdaber + $skyber + $databer + $looseber +$backupber +
                      $exhardber + $otherber;
        
        
        
        if ($aiootherber == 'NaN') {
            $aiootherber = 0;
        }
        
        
        $bercheck = "select count(*) as c from berresults where ordernum =".$ord;
        
        
        $berexs = $this->sdb->prepare($bercheck);
        $berexs->execute();
        $exs = $berexs->fetch(\PDO::FETCH_ASSOC);
        if (!$exs['c']) {
            $bersql = "
        insert into berresults(ordernum)
        values(".$ord.")
        
        ";
            $berstmt = $this->sdb->prepare($bersql);
            $berstmt->execute();
            $fw = fopen($_SERVER["DOCUMENT_ROOT"]."/is_done_query_berlist.txt", "a+");
            fwrite($fw, "\n"."ber"."\n");
            fclose($fw);
        }
        
        $sqal = "
        
        
        
        update berresults
        SET PC = ".$pcber.",
        [PC_APP] = ".$pcotherber.",
        [All in One] = ".$aiober.",
        AIO_Other = ".$aiootherber.",
        LAPTOP = ".$lapber.",
        LAP_APP = ".$lapotherber.",
        SmartPhone = ".$spber.",
        [ApplePhone] = ".$appspber.",
        [NonSmart] = ".$nspber.",
        Appletab = ".$apptabber.",
        TABLET = ".$tabber.",
        TFT = ".$tftber.",
        [TFT TV] = ".$tfttvber.",
        [SERVER] = ".$srvber.",
        Switch = ".$swber.",
        Smartboard = ".$smber.",
        Printers = ".$priber.",
        [MFD_Printer] = ".$mfdber.",
        [Batts_Haz] = ".$bathazber.",
        [Batts_NonHaz] = ".$batnonhazber.",
        Projector = ".$projber.",
        CRT_Monitor = ".$crtber.",
        [Thin Client] = ".$thinber.",
        Scanner = ".$scanber.",
        PDA = ".$pdaber.",
        SkyBoxes = ".$skyber.",
        GamesConsole = ".$gameber.",
        DataTape = ".$databer.",
        LooseHDD = ".$looseber.",
        BackUpDevs = ".$backupber.",
        ExtHDD = ".$exhardber.",
        AIO_App = ".$aioappber.",
        Other = ".$otherber.",
        total = ".$totalber."
        where OrderNum =".$ord."
        
        
        
        
        
        update Collections_Log 
        set [Printers] = ".$priber.",
        [MFD_Printer] = ".$mfdber.",
        [TV_CRT_Monitor] = ".$crtber.",
        [Scanner] = ".$scanber.",
        [Projector] = ".$projber.",
        [Switch] = ".$swber.",
        [Smartboard] = ".$smber.",
        [Other] = ".$otherber.",
        HDD = ".$looseber.",
        Batts_Haz = ".$bathazber.",
        Batts_NonHaz = ".$batnonhazber.",
        TFT = ".$tftqty.",
        [TFT TV] = ".$tfttvber.",
        [PC] = ".$pcqty.",
        [PC_app] = ".$pcotherqty.",
        AllInOne = ".$aioqty.",
        [AIO_APP] = ".$aioappqty.",
        [laptop] = ".$lapqty.",
        [lap_App] = ".$lapotherqty.",
        [server] = ".$srvqty.",
        [tablet] = ".$tabqty.",
        [Appletablet] = ".$apptabqty.",
        [Applephone] = ".$appspqty.",
        AppleTablet_Kg = ".$apptabwgt.",
        ApplePhone_Kg = ".$appspwgt.",
        NonSmart = ".$nspqty.",
        SmartPhone = ".$spqty.",
        ThinClient = ".$thinber.",
        AIO_Kg = ".$iseriesaiowgt.",
        AIO_App_Kg  = ".$aioappwgt.",
        BackUpDevs_Kg = ".$backupwgt.",
        Batts_Haz_Kg = ".$bathazwgt.",
        Batts_NonHaz_Kg = ".$batnonhazwgt.",
        CRT_Monitor_Kg = ".$crtwgt.",
        [DataTape _Kg] = ".$datawgt.",
        [GamesConsole _Kg] = ".$gamewgt.",
        GeneralWEEE_Kg = ".$sweewgt.",
        HDDs_Kg = ".$loosewgt.",
        Laptop_Kg = ".$isereslapwgt.",
        [LAPTOP_APP_kg] = ".$lapotherwgt.",
        MFD_Printer_Kg = ".$mfdwgt.",
        NonSmartPhone_kg = ".$nspwgt.",
        Other_Kg = ".$otherwgt.",
        pc_kg = ".$iserespc.",
        PC_App_Kg = ".$pcotherwgt.",
        PDAs_Kg = ".$pdawgt.",
        LooseHDD_Kg  = ".$loosewgt.",
        ExHDD_Kg  = ".$exhardwgt.",
        Printer_kg = ".$priwgt.",
        Projector_kg =	".$projwgt.",
        Scanner_kg = ".$scanwgt.",
        [ScrapCable _Kg] = ".$scrapwgt.",
        Server_Kg= ".$srvwgt.",
        [SkyBoxes _Kg] = ".$skywgt.",
        SmartPhone_Kg = ".$spwgt.",
        Smartboard_Kg = ".$smwgt.",
        Switch_kg = ".$swwgt.",
        [TABLET_kg] = ".$tabwgt.",
        TFT_TV_Kg = ".$tfttvwgt.",
        [TFT_kg] = ".$tftwgt.",
        [ThinClient_kg] = ".$thinwgt.",
        [Total_KG] = ".$totalwe." ,
        TotalWeight_Kg = ".$totalwe." ,
        [CommissUnits] = ".$commis.",
        [NonCommissUnits] = ".$noncommis.",
        dept = '".$dept."',
        [AccountManager] = '".$accmanager."',
        COD_pwork_Checked = '".$codpaper."',
        [ConsignmentNoteCode] = '".$Consign."',
        [SharedWith] = '".$shared."',
        wipe = '".$enchanced."',
        [TotalUnit] = '".$totaluni."',
        modifed_date = getdate(),
        changedate = getdate()
        
        
        where OrderNum =".$ord."
        
        update Collections_Log 
        set GeneralWEEE_percent = (GeneralWEEE_Kg/[Total_KG])
        where OrderNum =".$ord."
        
        
        update Collections_Log 
        set NumOfDaysOld = datediff(day, DateCollected, getdate()) 
        where OrderNum =".$ord;
        
        $fh = fopen($_SERVER["DOCUMENT_ROOT"]."/wgtoutput.txt", "a+");
        fwrite($fh, $sqal."\n");
        fclose($fh);
        
        
        $stmtup = $this->sdb->prepare($sqal);
        $stmtup->execute();
        
        
        $fh = fopen($_SERVER["DOCUMENT_ROOT"]."/wgtoutput.txt", "a+");
        fwrite($fh, $sqal."\n");
        fclose($fh);
    }
}
