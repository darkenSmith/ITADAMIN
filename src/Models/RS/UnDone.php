<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use App\Models\RS\CurlStatuschange;
use Exception;


/**
 * Class UnDone
 * @package App\Models\RS
 */
class UnDone extends AbstractModel
{
    public $response;
    public $id;

    /**
     * UnDone constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function undo()
    { 
        $apicall = new CurlStatuschange();
        if(isset($_POST['arr'])){
        $arr = $_POST['arr'];
        }
        
        $dell = 0;
        
          if(isset($arr)){
        
        
        
        foreach ($arr  as $value) {
        
        
        $getord = "
        select replace(ord, 'ORD-', '')as ord , Customer_name, postcode  from request where request_id = '".$value."'
        
        ";
        
        $Ffw = fopen($_SERVER["DOCUMENT_ROOT"]."/STAGE1_query_data.txt","a+");
        fwrite($Ffw,"---------------------------------\n".$getord."\n\n");
        fclose($Ffw);
        
        
        $ordstmt = $this->sdb->prepare($getord);
        $ordstmt->execute();
        $ordde = $ordstmt->fetch(\PDO::FETCH_ASSOC);
        $who =  str_replace( '@stonegroup.co.uk', '', $_SESSION['user']['username']);
        
        
          $colupdate ="
        
        
         delete from Collections_Log where OrderNum = '".$ordde['ord']."'
        
        
        update request
        set done = ".$dell.",
        laststatus = 'Booked',
        modifydate = getdate(),
        updatedBy = '".$who."'
        where Request_ID =".$value."
        
          
        update Booked_Collections
        set booking_status = 'undone'
        where RequestID ='".$value."'
        ";
        
          $stmtu = $this->sdb->prepare($colupdate);
          $stmtu->execute();
        
          // out for now cant connect while vpn
 // $apicall->updateAPI($value, 'Done');
        
          $fw = fopen($_SERVER["DOCUMENT_ROOT"]."//undone_query_data.txt","a+");
          fwrite($fw,"---------------------------------\n".$colupdate."\n\n");
          fclose($fw);
        
              }
          }
        
        
        // echo $sqlii;
        
        
    }


    public function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
         
        return str_replace("-"," ",preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
      }

}

