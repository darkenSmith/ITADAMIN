<?php

namespace App\Models\Conf;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use App\Models\RS\CurlStatuschange;

/**
 * Class UnBook
 * @package App\Models\Conf
 */
class UnBook extends AbstractModel
{
    public $response;

    /**
     * UnBook constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        parent::__construct();
    }

    public function unbookrequest()
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
            confirmed = 0,
            collection_date = NULL,
            modifydate = getdate(),
            updatedBy = '" . $who . "'
            where Request_ID =" . $value . "
            delete from Booked_Collections
            where RequestID ='" . $value . "' and ([SurveyComplete] like  ''  or [SurveyComplete]  is null)";

            try {
                $stmtu = $this->sdb->prepare($colupdate);
                $stmtu->execute();
                $apicall->updateAPI($value, 'Cancelled');
            } catch (Exception $e) {
                Logger::getInstance("UnBook.log")->warning(
                    'unbookrequest',
                    [
                        'line' => $e->getLine(),
                        'error' => $e->getMessage()
                    ]
                );
            }
        }

        return true;
    }
}
