<?php

namespace App\Models\RS;

use App\Helpers\Database;
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
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function upload()
    {

                
        if (!isset($_SESSION)) {
            session_start();
        }
            
            $id = '';
            
            $id = $_SESSION['id'];
        if (isset($_FILES["file"]["type"])) {
            $validextensions = array("jpeg", "jpg", "png", "PNG");
            $temporary = explode(".", $_FILES["file"]["name"]);
            $file_extension = end($temporary);
            
            if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
            ) && ($_FILES["file"]["size"] > 1000 && $_FILES["file"]["size"] < 1e+7)//Approx. 100kb files can be uploaded.
            && in_array($file_extension, $validextensions)) {
                if ($_FILES["file"]["error"] > 0) {
                    echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
                } else {
                    if (file_exists($_SERVER['DOCUMENT_ROOT']."/upload/" . $_FILES["file"]["name"])) {
                        echo $_FILES["file"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
                    } else {
                        $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
                        $targetPath = $_SERVER['DOCUMENT_ROOT']."/upload/".substr($_FILES['file']['name'], 0, strpos($_FILES['file']['name'], "."))."-".$id.substr($_FILES['file']['name'], strpos($_FILES['file']['name'], ".")); // Target path where file is to be stored
            
                        $path_parts = pathinfo($targetPath);
            
                    // $date = date_create();
                    // $stringdate =  date_format($date, ' Y-m-d H:i:s') . "\n";
            
                        $sql = "INSERT
                    INTO
                        customerPics
                    VALUES (
                        '/upload/".$path_parts['filename'].'.'.$path_parts['extension']."',
                        '".date("Y-m-d H:i:s")."',
                        ".$id."
                    )
            
                    UPDATE
                        Booked_Collections
                    SET
                        Pic = '".date("Y-m-d H:i:s")."'
                    WHERE
                        RequestID = '".$id."'
                    ";
            
                    // echo $sql;
            
                        $pictime = date("Y-m-d H:i:s");
            
                    
            
                        $stmt = $this->sdb->prepare($sql);
            
                        $stmt->execute();
            
                        if ($stmt === false) {
                            die(print_r($sql, true));
                        }
            
                        $dest = $targetPath;
            
                        move_uploaded_file($sourcePath, $targetPath); // Moving Uploaded file
            
                        return "<BR><span id='success'><b>Image Uploaded Successfully...!! Please Refresh to see images.</b></span><br/><BR>";
                    // echo "<br/><b>File Name:</b> " . $_FILES["file"]["name"] . "<br>";
                    // echo "<b>Type:</b> " . $_FILES["file"]["type"] . "<br>";
                    // echo "<b>Size:</b> " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
                    // echo "<b>Temp file:</b> " . $_FILES["file"]["tmp_name"] . "<br>";
            
                    // $emg =  "<div id='image_preview'><img id='previewing' name='previewing' src='/".$dest."' alt='' width='250' height='200'></div>";
                    // $_SESSION['loc'] = $emg;
                    }
                }
            } else {
                return "<span id='invalid'>***Invalid file Size or Type***<span>";
            }
        }
    }
}
