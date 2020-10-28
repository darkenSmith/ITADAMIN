<?php

namespace App\Models\RS;

use App\Helpers\Config;
use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use Exception;

/**
 * Class BookedAPIupdate
 * @package App\Models\RS
 */
class BookedAPIupdate extends AbstractModel
{
  public $response;
  public $id;
  private $stoneApi;

    /**
     * BookedAPIupdate constructor.
     */
    public function __construct()
    {
      $this->stoneApi = Config::getInstance()->get('stone_api');
        parent::__construct();
    }

    public function updatebookdateAPI($req, $bookdate)
    {

      Logger::getInstance("responseAPIdate.log")->debug(
        'BookedAPIupdate',
        [__LINE__]
    );
      try{


      
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->stoneApi['url'] . "stoneapp/updateDate/".$req."/".$bookdate,
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



        $this->response = $output;
        return $this->response;
        
      }catch(Exception $e){

        Logger::getInstance("responseAPIdateerr.log")->debug(
          'BookedAPIupdate',
          [$e->getMessage()]
      );

      }
    }
}
