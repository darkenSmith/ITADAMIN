<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use Exception;

/**
 * Class NewCompany
 * @package App\Models
 */
class NewCompany extends AbstractModel
{

    public $response;

    /**
     * CompanyUpdate constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        parent::__construct();
    }


    public function add()
    {

        
            
        $name = $_POST['compname'];
        $locat = $_POST['locat'];
        $dept = $_POST['deptpart'];
        $owner = $_POST['own'];
        $gdpr = $_POST['gdprcon'];
        $amr = $_POST['rebatestat2'];
        $reb = $_POST['rebatestat'];
        $notes = $_POST['notes'];
//$prevown = $_POST['prevown'];
//$dataadd = $_POST['dataadd'];
        $crmid = $_POST['crmid'];
        $cmpid = $_POST['cmpid'];
        $shrdwith = $_POST['shrdwith'];
//$type = $_POST['type '];






        $safename = $this->clean_data($name, "text");
        $safelocat = $this->clean_data($locat, "text");
        $safedept = $this->clean_data($dept, "text");
//$safeowner = $this->clean_data($owner ,"text");
        $safegdpr = $this->clean_data($gdpr, "text");
        $safeamr = $this->clean_data($amr, "text");
        $safereb = $this->clean_data($reb, "text");
        $safenotes = $this->clean_data($notes, "text");
//$safeprevown = $this->clean_data($prevown, "text");
        $safeshared = $this->clean_data($shrdwith, "text");
//$safetype = $this->clean_data($type, "text");


        $safecrm = $this->clean_data($crmid, "text");
        $safecmpid = $this->clean_data($cmpid, "text");



        $sqlcheckcomp = "select count(*) c from Companies where CompanyName like '".$safename."'";


        $stmtcheck = $this->sdb->prepare($sqlcheckcomp);
        if (!$stmtcheck) {
            echo "failed to check company";
            die();
        }
        $stmtcheck->execute();
        $data = $stmtcheck->fetch(\PDO::FETCH_ASSOC);

        if (!$data['c'] > 0) {
            $sql = "

INSERT INTO Companies([CompanyName], Location, Department, [Owner], GDPR, is_AMR, is_Rebate, Notes, [DateAdded], cmp, crm, sharedWith)
values('".$safename."','".$safelocat."','".$safedept."','".$owner."','".$safegdpr."','".$safeamr."', '".$safereb."', '".$safenotes."', GETDATE(), '".$safecrm."', '".$safecmpid."', '".$safeshared."')

";



            $stmt = $this->sdb->prepare($sql);
            if (!$stmt) {
                echo "failed to add company";
  
                die();
            }
            $stmt->execute();

            return "success";
        } else {
            return "company already in company tab";
        }
    }


    public function santizestring($string)
    {
        $clean = filter_var($string, FILTER_SANITIZE_STRING);
        return $clean;
    }
      
    public function clean_data($value, $type)
    {
        if ($type == "text") {
                          $value = preg_replace("/[^a-zA-Z0-9\-\_\ go]/", "", $value);
        }
        if ($type == "email") {
                      $value = preg_replace("/[^a-zA-Z0-9\-\.\@]/", "", $value);
        }
        if ($type == "date") {
                      $value = preg_replace("/[^a-zA-Z0-9\-\.\@\\\/]/", "", $value);
        }
        if ($type == "password") {
                      $value = preg_replace("/[\'\"\;]/", "", $value);
        }
        if ($type == "number") {
                      $value = preg_replace("/![0-9]/", "", $value);
        }
        if ($type == "array") {
                      $value = preg_replace("/[^a-zA-Z0-9\,]/", "", $value);
        }
        if ($type == "mac") {
                      $value = preg_replace("/[^a-zA-Z0-9\,\:]/", "", $value);
        }
            $clean = $this->santizestring($value);
            return $value;
    }
}
