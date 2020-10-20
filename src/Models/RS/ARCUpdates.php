<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

/**
 * Class ARCUpdates
 * @package App\Models\RS
 */
class ARCUpdates extends AbstractModel
{
    public $response;
    public $id;

    /**
     * ARCUpdates constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function update()
    {

        $arr = $_POST['arr'];
        $message = $_POST['message'];
        $type = $_POST['type'];
        $user =  $_SESSION['user']['firstname'][0].$_SESSION['user']['lastname'][0];
        
        $Ffw2 = fopen($_SERVER["DOCUMENT_ROOT"]."/arrayAMR_query_data.txt", "a+");
        fwrite($Ffw2, "---------------------------------\n".print_r($arr, true)."\n\n");
        fclose($Ffw2);
        
        foreach ($arr as $value) {
            if ($type == 'amr') {
                $getord = "
        
        select replace(ord, 'ORD-', '') as ord from request where request_id = '".$value."'
              
        
        ";
        
                $Ffw = fopen($_SERVER["DOCUMENT_ROOT"]."/STAGE1AMR_query_data.txt", "a+");
                fwrite($Ffw, "---------------------------------\n".$getord."\n\n");
                fclose($Ffw);
        
        
                $ordstmt = $this->sdb->prepare($getord);
                $ordstmt->execute();
                $ordde = $ordstmt->fetch(\PDO::FETCH_ASSOC);
        
        
        // $Ffw2 = fopen($_SERVER["DOCUMENT_ROOT"]."/AMRcompdastage1.txt","a+");
        // fwrite($Ffw2,"---------------------------------\n". print_r($ordde , true)."\n\n");
        // fclose($Ffw2);
        
        
                $cust = $ordde['Customer_name'];
                $postcode = $ordde['postcode'];
                $ordrn = $ordde['ord'] ?? 00;
        
        
                $cmpsql = "SELECT DISTINCT * from getCompanyinfo(".$ordrn.")";
                $cmpstmt = $this->sdb->prepare($cmpsql);
                $cmpstmt->execute();
                $cmp = $cmpstmt->fetch(\PDO::FETCH_ASSOC);
        
                $Ffw2 = fopen($_SERVER["DOCUMENT_ROOT"]."/AMRcompda.txt", "a+");
                fwrite($Ffw2, "---------------------------------\n".print_r($cmp, true)."\n\n");
                fclose($Ffw2);
        
        
        
                $cmpnumber = $cmp['CMP'] ?? 00000;
        
        
                $colupdate ="
        
        
          update Collections_Log 
          set AMR_Comp = 'yes',
          changedate = getdate(),
          ChangedBy = '".$user."'
          where  ordernum = '".$ordrn."'
        
          update Companies 
          set is_AMR = 'yes'
          where  cmp ='".$cmpnumber."'
        
        
        
          update Collections_Log 
          set RPT = 'AMR:' + isnull(AMR_Comp,'EMPTY') + '' + 'Rebate:'+ isnull(is_Rebate, 'EMPTY')
          where  ordernum = '".$ordrn."'
        
        
        ";
                $fw = fopen($_SERVER["DOCUMENT_ROOT"]."/AMRUPDATE_COL_query_data.txt", "a+");
                fwrite($fw, "---------------------------------\n".$colupdate."\n\n");
                fclose($fw);
        
                $stmtu = $this->sdb->prepare($colupdate);
                $stmtu->execute();
        
        
                $fw = fopen($_SERVER["DOCUMENT_ROOT"]."/AMRUPDATE_COL_query_data.txt", "a+");
                fwrite($fw, "---------------------------------\n".$colupdate."\n\n");
                fclose($fw);
            } elseif ($type == 'cod') {
                $getord = "
        select replace(ord, 'ORD-', '') as ord  from request where request_id = '".$value."'
        
        ";
        
                $Ffw = fopen($_SERVER["DOCUMENT_ROOT"]."/STAGE1AMR_query_data.txt", "a+");
                fwrite($Ffw, "---------------------------------\n".$getord."\n\n");
                fclose($Ffw);
        
        
                $ordstmt = $this->sdb->prepare($getord);
                $ordstmt->execute();
                $ordde = $ordstmt->fetch(\PDO::FETCH_ASSOC);
        
        
                $cust = $ordde['Customer_name'];
                $postcode = $ordde['postcode'];
                $ordrn = $ordde['ord'];
        
        
                
        
        
                $colupdate ="
          update Collections_Log 
          set COD_pwork_Checked = 'YES',
          changedate = getdate(),
          ChangedBy = '".$user."'
          where  ordernum = '".$ordrn."'
        ";
        
                $fw = fopen($_SERVER["DOCUMENT_ROOT"]."/CODUPDATE_COL_query_data.txt", "a+");
                fwrite($fw, "---------------------------------\n".$colupdate."\n\n");
                fclose($fw);
        
                $stmtu = $this->sdb->prepare($colupdate);
                $stmtu->execute();
        
        
                $fw = fopen($_SERVER["DOCUMENT_ROOT"]."/CODUPDATE_COL_query_data.txt", "a+");
                fwrite($fw, "---------------------------------\n".$colupdate."\n\n");
                fclose($fw);
            } elseif ($type == 'rebate') {
                $getord = "
              select replace(ord, 'ORD-', '') as ord from request where request_id = '".$value."'
              
              ";
              
        
                $Ffw = fopen($_SERVER["DOCUMENT_ROOT"]."/STAGE1AMR_query_data.txt", "a+");
                fwrite($Ffw, "---------------------------------\n".$getord."\n\n");
                fclose($Ffw);
        
        
                $ordstmt = $this->sdb->prepare($getord);
                $ordstmt->execute();
                $ordde = $ordstmt->fetch(\PDO::FETCH_ASSOC);
        
        
        
        
        
                $cust = $ordde['Customer_name'];
                $postcode = $ordde['postcode'];
                $ordrn = $ordde['ord'];
        
                $cmpsql = "SELECT DISTINCT * from getCompanyinfo(".$ordrn.")";
                $cmpstmt = $this->sdb->prepare($cmpsql);
                $cmpstmt->execute();
        
        
                $cmp = $cmpstmt->fetch(\PDO::FETCH_ASSOC);
        
        
        
                $colupdate ="
        
        
        
          update Collections_Log 
          set Rebate = '".$message."',
          IS_REBATE = 'Yes',
          AMR_Comp = 'Yes',
          changedate = getdate(),
          ChangedBy = '".$user."'
          where  ordernum = '".$ordrn."'
        
          
          update Collections_Log 
          set RPT = 'AMR:' + isnull(AMR_Comp,'yes') + '' + 'Rebate:'+ isnull(Rebate, 'yes')
          where  ordernum = '".$ordrn."'
        
          update Companies 
          set is_Rebate = '".$message."',
          is_AMR = 'yes'
          where  cmp = '".$cmp['cmp']."'
        ";
                $fw = fopen($_SERVER["DOCUMENT_ROOT"]."/no_COL_query_data.txt", "a+");
                fwrite($fw, "---------------------------------\n".$colupdate."\n\n");
                fclose($fw);
        
                $stmtu = $this->sdb->prepare($colupdate);
                $stmtu->execute();
            } else {
                $fw = fopen($_SERVER["DOCUMENT_ROOT"]."/no_COL_query_data.txt", "a+");
                fwrite($fw, "---------------------------------\n".$colupdate."\n\n");
                fclose($fw);
            }
        }
        
        
        //var_dump($arr);
        
        // echo $sqlii;
    }
}
