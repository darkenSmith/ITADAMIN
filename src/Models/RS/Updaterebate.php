<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use Exception;

/**
 * Class Updaterebate
 * @package App\Models\RS
 */
class Updaterebate extends AbstractModel
{
    public $response;
    public $id;

    /**
     * Updaterebate constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }

    public function update()
    {
        
       
$id = $_POST['id'];
$ord = $_POST['ord'];
$mon = $_POST['mon'];
$datee = $_POST['datee'];
$name = $_POST['name'];
$val1 = $_POST['val1'];
$val2 = $_POST['val2'];
$val3 = $_POST['val3'];
$invdate = $_POST['invdate'];
$cinn = $_POST['cinn'];
$status = $_POST['status'];


$safeord = $this->clean_data($ord ,"text");
$safemon= $this->clean_data($mon ,"text");
//$safedatee= clean_data($datee ,"date");
$safename = $this->clean_data($name ,"text");
$safeval1 = $this->clean_data($val1  ,"number");
$safeval2 = $this->clean_data($val2  ,"number");
$safeval3 = $this->clean_data($val3  ,"number");
//$safeinvdate= clean_data($invdate ,"date");
$safecinn = $this->clean_data($cinn ,"text");
$safestatus = $this->clean_data($status ,"text");

if($datee == ''){

  $timin = 'NULL';
}

if($invdate == ''){

  $invdate = 'NULL';
}else{
  
$timin =  date("d-m-Y",strtotime($datee));
$timin2 =  date("d-m-Y",strtotime($invdate));
}


$sql = "
set language british;
update rebate
set [Month] = '".$safemon."',
[DateSent] = '".$timin."',
[CustomerName] = '".$safename."',
ValueExclVAT = ".$safeval1.",
InvValueExclVAT = ".$safeval2.",
DiffExclVAT = ".$safeval3.",
DateInvReceived = '".$timin2."',
CommentsInvNumber = '".$cinn."',
Status = '".$status."'
where RebateID = '".$id."'

";

$stmt = $this->sdb->prepare($sql);
if (!$stmt) {
  // echo "\nPDO::errorInfo():\n";
  // print_r($this->sdb->errorInfo());
  die();
}
$stmt->execute();


$sqlcheck = "update Rebate
set Status = 'Claimed'
where [CommentsINVNumber] not like '' and  [DiffExclVAT] = 0";
$stmtcheck = $this->sdb->prepare($sqlcheck);
$stmtcheck->execute();



        
        // echo $sqlii;
    }


    public function Boosted(){
             

$ord = $_POST['ord'];
$cinn = $_POST['cinn'];




$safeord = $this->clean_data($ord ,"text");
$user = $_SESSION['user']['firstname'][0] . $_SESSION['user']['lastname'][0];
$ordernum = $safeord;

$sqlup = "
      UPDATE rebate 
        set 
        InvValueExclVAT = ValueExclVAT,
        updateby = '" . $user . "',
        status = 'Claimed', 
        CommentsInvNumber = '".$cinn.":BOOST',
        DiffExclVAT = 0,
        DateInvReceived = GETDATE(),
        CommentsInvNumber = '".$cinn."',
        WHERE replace(ORD, 'ORD-', '') = replace('" . $ordernum. "','ORD-', '') ";
    $dataof = $this->sdb->prepare($sqlup);
    $dataof->execute();

    $sqp = "update Collections_Log
            SET  invoiceAmt = ValueExclVAT,
            invoicedate = getdate()
            FROM REBATE as r with(nolock)
            join collections_log as cl with(nolock) on
            cl.OrderNum = r.ord
            where  replace(ORD, 'ORD-', '') = replace('" . $ordernum . "','ORD-', '')
          ";
    $dataof2 = $this->sdb->prepare($sqp);
    $dataof2->execute();

          $sboostup = "update boosts
          set Status = 'Claimed'
          replace(b.ORD, 'ord-', '') = replace($ordernum, 'ord-', '')
        ";
          $dataof3 = $this->sdb->prepare($sboostup);
          $dataof3->execute();

          return "boosted";





    }


    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
         
        return str_replace("-", " ", preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
    }

    public function clean_data($value,$type) {
      if ($type == "text") {
                      $value = preg_replace("/[^a-zA-Z0-9\-\_\ go]/","",$value);
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
