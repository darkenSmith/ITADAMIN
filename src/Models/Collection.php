<?php

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\FileHelper;

/**
 * Class Collection
 * @package App\Models
 */
class Collection extends AbstractModel
{

    public $collections;
    public $heading;

    /**
     * Collection constructor.
     */
    public function __construct()
    {
        $this->heading = 'Collections';

        parent::__construct();
    }

    public function getCollections()
    {
        $sql = 'SELECT
					Request_ID,
					date(Request_date_added),
					Request_Customer_contact,
					Request_Customer_email,
					Request_Customer_contact,
					Request_Customer_phone,
					Request_Customer_name,
					Request_town,
					Request_county,
					Request_postcode,
					Request_total_weight,
					Request_TotalUnits
				FROM
					`recyc_collection_requests`
				WHERE
				Request_is_done = 0
				and Request_deleted = 0';
        $result = $this->rdb->prepare($sql);
        $result->execute();
        $this->collections = $result->fetchAll(\PDO::FETCH_OBJ);
    }

    public function upload()
    {
        $this->sdb = Database::getInstance('sql01');

        $emg = '';
        $id = '';

        $id = $_SESSION['id'];
        if (isset($_FILES["file"]["type"])) {
            $validextensions = array("jpeg", "jpg", "png", "PNG");
            $temporary = explode(".", $_FILES["file"]["name"]);
            $file_extension = end($temporary);

            $uploadDir = FileHelper::getInstance()->getRealPath(PROJECT_DIR . 'uploads');

            if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
                ) && ($_FILES["file"]["size"] > 1000 && $_FILES["file"]["size"] < 1e+7)//Approx. 100kb files can be uploaded.
                && in_array($file_extension, $validextensions)) {
                if ($_FILES["file"]["error"] > 0) {
                    return "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
                } else {
                    $uploadPath = $uploadDir . '/images';
                    if (file_exists($uploadPath . '/' . $_FILES["file"]["name"])) {
                        return $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
                    } else {
                        $sourcePath = $_FILES['file']['tmp_name'];
                        $targetPath = $uploadPath . "/" .
                            substr(
                                $_FILES['file']['name'],
                                0,
                                strpos($_FILES['file']['name'], ".")
                            )
                            . "-" . $id
                            . substr($_FILES['file']['name'],
                                strpos($_FILES['file']['name'], ".")
                            );
                        $path_parts = pathinfo($targetPath);

                        $sql = "INSERT
        INTO
          customerPics
        VALUES (
          '/uploads/images/" . $path_parts['filename'] . '.' . $path_parts['extension'] . "',
          '" . date("Y-m-d H:i:s") . "',
          " . $id . "
        )

        UPDATE
          Booked_Collections
        SET
          Pic = '" . date("Y-m-d H:i:s") . "'
        WHERE
          RequestID = '" . $id . "'
        ";

                        $pictime = date("Y-m-d H:i:s");

                        $stmt = $this->sdb->prepare($sql);

                        $stmt->execute();
                        $dest = $targetPath;

                        move_uploaded_file($sourcePath, $targetPath);

                        return "<BR><span id='success'><b>Image Uploaded Successfully...!! Please Refresh to see images.</b></span><br/><BR>";
                    }
                }
            } else {
                return "<span id='invalid'>***Invalid file Size or Type***<span>";
            }
        }
    }
}
