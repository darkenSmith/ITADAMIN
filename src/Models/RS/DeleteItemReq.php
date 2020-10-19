<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;



/**
 * Class DeleteItemReq
 * @package App\Models\RS
 */
class DeleteItemReq extends AbstractModel
{
    public $response;
    public $id;

    /**
     * DeleteReq constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function deleteitemlist()
    { 
      
        $stuff = $_POST['stuff']; 
        $dell = 1;
        
        foreach ($stuff as $value) {
        
          $user =  $_SESSION['user']['firstname'][0].$_SESSION['user']['lastname'][0];
        
          $who =  str_replace( '@stonegroup.co.uk', '', $user);
        
         $colupdate ="
        
          update request  
          set deleted =".$dell.",
          deletedBy = '".$who."'
          where Request_ID =".$value."
          
        update Booked_Collections 
        set is_canceled = 1
        where  RequestID like '".$value."'";
        
        
        $stmtu = $this->sdb->prepare($colupdate);
        $stmtu->execute();
        
        
        
        
          }

    }

}

