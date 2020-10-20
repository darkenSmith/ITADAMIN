<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use App\Models\RS\CurlStatuschange;
use Exception;


/**
 * Class OnHold
 * @package App\Models\RS
 */
class OnHold extends AbstractModel
{
    public $response;
    public $id;

    /**
     * OnHold constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function onholdlist(){ 

        $apicall = new CurlStatuschange();

        $ord = $_POST['stuff'];
        $who =  str_replace( '@stonegroup.co.uk', '', $_SESSION['user']['username']);
        

        foreach ($ord as $value) {
        $colupdate ="
        update request
        set laststatus = 'On-Hold',
        confirmed = 0,
        modifedby = '".$who."',
        modifydate = getdate()
        where Request_ID ='".$value."'";

        $stmtu = $this->sdb->prepare($colupdate);
        $stmtu->execute();

        // out for now cant connect while vpn
 // $apicall->updateAPI($value, 'Done');

        }
    }

}

