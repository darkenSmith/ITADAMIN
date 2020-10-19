<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;



/**
 * Class AddLineitem
 * @package App\Models\RS
 */
class AddLineitem extends AbstractModel
{
    public $response;
    public $id;

    /**
     * AddLineitem constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
        parent::__construct();
    }

    public function addline()
    { 

        $requestid = $_POST['reqidn'];
        $partid = $_POST['partselected'];
        $workingqtynew = $_POST['workingqtynew'];
        
        
        $othername = '';
        
        if(isset($_POST['othername'])){
            
        $othername = $_POST['othername'];
        
        }
        
        
        
        
        
        
        
        if($partid == 19){
        
        
            $addupdat ="
        
            
            insert into Req_Detail(req_id, prod_id, qty, Asset_req, Wipe, other1_name)
            values(".$requestid.", ".$partid.", ".$workingqtynew.", 0, 0, '".$othername."')
            
            ";
            
            
            $upstmt = $this->sdb->prepare($addupdat);
            $upstmt->execute();
        
        }else if($partid == 21){
        
        
            $addupdat ="
        
            
            insert into Req_Detail(req_id, prod_id, qty, Asset_req, Wipe, other2_name)
            values(".$requestid.", ".$partid.", ".$workingqtynew.",  0, 0, '".$othername."')
            
            ";
            
            
            $upstmt = $this->sdb->prepare($addupdat);
            $upstmt->execute();
        
        }else if($partid == 23){
        
        
            $addupdat ="
        
            
            insert into Req_Detail(req_id, prod_id, qty, Asset_req, Wipe, other3_name)
            values(".$requestid.", ".$partid.", ".$workingqtynew.",  0, 0, '".$othername."')
            
            ";
            
            
            $upstmt = $this->sdb->prepare($addupdat);
            $upstmt->execute();
        
        }
        else if($partid == 65){
        
        
            $addupdat ="
        
            
            insert into Req_Detail(req_id, prod_id, qty, Asset_req, Wipe, other4_name)
            values(".$requestid.", ".$partid.", ".$workingqtynew.",  0, 0, '".$othername."')
            
            ";
            
            
            $upstmt = $this->sdb->prepare($addupdat);
            $upstmt->execute();
        
        }
        else if($partid == 67){
        
        
            $addupdat ="
        
            
            insert into Req_Detail(req_id, prod_id, qty, Asset_req, Wipe, other5_name)
            values(".$requestid.", ".$partid.", ".$workingqtynew.",  0, 0, '".$othername."')
            
            ";
            
            
            $upstmt = $this->sdb->prepare($addupdat);
            $upstmt->execute();
        
        }else if($partid == 69){
        
        
            $addupdat ="
        
            
            insert into Req_Detail(req_id, prod_id, qty, Asset_req, Wipe, other6_name)
            values(".$requestid.", ".$partid.", ".$workingqtynew.",  0, 0, '".$othername."')
            
            ";
            
            
            $upstmt = $this->sdb->prepare($addupdat);
            $upstmt->execute();
        
        }else{
        
            
            $addupdat ="
        
            
        insert into Req_Detail(req_id, prod_id, qty, Asset_req, Wipe)
        values(".$requestid.", ".$partid.", ".$workingqtynew.",  0, 0)
        
        ";
        
        
            $upstmt = $this->sdb->prepare($addupdat);
            $upstmt->execute();
        }
        
 
    }

}

