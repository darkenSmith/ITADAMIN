<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use Exception;

/**
 * Class Togglecharge
 * @package App\Models
 */
class Charge extends AbstractModel
{

    public $response;

    /**
     * Togglecharge constructor.
     */
    public function __construct()
    {
        $this->rdb =  Database::getInstance('sql01');
        parent::__construct();
    }


    public function toggle(){


///set request to Charge///


$stuff = $_POST['stuff'];
$dell = 1;


$who =  str_replace( '@stonegroup.co.uk', '', $_SESSION['user']['username']);


    foreach ($stuff as $value) {
        
    $sqlc = "select isnull(charge, 0) as charge, case when  isnull(charge, 0) = 1 then 0 else 1 end as op    from request where Request_id =".$value;
    $stmtcha = $this->rdb->prepare($sqlc);
    try{
      $stmtcha->execute();
    $data = $stmtcha->fetch(\PDO::FETCH_ASSOC);

    $chstat = $data['op'];
      $colupdate ="
      

      update request  
      set charge =".$chstat.",
      modifedby = '".$who."',
      modifydate = getdate()
      where Request_ID =".$value; 
      
      $stmtu = $this->rdb->prepare($colupdate);
      $stmtu->execute(); 
      }catch(Exception $e){
        var_dump($e);
      }
    
      
      }
    }



}
