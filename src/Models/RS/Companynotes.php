<?php
namespace App\Models\RS;

use App\Helpers\Logger;
use App\Models\AbstractModel;
use App\Helpers\Database;

/**
 * Class Companynotes
 * @package App\Models\RS
 */
class Companynotes extends AbstractModel
{
    public $response;
    public $id;

    /**
     * Companynotes constructor.
     */
    public function __construct()
    {
        $this->sdb =  Database::getInstance('sql01', false);
        parent::__construct();
    }

    public function getdata()
    {

        $sql="
        select 
        id as id,
        [CompanyName] as name,
        Location as loc,
        crm,
        cmp,
        letter as let,
        Department as dep,
        Owner as own,
        GDPR as gdpr,
        is_AMR as isamr,
        is_Rebate as isreb,
        rpt,
        Sector as sec,
        Type as typ,
        Notes as note,
        sharedWith,
        [prevowner] as prev,
        [DateAdded] as dadd,
        Reqwuest_id as reqid
        from Companies
        where Location like :postcode
        and (owner like :owner or owner is null) 
        and [CompanyName] like :ent
        and Department like :deptfilter
        and Owner like :ownfilter
        and isnull(rpt, 'AMR') like :amrst";
        //order by :filter asc";

        $executeData = [
            ':postcode' => '%' . (isset($_POST["postcode"]) ? $_POST["postcode"] : '%') . '%',
            ':owner' => (isset($_POST["owner"]) ? $_POST["owner"].'%' : '%') . '%',
            ':ent' => '%' . (isset($_POST["ent"]) ? $_POST["ent"] : '%') . '%',
            ':deptfilter' => '%' . (isset($_POST["deptfilter"]) ? $_POST["deptfilter"] : '') . '%',
            ':ownfilter' => '%' . (isset($_POST["ownfilter"]) ? $_POST["ownfilter"] : '') . '%',
            ':amrst' => '%' . (isset($_POST["amrst"]) ? $_POST["amrst"] : '%') . '%',
            //':filter' => (isset($_POST["filter"]) ? $_POST["filter"] : "[CompanyName]"),
        ];

        Logger::getInstance("CompanyNotes.log")->debug(
            'sql',
            [
                [trim(preg_replace('/\s\s+/', ' ', $sql)), $executeData],
                $executeData
            ]
        );

        $stmt = $this->sdb->prepare($sql);
        $stmt->execute($executeData);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $i = 0;

        $table = "<tbody>";
        foreach ($data as $row) {
            $i++;
            $table .= " 
            <tr class='trstuff'>
            <td  ><input type='checkbox' id='chkid' class='checkboxes' value='".$row['id']."' ></td>
            <td id='btnns'><button type='submit'  style='visibility:hidden;'  id='sav'  CLASS='btn btn-success'> Save </button><button type='submit' id='edi' CLASS='btn btn-info'> Edit </button><BR><br></td>
            <td hidden><textarea disabled id='cmpnum' class='editgroup'>".$row['cmp']."</textarea></td>
            <td hidden><textarea disabled id='crmnum' class='editgroup'>".$row['crm']."</textarea></td>
        <td><textarea disabled id='comname' class='editgroup'>".$row['name']."</textarea></td>
        <td><textarea disabled id='locname' class='editgroup'>".$row['loc']."</textarea></td>
        <td><textarea disabled id='depname' class='editgroup'>".$row['dep']."</textarea></td>
        <td><textarea disabled id='ownname' class='editgroup'>".$row['own']."</textarea></td>
        <td><textarea disabled id='shrw' class='editgroup'>".$row['sharedWith']."</textarea></td>
        <td hidden><textarea disabled id='gdprst' class='editgroup'>".$row['gdpr']."</textarea></td>
        <td hidden><textarea disabled id='isamr' class='editgroup'>".$row['isamr']."</textarea></td>
        <td ><textarea disabled id='isreb' class='editgroup'>".$row['rpt']."</textarea></td>
        <td hidden>".$row['sec']."</td>
        <td hidden>".$row['typ']."</td>
        <td><textarea disabled id='notestuff' class='editgroup'>".$row['note']."</textarea></td>
        <td hidden>".$row['prev']."</td>
        <td hidden>".$row['dadd']."</td>
        <td hidden id='compid'>".$row['id']."</td>

            </tr>
        ";
        }
        $table .= "  </tbody>
        </table>
        ";

        $this->response = $table;
        return $this->response;
    }

    public function getareas()
    {
      
            $sqlstuff2 = "select distinct case when [owner] = 'Recycling' then 'ITAD' else [owner] end as own from Companies with(nolock)";

            $stmt3 = $this->sdb->prepare($sqlstuff2);
            $stmt3->execute();
            $datastuff2 = $stmt3->fetchAll(\PDO::FETCH_ASSOC);
            $this->response = $datastuff2;
            return $this->response;
    }

    public function getdept()
    {
        $sqlstuff = "select distinct( case when Department like 'Recycling' then 'ITAD' else Department end) as Department  from Companies
        where (Department is not null) order by Department";
        
        $stmt2 = $this->sdb->prepare($sqlstuff);
        $stmt2->execute();
        
        $datastuff = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

        $this->response = $datastuff;
        return $this->response;
    }

    public function getowners()
    {
        $owner = "select  name from owners with(nolock) order by name asc";
        $owntstmt = $this->sdb->prepare($owner);
        $owntstmt->execute();
        $owndata = $owntstmt->fetchall(\PDO::FETCH_ASSOC);

        $this->response = $owndata;
        return $this->response;
    }



    public function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        
        return str_replace("-", " ", preg_replace('/[;:*^]/', '', $string)); // Removes special chars.
    }
}
