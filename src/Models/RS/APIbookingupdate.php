<?php

namespace App\Models\RS;


/**
 * Class BookedAPIupdate
 * @package App\Models\RS
 */
class BookedAPIupdate
{
    // public $response;
    // public $id;
    // private $stoneApi;

    /**
     * BookedAPIupdate constructor.
     */
    public function __construct()
    {

    }

    public function updatebookdateAPI($req, $bookdate)
    {
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

        // Logger::getInstance("responseAPIdate.log")->debug(
        //     'BookedAPIupdate',
        //     [$output]
        // );

        $this->response = $output;
        return $this->response;
    }
}
