<?php

namespace App\Models\RS;

use App\Helpers\Config;
use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use Exception;

/**
 * Class CurlStatuschange
 * @package App\Models\RS
 */
class CurlStatuschange extends AbstractModel
{
    public $response;
    public $id;
    private $stoneApi;

    /**
     * CurlStatuschange constructor.
     */
    public function __construct()
    {
        $this->stoneApi = Config::getInstance()->get('stone_api');

        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }

    public function updateAPI($req, $status)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->stoneApi['url'] . "stoneapp/collectionStatus/".$req."/".$status,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_SSL_VERIFYPEER  => false,
          CURLOPT_HTTPHEADER => array(),
        ));
        $output = curl_exec($curl);

        Logger::getInstance("responseAPI.log")->debug(
            'CurlStatuschange',
            [$output]
        );

        $this->response = $output;
        return $this->response;
    }
}
