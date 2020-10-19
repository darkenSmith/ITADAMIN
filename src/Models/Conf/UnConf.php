<?php

namespace App\Models\Conf;

use App\Helpers\Database;
use App\Models\AbstractModel;
use Exception;


/**
 * Class UnConf
 * @package App\Models\RS
 */
class UnConf extends AbstractModel
{
    public $response;
    public $id;

    /**
     * UnConfirm constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function unconfirmlist()
    { 
     


            $stuff = $_POST['stuff'];
            $dell = 0;
            $who =  str_replace( '@stonegroup.co.uk', '', $_SESSION['user']['username']);


            foreach ($stuff as $value) {
            $colupdate ="

            update request
            set confirmed = ".$dell.",
            laststatus = 'Booked',
            modifydate = getdate(),
            updatedBy = '".$who."'
            where Request_ID =".$value."

            
            update Booked_Collections
            set booking_status = 'Unconfimed'
            where RequestID ='".$value."'
            ";

            $stmtu = $this->sdb->prepare($colupdate);
            $stmtu->execute();



            }

    }

}

