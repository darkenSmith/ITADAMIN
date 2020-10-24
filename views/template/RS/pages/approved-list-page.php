<script>
    jQuery(document).ready(function ($) {
        $('#search').keyup(function () {
            search_table($(this).val());
        });

        function search_table(value) {
            $('#tab tbody tr').each(function () {
                let found = 'false';
                $(this).each(function () {
                    if ($(this).text().toLowerCase().indexOf(value.toLowerCase()) >= 0) {
                        found = 'true';
                    }
                });
                if (found == 'true') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        $('#tab tr .appbtn').click(function () {
            let idnum = $(this).closest('tr').attr('id');
            $.ajax({
                type: "POST",
                url: "/RS/approvedupdate/",
                data: {idnum: idnum},
                success: function (data) {
                    alert("Done");
                }
            });
            location.reload();
        });
    });
</script>

<?php
$new = json_decode($list);

$table = "
<div align='center'>  <label> Search         <a data-toggle='tooltip' class='tooltipLink' data-original-title='filter's data in visble table.'>
<span class='glyphicon glyphicon-info-sign'></span>
</a></label> <input type='text' name='search' id='search' class='form-control' />  
</div>

<h1> Approve List </h1>
<table class='table applisttab' id='tab'>
<thead>
    <tr>
    <th></th>
    <th> username</th>
    <th> firstname</th>
    <th> lastname</th>
    <th> email</th>
    <th> approved </th>
    <th> Company Name </th>
    <th> Company Number </th>
    <th> CMP </th>
    </tr>
    </thead> <tbody>";

foreach ($new as $l) {
    $userna = $l->username;
    $firstname = $l->firstname;
    $lastname = $l->lastname;
    $email = $l->email;
    $approved = $l->approved;
    $idd = $l->idnum;
    $cmp = $l->cmp;
    $companyname = $l->company_name;
    $CompanyNUM = $l->CompanyNUM;

    $table .= "
<tr id='" . $idd . "'>
    <td><input type='submit' class='appbtn btn " . (($approved != 'Y') ? "btn-warning" : "btn-success") . "' id='test' value='toggle approved' </td>
    <td>" . $userna . " </td>
    <td>" . $firstname . " </td>
    <td>" . $lastname . " </td>
    <td>" . $email . " </td>
    <td>" . $approved . " </td>
    <td>" . $companyname . " </td>
    <td>" . $companyname . " </td>
    <td>" . $cmp . " </td>

    <tr>
    ";
}
$table .= "    </tbody>
    </table>";

echo $table;
