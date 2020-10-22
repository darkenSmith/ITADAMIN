<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Helpers\FileHelper;
use App\Models\AbstractModel;

/**
 * Class UploadImage
 * @package App\Models\RS
 */
class UploadImage extends AbstractModel
{
    public $response;
    public $id;

    /**
     * UploadImage constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }

    public function upload()
    {
        $emg = '';
        $id = '';

        $id = $_SESSION['id'];
        if (isset($_FILES["file"]["type"])) {
            $validextensions = array("jpeg", "jpg", "png", "PNG");
            $temporary = explode(".", $_FILES["file"]["name"]);
            $file_extension = end($temporary);
            $fileHelper = FileHelper::getInstance();
            if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
                ) && ($_FILES["file"]["size"] > 1000 && $_FILES["file"]["size"] < 1e+7)//Approx. 100kb files can be uploaded.
                && in_array($file_extension, $validextensions)) {
                if ($_FILES["file"]["error"] > 0) {
                    return "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
                } else {
                    $uploadPath = $fileHelper->getRealPath(PROJECT_DIR . 'uploads/images');
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
