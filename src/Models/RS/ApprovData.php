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
        $sql = "SELECT username, firstname, lastname, email, approved, id as idnum  from recyc_users;";
        $result = $this->rdb->query($sql);

        $data = $result->fetchAll(\PDO::FETCH_OBJ);

        $dataArray = array();

        foreach ($data as $val) {
            $dataArray[] = $val;
        }

        try {
            $this->response = json_encode($dataArray, JSON_THROW_ON_ERROR);
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
          set approved = (case when approved = 'Y' then 'N' ELSE 'Y' END),
          active =  (case when active = 1 then 0 ELSE 1 END)
          Where id = :id";

            $result = $this->rdb->prepare($sql);
            $result->execute(array(':id' => $id));
        }
    }
}
