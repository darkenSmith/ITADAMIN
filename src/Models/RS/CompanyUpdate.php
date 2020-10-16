<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use Exception;

/**
 * Class CompanyUpdate
 * @package App\Models
 */
class CompanyUpdate extends AbstractModel
{

    public $response;

    /**
     * CompanyUpdate constructor.
     */
    public function __construct()
    {
      $this->sdb = Database::getInstance('sql01');
      $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }


    public function update(){

        
    if(isset($_POST['type']) == 'multi'){

        $arr = $_POST['idarray'];
    
        foreach ($arr  as $value) {
                $delcomp = "
                DELETE from Companies where ID ='".$value."'";
    
                $compstmt = $this->sdb->prepare($delcomp);
                $compstmt->execute();
    
        }
    
    }else{
    
    $name = $_POST['name'];
    $dept = $_POST['dept'];
    $notes = $_POST['notes'];
    $loc = $_POST['loc'];
    $owner = $_POST['owner'];
    $gdpr = $_POST['gdpr'];
    $amr = $_POST['amr'];
    $reb = $_POST['reb'];
    $id = $_POST['id'];
    $crm = $_POST['crm'];
    $cmp = $_POST['cmp'];
    $report = $_POST['report'];
    $shared = $_POST['shared'];
    
    if(isset($_POST['del'])){
    
        $del = $_POST['del'];
        $id = $_POST['id'];
        if($del == 1){
    
    
    
    
        $delcompsql = "delete from Companies where id =".$id;
    
        $delstmt = $this->sdb->prepare($delcompsql);
    if (!$delstmt) {
        echo "\nPDO::errorInfo():\n";
        print_r($this->sdb->errorInfo());
        die();
    }
    $delstmt->execute();
    
    
        }
    
    
    
    
    }if(isset($_POST["del"]) ? $_POST["del"] : '0' <> 1){
        
    
    
    

    
    
    $safename = $this->clean_data($name ,"text");
    $safedept = $this->clean_data($dept ,"text");
    $safeowner= $this->clean_data($owner ,"text");
    $safegdpr = $this->clean_data($gdpr ,"text");
    $safeamr = $this->clean_data($amr  ,"text");
    $safereb = $this->clean_data($reb  ,"text");
    $safenotes = $this->clean_data($notes  ,"text");
    $saferpt = $this->clean_data($report, "text");
    
    
    
    
    
    
    $sql = "
    
    update Companies
    set CompanyName = '".$safename."',
    Department = '".$safedept."',
    Owner = '".$owner."',
    GDPR = '".$safegdpr."',
    is_AMR = '".$safeamr."',
    is_Rebate = '".$safereb."',
    CRM = '".$crm."',
    CMP = '".$cmp."',
    location = '".$loc."',
    notes = '".$safenotes."',
    sharedWith = '".$shared."',
    rpt = '".$saferpt."'
    where ID like '".$id."'
    
    
    
    ";
    
    
    $stmt = $this->sdb->prepare($sql);
    if (!$stmt) {
        echo "\nPDO::errorInfo():\n";
        print_r($this->sdb->errorInfo());
        die();
    }
    $stmt->execute();
        }
    }

        }

    public function clean_data($value,$type) {
            if ($type == "text") {
                            $value = preg_replace("/[^a-zA-Z0-9\-\_\.\@\ go]/","",$value);
            }
            if ($type == "email") {
                            $value = preg_replace("/[^a-zA-Z0-9\-\.\@]/","",$value);
            }
            if ($type == "date") {
                            $value = preg_replace("/[^a-zA-Z0-9\-\.\@\\\/]/","",$value);
            }
            if ($type == "password") {
                            $value = preg_replace("/[\'\"\;]/","",$value);      
            }
            if ($type == "number") {
                            $value = preg_replace("/![0-9]/","",$value);        
            } 
            if ($type == "array") {
                            $value = preg_replace("/[^a-zA-Z0-9\,]/","",$value);                        
            }              
            if ($type == "mac") {
                            $value = preg_replace("/[^a-zA-Z0-9\,\:]/","",$value);                     
            }              
            return $value;
        }


}
