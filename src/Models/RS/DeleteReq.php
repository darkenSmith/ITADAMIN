<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use App\Models\RS\CurlStatuschange;

/**
 * Class DeleteReq
 * @package App\Models\RS
 */
class DeleteReq extends AbstractModel
{
    public $response;
    public $id;
    public $status;
    private $apicall;

    /**
     * DeleteReq constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }

    public function deletelist()
    {
      
        $apicall = new CurlStatuschange();

        $stuff = $_POST['stuff'];
        $dell = 1;
        
        foreach ($stuff as $value) {
            $user =  $_SESSION['user']['firstname'][0].$_SESSION['user']['lastname'][0];
        
            $who =  str_replace('@stonegroup.co.uk', '', $user);
        
            $colupdate ="
        
            update request  
            set deleted =".$dell.",
            laststatus = 'cancelled',
            deletedBy = '".$who."'
            where Request_ID =".$value."
          
            update Booked_Collections 
            set is_canceled = 1
            where  RequestID like '".$value."'";
            $stmtu = $this->sdb->prepare($colupdate);
            $stmtu->execute();

            $apicall->updateAPI($value, 'cancelled');
        }
    }
}
