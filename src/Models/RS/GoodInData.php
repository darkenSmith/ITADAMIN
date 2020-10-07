<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

class GoodInData extends AbstractModel
{
    public $datacheck;
    public $ordernum;
    public $infostuff;
    public $custname;
    public $custord;
    public $submitedinfo;
    public $drivers;

    /**
     * GoodInData constructor.
     */
    public function __construct()
    {
        $this->gdb = Database::getInstance('greenoak');
        $this->sdb = Database::getInstance('sql01');

        parent::__construct();
    }

    public function getcount($rid)
    {

        if ($rid) {
            $sql = "SELECT count(*) as cn from request as rt
        join Req_Detail as rd on
        rd.req_id = rt.Request_id
        join productlist as ppl on 
        ppl.product_ID = rd.prod_id
        
        where rt.Request_id =" . $rid;


            $fh = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/stage1goodquery.txt", "a+");
            fwrite($fh, $sql . "\n");
            fclose($fh);

            $stmt = $this->sdb->prepare($sql);
            $stmt->execute();
            $this->datacheck = $data = $stmt->fetch(PDO::FETCH_ASSOC);


            $fh = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/goodcheckres.txt", "a+");
            fwrite($fh, $sql . "\n");
            fclose($fh);


            return $this->datacheck;
        }
    }

    public function getdata($rid)
    {


        if ($rid) {
#
            $sql = "SELECT distinct ppl.Product, rd.qty from request as rt
        join Req_Detail as rd on
        rd.req_id = rt.Request_id
        join productlist as ppl on 
        ppl.product_ID = rd.prod_id
        
        where rt.Request_id =" . $rid;


            $fh = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/stage1productquery.txt", "a+");
            fwrite($fh, $sql . "\n");
            fclose($fh);

            $stmt = $this->sdb->prepare($sql);
            $stmt->execute();
            $this->infostuff = $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            $fh = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/goodquery.txt", "a+");
            fwrite($fh, print_r($this->infostuff, true) . "\n");
            fclose($fh);


            return $this->infostuff;
        }
    }


    public function getname($rid)
    {


        if (!$rid) {
            echo "sorry";
        } else {

            $sql2 = "SELECT Customer_name from request where Request_id =" . $rid;


            $fh2 = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/stage1goodname.txt", "a+");
            fwrite($fh2, $sql2 . "\n");
            fclose($fh2);

            $stmt2 = $this->sdb->prepare($sql2);
            $stmt2->execute();
            $this->custname = $data2 = $stmt2->fetchAll(\PDO::FETCH_ASSOC);


            $fh2 = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/goodname.txt", "a+");
            fwrite($fh2, $sql2 . "\n");
            fclose($fh2);


            return $this->custname;
        }
    }


    public function getord($rid)
    {


        if (!$rid) {
        } else {

            $sql3 = "SELECT
        salesordernumber as ord,
                ConsignmentNumber AS consign,
                d.WasteTransferNumber as wtn,
                SICCode,
            c.SiteCode as SiteCode
              FROM
                [dbo].Delivery AS d
              JOIN
                SalesOrders AS s ON d.SalesOrderID = s.SalesOrderID
           left join company as c with(nolock) on
            c.CompanyID = s.CompanyID
              WHERE
                CustomerPONumber LIKE '%" . $rid . "%'";


            $fh3 = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/stage1goodname.txt", "a+");
            fwrite($fh3, $sql3 . "\n");
            fclose($fh3);

            $stmt3 = $this->gdb->prepare($sql3);
            $stmt3->execute();
            $this->custord = $data3 = $stmt3->fetch(\PDO::FETCH_ASSOC);


            $fh3 = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/ordgoodname.txt", "a+");
            fwrite($fh3, $sql3 . "\n");
            fclose($fh3);


            return $this->custord;
        }
    }


    public function getdrivers($rid)
    {


        if (!$rid) {
        } else {

            $driversql = "

            select driver1, driver2  FROM RECwarehouse_collection WHERE REQUESTID ='" . $rid . "'
    
    ";
            $driverstmt = $this->sdb->prepare($driversql);
            $driverstmt->execute();
            $drivernames = $driverstmt->fetch(\PDO::FETCH_ASSOC);

            $this->drivers = $data4 = $driverstmt->fetch(\PDO::FETCH_ASSOC);


            $fh3 = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/ordgoodname.txt", "a+");
            fwrite($fh3, $driversql . "\n");
            fclose($fh3);


            return $this->drivers;
        }
    }


    public function getsubinfo($rid)
    {
        if (!$rid) {
        } else {

            $sqlgrab = " select BL, ber, weight, PRODUCT from [recwharehousedetailbyline](" . $rid . ", '%')";


// $fh3 = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/stage1goodname.txt","a+");
// fwrite($fh3,$sqlgrab ."\n");
// fclose($fh3);

            $stmtgrab = $this->sdb->prepare($sqlgrab);
            $stmtgrab->execute();
            $this->submitedinfo = $datagrab = $stmtgrab->fetchall(\PDO::FETCH_ASSOC);


            // $fh3 = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/ordgoodname.txt","a+");
            // fwrite($fh3,$sqlgrab ."\n");
            // fclose($fh3);


            return $this->submitedinfo;
        }
    }
}
