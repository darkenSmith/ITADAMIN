<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

/**
 * @TODO a lot of error / exceptions in this file!!!
 */

/**
 * Class BdmDetail
 * @package App\Models\RS
 */
class BdmDetail extends AbstractModel
{
    /**
     * BdmDetail constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        parent::__construct();
    }

    public function totals($rid)
    {
        $rid->$_GET['reqid'] ?? '';

        if ($rid == 'de') {
            $rid = '%';
        }


        $totalswu = "
            select 
            
            SUM(qty) as totalunits,
            sum(qty * convert(DECIMAL(9 , 2),typicalweight)) as totalweight,
            convert(int, sum(qty * convert(DECIMAL(9 , 2),typicalweight))) as totalweightint
            
                from request r
            join Req_Detail as rr on
            r.request_id = rr.req_id
            join productlist as p on
            p.product_ID = rr.prod_id 
            where request_id = " . $rid . "
            
            group by
             
            r.request_id
            
            order by r.request_id
            ";

        $fh = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/productbdm.txt", "a+");
        fwrite($fh, $totalswu . "\n");
        fclose($fh);

        $stmt = $this->sdb->prepare($totalswu);

        $stmt->execute();
        $this->$datatotals = $data2 = $stmt->fetch(\PDO::FETCH_ASSOC);


        return $this->$datatotals;
    }


    public function getrequestdata($rid)
    {


        $rid->$_GET['reqid'] ?? '';


        $sqlextra = "
      select 
      p.Product as prodname, 
       r.prod_id as prodid,
        QTY as w,
         wipe as wip,
         Asset_req as asset,
         isnull(R.other1_name, 'Empty') as o1,
         isnull(R.other2_name, 'Empty') as o2,
         isnull(R.other3_name, 'Empty') as o3,
         isnull(R.other4_name, 'Empty') as o4,
         isnull(R.other5_name, 'Empty') as o5,
         isnull(R.other6_name, 'Empty') as o6,
      SUM(QTY) as totalunits,
      sum(QTY * convert(DECIMAL(9 , 2),typicalweight)) as totalweight
    
        from request rt
    join Req_Detail as r on
    rt.request_id = r.req_id
    join productlist as p on
    p.product_ID = r.prod_id 
    where rt.request_id = " . $rid . "
    
    group by
    p.Product, 
    rt.request_id, 
     r.prod_id,
     qty,
          wipe,
       Asset_req,
       R.other1_name,
       R.other2_name,
       R.other3_name,
       R.other4_name,
       R.other5_name,
       R.other6_name,
        p.[order]
    
    
    order by rt.request_id, p.[order]
    asc

    ";


        $fh = fopen($_SERVER["DOCUMENT_ROOT"] . "/RS_Files/productbdm.txt", "a+");
        fwrite($fh, $sqlextra . "\n");
        fclose($fh);

        $stmt2 = $this->sdb->prepare($sqlextra);

        $stmt2->execute();
        $this->$dataprod = $data = $stmt2->fetchall(\PDO::FETCH_ASSOC);


        return $this->$dataprod;
    }
}
