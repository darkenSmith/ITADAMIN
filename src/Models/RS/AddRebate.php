<?php

use Httpful\Response;

class approvdata{

    
   

   public $response;
   public $id;

   public function __construct()
   {
    $this->rdb =  Db::getInstance('recycling');
      // $this->gdb =  Db::getInstance('greenoak');
    
  }
  
 

    public function getlist(){

        $sql = "SELECT username, firstname, lastname, email, approved, id as idnum  from recyc_users;";
        $result = $this->rdb->prepare($sql);
        $result->execute();
      
        $data = $result->fetchAll(PDO::FETCH_OBJ);

        $dataarry = array();

        foreach($data as $val){
          array_push($dataarry, $val);
        }
        $this->response =json_encode($dataarry);
                   return $this->response;

    }

    public function updateapp(){


      $id = isset($_POST['idnum']) ? $_POST['idnum'] : '';

      if(!empty($id)){
         
          $this->apicurlrequest($id);

          $sql = "UPDATE recyc_users
          set approved = (case when approved = 'Y' then 'N' ELSE 'Y' END),
          active = (case when active = 1 then 0 ELSE 1 END)
          Where id = :id";
          
          $result = $this->rdb->prepare($sql);
          $result->execute(array(':id' => $id));

          return $this->response;
      }
   
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

?>