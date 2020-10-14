<?php

namespace App\Models\RS;

use App\Models\AbstractModel;
use App\Helpers\Database;
use Exception;

class GoodSoutData extends AbstractModel
{

    public $pallets;
    public $loads;
    public $totalloads;

    

    public function __construct() {
		//$this->sdb =  Database::getInstance('sql01');
		
    }
    public function getpallets(){

        
        $sql= "select *  from pallets ";




                $stmt = $this->sdb->prepare($sql);
                $stmt->execute();
                $this->pallets = $data = $stmt->fetchall(\PDO::FETCH_ASSOC);





        return $this->pallets;


        }



        public function getloads(){


        
        $sql2= "	select loadnum, sum(weight) totalwgt, l.company, l.staus, convert(varchar(20),l.despatch_date, 103) as despatch_date from palletloads as pl
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


        public function getloadtotals(){

    


        $sql3= "	select loadnum, sum(weight) wgt,type, max(supplier) supplier , max(palletref) as pallets from palletloads
		group by loadnum, type
		order by loadnum";





                $stmt2 = $this->sdb->prepare($sql3);

                try{
                $stmt2->execute();
                $this->totalloads = $stmt2->fetchall(\PDO::FETCH_ASSOC);
                }catch(Exception $e){

                    var_dump($e);

                }
           





        return $this->totalloads;


        }



    }

    


