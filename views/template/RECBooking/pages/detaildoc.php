<?php

use App\Helpers\Database;

$sdb = Database::getInstance('sql01');
$gdb = Database::getInstance('greenoak');
$rdb = Database::getInstance('recycling');
?>
<style>
    th {
        background: #EAEAEA;
    }

    textarea {
        width: 300px;
        height: 150px;
    }

    #emailcon {

        margin-right: 240px;
    }

</style>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" href="/RS/booking/">Booked Collection</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/RS/arc/">Collected</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/RS/RGR/">Recycling Goods Receipting </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/RS/companynote/">Company Notes</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/RS/rebatepage/">Rebates</a>
    </li>

</ul>

<hr>
<h1>Request Details</h1>
<h3> <?php echo $_GET['rowid']; ?> </h3>
<div id='button'>
    <button type='button' id='buttonID2' class='buttonIDed btn btn-primary'>edit</button>
    <button type='button' id='buttondoneID2' class='btnbuttondoneID btn btn-success'>Done</button>

</div><br>


<div class="row">

    <?php
    $rowid = isset($_GET['rowid']) ? " WHERE Request_ID = " . $_GET["rowid"] : "";
    $rowid2 = isset($_GET['rowid']) ? " WHERE RequestID = '" . $_GET["rowid"] . "'" : "";
    $justID = $_GET["rowid"];


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
where request_id = " . $justID . "

group by
 
r.request_id

order by r.request_id
";


    $sql = "
select
 

Request_ID as id,
dbo.[zzfnRemoveNonNumericCharacters](ORD) as ordnum,
prev_orders as prev,
Customer_name name,
replace([Owner], ' ', '') as ownerreq,
approved,
process,
help_onsite,
replace(customer_contact, '-', ' ') as contact,
town as twn,
request_date_added as reqadd,
left(postcode, 2) as prefix,
rtrim(customer_email) as email,
[Parking Notes] as parkingnotes,
[Early Acess notes] as accessnotes,
contact_tel as tel,
customer_phone as cphone,
customer_contact_positon as position,
add1 + add2 + add3 as address,
add1 as address1,
add2 as address2,
add3 as address3,
GDPRconf as gdprcon,
lift,
 ground, 
 steps,
  twoman,
postcode as  postcode,
bio_pass as BIOS_Password,
(SELECT [survey_deadline] FROM Booked_Collections " . $rowid2 . ")  as survdue,
(SELECT [surveysnet_date] FROM Booked_Collections " . $rowid2 . ")  as survsenttime,
req_col_instrct as CollectionInstruction,
isnull(request_col_date, '') as CollectionDate,
avoidtimes,
collection_date as ProposedDate,
done_date as RequestDoneDate,
Update_Note as Updatenote,
req_note as statnote ,
updatedBy as RequestUpdatedBy,
ISNULL([TYPE], '') as typ,
owner,
ORD as  salo

 from request" . $rowid;


    $stmt = $sdb->prepare($sql);
    if (!$stmt) {
        echo "\nPDO::errorInfo():\n";
        print_r($sdb->errorInfo());
        die();
    }
    $stmt->execute();

    echo "
<div class='table'>
  <table cellpadding='3' cellspacing ='3' class='table' id='ntb'>
  <thead>
  ";

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
where rt.request_id = " . $justID . "

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


    $stmt2 = $sdb->prepare($sqlextra);
    if (!$stmt2) {
        echo "\nPDO::errorInfo():\n";
        print_r($sdb->errorInfo());
        die();
    }
    $stmt2->execute();

    $data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);


    foreach ($data as $row) {

        $sqlnotes = "
SET NOCOUNT on
declare @postcode varchar(10)

set @postcode='" . $row['postcode'] . "'";
    }


    $sqlnotes .= "select top 1
C.gdpr as gdpr, 
c.is_AMR as amrc,
isnull(replace(c.location, ' ', ''), 'null'), 
isnull(max(c.notes), 'no notes') as notes
from request as rt2
full join companies as c on
 Location = @postcode and 
 Customer_name = [CompanyName]

 where  Location = '" . $row['name'] . "'+@postcode
group by isnull(replace(c.location, ' ', ''), 'null')
, C.gdpr
,c.is_AMR



order by isnull(replace(c.location, ' ', ''), 'null')
";


    $stmtnote = $sdb->prepare($sqlnotes);
    if (!$stmtnote) {
        echo "\nPDO::errorInfo():\n";
        print_r($sdb->errorInfo());
        die();
    }
    $stmtnote->execute();
    $datanote = $stmtnote->fetchAll(PDO::FETCH_ASSOC);


    //var_dump($sqlnotes);

    $stmttotal = $sdb->prepare($totalswu);
    if (!$stmttotal) {
        echo "\nPDO::errorInfo():\n";
        print_r($sdb->errorInfo());
        die();
    }
    $stmttotal->execute();

    $datar = $stmttotal->fetchAll(PDO::FETCH_ASSOC);

    ///// end of image upload///


    ////// start of bottom feilds /////


    // while($row = sqlsrv_fetch_array($stmt)){
    foreach ($datar as $roww) {


        echo "
<tr>
<th> Total Units </th>
<td>" . $roww['totalunits'] . "</td>
</tr>
<th> Total Weight </th>
<td>" . $roww['totalweight'] . "</td>
</tr>
 <th> Items </th>
<th> Qty Working </th>
 <th hidden>AssetMgmt Required </th>
 <th hidden>HMG IA Standard No:5 Required </th>
 </thead>
 <tbody>

";
    }
    $i = 0;

    $pl = array();
    // while($row = sqlsrv_fetch_array($stmt)){
    foreach ($data2 as $row) {

        //Creates a loop to loop through results
        if ($row['asset'] == 1) {
            $nd = "checked";
            $ass = 1;
        } else {
            $nd = '';
            $ass = 0;
        }
        if ($row['wip'] == 1) {
            $nd2 = "checked";
            $wip = 1;
        } else {
            $nd2 = '';
            $wip = 0;
        }


        //<td hidden><input type='checkbox' id='w' name='wipe' class='group1'  ".$nd2." disabled></td>
        //  <td hidden><input hidden type='checkbox' id='v'   class='group1'  ".$nd ." disabled></td>

        echo "
    <tr id=row" . $row['prodid'] . ">
   <td id=" . $row['prodid'] . " class='partis'>" . $row['prodname'] . "</td>
   <td  hidden><input type='text' class='idp' value='" . $row['prodid'] . "' ></td>
   <td><input type='text' class='workin  group1' value='" . $row['w'] . "' disabled></td>
   <td class='delline'><a href='javascript:void(0);'  class='remove'><span class='glyphicon glyphicon-remove'></span></a></td>
   </tr>
";

        if ($row['prodname'] == 'Other1') {
            echo "
   <td> <input type='text' id='other1' class='group1' value='" . $row['o1'] . "' disabled></td>";
        } else if ($row['prodname'] == 'Other2') {
            echo "
    <td> <input type='text' id='other2' class='group1' value='" . $row['o2'] . "' disabled></td>";
        } else if ($row['prodname'] == 'Other3') {
            echo "
      <td> <input type='text' id='other3' class='group1' value='" . $row['o3'] . "' disabled></td>";
        }

        if ($row['prodname'] == 'Other4') {
            echo "
        <td> <input type='text' id='other4' class='group1' value='" . $row['o4'] . "' disabled></td>";
        } else if ($row['prodname'] == 'Other5') {
            echo "
         <td> <input type='text' id='other5' class='group1' value='" . $row['o5'] . "' disabled></td>";
        } else if ($row['prodname'] == 'Other6') {
            echo "
           <td> <input type='text' id='other6' class='group1' value='" . $row['o6'] . "' disabled></td>";
        }


        $pl[] = $row['prodid'];

    }

    echo " 
  </tbody>
  </table>
</div>

<div class='button'>
  <button  type='button' id='addlineBTN' class='btndel btn btn-info'>Add </button>
</div>
<br>
<input type='hidden' id='rowid' value='" . $rowid . "'>
<input type='hidden' id='justID' value='" . $justID . "'>
";


    $addsql = "
select product_ID , product  from productlist where product_id not in(19, 21, 23)
and active = 1
 order by [order] asc

";


    $stmtadd = $sdb->prepare($addsql);
    if (!$stmtadd) {
        echo "\nPDO::errorInfo():\n";
        print_r($sdb->errorInfo());
        die();
    }
    $stmtadd->execute();

    $adddata = $stmtadd->fetchAll(PDO::FETCH_ASSOC);

    echo " 

<div id ='newlinedata'>

<label for='part-select'>Choose a part:</label>

<select id='part-select'>
    <option value='' selected>Please choose an option</option>
    ";

    foreach ($adddata as $op) {


        echo "
    <option value='" . $op['product_ID'] . "'>" . $op['product'] . "</option>

";


    }

    echo " 
</select>
<input type='number' id='newwork' min=0 value='' placeholder='Working Qty'>
<input type='text' id='othername' value='' placeholder='Part Name'>
<button type='button' id='subline' class='subnewline btn btn-success'>submit </button>
</div>";

    //var_dump($pl);


    ?>


    <script>


        var prodid = [];

        prodid = '<?php echo json_encode($pl); ?>';


        $(document).ready(function () {


            var updateall = 0;


            $('.adduser').hide();
            $('#subuser').hide();
            $('#compa').hide();


            $('#roles').on('change', function () {
                if ($('#roles option:selected').val() == 3 || $('#roles option:selected').val() == 4) {

                    $('#compa').show();
                } else {
                    $('#compa').hide();
                }

            });


            $('#apsupdate').hide();

            $('#apsedit').on('click', function () {

                $('#apsupdate').show();
                $('#apsedit').hide();
            });


            $('#subuser').on('click', function () {


                var name = $('#name_ed').val();
                var namesp = name.split(" ");
                var firstname = namesp[0];
                var lastname = namesp[1];
                var email = $('#emailuser option:selected').val();
                var role_id = $('#roles option:selected').val();
                var customer_id = $('#companies option:selected').val();

                username = email;

                if (role_id == 0 || customer_id == 0) {

                    alert('please select role type');
                } else {


                    jQuery.ajax({
                        url: "/admin/addUserPost",
                        type: "POST",
                        data: {
                            role_id: role_id,
                            firstname: firstname,
                            username: username,
                            lastname: lastname,
                            email: email,
                            customer_id: customer_id
                        },
                        success: function (data) {
                            jQuery('#result').show();
                            jQuery('#result').html(data);
                        }
                    });
                }

            });


            if ($('#portalstat').text() == 'Customer has portal access') {

                $('#addport').hide();

            }


            $('#addport').on('click', function () {

                // alert('hello');
                $('#addport').hide();

                $('#subuser').show();
                $('.adduser').show();

            });


            // $("#lorrytype").hide();
            $('#othername').hide();

            $(".btnbuttondoneID").hide();
            $(".buttonIDed").click(function () {
                $(".btnbuttondoneID").show();
                $(".buttonIDed").hide();
                alert("You are about to edit.");
                $("input.group2").removeAttr("disabled");
                $("input.group1").removeAttr("disabled");
                $("select.group1").removeAttr("disabled");

                $("textarea.group1").removeAttr("disabled");
            });

            // $("#buttondoneID, btnbuttondoneID").click(function(){
            //    $("input.group1").attr('disabled', true);
            //    $("textarea.group1").attr('disabled', true);
            //    $("#buttondoneID, btnbuttondoneID").hide();
            //    $("#buttonID, btneddy").show();
            // });

            var work = [];
            var assettick;
            var wipetick;
            var reqlines;


            $(".btnbuttondoneID").click(function () {


                var email = $("#email_ed").val();
                var name = $("#name_ed").val();
                var twn = $("#twn_ed").val();
                var address1 = $("#add1_ed").val();
                var address2 = $("#add2_ed").val();
                var address3 = $("#add3_ed").val();
                var tel = $("#tel_ed").val();
                var custphone = $("#telcust_ed").val()
                var consi = $("#consi").val();
                var ram = $("#conram").val();
                var consw = $("#consw").val();
                var condep = $("#condep").val();
                var conam = $("#conam").val();
                var ordnum = $("#ord_name").val();
                var manord = $("#manord").val();
                var pos = $("#pos_ed").val();
                var biopass = $("#bio_ed").val();
                var colinstruct = $("#collinstr_ed").val();
                var colldate = $("#coldate_ed").val();
                var avoid = $("#avoid_ed").val();
                var upnotes = $("#updatenote_ed").val();
                var reqby = <?php echo "'" . str_replace('@stonegroup.co.uk', '', $_SESSION['user']['username']), '', '' . "'" ?>;
                var postcode = $("#post_ed").val();
                var reqstat = $("#reqstatenote_ed").val();
                var reqowner = $("#reqown_ed").val();
                var dtype = $('#dtype_ed').val();
                var previousorders = $('#reqprev_ed').val();
                var approved = $('#app_ed').val();
                var process = $('#pro_ed').val();
                var gdprselect = $('#gdpr-select option:selected').val();
                var deadline = $('#survdead2').val();
                var emailsentdate = $('#sentdate').val();
                var reqid = $("#justID").val();
                var emailsent = $("#conf").val();

                if ($('#survdead2').val() === '01-01-70 00:00:00') {

                    $('#survdead2').val('');

                }


                if (gdprselect.length <= 0) {

                    gdprselect = 0;
                }


                //alert(reqid);


                var checkbox_value = "";


                var other1name = $('#other1').val();

                // alert(other1name);


                var other2name = $('#other2').val();

                //  alert(other2name);


                var other3name = $('#other3').val();

                var other4name = $('#other4').val();

// alert(other1name);


                var other5name = $('#other5').val();


                var other6name = $('#other6').val();

                //  alert(other3name);


                $.each(JSON.parse(prodid), function (index, value) {


                    if ($("#ntb #row" + value + " #v").prop("checked") == true) {
                        assettick = 1;


                    } else if ($("#ntb #row" + value + " #v").prop("checked") == false) {
                        assettick = 0;

                    }
                    if ($("#ntb #row" + value + " #w").prop("checked") == true) {
                        wipetick = 1;


                    } else if ($("#ntb #row" + value + " #w").prop("checked") == false) {
                        wipetick = 0;


                    }

                    var id = $("#ntb #row" + value).find(".idp").val();
                    var working = $("#ntb #row" + value).find(".workin").val();
                    //var notworking =$("#ntb #row"+value).find(".notworkin").val();


                    //alert(id);
                    work.push({id: id, working: working, asset: assettick, wipe: wipetick});
                    reqlines = JSON.stringify(work);
                    console.log(reqlines);

                });

                $.ajax({
                    type: "POST",
                    url: "/RS/updatadata/",
                    data: {
                        other1name: other1name,
                        other2name: other2name,
                        other3name: other3name,
                        other4name: other4name,
                        other5name: other5name,
                        other6name: other6name,
                        reqlines: reqlines,
                        email: email,
                        name: name,
                        address1: address1,
                        address2: address2,
                        address3: address3,
                        tel: tel,
                        consi: consi,
                        ram: ram,
                        consw: consw,
                        condep: condep,
                        conam: conam,
                        ordnum: ordnum,
                        manord: manord,
                        pos: pos,
                        biopass: biopass,
                        colinstruct: colinstruct,
                        colldate: colldate,
                        upnotes: upnotes,
                        reqby: reqby,
                        reqid: reqid,
                        twn: twn,
                        postcode: postcode,
                        custphone: custphone,
                        reqstat: reqstat,
                        reqowner: reqowner,
                        dtype: dtype,
                        previousorders: previousorders,
                        approved: approved,
                        process: process,
                        gdprselect: gdprselect,
                        deadline: deadline,
                        emailsent: emailsent,
                        avoid: avoid,
                        emailsentdate: emailsentdate
                    },

                    success: function (data) {
                        //alert(data);
                        //  alert("success!");
                        // location.reload(true);
                        //alert("con7: " + con7 + "rowid" + rowid);
                        updateall++;
                        reload();
                        $('#apsupdate').click();
                    }
                });

                //setInterval('location.reload()', 5000);


            });


            $("#buttondelID, btndel").click(function () {
//alert("hello");
                var del = 1;
                var rid = $("#justID").val();


                $.ajax({
                    type: "POST",
                    url: "/RS/delrequest/",
                    data: {
                        del: del,
                        rid: rid
                    },
                    success: function (data) {
                        // alert(data);
                        // alert("success!");
                        //alert("con7: " + con7 + "rowid" + rowid);
                        // alert(rid);
                    }
                });

            });
            var lo = '';
            var flag = 0

            if ($('#lorry').prop("checked") == true) {
                // alert("Checkbox is checked.");
                flag = 1;
                lo = 'Yes';
                //alert(lo);
                $("#lorrytype").show();
            }
            if ($('#lorry').prop("checked") == false) {
                // alert("Checkbox is unchecked.");
                lo = 'No';
                flag = 0;
                // alert(lo);
                //$("#lorrytype").hide();
            }


            $('#ntb input[type="checkbox"]').click(function () {

                if ($('#lorry').prop("checked") == true) {
                    //6 alert("Checkbox is checked.");
                    flag = 1;
                    lo = 'Yes';
                    // alert(lo);
                    $("#lorrytype").show();
                } else if ($('#lorry').prop("checked") == false) {
                    //  alert("Checkbox is unchecked.");
                    lo = 'No';
                    flag = 0;
                    // alert(lo);
                    //  $("#lorrytype").hide();
                }
            });

            // $("select.bookingstat").change(function(){
            //         var bookingstatus = $(".bookingstat option:selected").val();
            //     });
            var clicks = 0;


            $('#apsedit').on('click', function () {

                clicks++;

                if (clicks == 1) {
                    $("input.group2").attr('disabled', false);
                    $("input.group1").attr('disabled', false);
                    $("select.group1").attr('disabled', false);
                    $("textarea.group2").attr('disabled', false);
                }
                if (clicks == 2) {

                    $("input.group2").attr('disabled', true);
                    $("input.group1").attr('disabled', true);
                    $("select.group1").attr('disabled', true);
                    $("textarea.group2").attr('disabled', true);

                    clicks = 0;

                }


            });


            $("#apsupdate").on('click', function () {

                // alert('Updated');
                var e = document.getElementById("bookingstat");


                var deadline = '';


                var bookingstatus = e.options[e.selectedIndex].text;
                //alert(bookingstatus);
                var owner = $('#reqown_ed').val();
                var drivername = $('#drname').val();
                var vehicreg = $('#vregs').val();
                var constat = $('#constat').val();
                var connotes = $('#conote').val();
                var confemail = $('#emalconf').val();
                var sursent = $('#SurveySent').val();
                var surcomp = $('#SurveyComp').val();
                var sentby = $('#sentby').val();
                var jobno = $('#jn_up').val();
                var accup = $('#acc_up').val();
                var a_status = $("#a_up").val();
                var p_status = $("#p_up").val();
                var s_status = $("#s_up").val();
                var notes_status = $("#notes_up").val();
                var rowid = $("#justID").val();
                var lortype = $("#lorrytype option:selected").val();
                var help = $("#help_on").val();
                var early = $("#eaccessnote").val();
                var parking = $("#parkingnotes").val();
                var lift = $("#lift").val();
                var steps = $("#steps").val();
                var ground = $("#ground").val();
                var twoman = $("#twoman").val();


                // if($('#survdead').length > 0){

                var deadline = $('#survdead2').val();

                //   alert(deadline);
                // }

//alert(deadline);


                $.ajax({
                    type: "POST",
                    url: "/RS/UpdateAPS/",
                    data: {
                        bookingstatus: bookingstatus,
                        drivername: drivername,
                        vehicreg: vehicreg,
                        constat: constat,
                        connotes: connotes,
                        confemail: confemail,
                        sursent: sursent,
                        surcomp: surcomp,
                        sentby: sentby,
                        jobno: jobno,
                        accup: accup,
                        a_status: a_status,
                        p_status: p_status,
                        s_status: s_status,
                        notes_status: notes_status,
                        rowid: rowid,
                        lo: lo,
                        flag: flag,
                        owner: owner,
                        deadline: deadline,
                        help: help,
                        early: early,
                        parking: parking,
                        lift: lift,
                        ground: ground,
                        steps: steps,
                        twoman: twoman

                    },
                    success: function (data) {
                        // alert(data);
                        // alert("success!");
                        //console.log(lortype);
                        updateall++;
                        reload();
                        console.log(updateall);
                        $('#buttondoneID').click();

                        // location.reload(true);
                        //alert("con7: " + con7 + "rowid" + rowid);
                    }
                });

                $("input.group2").attr('disabled', true);
                $("textarea.group2").attr('disabled', true);

            });

            function reload() {
                if (updateall == 2) {

                    location.reload(true);
                }
            }


        });


    </script>

    <div id="result"></div>


</div>
<!--</table>-->

<div class="col-xl-4">
    <?php


    $help = '';
    $earlyacc = '';
    $parking = '';


    $postcode = '';
    $salesordernum = '';

    $stmtnote = $sdb->prepare($sqlnotes);
    if (!$stmtnote) {
        echo "\nPDO::errorInfo():\n";
        print_r($sdb->errorInfo());
        die();
    }
    $stmtnote->execute();


    echo "<table cellpadding='1' cellspacing ='1' class='table'>";


    $sqlord = "


        select replace(SalesOrderNumber, 'ORD-', '') as ord from [greenoak].[we3recycler].[dbo].SalesOrders where CustomerPONumber like '%" . $_GET['rowid'] . "'
        ";

    $stmtord = $gdb->prepare($sqlord);

    $stmtord->execute();
    $dataord = $stmtord->fetch(PDO::FETCH_ASSOC);
    if (isset($dataord['ord'])) {
        $salesordernum = $dataord['ord'];
    }


    echo "        <tr>
        <th >GreenOak ORD</th>
        <td><input type='text' id='ord_name' class='group1' value='" . $dataord['ord'] . "' disabled></td>
      </tr></table>";


    //    $ordupdate = "update request
    //    set ord = '".$dataord['ord']."'
    //    where request_id = ".$justID;

    // $ordstmtup = $sdb->prepare($sqlord);

    // $ordstmtup->execute();
    // $ec = $stmtbook2->fetch(PDO::FETCH_ASSOC);


    // while($row = sqlsrv_fetch_array($stmt)){
    foreach ($data as $row) {


        $portalcheck = "SELECT case when COUNT(*) > 0 then 'Customer has portal access' else 'Customer has no portal access' end AS usercheck FROM recyc_users WHERE username ='" . $row['email'] . "'";
        $portstmt = $rdb->prepare($portalcheck);
        $portstmt->execute();
        $port = $portstmt->fetch(PDO::FETCH_ASSOC);

        echo "<h4 id='portalstat'>" . $port['usercheck'] . "</h4>  <button type='button' id='addport' class='portadd btn btn-success'>Add to portal </button><button type='button' id='subuser' class='portadd btn btn-success'>submit </button>
       
          <div class= 'adduser'>
          <hr> ";


        $complist = "SELECT * FROM recyc_company_sync ";
        $compstmt = $rdb->prepare($complist);
        $compstmt->execute();
        $companies = $compstmt->fetchall(PDO::FETCH_ASSOC);


        $permlist = "SELECT * FROM recyc_roles where id not in(1, 2, 7) ";
        $permstmt = $rdb->prepare($permlist);
        $permstmt->execute();
        $perm = $permstmt->fetchall(PDO::FETCH_ASSOC);

        echo "<div id='compa'><label> assign company:</label><Select id='companies'>
          <option value=0 selected>Please select a Company</option>
          ";

        foreach ($companies as $comp) {

            echo "<option value='" . $comp['company_id'] . "'>" . $comp['company_name'] . "</option> ";


        }


        echo "</select></div><br>";

        echo "<label> assign role:</label><Select id='roles'>
          <option value='0' selected>Please select a role type</option>";

        foreach ($perm as $roles) {

            echo "<option value='" . $roles['id'] . "'>" . $roles['name'] . "</option> ";


        }


        echo "</select>";

        $emaillist = $row['email'];

        $list = explode(" ", $emaillist);
        //var_dump($list);
        echo "<Select id='emailuser'>
          <option value='0' selected>Please select a email</option>";


        foreach ($list as $e) {
            //var_dump($e);
            echo "<option value='" . $e . "'>" . $e . "</option> ";


        }

        echo "</select></div>";


        $_SESSION['owner'] = $row['ownerreq'];


        $geprstring = '';

        if ($row['gdprcon'] == 1) {
            $geprstring = 'Yes';
        } else {
            $geprstring = 'No';
        }

        if (isset($row['ProposedDate'])) {

            $timepro = date("d/m/y", strtotime($row['ProposedDate']));
            $_SESSION['protime'] = $timepro;
        } else {

            $timepro = '';
            $_SESSION['protime'] = $timepro;
        }


        if (isset($row['survdue'])) {

            $DEADtime = date("d-m-y H:i:s", strtotime($row['survdue']));
        } else {

            $DEADtime = '';
        }


        $_SESSION['newdeadline'] = $DEADtime;


        echo "<table cellpadding='1' cellspacing ='1' class='table'>";
        $menudet = "
          <tr hidden>
          <th >Manual ORD</th>
          <td><input type='text' id='manord' class='group1' value='" . $row['salo'] . "' disabled></td>
        </tr>


          <tr>
          <th >GDPR</th>
          <td>        
            <select id='gdpr-select'  class='group1'>
          <option value='" . $row['gdprcon'] . "' selected>" . $geprstring . "</option>
          <option value='0' >No</option>
          <option value='1' >Yes</option>
          </select>
          <input type='text' id='gdprs' value='" . $geprstring . "' disabled>
          </td>
        </tr>
          <tr>
            <th >Name</th>
            <td><input type='text' id='name_ed' class='group1' value='" . $row['name'] . "' disabled></td>
          </tr>
          <tr>
            <th> Requested By  </th>
            <td>" . $row['contact'] . "</td>
          </tr>
          <tr>
            <th>Email Address</th>
            <td><input type='text' id='email_ed' size='90' class='group1' value='" . $row['email'] . "' disabled></td>
          </tr>
          <tr>
            <th> customer Telephone  </th>
            <td><input type='text' id='telcust_ed' class='group1' value='" . $row['cphone'] . "' disabled></td>
          </tr>
          <tr>
          <th> Contact Telephone  </th>
          <td><input type='text' id='tel_ed' class='group1' value='" . $row['tel'] . "' disabled></td>
        </tr>
          <tr>
            <th>Position </th>
            <td><input type='text' id='pos_ed' class='group1' value='" . $row['position'] . "' disabled></td>
          </tr>
          <tr>
            <th> Address1 </th>
            <td><input size='48' type='text' id='add1_ed' class='group1' value='" . $row['address1'] . "' disabled></td>
          </tr>
          <tr>
            <th> Address2 </th>
            <td><input size='48' type='text' id='add2_ed' class='group1' value='" . $row['address2'] . "' disabled></td>
          </tr>
          <tr>
            <th> Address3 </th>
            <td><input size='48' type='text' id='add3_ed' class='group1' value='" . $row['address3'] . "' disabled></td>
          </tr>
          <tr>
          <th> Postcode </th>
          <td><input type='text' id='post_ed' class='group1' value='" . $row['postcode'] . "' disabled></td>
        </tr>
        <tr>
        <th> Town </th>
        <td><input type='text' id='twn_ed' class='group1' value='" . $row['twn'] . "' disabled></td>
      </tr>";

        $sqlowners = "select replace(name, ' ', '') as name from owners";

        $stmtowners = $sdb->prepare($sqlowners);
        if (!$stmtnote) {
            echo "\nPDO::errorInfo():\n";
            print_r($sdb->errorInfo());
            die();
        }
        $stmtowners->execute();
        $dataoenrs = $stmtowners->fetchAll(PDO::FETCH_ASSOC);

        $menudet .= "<tr>
      <th> Owner </th>
      <td>
      
      <select id='reqown_ed' class='group1' disabled>
      <option selected>" . $row['ownerreq'] . " </option>";

        foreach ($dataoenrs as $owners) {
            $menudet .= " <option>
          " . $owners['name'] . "
          
      </option>";
        }


        $help = $row['help_onsite'];
        $earlyacc = $row['accessnotes'];
        $parking = $row['parkingnotes'];
        $lift = $row['lift'];
        $ground = $row['ground'];
        $steps = $row['steps'];
        $twoman = $row['twoman'];

        $menudet .= "</select></td>
    </tr>";


        $menudet .= "<tr>
    <th> Previous Date </th>
    <td><input type='text' id='reqprev_ed' size='150' class='group1' value='" . $row['prev'] . "' disabled></td>
  </tr>
    <tr>
    <th> Vehicle </th>
    <td><input type='text' id='dtype_ed' class='group1' value='" . $row['typ'] . "' disabled></td>
  </tr>
  <tr>
  <th> Approved  </th>
  <td><input type='text' size='40' id='app_ed' class='group1' value='" . $row['approved'] . "' disabled></td>
</tr>
<tr>
<th> Process  </th>
<td><input type='text' size='40' id='pro_ed' class='group1' value='" . $row['process'] . "' disabled></td>
</tr>
<tr>
<th> BIOS Password  </th>
<td><input type='text' id='bio_ed' class='group1' value='" . $row['BIOS_Password'] . "' disabled></td>
</tr>
<tr>
<th>Collection Instruction</th>
<td><input type='text' size='150' id='collinstr_ed' class='group1' value='" . $row['CollectionInstruction'] . "' disabled></td>
</tr>

<tr>
<th> Availability  </th>
<td><input type='text' size='150' id='coldate_ed' class='group1' value='" . $row['CollectionDate'] . "' disabled></td>
</tr>
<tr> 
<th>  Help On-Site</th>
<td><input type='text' id='help_on' class='group1' value='" . $help . "' disabled></td>
</tr>
<tr> 
<th>  Two-Man</th>
<td><input type='text' id='twoman' class='group1' value='" . $twoman . "' disabled></td>
</tr>
<tr> 
<th>  Steps </th>
<td><input type='text' id='steps' class='group1' value='" . $steps . "' disabled></td>
</tr>
<tr> 
<th>  Ground Floor </th>
<td><input type='text' id='ground' class='group1' value='" . $ground . "' disabled></td>
</tr>
<tr> 
<th>  Lift  </th>
<td><input type='text' id='lift' class='group1' value='" . $lift . "' disabled></td>
</tr>
<tr>
<th> Avoid Times  </th>
<td><input type='text' size='150' id='avoid_ed' class='group1' value='" . $row['avoidtimes'] . "' disabled></td>
</tr>

<tr>
<th> Early Access Notes</th>
<td><input type='text' id='eaccessnote' class='group2' value='" . $earlyacc . "' disabled></td>
</tr>
<tr>
<th> Parking Notes</th>
<td><input type='text' id='parkingnotes' class='group2' value='" . $parking . "' disabled></td>
</tr>

<tr >
<th>Proposed Date</th>
<td id='prodate'>" . $timepro . "</td>
</tr></table>
 ";


        echo $menudet;


    }


    $stmtnote = $sdb->prepare($sqlnotes);
    if (!$stmtnote) {
        echo "\nPDO::errorInfo():\n";
        print_r($sdb->errorInfo());
        die();
    }
    $stmtnote->execute();

    $count = $stmtnote->rowCount();

    //echo $count;


    $datanote = $stmtnote->fetchAll(PDO::FETCH_ASSOC);
    foreach ($datanote as $row) {


        echo " <tr hidden>
        <th> notes</th>
        <td><textarea>  " . $row['notes'] . " </textarea></td>
        </tr>
        <tr hidden>
                <th> GDPR Contract </th>
                <td> " . $row['gdpr'] . " </td>
        </tr>
        <tr hidden>

        <th> Company AMR status </th>
        <td> " . $row['amrc'] . " </td>
</tr>
        
        </table>";
    }


    ?>


    <script>
        $(document).ready(function (e) {


            //     (function($) {
            //   $.format = DateFormat.format;
            // }(jQuery));


//             $("#form1").submit(function(e) {
//    e.preventDefault();


// });


            $('#newlinedata').hide();


            $("#addlineBTN").click(function () {

                $('#newlinedata').show();
                $('#addlineBTN').hide();

            });


            var ordnum = $("#ord_name").val();
            var manord = $("#manord").val();
            var stopadd = 0;

            var sesh = '<?php $man ?>';

            sesh = manord;


            $("#newlinedata #part-select").on('change', function () {

                $('#ntb tbody tr').each(function () {

                    part = $(this).find(".partis").text();


                    if ($('#part-select option:selected').text() == part) {
                        stopadd = 1;
                        console.log(stopadd);
                        $("#part-select").val(0);

                        alert('cant add Duplicates');

                    }


                });


                // alert('hello');

                var partselected = $('#part-select option:selected').val();

                if (partselected == 19 || partselected == 21 || partselected == 23 || partselected == 65 || partselected == 67 || partselected == 69) {

                    $('#othername').show();


                }
            });


            $("#subline").click(function () {

                if (stopadd == 1) {


                    alert('stop dupe');


                }

                $('#newlinedata').hide();
                $('#addlineBTN').show();

                var workingqtynew = $('#newwork').val();
                // var notworkingqtynew = $('#newnotwork').val();

                var reqidn =  <?php echo $_GET['rowid']; ?> ;
                var partselected = $('#part-select option:selected').val();
                var part = '';


                var othername = '';

                if ($('#othername').length > 0) {

                    othername = $('#othername').val();


                }

                if ($('#newwork').val() == '') {

                    workingqtynew = 0;
                }


                if ($('#newnotwork').val() == '') {

                    notworkingqtynew = 0;
                }


                //  alert(workingqtynew);

                $.ajax({
                    url: "/RS/Addnewline/", // Url to which the request is send
                    type: "POST",             // Type of request to be send, called as method
                    data: {
                        partselected: partselected,
                        workingqtynew: workingqtynew,
                        reqidn: reqidn,
                        othername: othername
                    }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    // To send DOMDocument or non processed data file it is set to false

                    success: function (data) {   // A function to be called if request succeeds

                        // alert('success');
                        // alert(data);
                        location.reload(true);
                        // $("#image_preview").html(data);
                    }
                });

            });


            $("#ntb").on('click', '.remove', function () {
                var working = $(this).find(".working").val();
                // var working = $(this).find(".notworkin").val();

                var trIndex = $(this).closest("tr").index();
                //   alert(newrow);
                if (trIndex > -1) {
                    var partid = $(this).closest("tr").find(".idp").val();
                    var workingqty = $(this).closest("tr").find(".workin").val();
                    // var notworkingqty =  $(this).closest("tr").find(".notworkin ").val();
                    var requestid = <?php echo $_GET['rowid']; ?>;
                    // $(this).closest("tr").remove();


                    $.ajax({
                        url: "/RS/Delline/", // Url to which the request is send
                        type: "POST",             // Type of request to be send, called as method
                        data: {
                            partid: partid,
                            workingqty: workingqty,
                            // notworkingqty : notworkingqty,
                            requestid: requestid
                        }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                        // To send DOMDocument or non processed data file it is set to false

                        success: function (data) {   // A function to be called if request succeeds

                            // alert('success');
                            // alert(data);
                            location.reload(true);
                            // $("#image_preview").html(data);
                        }
                    });


                }
            });


            $('#loading').hide();
            $("#uploadimage").on('submit', (function (e) {
                e.preventDefault();

                $("#message").empty();
                $('#loading').show();

                $.ajax({
                    url: "/RECbooking/php_upload.php", // Url to which the request is send
                    type: "POST",             // Type of request to be send, called as method
                    data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    contentType: false,       // The content type used when sending data to the server.
                    cache: false,             // To unable request pages to be cached
                    processData: false,        // To send DOMDocument or non processed data file it is set to false

                    success: function (data) {   // A function to be called if request succeeds
                        $('#loading').hide();
                        $("message").html(data);
                        $("#image_preview").html(data);
                    }
                });
            }));

            // Function to preview image after validation
            $(function () {
                $("file").change(function () {
                    $("message").empty(); // To remove the previous error message
                    var file = this.files[0];
                    var imagefile = file.type;
                    var match = ["image/jpeg", "image/png", "image/jpg"];
                    if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2]))) {
                        $('previewing').attr('src', 'noimage.png');
                        $("message").html("<p id='error'>Please Select A valid Image File</p>" + "<h4>Note</h4>" + "<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
                        return false;

                    } else {
                        var reader = new FileReader();
                        reader.onload = imageIsLoaded;
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            });

            function imageIsLoaded(e) {
                $("file").css("color", "green");
                $('image_preview').css("display", "block");
                $('previewing').attr('src', e.target.result);
                $('previewing').attr('width', '500px');
                $('previewing').attr('height', '500px');
            };
        });

        <?php
        $sqly = 'SELECT filename AS fi FROM customerPics WHERE Request_ID =' . $_GET['rowid'];
        $stmt6 = $sdb->prepare($sqly);
        if ($stmt6 === false) {
            die("SQL query failed: " . $sqly);
        }
        $stmt6->execute();
        $data = $stmt6->fetchAll(PDO::FETCH_ASSOC);

        $images = array();
        foreach ($data as $row) {
            $images[] = $row['fi'];
        }


        ?>

        var index = 1;

        function changeing() {

            var imagearray = ["<?php echo implode('","', $images);?>"];
            var myimage = document.getElementById('kool');
            // alert(imagearray[index]);
            myimage.setAttribute("src", imagearray[index]);
            index++;
            console.log(index);
            console.log(imagearray);
            if (index > imagearray.length - 1) {
                {
                    index = 0;
                }
            }
            console.log(index);
            console.log(index.length);
            console.log(imagearray);
            console.log(imagearray.length);
        }


    </script>


    <h1>More details</h1>


    <?php
    $DEADtime = '';
    /*----------------------------------------------------------------------------------------------------
Change Log
Date			tag						Ticket				By						Description
------------------------------------------------------------------------------------------------------
06/09/18	Created											Alex.Smith		Created
07/09/18	tidy                        Neil.Baker    Tidy HTML and remove incorrect tags, remove duplicate includes for DB, move DB include to top of file
------------------------------------------------------------------------------------------------------*/

    $sqlaps = "

set language BRITISH;
select
isnull(A, 'not set') AS a,
ISNULL(P, 'not set') AS p,
isnull(prev_date, 'Empty') as booknote,
LorryFlag as lflag,
lorrytype as lotype,
ISNULL([APS_notes], 'not set') AS notes,
isnull([job_notes], 'not set') as jn,
isnull([Access_notes], 'not set') as accn,
Lorry as lo,
isnull([ConfirmEmailSent], 'No') as cone,
isnull([SurveySent], 'No') as sysent,
[SurveyComplete] as scom,
isnull([SentBy], 'n/a') as sby,
[ContractStatus] as cstat,
isnull([ContractNotes], 'empty') as cnote,
isnull([DriverName], 'not set') as dnam,
isnull([VehicleReg], 'not set') as vreg,
booking_status as stat,
[is_canceled] as can, 
datediff(day, surveysnet_date, survey_deadline) as deaddays,
survey_deadline as deadline,
isnull([email_sent], 'No') as email_sent,
surveysnet_date as sursentdate,
emailsentdate,
owner as own

from Booked_Collections
WHERE
  RequestID LIKE " . $_GET['rowid'];

    $stmtaps = $sdb->prepare($sqlaps);
    if ($stmtaps === false) {
        die("SQL query failed: " . $sqlaps);
    }
    $stmtaps->execute();
    $data = $stmtaps->fetchAll(PDO::FETCH_ASSOC);

    //make the rest varibles in detialData


    $test = 0;

    // while($arow = sqlsrv_fetch_array($stmtaps)){
    foreach ($data as $arow) {


        $lorry = $arow['lflag'];


        $_SESSION['lorryflag'] = $lorry;


        if ($lorry == 1) {
            $loc = "checked";
            $l = "yes";
        } else {
            $loc = " ";
            $l = "no";
        }
        //echo $l;

        $can = 'Cancelled';
        $pend = 'Pending';
        $failed = 'failed';


        echo "<label HIDDEN>Select Booking Status:</label>
<select HIDDEN id='bookingstat'>
  <option value='Collected'>Collected</option>
  <option value='Pending'>Pending</option>
  <option value='Failed'>Failed</option>
  <option value='Booked'>Booked</option>
  <option value='Cancelled'>Cancelled</option>
  <option value='On Hold'>On Hold</option>
</select><br>
<input HIDDEN type='text' id='boostat' class='group2' value='" . $arow['stat'] . "' disabled> <br><br>";


        echo "
<table id='bookedinfo' cellpadding='1' cellspacing='1' class='table'>

<tr hidden>

<th>PREV_DATE:</th>
<td><textarea id='boknote'>" . $arow['booknote'] . "</textarea><br>
<button type='button' id='btnstats' class='btn btn-primary'>change </button></td>
</tr>

<tr hidden>
<td><label>Driver Name:</label> <input type='text' id='drname' class='group2' value='" . $arow['dnam'] . "' disabled> <br><br></td>
<td><label>Vehicle Reg:</label> <input type='text' id='vregs' class='group2' value='" . $arow['vreg'] . "' disabled> <br><br></td>
</tr>
<tr hidden>
<th>Contract Status:</th>
 <td><input type='text' id='constat' class='group2' value='" . $arow['cstat'] . "' disabled></td>
</tr>
 <tr hidden>
<th>Contract Notes:</th> 
<td><textarea disabled id='conote' class='group2'>" . $arow['cnote'] . " </textarea> <br><br><hr></td>
</tr>

<tr>
<td><button type='button' id='apsedit' class='btn btn-primary'>edit </button><span>  </span>
<button type='button' id='apsupdate' class='btn btn-success'>Done</button></td>
</tr>



<tr>
<th> email sent?:</th>
<td>  <input type='text' id='conf' class='group2' value='" . $arow['email_sent'] . "' disabled><br/></td>
</tr>
<tr> 
<th> Email info</th>
<td hidden><input type='checkbox' id='lorry' " . $loc . "> lorry needed?</td>
<td id='lorryty' ><input type='text' id='lorrytttt' value='" . $arow['lotype'] . "' disabled></td>
<td><th> Email's </th><td>
<select id='lorrytype'>
<option selected='selected' value='def' >Please select type</option>
  <option value='sbvan'>Standard Booking Conf Email – Van</option>
  <option value='sb2van'>Standard Booking Conf Email – 2 Van</option>
  <option value='sblorry7'>Standard Booking Conf Email – 7.5T Lorry</option>
  <option value='sblorry14'>Standard Booking Conf Email – 14T Lorry</option>
  <option HIDDEN value='GB'>Guardian Booking</option>
  <option hidden value='b7van'>Site Survey – Van</option>
  <option hidden value='b72van'>Site Survey – 2 Van</option>
  <option hidden value='b77lorry'>Site Survey – 7.5T Lorry</option>
  <option hidden value='b714lorry'>Site Survey – 14T Lorry</option>
  <option HIDDEN value='b7GB'>8.	Booked within 7 Days - Guardian</option>
  <option value='CHSb7van'> Chase - Van </option>
  <option value='CHS75T'> Chase - 7.5T </option>
  <option value='CHS14T'> Chase - 14T </option>
</select>
</td>
<td><button type='button' id='emailcon' class='btn btn-primary'>Send Email</button></td>
</tr>





</table>

";
//// taken out from table above
// <tr>
// <p hidden id='daycount'> ".$arow['deaddays']."</p>
// </tr>
//$deadlinetime = $_SESSION['newdeadline'];

///////////////

        if (isset($arow['email_sent'])) {

            $deadtime = date("d-m-y H:i:s", strtotime($arow['deadline']));
        } else {

            $deadtime = '';

        }


        if (isset($arow['sursentdate'])) {

            $survtime = date("d-m-y H:i:s", strtotime($arow['sursentdate']));
        } else {

            $survtime = '';

        }

        if (isset($arow['emailsentdate'])) {

            $emailsentdate = date("d-m-y H:i:s", strtotime($arow['emailsentdate']));
        } else {

            $emailsentdate = '';

        }


        echo "  <br>
  <table>
  <tr>
  <th>Conf Email Sent?:</th>
   <td><input type='text' id='emalconf' class='group2'  value='" . $arow['cone'] . "' disabled> <br><br></td>
  </tr>
  <tr>
 
  <td hidden><input type='text' id='SurveySent' class='group2' value='" . $arow['sysent'] . "' disabled><br> <br></td>
  </tr>
  <tr>
  <th>Survey Complete?: </th>
  <td><input type='text' id='SurveyComp' class='group2' value='" . $arow['scom'] . "' disabled><br><br></td>
  </tr>
  <tr>
  <th hidden>Survey Sent Date: </th>
  <td hidden><input type='text' id='Surveysdate' class='group2' value='" . $survtime . "' disabled><br><br></td>
  </tr>
  <tr>
  <th>Survey Deadline</th>
  <td> <input type='text' id='survdead2' class='group2' value='" . $deadtime . "' disabled><span>   </span><input type='checkbox' id='manual'/><span>   </span><label>override </label>  e.g ( DD-MM-YY h:m:s ) </td>
  </tr>
  <tr>
  <th>Email Sent Date</th>
  <td> <input type='text' id='sentdate' class='group2' value='" . $emailsentdate . "' disabled></td>
  </tr>
  <tr>
  <th>Sent By:</th>
  <td> <input type='text' id='sentby' class='group2' value='" . $arow['sby'] . "' disabled><br><br> </td>
  </tr>


  </table>";

//   <th>  Help On-Site</th>
// <td><input type='text' id='help_on' class='group2' value='".$help."' disabled></td>
// </tr>
// <tr> 
// <th>  Two-Man</th>
// <td><input type='text' id='twoman' class='group2' value='". $twoman."' disabled></td>
// </tr>
// <tr> 
// <th>  Steps </th>
// <td><input type='text' id='steps' class='group2' value='". $steps."' disabled></td>
// </tr>
// <tr> 
// <th>  Ground Floor </th>
// <td><input type='text' id='ground' class='group2' value='". $ground."' disabled></td>
// </tr>
// <tr> 
// <th>  Lift  </th>
// <td><input type='text' id='lift' class='group2' value='". $lift."' disabled></td>
// </tr>


        echo "
  <br>
  <table border='1' cellspacing='0' class='asp' hidden>
  <tr>
    <th> A </th>
    <th> P </th>
    <th> APSnotes </th>
  </tr>
  <tr>
    <Td> <input type='text' id='a_up' class='group2' value='" . $arow['a'] . "' disabled> </td>
    <Td> <input type='text' id='p_up' class='group2' value='" . $arow['p'] . "' disabled> </td>
    <Td> <input type='text' id='notes_up' class='group2' value='" . $arow['notes'] . "' disabled> </td>

  </tr>
  </table>";


        echo "<br>
  <table border='1' cellspacing='0' class='aspnotes' hidden>
  <tr>
    <th>Job Notes</th>
    <th> Access Notes </th>
  </tr>
  <tr>
    <Td> <input type='text' id='jn_up' class='group2' value='" . $arow['jn'] . "' disabled> </td>
    <Td> <input type='text' id='acc_up' class='group2' value='" . $arow['accn'] . "' disabled> </td>
 

  </tr>
  </table>
 ";


    }


    ?>



    <?php


    //require("amrsqlquery.php");
    ?>

    <?php


    $datechange = 0;

    // $stmttotal = $sdb->prepare($totalswu);
    // if (!$stmttotal) {
    //   echo "\nPDO::errorInfo():\n";
    //   print_r($sdb->errorInfo());
    //   die();
    // }
    // $stmttotal->execute();

    // $datar = $stmttotal->fetchAll(PDO::FETCH_ASSOC);

    // ///// end of image upload///


    // ////// start of bottom feilds /////

    //       echo "<table id='tbdtl' cellpadding='1'cellspacing ='1'>";
    //         // while($row = sqlsrv_fetch_array($stmt)){
    //         foreach($datar as $roww) {

    //         }

    //         $stmtrest = $sdb->prepare($sql);
    //         if( $stmtrest === false) {
    //             die("SQL query failed: ".$sql);
    //         }
    //         $stmtrest->execute();
    //         $datarest = $stmtrest->fetchAll(PDO::FETCH_ASSOC);

    //         foreach($datarest as $row) {


    //         echo"


    //           <tr>
    //             <th> Request Done Date  </th>
    //             <td>". $row['RequestDoneDate'] ."</p></td>
    //           </tr>
    //           <tr>
    //             <th> Update note  </th>
    //             <td><input type='text' id='updatenote_ed' class='group1' value='". $row['Updatenote'] ."' disabled></td>
    //           </tr>
    //           <tr HIDDEN>
    //           <th> Request Stat note  </th>
    //           <td><input type='text' id='reqstatenote_ed' class='group1' value='". $row['statnote'] ."' disabled></td>
    //         </tr>
    //           <tr>
    //             <th>Request Updated By</th>
    //             <td><input type='text' id='reqby_ed' class='group1' value='". $row['RequestUpdatedBy'] ."' disabled></td>
    //           </tr>

    //           <br>

    //           ";
    //         }

    //         $booksql = "SELECT RIGHT(ORD, 7) AS ordsa FROM Booked_Collections WHERE RequestID LIKE '".$justID."'";
    //         $stmtbook = $sdb->prepare($booksql);

    //         if( $stmtbook === false) {
    //             die("SQL query failed: ".$booksql);
    //         }

    //         $stmtbook->execute();
    //         $r = $stmtbook->fetch(PDO::FETCH_ASSOC);

    //         $_SESSION['ordnum'] =   $dataord['ord'];
    //         $trimo = substr($dataord['ord'], 4);


    //         $_SESSION['ord'] = $trimo;


    //         // include_once('dbgreen.php'); // Now part of db.php

    //         $booksql2 = "SELECT
    //           [ITADAccountManager] AS ram,
    //           [Shared With] AS sw,
    //           Dept AS dep,
    //           [AccountManager] AS am
    //         FROM
    //           Collections_Log
    //         WHERE
    //           ordernum LIKE '". $dataord['ord'] ."'

    //           SELECT RIGHT(ORD, 7) AS orr FROM Request JOIN Collections_Log ON RIGHT(ORD, 7) = OrderNum
    //         ";

    //         $stmtbook2 = $sdb->prepare($booksql2);

    //         if( $stmtbook2 === false) {
    //             die("SQL query failed: ".$booksql2);
    //         }

    //         $stmtbook2->execute();
    //         $ec = $stmtbook2->fetch(PDO::FETCH_ASSOC);

    //         // $stmtbook2 = sqlsrv_query( $sdb, $booksql2);
    //         // if( $stmtbook2 === false) {
    //         //     die( print_r( sqlsrv_errors(), true) );
    //         // }
    //         // $ec = sqlsrv_fetch_array($stmtbook2);

    //         $sqlgreen = "SELECT
    //         ConsignmentNumber AS consign,
    //         d.WasteTransferNumber as wtn,
    //         SICCode,
    //     c.SiteCode as SiteCode
    //       FROM
    //         [dbo].Delivery AS d
    //       JOIN
    //         SalesOrders AS s ON d.SalesOrderID = s.SalesOrderID
    //    left join company as c with(nolock) on
    //     c.CompanyID = s.CompanyID
    //       WHERE
    //         SalesOrderNumber LIKE  '%".$dataord['ord']."'";

    //         $stmtgreen = $gdb->prepare($sqlgreen);

    //         $stmtgreen->execute();

    //         if( $stmtgreen === false) {
    //             die("SQL query failed: ".$sqlgreen);
    //         }

    //         if(isset($dataord['ord'])){
    //           echo "<p>allowed</p>";
    //         }else{
    //           echo "<p>Needs to be done first. make changes in achive.</p>";
    //         }

    //         $rg = $stmtgreen->fetch(PDO::FETCH_ASSOC);

    //         echo "
    //         <tr hidden>
    //           <th>Consignment Number</th>
    //           <td><input type='text' id='consi' class='group1' value='".$rg['SiteCode']."/".$rg['SICCode']."' disabled></td>
    //         </tr>
    //         <tr hidden>
    //            <th> ITADAccountManager </th>
    //            <td><input type='text' id='conram' class='group1' value='".$ec['ram']."' disabled></td>
    //         </tr>
    //         <tr hidden>
    //           <th>Shared With</th>
    //           <td><input type='text' id='consw' class='group1' value='".$ec['sw']."' disabled></td>
    //         </tr>
    //         <tr hidden>
    //           <th> Dept </th>
    //           <td><input type='text' id='condep' class='group1' value='".$ec['dep']."' disabled></td>
    //         </tr>
    //         <tr hidden>
    //           <th> AccountManager </th>
    //           <td><input type='text' id='conam' class='group1' value='".$ec['am']."' disabled></td>
    //         </tr>
    //       </table>
    //       <br>

    //       ";


    $_SESSION['id'] = $_GET['rowid'];

    ?>


    <script type="text/javascript">

        $(document).ready(function (e) {


            var manual = 0;


            var inputdateedit = '';

            $('#survdead2').on('input', function () {


                inputdateedit = $(this).val();


                console.log(inputdateedit);


            });


            var gdprselect = $('#gdpr-select option:selected').val();

            if (gdprselect == 0) {

                $('#bookingdate').prop('disabled', true);
                $('#subdateform').prop('disabled', true);


            } else {

                $('#bookingdate').prop('disabled', false);
                $('#subdateform').prop('disabled', false);
            }


            $('#ntb tbody tr').each(function () {

                var part = $(this).find(".partis").text();

//alert(part);


                if (part == 'CRT' || part == 'Projector' || part == 'DesktopPrinter' || part == 'Standalone_Printer' || part == 'Other1' || part == 'Other2' || part == 'Other3' || part == 'Other4' || part == 'Other5' || part == 'Other6' || part == 'UPS_Small' ||
                    part == 'SmartBoard') {

                    $(this).find("#w").hide();
                    $(this).find("#v").hide();
                } else if (part == 'TFT' || part == 'Switches' || part == 'Harddrive' || part == 'Server' || part == 'Apple Phone' || part == 'Smart Phone' || part == 'Apple Tablet' || part == 'Tablet' || part == 'TV' || part == 'Mobile_Phone(NonSmart)') {

                    $(this).find("#w").hide();
                    $(this).find("#v").show();
                } else {

                    $(this).find("#w").show();
                    $(this).find("#v").show();


                }

            });


            $("#btnstats").click(function () {

                // alert("hello");
                var bokstat = $("#bookingstat option:selected").val();
                var prevnote = $("#boknote").val();
                var req = $("#reqid").val();
                // alert(bokstat);
                // alert(req);
                // alert(prevnote);


                $.ajax({
                    url: "/RS/bookstat/", // Url to which the request is send
                    type: "POST",             // Type of request to be send, called as method
                    data: {
                        bokstat: bokstat,
                        req: req,
                        prevnote: prevnote
                    }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    // To send DOMDocument or non processed data file it is set to false

                    success: function (data) {   // A function to be called if request succeeds

                        //  alert('success');
                        // alert(data);
                        location.reload(true);
                        // $("#image_preview").html(data);
                    }
                });


            });


            /*if($("#lorrytttt").val() != ""){

                 $("#lorrytype").hide();
                 $('#emailcon').prop('disabled', true);

            }else{

              $("#lorrytype").show();
                 $('#emailcon').prop('disabled', false);

            }*/

            $("#amrupdate").click(function () {

                var ord = <?php  if (isset($_SESSION['ord'])) {
                        echo $_SESSION['ord'];
                    }
                    echo '0';
                    ?>;
                var amrs = $('#upamr').val();
                var amrcomp = $('#amr').val();
                var isrebate = $('#isre').val();


                $.ajax({
                    url: "/RS/amrupdate/", // Url to which the request is send
                    type: "POST",             // Type of request to be send, called as method
                    data: {
                        ord: ord,
                        amrs: amrs,
                        amrcomp: amrcomp,
                        isrebate: isrebate
                    }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    // To send DOMDocument or non processed data file it is set to false

                    success: function (data) {   // A function to be called if request succeeds

                        // alert('success');
                        // alert(data);
                        // $("#image_preview").html(data);
                    }
                });
            });


            $('#manual').click(function () {

                $('#survdead2').prop('disabled', false);


                if ($(this).is(":checked")) {
                    // alert('hit');
                    manual = 1;
                } else {
                    manual = 0;
                    $('#survdead2').prop('disabled', true);
                }


            });


            $("#emailcon").click(function () {


                var reqid = $("#reqid").val();
                var emailaddress = $("#email_ed").val();
                var loorytype = $("#bookedinfo #lorrytype option:selected").val();
                var date = $('#prodate').html();
                var owner = $("#reqown_ed option:selected").text();
                var deadlinetome = '<?php $cenvertedTime = date('Y-m-d H:i:s', strtotime('-4 day -12 hour', strtotime(isset($row['CollectionDate']) ? $row['CollectionDate'] : ' '))); echo $cenvertedTime?>';

                var manualtime = $('#survdead2').val();


                if (inputdateedit !== '') {

                    deadlinetome = inputdateedit;

                }
                deadlinedateFomat = new Date(deadlinetome);


                var d = new Date(date);

                if (owner === '') {

                    owner = 'alex.smith';
                }


                //alert(manualtime);

                //var date = $('#tbdtl #prodate').val($.datepicker.formatDate('dd M yy', new Date()));


                // alert(manual);

                $.ajax({
                    url: "/RS/emailconf", // Url to which the request is send
                    type: "POST",             // Type of request to be send, called as method
                    data: {

                        loorytype: loorytype,
                        emailaddress: emailaddress,
                        reqid: reqid,
                        date: date,
                        owner: owner,
                        deadlinedateFomat: deadlinedateFomat,
                        manualtime: manualtime,
                        manual: manual
                    }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    // To send DOMDocument or non processed data file it is set to false

                    success: function (data) {   // A function to be called if request succeeds

                        //alert('success');
                        //alert(data);
                        location.reload(true);

                    }
                });


                //  if($('#lorry').prop("checked") == true){


                //  });

            });


        });


    </script>


    <!-------------------------------Upload images----------------------------------------------->


    <?php

    if (isset($_GET['btn'])) {
        $test = strtotime(str_replace('/', '-', $_GET['pd']));
        $timin = date("Y-m-d", $test);
    }
    if (!isset($_GET['pd'])) {

    } else {

        //echo "Now in Booking";

        $test = strtotime(str_replace('/', '-', $_GET['pd']));

        $timin = date("Y-m-d", $test);

        $sqlUpdate = "UPDATE
              request
            SET
            collection_date ='" . $timin . "',
            been_collected = 1
            WHERE
              Request_ID LIKE " . $_GET["rowid"];

        $datechange = 0;

        $stmtp = $sdb->prepare($sqlUpdate);
        $stmtp->execute();


        $CHECKIFTHERESQL = "
              set nocount on
              declare @test varchar(max)
              declare @name varchar(50)
              set @test = '" . $_GET["rowid"] . "'
              set @name = '" . $row['name'] . "'
              select  isnull((case when Customer like @name then 'There all ready' else 'new record' end),'new rec') as Checker from Booked_Collections where RequestID like @test
              group by Customer

              ";


        $st = $sdb->prepare($CHECKIFTHERESQL);
        $st->execute();
        $r = $st->fetch(PDO::FETCH_ASSOC);


        //var_dump($CHECKIFTHERESQL);

        $date = date_create($time);
        date_add($date, date_interval_create_from_date_string('4 days'));
        $datediff = date_format($date, 'Y/m/d H:i:s');

        $cenvertedTime = date('Y-m-d H:i:s', strtotime('+16 hour', strtotime($datediff)));

        if (!$r['Checker'] == 'There all ready') {

            $userint = get_current_user();

            $result = strtoupper(substr($userint, 0, 2));


            $_SESSION['by'] = $result;


            $ord = $dataord['ord'];


            if ($ord == '') {

                if (isset($_SESSION['ordman'])) {

                    $ord = $_SESSION['ordman'];
                }
            }


            $bookrecord = "

            insert into Booked_Collections( RequestID, Customer, Email, Contact, Phone,
             Town, PostCode, Prefix, Area1, EstWeight,[Est Total Units], SubmitDate, BookedCollectDate, [is_canceled], booking_status, [sentby], owner, [deliveryType], prev_date, ORD, [Job_notes], [Access_Notes], [APS_notes], A, P)
            
            values('" . $_GET["rowid"] . "', '" . $row['name'] . "', '" . $row['email'] . "', '" . $row['contact'] . "', '" . $row['cphone'] . "', '" . $row['twn'] . "', '" . $row['postcode'] . "', '" . $row['prefix'] . "', '" . $row['twn'] . "',
            " . $roww['totalweightint'] . ", " . $roww['totalunits'] . ", '" . $row['reqadd'] . "', '" . $timin . "', 0, 'Date Set', '" . $result . "', '" . $row['ownerreq'] . "', '" . $row['typ'] . "', '" . $row['prev'] . "', '" . $ord . "', '" . $row['CollectionInstruction'] . "', '" . $row['CollectionDate'] . "', '" . $row['approved'] . "  " . $row['process'] . "', '" . $row['approved'] . "', '" . $row['process'] . "')
            
            set nocount on
            update request
            set confirmed = '0',
            laststatus = 'booked'
            where Request_ID ='" . $_GET["rowid"] . "'";


            $stmt = $sdb->prepare($bookrecord);
            $stmt->execute();


            echo "<meta http-equiv='refresh' content='0'>";

        } else {

            echo "<p>in booked collections log</p>";
            $sqlbookup = "     
              update Booked_Collections
              set BookedCollectDate = '" . $timin . "'
              where RequestID ='" . $_GET["rowid"] . "'
              
              ";
            $bookupstmt = $sdb->prepare($sqlbookup);
            $bookupstmt->execute();
        }
        if ($stmtp === false) {
            // failed
        } else {
            $datechange = 1;
        }
        if ($datechange === 1) {
            echo "
              <div id='myModal' class='modal fade' role='dialog'>
                <div class='modal-dialog'>
                  <div class='modal-content'>
                    <div class='modal-header'>
                      <button type='button' class='close' data-dismiss='modal'>&times;</button>
                      <h4 class='modal-title'>Modal Header</h4>
                    </div>
                    <div class='modal-body'>
                      <p>updated.</p>
                    </div>
                    <div class='modal-footer'>
                      <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                    </div>
                  </div>
                </div>
              </div>
              ";
        }

        $sqladdtoportal = '';

    }

    $stmttotal = $sdb->prepare($totalswu);
    if (!$stmttotal) {
        echo "Connection Issue: please contact MIS.";
        print_r($sdb->errorInfo());
        die();
    }
    $stmttotal->execute();

    $datar = $stmttotal->fetchAll(PDO::FETCH_ASSOC);

    ///// end of image upload///


    ////// start of bottom feilds /////

    echo "<table id='tbdtl' cellpadding='1' cellspacing ='1'>";
    // while($row = sqlsrv_fetch_array($stmt)){
    foreach ($datar as $roww) {

    }

    $stmtrest = $sdb->prepare($sql);
    if ($stmtrest === false) {
        die("SQL query failed: " . $sql);
    }
    $stmtrest->execute();
    $datarest = $stmtrest->fetchAll(PDO::FETCH_ASSOC);

    foreach ($datarest as $row) {


        echo "
  
    
          <tr>
            <th> Request Done Date  </th>
            <td>" . $row['RequestDoneDate'] . "</td>
          </tr>
          <tr>
            <th> Update note  </th>
            <td><input type='text' id='updatenote_ed' class='group1' value='" . $row['Updatenote'] . "' disabled></td>
          </tr>
          <tr HIDDEN>
          <th> Request Stat note  </th>
          <td><input type='text' id='reqstatenote_ed' class='group1' value='" . $row['statnote'] . "' disabled></td>
        </tr>
          <tr>
            <th>Request Updated By</th>
            <td><input type='text' id='reqby_ed' class='group1' value='" . $row['RequestUpdatedBy'] . "' disabled></td>
          </tr>
          </table><br>

       
          
          ";
    }

    ?>

    <div class="stuff">

        <form id="uploadimage" name="uploadimage" method="post" enctype="multipart/form-data">


            <?php
            $sqly = 'SELECT filename AS fi FROM customerPics WHERE Request_ID =' . $_GET['rowid'];
            $stmt6 = $sdb->prepare($sqly);
            if ($stmt6 === false) {
                die("SQL query failed: " . $sqly);
            }
            $stmt6->execute();
            $data = $stmt6->fetch(PDO::FETCH_ASSOC);
            $path = isset($data['fi']) ? $data['fi'] : ' ';

            if ($data) {
                echo "<div id='image_preview'><img id='kool' src='" . $path . "' alt='' width='250' height='200'></div>";
            }
            ?>

            <hr id="line">

            <div id="selectImage">   <!--- had name changed to ID ---->
                <label>Select Your Image</label><br/>
                <input type="file" name="file" id="file" required/>
                <input type="submit" value="Upload" class="submit"/>
            </div>
            <p><strong>types: jpeg, jpg, png </strong></p>
            <p><strong>size: &lt;= 9mb </strong></p>
        </form>
    </div>

    <h4 id='loading'>loading..</h4>

    <div id="message"></div>
</div>


<button onclick="changeing()">next</button>

<div class="col-xl-4">


</div>
<div>
    <button type='button' id='buttonID' class='buttonIDed btn-primary'>edit</button>
    <button type='button' id='buttondoneID' class='btnbuttondoneID btn btn-success'>Done</button>
</div>
<br/>

<div class="col-xl-4">
    <div class="form-group">
        <form method="get" action="/RS/detialdoc" id="form1">
            <input hidden type="text" name="rowid" id="reqid" value="<?php echo $_GET['rowid']; ?>">
            <div class="col-sm-10">
                <label for="bookingdate"><b>Proposed Date:</b></label>
                <input type="date" class="form-control" name="pd" value="" id='bookingdate'>

                <button type="submit" form="form1" value="Submit" name="btn" id='subdateform' data-toggle="modal"
                        data-target="#myModal">Submit
                </button>
            </div>
        </form>
    </div>
</div>
<div class="col-xl-4">
    <div class="form-group">


        <!-------Ordernumber Checker ------->
        <form hidden method='get' target="_blank" action="/RS/Ordcheck" id="Checkform">
            <label> Ordernumber Checker </label>
            <input type="text" placeholder="e.g 6019328" id="ordernumval" name="ordernumval" value="">
            <button type="submit" value="Submit" id="ordercheck">Check</button>
        </form>
    </div>
</div>
<br>
<br>
<br>
</div>


<?PHP

$booksql = "SELECT isnull(RIGHT(ORD, 7), 0000) AS ordsa FROM Booked_Collections WHERE RequestID LIKE '" . $justID . "'";
$stmtbook = $sdb->prepare($booksql);

if ($stmtbook === false) {
    die("SQL query failed: " . $booksql);
}

$stmtbook->execute();
$r = $stmtbook->fetch(PDO::FETCH_ASSOC);

$_SESSION['ordnum'] = $dataord['ord'];
$trimo = substr($dataord['ord'], 4);


$_SESSION['ord'] = $trimo;


// include_once('dbgreen.php'); // Now part of db.php

$booksql2 = "SELECT
          [ITADAccountManager] AS ram,
          [SharedWith] AS sw,
          Dept AS dep,
          [AccountManager] AS am
        FROM
          Collections_Log
        WHERE
          ordernum LIKE '" . $dataord['ord'] . "'

          SELECT RIGHT(ORD, 7) AS orr FROM Request JOIN Collections_Log ON RIGHT(ORD, 7) = OrderNum
        ";

$stmtbook2 = $sdb->prepare($booksql2);

if ($stmtbook2 === false) {
    die("SQL query failed: " . $booksql2);
}

$stmtbook2->execute();
$ec = $stmtbook2->fetch(PDO::FETCH_ASSOC);

// $stmtbook2 = sqlsrv_query( $sdb, $booksql2);
// if( $stmtbook2 === false) {
//     die( print_r( sqlsrv_errors(), true) );
// }
// $ec = sqlsrv_fetch_array($stmtbook2);

$sqlgreen = "SELECT
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
        SalesOrderNumber LIKE  '%" . $dataord['ord'] . "'";

$stmtgreen = $gdb->prepare($sqlgreen);

$stmtgreen->execute();

if ($stmtgreen === false) {
    die("SQL query failed: " . $sqlgreen);
}

if (isset($dataord['ord'])) {
    echo "<label>allowed</label>";
} else {
    echo "<p>Needs to be done first. make changes in achive.</p>";
}

$rg = $stmtgreen->fetch(PDO::FETCH_ASSOC);

if (isset($rg['SiteCode'])) {

    $sitecode = $rg['SiteCode'];
    $siccode = $rg['SICCode'];
} else {
    $sitecode = '';
    $siccode = '';
}

echo "
        <table id='tbdtl' cellpadding='1' cellspacing ='1'>
        <tr hidden>
          <th>Consignment Number</th>
          <td><input type='text' id='consi' class='group1' value='" . $sitecode . "/" . $siccode . "' disabled></td>
        </tr>
        <tr hidden>
           <th> ITADAccountManager </th>
           <td><input type='text' id='conram' class='group1' value='" . $ec['ram'] . "' disabled></td>
        </tr>
        <tr hidden>
          <th>Shared With</th>
          <td><input type='text' id='consw' class='group1' value='" . $ec['sw'] . "' disabled></td>
        </tr>
        <tr hidden>
          <th> Dept </th>
          <td><input type='text' id='condep' class='group1' value='" . $ec['dep'] . "' disabled></td>
        </tr>
        <tr hidden>
          <th> AccountManager </th>
          <td><input type='text' id='conam' class='group1' value='" . $ec['am'] . "' disabled></td>
        </tr>
      </table>
      <br>
   
      ";


?>
<br>
<br>

</div>


</div>
</div>
</div>


