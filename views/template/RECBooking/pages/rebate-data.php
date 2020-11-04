<?php
$timedateinv = '';
$timedatesent = '';
$expiredate = '';

?>

<table id='company' width='100%' class='sortable table table-hover'>
    <thead>
    <tr>
        <th></th>
        <th> ORD</th>
        <th> Month</th>
        <th> Date Sent</th>
        <th> RaisedBy</th>
        <th> Customer Name</th>
        <th> Val Exc VAT</th>
        <th> Val INV Exc VAT</th>
        <th> Difference Exc VAT</th>
        <th> Date INV Received</th>
        <th> Comments INV Number</th>
        <th> Status</th>
        <th hidden> RebateID</th>
        <th> Expire date</th>
        <th hidden> CMP_Num</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    foreach ($rebateData as $row) {
        $i++;
        if ($row['date'] == '') {
            $timedatesent = '';
        } else {

            $timedatesent = date("d-m-Y", strtotime($row['date']));
        }

        if ($row['expdate'] == NULL) {
            $expiredate = ' ';
        } else {

            $expiredate = date("d-m-Y", strtotime($row['expdate']));
        }

        if ($row['divr'] == '') {
            $timedateinv = ' ';
        } else {

            $timedateinv = date("d-m-Y", strtotime($row['divr']));
            if ($timedateinv == '01-01-1970') {
                $timedateinv = '';
                $_SESSION['timedateinv'] = $timedateinv;
            }
        }

        echo " 
        <tr class='trstuff'>
        <td id='btnns'><button type='submit'  style='visibility:hidden;'  id='sav'  class='btn btn-success'> Save </button><button type='submit' id='edi' class='btn btn-secondary'> Edit </button><button type='submit' id='boostbtn' style='visibility:hidden;' class='btn btn-info'> Boost </button></td>
        <td><textarea disabled id='ordnum' class='editgroup'>" . $row['ORD'] . "</textarea></td>
        <td><textarea disabled id='month' class='editgroup'>" . $row['Month'] . "</textarea></td>
        <td><textarea disabled id='datee' class='editgroup'>" . $timedatesent . "</textarea></td>
        <td>" . $row['RaisedBy'] . "</td>
        <td><textarea disabled id='name' class='editgroup'>" . $row['name'] . "</textarea></td>
        <td><textarea disabled id='vat1' class='editgroup'>" . $row['vevat'] . "</textarea></td>
        <td><textarea disabled id='vat2' class='editgroup'>" . $row['viev'] . "</textarea></td>
        <td><textarea disabled id='vat3' class='editgroup'>" . $row['dev'] . "</textarea></td>
        <td><textarea disabled id='invdate'  class='editgroup'>" . $timedateinv . "</textarea></td>
        <td><textarea disabled id='cinn' class='editgroup'>" . $row['cin'] . "</textarea></td>
        <td><select class='editgroup' disabled id='status'>
        <option selected>" . $row['stat'] . "</option>
        <option>Claimed</option>
        <option>Part-Claimed</option>
        <option> Awaiting </option>
        <option> Deleted </option>
        </select></td>
        <td hidden><textarea disabled id='idnum'> " . $row['rid'] . "</textarea></td>
        <td><textarea disabled id='dateEXP' class='editgroup'>" . $expiredate . "</textarea></td>
        <td hidden>" . $row['cmp'] . "</td>
    
    
        </tr>
       ";
    }
    ?>

    </tbody>
</table>
