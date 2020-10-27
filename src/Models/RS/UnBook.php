<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use App\Models\RS\CurlStatuschange;

/**
 * Class UnBook
 * @package App\Models\RS
 */
class UnBook extends AbstractModel
{
    public $response;
    public $id;

    /**
     * UnBook constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        parent::__construct();
    }

    public function undo()
    {
        $apicall = new CurlStatuschange();
        $stuff = $_POST['stuff'];
        $dell = 0;
        $who = str_replace('@stonegroup.co.uk', '', $_SESSION['user']['username']);
        
        foreach ($stuff as $value) {
            $colupdate = "
        
        update request
        set been_collected = " . $dell . ",
        laststatus = 'UnBooked',
        deleted = 0,
        collection_date = NULL,
        modifydate = getdate(),
        updatedBy = '" . $who . "'
        where Request_ID =" . $value . "
        
          
        delete from Booked_Collections
        where RequestID ='" . $value . "' and ([SurveyComplete] like  ''  or [SurveyComplete]  is null)";

            $stmtu = $this->sdb->prepare($colupdate);
            $stmtu->execute();
            $apicall->updateAPI($value, 'Cancelled');
        }

        return true;
    }
}
