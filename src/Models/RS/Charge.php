<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;
use Exception;

/**
 * Class Charge
 * @package App\Models
 */
class Charge extends AbstractModel
{

    public $response;

    /**
     * Charge constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        parent::__construct();
    }

    public function toggle()
    {
        $stuff = $_POST['stuff'];
        $dell = 1;

        $who =  str_replace('@stonegroup.co.uk', '', $_SESSION['user']['username']);

        foreach ($stuff as $value) {
            $sqlc = "SELECT ISNULL(charge, 0) AS charge, CASE WHEN ISNULL(charge, 0) = 1 THEN 0 ELSE 1 END AS op FROM Request WHERE Request_id ='" . $value ."'";
            $stmtcha = $this->sdb->prepare($sqlc);
       // $stmtcha->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $stmtcha->execute();
            $data = $stmtcha->fetch(\PDO::FETCH_ASSOC);

            $chstat = $data['op'];
            $colupdate ="
      set nocount on
      update request  
      set charge = $chstat,
      modifedby = '".$who."',
      modifydate = getdate()
      where Request_ID =".$value;
        }
        $stmtu = $this->sdb->prepare($colupdate);
        $stmtu->execute();
      //return true;
    }
}
