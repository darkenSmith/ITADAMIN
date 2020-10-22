<?php

namespace App\Controllers;

use App\Helpers\App;
use App\Helpers\FileHelper;
use App\Models\Company;
use App\Models\OrderSync;
use App\Models\Upload;
use App\Models\Order;

/**
 * Class OrderController
 * @package App\Controllers
 */
class OrderController extends AbstractController
{

    public function view()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            $id = date('id');
        }

        $app = new App();
        $order = new Order();
        $order->loadById($id);

        $this->template->view(
            'order/details',
            [
                'order' => $order,
                'id' => $id,
                'app' => $app
            ]
        );
    }


    public function download()
    {
        if (isset($_GET['file'])) {
            //@todo security check to match file belongs to logged in user
            $filename = base64_decode($_GET['file']);
            $userFile = explode('/', $filename);
            $userFile = $userFile[1];
            $uploadDir = FileHelper::getInstance()->getRealPath(PROJECT_DIR . 'uploads');

            $filepath = '/uploads/pdf/' . $filename;

            if (file_exists($filepath)) {
                $data = file_get_contents($filepath);
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$userFile");
                header("Content-Type: application/pdf");
                header('Content-Length: ' . filesize($filepath));
                header("Content-Transfer-Encoding: binary");
                echo $data;
            }
        }

        if (isset($_GET['newfile'])) {
            //@todo security check to match file belongs to logged in user
            $filename_original = $_GET['newfile'];

            $filename = base64_decode($filename_original);

            // Change tag: getFileFormat
            if (isset($_GET["format"])) {
                $format = base64_decode($_GET["format"]);
            } else {
                $format = "application/pdf";
            }

            // Change tag: addExtension
            if ($format == "application/pdf") {
                $extension = ".pdf";
            } else {
                if ($format == "application/vnd.ms-excel") {
                    $extension = ".xls";
                } elseif ($format == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                    $extension = ".xlsx";
                } elseif ($format == "image/tiff") {
                    $extension = ".tif";
                } elseif ($format == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
                    $extension = ".docx";
                } else {
                    $extension = ".pdf";
                }
            }

            $userFile = explode('+', $filename);

            $userFile = str_replace(' ', '-', $userFile[3]).$extension; //'.pdf';

            // Change tag: serverPath
            $filepath = PROJECT_DIR . 'uploads/pdf/'. $filename_original.'.pdf';
            if (file_exists($filepath)) {
                header("Content-Description: File Transfer");
                header("Content-Type: ".$format);
                header('Content-Disposition: attachment; filename="'.$userFile.'"');
                header("Content-Transfer-Encoding: binary");
                header('Content-Length: '.filesize($filepath));
                header('Pragma: no-cache');
                // Change tag: cleanOutput
                ob_get_clean();
                readfile($filepath);
                ob_end_flush();
            }

        }
    }


    public function upload()
    {
        if (isset($_FILES['file']['size']) && $_FILES['file']['size'] > 0) {
            if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $upload = new upload();
                $upload->save();
                header('Location: /order/view/' . $_POST['id']);
            } else {
                die("Upload failed with error code " . $_FILES['file']['error']);
            }
        } else {
            die('Nothing Uploaded');
        }
    }

    public function delete()
    {
        if (isset($_GET['file'])) {
            $filename = base64_decode($_GET['file']);
            $uploadDir = FileHelper::getInstance()->getRealPath(PROJECT_DIR . 'uploads');
            $filepath = $uploadDir . '/pdf/' . $filename;
            unlink($filepath);
        }

        if (isset($_GET['newfile'])) {
            //@todo security check to match file belongs to logged in user
            $filename = $_GET['newfile'];
            $filename = base64_decode($filename);
            $userFile = explode('+', $filename);
            $userFile = str_replace(' ', '-', $userFile[3]) . '.pdf';
            $uploadDir = FileHelper::getInstance()->getRealPath(PROJECT_DIR . 'uploads');

            $filepath = $uploadDir . '/pdf/' . $_GET['newfile'] . '.pdf';
            unlink($filepath);

            $upload = new Upload();
            $upload->delete($_GET['newfile'], $_GET['view']);
        }
        header('Location: /order/view/' . $_GET['view']);
    }

    // Change tag: orderSync
    public function sync()
    {
        // Get companies
        $companies = new Company();
        $companies->refresh(false);

        // Get orders
        $sync = new OrderSync();
        $sync->start();

        if ($sync->orders) {
            $sync->process();
        }
    }
}
