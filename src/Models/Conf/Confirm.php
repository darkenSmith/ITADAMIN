<?php

namespace App\Models\Conf;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use App\Models\RS\CurlStatuschange;
use Exception;

/**
 * Class Confirm
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
        $this->sdb = Database::getInstance('sql01');
        parent::__construct();
    }

    public function confirmlist()
    {
        $apicall = new CurlStatuschange();
        $stuff = $_POST['stuff'];

        $who = str_replace('@stonegroup.co.uk', '', $_SESSION['user']['username']);
        foreach ($stuff as $value) {
            $colupdate = "

update request
set confirmed = 1,
laststatus = 'Confirmed',
modifedby = '" . $who . "'
where Request_ID =" . $value . "

  
update Booked_Collections
set booking_status = 'confirmed'
where RequestID ='" . $value . "'";

            $stmtu = $this->sdb->prepare($colupdate);
            $apicall->updateAPI($value, 'Confirmed');
            try {
                $stmtu->execute();
            } catch (Exception $e) {
                Logger::getInstance("Confirm.log")->warning(
                    'confirmlist',
                    [$e->getMessage()]
                );
            }
        }
    }
}
