<?php

namespace App\Models\Conf;

use App\Helpers\Database;
use App\Models\AbstractModel;
use Exception;


/**
 * Class ApprovData
 * @package App\Models\RS
 */
class Confirm extends AbstractModel
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

    public function confirmlist()
    { 
     


    $stuff = $_POST['stuff'];


// $fw = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/constart_data.txt","a+");
// fwrite($fw,"---------------------------------\n".print_r($colupdate,true)."\n\n".print_r($ro,true)."\n");
// fclose($fw);
$who =  str_replace( '@stonegroup.co.uk', '', $_SESSION['user']['username']);
foreach ($stuff as $value) {
  $colupdate ="

update request
set confirmed = 1,
laststatus = 'Confirmed',
modifedby = '".$who."'
where Request_ID =".$value."

  
update Booked_Collections
set booking_status = 'confirmed'
where RequestID ='".$value."'";

  $stmtu = $this->sdb->prepare($colupdate);
try{
  $stmtu->execute();
}catch(Exception $e){
    var_dump($e);

}



 



}




// echo $sqlii;


    
       
 
    }

}

