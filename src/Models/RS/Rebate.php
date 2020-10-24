<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

class Rebate extends AbstractModel
{
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');

        parent::__construct();
    }

    public function getData()
    {
        if (isset($_POST["cmp"])) {
            $cmp = $this->clean($_POST["cmpn"]);
            $ord = $this->clean($_POST["orde"]);
        }

        $sql = "


SELECT
rebateid as rid
,[ORD]
      ,[Month]
      ,[DateSent] as date
      ,[RaisedBy]
      ,[CustomerName] as name
      ,ValueExclVAT as vevat
      ,InvValueExclVAT as viev
      ,isnull(ValueExclVAT, 0) - isnull(InvValueExclVAT, 0)  as dev
      ,DateInvReceived as divr
      ,[CommentsINVNumber] as cin
      ,[Status] as stat
      ,[RebateID] as rid
      ,[CMP_Num] as cmp
      ,[ExpireDate] as expdate
  FROM [Rebate] where /*[CMP_Num] like '%" . (isset($cmp) ? $cmp : '%') . "%'
  and*/ [ORD] like '%" . (isset($ord) ? $ord : '%') . "%' and status not like ' Deleted '
  order by 
  " . (isset($_POST["filter"]) ? $_POST["filter"] : "date") . " desc
";

        $stmt = $this->sdb->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string);
        return str_replace("-", " ", preg_replace('/[;:*^]/', '', $string));
    }

    public function add()
    {
        $user = $_SESSION['user']['firstname'][0] . $_SESSION['user']['lastname'][0];
        $ord = $_POST['arr2'];
        $inv = $_POST['message'];

        foreach ($ord as $o) {
            $ordernum = str_replace('ORD-', '', $o);

            $ordernum = str_replace('ORD-', '', $ordernum);
            $ordernum = str_replace('ORD ', '', $ordernum);
            $sql = "UPDATE Collections_Log
                SET rebate = '" . $inv . "'
                where replace(OrderNum, 'ORD-', '') = '" . $ordernum . "' ";
            $update = $this->sdb->prepare($sql);
            $update->execute();

            $sqlcheck = "select count(*) as c from rebate where ord like '" . $ordernum . "' ";
            $stmt = $this->sdb->prepare($sqlcheck);
            $stmt->execute();
            $c = $stmt->fetch(\PDO::FETCH_ASSOC);

            $boolrebate = $c['c'];

            if ($boolrebate == 0) {
                $sql2 = "
                select ord, Customer_name, Cmp_number, cl.rebate
                from request as r
                join Collections_Log as cl on cast(replace(cl.OrderNum, 'ORD-', '') as varchar(max)) = cast(replace(r.ord, 'ORD-', '') as varchar(max))
                where cast(replace(cl.OrderNum, 'ORD-', '') as varchar(max)) = '".$ordernum."'
        ";
                $dataof = $this->sdb->prepare($sql2);
                $dataof->execute();

                $greendata = $dataof->fetch(\PDO::FETCH_ASSOC);

                $sqlin = "INSERT INTO rebate(ord, [Month], [DateSent], RaisedBy, [CustomerName], ValueExclVAT, InvValueExclVAT, DiffExclVAT, [CommentsINVNumber], Status, CMP_Num, updateby)
            values('" . $greendata['ord'] . "', substring(DATENAME(mm, GETDATE()), 0, 4), getdate(), '" . $user . "', '" . $greendata['Customer_name'] . "', '" . $greendata['rebate'] . "', 0, '" . $greendata['rebate'] . "', ' ', 'awaiting', '" . $greendata['Cmp_number'] . "', '" . $user . "' );";

                $insreb = $this->sdb->prepare($sqlin);
                $insreb->execute();


                $boostsql = "select 
                R.CMP_Num as 'cmp', 
                R.ORD as 'ordn', 
                R.ValueExclVAT * (SELECT Value FROM stone360config  WHERE Name = 'Boost_Ratio')as 'BoostValue',
                GETDATE() as 'BoostedDate',
                (select request_id from request as rr where replace(Rr.ORD, 'ORD-', '') like c.OrderNum) as 'id',
                'Awaiting' as 'Status'
                 from collections_log as c
                join rebate as r on 
                r.ORD like c.OrderNum  where c.OrderNum like :ord";
                $booststmt	= $this->sdb->prepare( $boostsql );
                $booststmt->execute(array(':ord' => $ordernum));
    
                $boostdata = $booststmt->fetch(\PDO::FETCH_OBJ);
    
    
                $boostins = "insert into boosts(CMP_Num, ord, Value, BoostDate, Status, Request_ID)
                            values(:cmp, :order, :val, getdate(), :status, :req)";
    
                        $booststmt	= $this->sdb->prepare( $boostins );
                        $booststmt->execute(array(':cmp' => $boostdata->cmp, ':order' => $boostdata->ordn, ':val' => $boostdata->BoostValue,
                      ':status' => $boostdata->Status, ':req' => $boostdata->id));
    
    
            } else {
                return 'all ready in rebate table -' . $o;
            }
            return 'done';
        }

        return 'fail';
    }

    public function create()
    {
        $ord = $_POST['addord'];
        $mon = $_POST['addmon'];
        $date = $_POST['adddate'];
        $addby = $_POST['addby'];
        $addname = $_POST['addname'];
        $addval1 = $_POST['addval1'];
        $addval2 = $_POST['addval2'];
        $addval3 = $_POST['addval3'];
        $invdate = $_POST['addinvdate'];
        $addcin = $_POST['addcin'];
        $addstat = $_POST['addstat'];
        $cmpnum = $_POST['addcmp'];

        $safeord = $this->cleanData($ord, "text");
        $safemon = $this->cleanData($mon, "text");
        $safeaddby = $this->cleanData($addby, "text");
        $safeaddname = $this->cleanData($addname, "text");
        $safeaddval1 = $this->cleanData($addval1, "number");
        $safeaddval2 = $this->cleanData($addval2, "number");
        $safeaddval3 = $this->cleanData($addval3, "number");
        $safecmp = $this->cleanData($cmpnum, "text");
        $safeaddcin = $this->cleanData($addcin, "text");
        $safeaddstat = $this->cleanData($addstat, "text");

        $timin = date("Y-m-d H:i:s", strtotime($invdate));
        $timin2 = date("Y-m-d H:i:s", strtotime($date));

        $sql = "
insert into rebate(ord, [month], [DateSent], RaisedBy, [CustomerName], [ValueExclVAT], [InvValueExclVAT], DiffExclVAT, [DateINVReceived], [CommentsINVNumber], [Status], CMP_Num)
values('" . $safeord . "','" . $safemon . "','" . $timin2 . "','" . $safeaddby . "','" . $safeaddname . "','" . $safeaddval1 . "', '" . $safeaddval2 . "', '" . $safeaddval3 . "', '" . $timin . "', '" . $safeaddcin . "','" . $safeaddstat . "', '" . $safecmp . "')
";
        try {
            $this->sdb->prepare($sql)->execute();
        } catch (\PDOException $e) {
            return false;
        }

        return true;
    }

    public function cleanData($value, $type)
    {
        if ($type == "text") {
            $value = preg_replace("/[^a-zA-Z0-9\-\_\ go]/", "", $value);
        }
        if ($type == "email") {
            $value = preg_replace("/[^a-zA-Z0-9\-\.\@]/", "", $value);
        }
        if ($type == "date") {
            $value = preg_replace("/[^a-zA-Z0-9\-\.\@\\\/]/", "", $value);
        }
        if ($type == "password") {
            $value = preg_replace("/[\'\"\;]/", "", $value);
        }
        if ($type == "number") {
            $value = preg_replace("/![0-9]/", "", $value);
        }
        if ($type == "array") {
            $value = preg_replace("/[^a-zA-Z0-9\,]/", "", $value);
        }
        if ($type == "mac") {
            $value = preg_replace("/[^a-zA-Z0-9\,\:]/", "", $value);
        }
        return $value;
    }

    public function update()
    {
        $id = $_POST['id'];
        $ord = $_POST['ord'];
        $mon = $_POST['mon'];
        $datee = $_POST['datee'];
        $name = $_POST['name'];
        $val1 = $_POST['val1'];
        $val2 = $_POST['val2'];
        $val3 = $_POST['val3'];
        $invdate = $_POST['invdate'];
        $cinn = $_POST['cinn'];
        $status = $_POST['status'];

        $safeord = $this->cleanData($ord, "text");
        $safemon = $this->cleanData($mon, "text");
        $safename = $this->cleanData($name, "text");
        $safeval1 = $this->cleanData($val1, "number");
        $safeval2 = $this->cleanData($val2, "number");
        $safeval3 = $this->cleanData($val3, "number");
        $safecinn = $this->cleanData($cinn, "text");
        $safestatus = $this->cleanData($status, "text");

        if ($datee == '') {
            $timin = 'NULL';
        }

        if ($invdate == '') {
            $invdate = 'NULL';
        } else {
            $timin = date("d-m-Y", strtotime($datee));
            $timin2 = date("d-m-Y", strtotime($invdate));
        }

        $sql = "
set language british;
update rebate
set [Month] = '" . $safemon . "',
[DateSent] = '" . $timin . "',
[CustomerName] = '" . $safename . "',
ValueExclVAT = " . $safeval1 . ",
InvValueExclVAT = " . $safeval2 . ",
DiffExclVAT = " . $safeval3 . ",
DateInvReceived = '" . $timin2 . "',
CommentsInvNumber = '" . $cinn . "',
Status = '" . $status . "'
where RebateID = '" . $id . "'";

        $sqlcheck = "update Rebate
set Status = 'Claimed'
where [CommentsINVNumber] not like '' and  [DiffExclVAT] = 0";

        try {
            $this->sdb->prepare($sql)->execute();
            $this->sdb->prepare($sqlcheck)->execute();
        } catch (\PDOException $e) {
            return false;
        }

        return true;
    }

    public function invoice()
    {
        $user = $_SESSION['user']['firstname'][0] . $_SESSION['user']['lastname'][0];
        $ord = $_POST['arr2'];
        $inv = $_POST['message'];

        foreach ($ord as $o) {
            $ordernum = STR_replace('ORD-', '', $o);

            $sqlup = "
        UPDATE rebate 
         set 
         InvValueExclVAT = ValueExclVAT,
         updateby = '" . $user . "',
         DiffExclVAT = 0,
         DateInvReceived = GETDATE(),
         [CommentsINVNumber] = '" . $inv . "'
         WHERE ORD = replace('" . $ordernum . "','ORD-', '') ";
            $dataof = $this->sdb->prepare($sqlup);
            $dataof->execute();

            $sqp = "update Collections_Log
        SET  invoiceAmt = ValueExclVAT
		FROM REBATE as r with(nolock)
		join collections_log as cl with(nolock) on
		cl.OrderNum = r.ord
        where  replace(ORD, '-ORD', '') = replace('" . $ordernum . "','ORD-', '')
                  ";
            $dataof2 = $this->sdb->prepare($sqp);
            $dataof2->execute();

         
        }

        return "invoice updated";
    }
}
