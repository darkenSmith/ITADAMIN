<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

/**
 * Class ApprovData
 * @package App\Models\RS
 */
class Download extends AbstractModel
{
    public $response;
    public $id;

    /**
     * ApprovData constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function getfile()
    {
     
    


        header("Content-type: text/csv");
        header("Content-disposition: attachment; filename = file.csv");
        readfile($_SERVER["DOCUMENT_ROOT"]."/Merge/file.csv");
        
        

        
        
                $time = date("Y-m-d H:i:s");
                $sqlog = "
        
                insert into [dbo].[RSAS_logs]
                values('".$_SERVER['REQUEST_URI']."', '".$_SESSION['user']['username']."', '".$time."') ";
        
        
                $stmtlog = $this->sdb->prepare($sqlog);
        if (!$stmtlog) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->sdb->errorInfo());
            die();
        }
                $stmtlog->execute();
        
                $log = $stmtlog->fetch(\PDO::FETCH_ASSOC);
        
                $fh = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/rsatrack.txt", "a+");
                fwrite($fh, $sqlog."\n");
                fclose($fh);
    }
}
