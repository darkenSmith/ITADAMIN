<?php

namespace App\Models;

use App\Helpers\FileHelper;

/**
 * Class upload
 * @package App\Models
 */
class Upload extends AbstractModel
{
    public $filename;
    public $ext;
    public $values;

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function save()
    {
        $file = $_FILES['file']['tmp_name'];
        $fileType = $_FILES['file']['type'];
        $type = stripslashes($_POST['document']);
        $order = stripslashes($_POST['id']);

        $user = $_SESSION['user']['id'];
        $date = date('Y-m-d-h-i-s');

        $filename = base64_encode($user . '+' . $date . '+' . $order . '+' . $type);

        $uploadDir = FileHelper::getInstance()->getRealPath(PROJECT_DIR . 'uploads');
        $filepath =  $uploadDir . '/pdf/'. $filename . '.pdf';

        rename($file, $filepath);

        $this->values = array(
            ':order' => $order,
            ':uploaded' => $date,
            ':filename' => $filename,
            ':filetype' => $type,
            ':format' => $fileType,
            ':download' => 1,
            ':user' => $user
        );

        $sql = 'INSERT INTO recyc_uploads (order_id, uploaded, file_type, filename, file_format, downloadable, user_id) 
				VALUES 
				(:order, :uploaded, :filetype,:filename, :format, :download, :user)';

        $query = $this->rdb->prepare($sql);
        $query->execute($this->values);
    }

    public function delete($filename, $order)
    {
        $sql = 'DELETE FROM recyc_uploads where filename = :filename and order_id = :order';
        $query = $this->rdb->prepare($sql);
        $query->execute(array(':filename' => $filename, ':order' => $order));
    }
}
