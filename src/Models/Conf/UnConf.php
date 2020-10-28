<?php

namespace App\Models\Conf;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use App\Models\RS\CurlStatuschange;

/**
 * Class UnConf
 * @package App\Models\RS
 */
class UnConf extends AbstractModel
{
    public $response;
    public $id;

    /**
     * UnConf constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }

    public function unconfirmlist()
    {
        $apicall = new CurlStatuschange();
        $stuff = $_POST['stuff'];
        $dell = 0;
        $who =  str_replace('@stonegroup.co.uk', '', $_SESSION['user']['username']);

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
            try {
                $stmtu = $this->sdb->prepare($colupdate);
                $stmtu->execute();
                $apicall->updateAPI($value, 'Booked');
            } catch (\Exception $e) {
                Logger::getInstance("UnConf.log")->warning(
                    'unconfirmlist',
                    [
                        'line' => $e->getLine(),
                        'error' => $e->getMessage()
                    ]
                );
            }
        }
    }
}
