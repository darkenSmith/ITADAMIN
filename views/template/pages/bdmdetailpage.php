<body>

<?php print_r($_POST['reqid']);  ?> 
<!DOCTYPE html>
<html lang="en">
<head>
<link href="/inc/css/bootstrap.min.css" rel="stylesheet">
<div class='container'>

<style>

    body{

        background-color: #ffff;
    }

    .container{

        background-color: white;


    }


</style>

<h1 class="page-header"> Stone Computers: BDM view <small>detail</small></h1>
<h2><?PHP echo $rid ?> </h2>

<?php




echo "
<div class='table'>
  <table cellpadding='3'cellspacing = '3' class='table' id='ntb'>
  <thead>
  ";


echo "
<tr>
<th> Total Units </th>
<td>". $reqprodtotals['totalunits'] ."</td>
</tr>
<th> Total Weight </th>
<td>". $reqprodtotals['totalweight'] ."</td>
</tr>
 <th> Items </th>
<th> Qty Working </th>
 <th>AssetMgmt Required </th>
 <th>HMG IA Standard No:5 Required </th>
 </thead>
 <tbody>

";


foreach($reqprodlist as $prop){

    $id = $prop['prodid'];
    $prodname = $prop['prodname'];
    $working = $prop['w'];
    $wip = $prop['wip'];
    $asset = $prop['asset'];
    $o1 = $prop['o1'];
    $o2 = $prop['o2'];
    $o3 = $prop['o3'];
    $o4 = $prop['o4'];
    $o5 = $prop['o5'];
    $o6 = $prop['o6'];
    $totalunits = $prop['totalunits'];
    $totalweight = $prop['totalweight'];


    if ($asset == 1) {
        $nd="checked";
        $ass = 1;
      }else{
        $nd = '';
        $ass = 0;
      }
      if ($wip == 1) {
        $nd2="checked";
        $wip = 1;
      }else{
        $nd2 = '';
        $wip = 0;
      }





 echo "
 <tr id=row".$id.">
<td id=".   $id." class='partis'>".$prodname."</td>
<td><input type='textbox' class='workin  group1' value='". $working."'/disabled></td>
  <td><input type='checkbox' id='v'   class='group1'  ".$nd ."/disabled></td>
<td><input type='checkbox' id='w' name='wipe' class='group1'  ".$nd2."/disabled></td>


</tr> 
";

if($prodname == 'Other1'){
echo "
<td> <input type='textbox' id='other1' class='group1' value='". $o1 ."'/disabled></td>";
}
else if($prodname  == 'Other2'){
 echo "
 <td> <input type='textbox' id='other2' class='group1' value='". $o2 ."'/disabled></td>";
 }
 else if($prodname == 'Other3'){
   echo "
   <td> <input type='textbox' id='other3' class='group1' value='". $o3 ."'/disabled></td>";
 }

    if($prodname  == 'Other4'){
     echo "
     <td> <input type='textbox' id='other4' class='group1' value='". $o4 ."'/disabled></td>";
     }
     else if($prodname  == 'Other5'){
      echo "
      <td> <input type='textbox' id='other5' class='group1' value='". $o5 ."'/disabled></td>";
      }
      else if($prodname == 'Other6'){
        echo "
        <td> <input type='textbox' id='other6' class='group1' value='". $o6 ."'/disabled></td>";
      }


      
}
echo "
      </tbody>
      </table>
    </div>
    
    
    <br><a href='http://recwebtest.stone.stonecomputers/RS/BDMview/' class='btn btn btn-warning' > Go Back</a>";
?>

</div>
</html>