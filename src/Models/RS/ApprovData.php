<?php

namespace App\Models\RS;

use App\Models\AbstractModel;

/**
 * Class ApprovData
 * @package App\Models\RS
 */
class ApprovData extends AbstractModel
{
    public $response;
    public $id;

    /**
     * ApprovData constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getlist()
    {
        $sql = "SELECT Userr.id, Userr.username, Userr.firstname, Userr.lastname, Userr.approved, Userr.CompanyNUM, c.company_name, c.cmp FROM recyc_users AS Userr

        left JOIN recyc_customer_links_to_company AS L ON
        L.user_id = Userr.id
        
        left JOIN recyc_company_sync AS c ON 
        c.company_id = L.company_id ";
        $result = $this->rdb->query($sql);

        $data = $result->fetchAll(\PDO::FETCH_OBJ);

        $dataArray = array();

        foreach ($data as $val) {
            $dataArray[] = $val;
        }

        try {
            $this->response = json_encode($dataArray);
            return $this->response;
        } catch (\JsonException $e) {
        }

        return [];
    }

    public function updateapp()
    {
        $id = isset($_POST['idnum']) ? $_POST['idnum'] : '';

        if (!empty($id)) {
            $sql = "UPDATE recyc_users
          set approved = (case when approved = 'Y' then 'N' ELSE 'Y' END)
          Where id = :id";

            try {
                $result = $this->rdb->prepare($sql);
                $result->execute(array(':id' => $id));
                $this->apicurlrequest($id);
            } catch (\PDOException $e) {
                return false;
            }

            return true;
        }
        return false;
    }

        public function apicurlrequest($userid){

            // create & initialize a curl session
    $curl = curl_init();


    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://stoneapi.stonegroup.co.uk/stoneapp/rmApproval/".$userid,
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

    $fp = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/responseAPI.txt", 'a+');
    fwrite($fp, $output);
    fclose($fp);


    $this->response = $output;
    return $this->response;
    }
}
