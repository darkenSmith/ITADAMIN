<?php

namespace App\Models\Conf;

use App\Helpers\Database;
use App\Models\AbstractModel;

/**
 * Class UnBook
 * @package App\Models\Conf
 */
class UnBook extends AbstractModel
{

    public $response;

    /**
     * CompanyUpdate constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function unbookrequest()
    {
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
        }

        return true;
    }
}
