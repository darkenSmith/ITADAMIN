<?php

namespace App\Models\RS;

use App\Helpers\Config;
use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;

/**
 * Class AddRebate
 * @package App\Models
 */
class AddRebate extends AbstractModel
{
    public $response;
    private $stoneApi;

    /**
     * AddRebate constructor.
     */
    public function __construct()
    {
        $this->stoneApi = Config::getInstance()->get('stone_api');

        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function apicurlrequest($userid)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->stoneApi['url'] . "stoneapp/rmApproval/" . $userid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(),
        ));
        $output = curl_exec($curl);

        Logger::getInstance("responseAPI.log")->debug(
            'apicurlrequest',
            [$output]
        );

        return $output;
    }
}
