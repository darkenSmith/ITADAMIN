<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use Exception;

/**
 * Class GoodsOutData
 * @package App\Models\RS
 */
class GoodsOutData extends AbstractModel
{
    public $pallets;
    public $loads;
    public $totalloads;

    /**
     * GoodSoutData constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        parent::__construct();
    }

    public function getpallets()
    {
        $sql = "select * from pallets ";
        $stmt = $this->sdb->prepare($sql);
        $stmt->execute();
        $this->pallets = $stmt->fetchall(\PDO::FETCH_ASSOC);
        return $this->pallets;
    }

    public function getloads()
    {
        $sql2 = "	select loadnum, sum(weight) totalwgt, l.company, l.staus, convert(varchar(20),l.despatch_date, 103) as despatch_date from palletloads as pl
        left join loads as l on
        l.loadnum_id = pl.loadnum and
        l.year = pl.year
                group by loadnum, l.company, l.staus, l.despatch_date
        ";

        $stmt1 = $this->sdb->prepare($sql2);
        $stmt1->execute();
        $this->loads = $stmt1->fetchall(\PDO::FETCH_ASSOC);

        return $this->loads;
    }

    public function getloadtotals()
    {
        $sql3 = "select loadnum, sum(weight) wgt,type, max(supplier) supplier , max(palletref) as pallets from palletloads
		group by loadnum, type
		order by loadnum";

        $stmt2 = $this->sdb->prepare($sql3);

        try {
            $stmt2->execute();
            $this->totalloads = $stmt2->fetchall(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::getInstance("GoodsOutData.log")->warning(
                "getloadtotals failed",
                [
                    $e->getMessage()
                ]
            );
        }

        return $this->totalloads;
    }

    public function goodsInAdd()
    {
        $loadnum = $_POST['loadnum'];
        $wgt = $_POST['wgt'];
        $Type = $_POST['Type'];
        $Supplier = $_POST['Supplier'];
        $palletnum = $_POST['palletnum'];

        $colupdate = "insert into palletloads(loadnum, weight, type, supplier, palletref)
  values(" . $loadnum . ", " . $wgt . ", '" . $Type . "', '" . $Supplier . "', " . $palletnum . ")";

        try {
            $stmtu = $this->sdb->prepare($colupdate);
            $stmtu->execute();

            return true;
        } catch (Exception $e) {
            Logger::getInstance("GoodsOutData.log")->warning(
                "goodsInAdd failed",
                [
                    $e->getMessage()
                ]
            );
        }

        return false;
    }

    public function closeLoad()
    {
        $despatch = $_POST['despatchdate'];
        $loadnum = $_POST['loadid'];
        $company = $_POST['company'];

        $date = str_replace('/', '-', $despatch);
        $newdate = date("Y-m-d", strtotime($date));

        $sql = "update palletloads
set dispatchdate = '" . $newdate . "',
[year] = year(getdate())
where loadnum = $loadnum

insert into loads (loadnum_id, company, despatch_date, createdate, staus, [year])
values('" . $loadnum . "', '" . $company . "', '" . $newdate . "', GETDATE(), 'closed', year(getdate()))";

        try {
            $stmt = $this->sdb->prepare($sql);
            $stmt->execute();

            return true;
        } catch (Exception $e) {
            Logger::getInstance("GoodsOutData.log")->warning(
                "closeLoad failed",
                [
                    $e->getMessage()
                ]
            );
        }

        return false;
    }
}
