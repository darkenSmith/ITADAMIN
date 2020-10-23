<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use Exception;

/**
 * Class UpdateAps
 * @package App\Models\RS
 */
class UpdateAps extends AbstractModel
{
    public $response;
    public $id;

    /**
     * UpdateAps constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }

    public function update()
    {
        $a_status = $this->clean($_POST['a_status']);
        $p_status = $this->clean($_POST['p_status']);
        $notes_status = $this->clean($_POST['notes_status']);
        $bookstat = $this->clean($_POST['bookingstatus']);
        $drivername = $this->clean($_POST['drivername']);
        $vech = $this->clean($_POST['vehicreg']);
        $constat = $this->clean($_POST['constat']);
        $connote = $this->clean($_POST['connotes']);
        $confemail = $this->clean($_POST['confemail']);
        $sursent = $this->clean($_POST['sursent']);
        $surcomp = $this->clean($_POST['surcomp']);
        $sentby = $_POST['sentby'];
        $jobnote = $this->clean($_POST['jobno']);
        $accnote = $this->clean($_POST['accup']);
        $lor = $this->clean($_POST['lo']);
        $id = $this->clean($_POST['rowid']);
        $flag = $this->clean($_POST['flag']);
        $deadline = $_POST['deadline'];
        $help = $this->clean($_POST['help']);
        $early = $this->clean($_POST['early']);
        $parking = $_POST['parking'];
        $lift = $this->clean($_POST['lift']);
        $ground = $this->clean($_POST['ground']);
        $Steps = $this->clean($_POST['steps']);
        $twoman = $this->clean($_POST['twoman']);
        //$emailsentdate = $this->clean($_POST['emailsentdate']);

        Logger::getInstance("updateAps.log")->debug(
            'manualupdate',
            [$deadline]
        );

        if (!isset($_POST['lortype'])) {
            $lorrytype = '';
        } else {
            $lorrytype = $this->clean($_POST['lortype']);
        }

        $own = $this->clean($_POST['owner']);

        $reqsql = "
        
        update request
        set Help_Onsite = '".$help."',
        [Early Acess notes] = '".$early."',
        [Parking Notes] = '".$parking."',
        lift = '".$lift."',
        steps = '".$Steps."',
        twoman = '".$twoman."',
        ground = '".$ground."'
        where Request_id = '".$id."'
        
        
        ";
        $stmtup = $this->sdb->prepare($reqsql);
        $stmtup->execute();

        Logger::getInstance("updateAps.log")->debug(
            'requestsurvyup',
            [$reqsql]
        );
        
        $sqal = "
        SET LANGUAGE british
        
        declare @date datetime 
        
        set @date = '".$deadline."'
        
        
        UPDATE
          Booked_Collections
        SET
          A = '" .$a_status."',
          P = '" .$p_status."',
          APS_Notes = '" .$notes_status."',
          Job_Notes = '".$jobnote."', 
          Access_Notes = '".$accnote."',
          DriverName  = '".$drivername."',
          VehicleReg = '".$vech."',
          ContractStatus = '".$constat."',
          [ContractNotes] = '".$connote."',
          ConfirmEmailSent = '".$confemail."',
          [SurveySent] = '".$sursent."',
          [SurveyComplete]  = '".$surcomp ."',
          [SentBy]  = '".$sentby."',
          [survey_deadline] = (select  format(@date, 'dd-MM-yyyy hh:mm:ss') ),
          lorry = '".$lor."',
          [owner] = replace('".$own."', ' ', '.') ,
          LorryFlag = '".$flag ."',
          LorryType = '".$lorrytype."',
          booking_status = '".$bookstat."'
        WHERE
          RequestID ='".$id."' ";

        $stmtup = $this->sdb->prepare($sqal);
        $stmtup->execute();

        echo "COMPLETED";
    }


    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens
        return str_replace("-", " ", preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
    }
}
