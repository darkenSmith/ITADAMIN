<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

/**
 * Class GoodInData
 * @package App\Models\RS
 */
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

            $stmt = $this->sdb->prepare($sql);
            $stmt->execute();
            $this->datacheck = $data = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $this->datacheck;
        }
    }

    public function getdata($rid)
    {
        if ($rid) {
            $sql = "SELECT distinct ppl.Product, rd.qty from request as rt
        join Req_Detail as rd on
        rd.req_id = rt.Request_id
        join productlist as ppl on 
        ppl.product_ID = rd.prod_id
        
        where rt.Request_id =" . $rid;

            $stmt = $this->sdb->prepare($sql);
            $stmt->execute();
            $this->infostuff = $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $this->infostuff;
        }
    }

    public function getname($rid)
    {
        if ($rid) {
            $sql2 = "SELECT Customer_name from request where Request_id =" . $rid;
            $stmt2 = $this->sdb->prepare($sql2);
            $stmt2->execute();
            $this->custname = $data2 = $stmt2->fetchAll(\PDO::FETCH_ASSOC);
            return $this->custname;
        }
    }

    public function getord($rid)
    {
        if ($rid) {
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

            $stmt3 = $this->gdb->prepare($sql3);
            $stmt3->execute();
            $this->custord = $data3 = $stmt3->fetch(\PDO::FETCH_ASSOC);

            return $this->custord;
        }
    }

    public function getdrivers($rid)
    {
        if ($rid) {
            $driversql = "

            select driver1, driver2  FROM RECwarehouse_collection WHERE REQUESTID ='" . $rid . "'
    
    ";
            $driverstmt = $this->sdb->prepare($driversql);
            $driverstmt->execute();
            $drivernames = $driverstmt->fetch(\PDO::FETCH_ASSOC);

            $this->drivers = $driverstmt->fetch(\PDO::FETCH_ASSOC);
            return $this->drivers;
        }
    }

    public function getsubinfo($rid)
    {
        if ($rid) {
            $sqlgrab = " select BL, ber, weight, PRODUCT from [recwharehousedetailbyline](" . $rid . ", '%')";

            $stmtgrab = $this->sdb->prepare($sqlgrab);
            $stmtgrab->execute();
            $this->submitedinfo = $datagrab = $stmtgrab->fetchall(\PDO::FETCH_ASSOC);

            return $this->submitedinfo;
        }
    }
}
