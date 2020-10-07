<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="/assets/js/jquery-3.3.1.js"></script>
    <script src="/assets/js/bootstrap.js"></script>
    <script src="/assets/js/printThis/printThis.js"></script>
    <link href="/assets/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="/assets/css/imgupload.css" rel="stylesheet">
    <script src="/assets/js/popper.js"></script>
    <title>Recycling Goods Receipting</title>

    <script type="text/javascript">
        jQuery(document).ready(function($) {

            var test = 0;
            var sum = 0;
            var val= 0;
            var arr = [];
            var arr2 = [];


            $('#intake tr').each(function() {
                // var cust = $('#custname').html();
                // var req = $('#rcnum').html();
                // var ord = $('#ordernum').html();
                // var drivers = $('#drivername').html();
                // var drivers2 = $('#drivername2').html();
                // var prod = $(this).find('#prod').text();
                // var request = $(this).find('.req').val();
                var bli = $(this).find('.bl').val();
                var beri = $(this).find('.ber').val();
                var wgti = $(this).find('.wgt').val();

                console.log(bli);

                if(bli == ''){
                    $(this).find('.bl').val(0);
                }
                if(beri == ''){
                    $(this).find('.ber').val(0);
                }
                if(wgti == ''){
                    $(this).find('.wgt').val(0);
                }


            });









            // var v= 0;
            $('#emailbtn').on('click', function(){

                //  console.log('hello');
                arr = [];
                arr2 = [];
                var scrap = $('#intake td #scrapwgt').val();
                var wee = $('#intake td #weewgt').val();
                var totalreds = $('#intake td #redtotal').val();
                var totalyells = $('#intake td #yeltotal').val();
                var totalunits = $('#intake td #totunits').val();
                var totalweights = $('#intake td #totalwgt').val();
                var driver = $('#intake td #drivername').val();
                var driver2 = $('#drivername2').val();
                var com1 = $('#com1').val();
                var com2 = $('#com2').val();
                var com3 = $('#com3').val();
                var comments = com1+"# "+com2+"# "+com3;
                var veri = $('#intake td #veri option:selected').text();
                var cust = $('#custname').text();
                var id = <?php echo $rid ? $rid : "";  ?>;

                var ord = $('#ordernum').text();







                $('#intake tr').each(function() {
                    // var cust = $('#custname').html();
                    // var req = $('#rcnum').html();
                    // var ord = $('#ordernum').html();
                    // var drivers = $('#drivername').html();
                    // var drivers2 = $('#drivername2').html();
                    var prod = $(this).find('#prod').text();
                    var request = $(this).find('.req').val();
                    var bli = $(this).find('.bl').val();
                    var beri = $(this).find('.ber').val();
                    var wgti = $(this).find('.wgt').val();



                    // console.log(scrp);

                    // var dis = $(this).find('.dis').val();
                    // console.log(request);



                    if(dis == null){
                        dis = 'none';


                    }else{



                    }
                    if(wgti == null){
                        wgti = 'none';


                    }else{



                    }


                    if(beri == null){
                        beri = 'none';


                    }else{



                    }
                    if(bli == null){
                        bli = 'none';


                    }else{



                    }


                    if(request == null){

                        request - 'none';

                    }else{



                    }


                    if(prod == ""){



                    }else{
                        arr.push({name: prod, request : request, bli : bli, beri : beri, wgti : wgti, dis : dis});


                    }






                });

                arr2.push({ord : ord,
                    id : id,
                    driver : driver,
                    driver2 : driver2,
                    scrap : scrap,
                    wee : wee,
                    totalreds : totalreds,
                    totalyells : totalyells,
                    totalunits : totalunits,
                    totalweights : totalweights,
                    cust : cust,
                    veri : veri,
                    comments : comments});



                var array1 = JSON.stringify(arr);
                var array2 = JSON.stringify(arr2);
                console.log(arr);
                console.log(arr2);

                $.ajax({
                    url: "/RS/goodsinmail/", // Url to which the request is send
                    type: "POST",             // Type of request to be send, called as method
                    data: {
                        array1 : array1,
                        array2 : array2
                        // notworkingqty : notworkingqty,

                    }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    // To send DOMDocument or non processed data file it is set to false

                    success: function(data){   // A function to be called if request succeeds

                        // alert('success');
                        //console.log(data);

                        location.reload("http://recwebtest.stonegroup.co.uk/RS/Goodsin/");
                        // $("#image_preview").html(data);
                    }
                });





            });



            //  console.log(arr2);


//         $('#emailbtn').on('click', function(){

// var content = $('#tablezone').html();
// console.log(content);
// var rid = $('#intake #rcnum').val();
// alert(rid);


// $.ajax({
//               url: "/RS/goodsinmail/", // Url to which the request is send
//               type: "POST",             // Type of request to be send, called as method
//               data: {
//                 content : content,
//                 rid : rid
//                // notworkingqty : notworkingqty,

//               }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
//                     // To send DOMDocument or non processed data file it is set to false

//               success: function(data){   // A function to be called if request succeeds

//              alert('success');
//              console.log(data);
//              //location.reload(true);
//                 // $("#image_preview").html(data);
//               }
//             });





// });









            $('#print').on('click',function(){
                printData();
            });


            refreshnum();



            $('#intake').on('input ', function(){



                refreshnum();




            });







            function refreshnum(){

                v = 0;
                blber = 0;
                wgttot = 0;
                redwgt = 0;
                totalredwgt = 0;
                yelwgt = 0;
                totalyelwgt = 0;

                $('#intake .red').each(function() {

                    $(this).find('.wgt').each(function() {

                        redwgt = Number($(this).val());
                        console.log(redwgt);
                        if (!isNaN(redwgt) && redwgt.length !== 0) {
                            totalredwgt += parseFloat(redwgt);

                        }
                    });


                });

                $('#intake .yellow').each(function() {


                    $(this).find('.wgt').each(function() {


                        yelwgt = Number($(this).val());

                        console.log(yelwgt);
                        if (!isNaN(yelwgt) && yelwgt.length !== 0) {
                            totalyelwgt += parseFloat(yelwgt);

                        }

                    });


                });






                $('#intake tr').each(function() {

                    val = 0;
                    bl = 0;
                    wgt = 0;
                    ber = 0;
                    totalcol = 0;
                    dis = 0;

                    req = $(this).find('.req').val();



                    $(this).find('.bl').each(function() {


                        bl = Number($(this).val());

                        if (!isNaN(bl) && bl.length !== 0) {
                            blber += parseFloat(bl);

                        }

                    });

                    $(this).find('.wgt').each(function() {


                        wgt = Number($(this).val());

                        if (!isNaN(wgt) && wgt.length !== 0) {
                            wgttot += parseFloat(wgt);

                        }

                    });




                    $(this).find('.ber').each(function() {


                        ber = Number($(this).val());

                        if (!isNaN(ber) && ber.length !== 0) {
                            blber += parseFloat(ber);

                        }



                    });


                    $(this).find('.req').each(function() {

                        val = Number($(this).val());

                        if (!isNaN(val) && val.length !== 0) {
                            v += parseFloat(val);

                        }
                    });






// $(this).find('.req').each(function() {


// val = Number($(this).val());







// });



                    totalcol += bl + ber;

                    dis =  totalcol - val;


                    $(this).find('.dis').each(function() {


                        $(this).val(dis);


                    });
                    $('#yeltotal').val(totalyelwgt);
                    $('#redtotal').val(totalredwgt);
                    $('#totunits').val(blber);
                    $('#totalreq').val(v);
                    $('#totalwgt').val(wgttot);
                });

            }


            function printData() {
                // var divToPrint=document.getElementById("intake");
                // newWin= window.open("");
                // newWin.document.write(divToPrint.outerHTML);
                // newWin.print();
                // newWin.close();
                $('#tablezone').printThis({
                    importCSS: true,
                    importStyle: true
                });
            }





        });
    </script>



</head>
<style type="text/css">


    body{

        -webkit-print-color-adjust: exact !important;
    }

    table, th, td, h2 {
        -webkit-print-color-adjust: exact !important;
        border: 1px solid black !important;
    }

    #container{
        -webkit-print-color-adjust: exact !important;

        padding-left:10% !important;

        padding-top:10% !important;

    }
    /* #tablezone{
     padding-left:20%;
     padding-right:30%;

    } */




    .xdebug-var-dump{

        padding-top:10% !important;
        -webkit-print-color-adjust: exact !important;

    }



    #intake .ewc{
        background-color:white !important;
        -webkit-print-color-adjust: exact !important;
    }

    #intake .red{
        background-color:#ffb2b2 !important;
        -webkit-print-color-adjust: exact !important;
    }


    #intake #redtotal{
        background-color:#ffb2b2 !important;
    }



    #intake .yellow{
        background-color:#f4f499 !important;
    }

    #intake #yeltotal{
        background-color:#f4f499 !important;
    }


    #intake .green{
        background-color:#b2d8b2 !important;
    }

    #intake .block{
        background-color:grey !important;
    }

    /* #intake {
      margin:0;

    } */

    #intake{
        max-width: 2480px !important;
        width:80% !important;
    }
    #intake td{
        width: auto !important;
        overflow: hidden !important;
        word-wrap: break-word !important;
    }



</style>

<body>


<div id='container'>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="/RS/Goodsin/">Goods in</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/RS/Goodsout/">Goods Out</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">Goods Out - WEE </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="">Goods Out - Reports</a>
        </li>
    </ul>

    <br> <br>




    <form method='POST' action='/RS/Goodsin/'>
        <input type='text' name='rid' id='reqid'/>
        <input type='submit'/>
    </form>

    <?php

    $pc = 0;

    if(isset($res) && $check['cn'] !== 0){



        $cust = '';

        foreach($name as $n){

            $cust = $n['Customer_name'];

        }


        $ord ='';

//echo print_r($driver, true);










//echo print_r($subinfo, true);


        foreach($subinfo as $info){


            //if($info['PRODUCT'] == 'TFT Monitors') {

            if($info['PRODUCT']== ' TFT Monitors '){




                $det = implode(",",$info);
                $blarr = array_slice($info, 0, 1);
                $tftmonbl= implode(",",$blarr);
                $berarr = array_slice($info, 1, 1);
                $tftmonber = implode(",", $berarr);
                $weightarr = array_slice($info, 2, 1);
                $tftmonwight = implode(",", $weightarr);



            }


            else if($info['PRODUCT'] == ' TFT TV Monitors ') {

                $tfttvnbl = $info['BL'];
                $tfttvber = $info['ber'];
                $tfttvwight = $info['weight'];

            }
            else if($info['PRODUCT'] == ' PC - DT/TOWER Generic ') {

                $pcbl = $info['BL'];
                $pcber = $info['ber'];
                $pcwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' PC - Apple ') {

                $pcappmonbl = $info['BL'];
                $pcappber = $info['ber'];
                $pcappwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' All in One - Generic ') {

                $aiobl = $info['BL'];
                $aiober = $info['ber'];
                $aiowight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' All in One - Apple Imac ') {

                $aioappimacbl = $info['BL'];
                $aioappimacber = $info['ber'];
                $aioappimacwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' All in One - Apple Emac ') {

                $aioemacbl = $info['BL'];
                $aioemacber = $info['ber'];
                $aioemacwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Laptops ') {

                $Laptopbl = $info['BL'];
                $Laptopber = $info['ber'];
                $Laptopwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Laptop - Apple Macbook ') {

                $applapbl = $info['BL'];
                $applapber = $info['ber'];
                $applapwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Servers ' ) {

                $srvbl = $info['BL'];
                $srvber = $info['ber'];
                $srvwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Tablets - Generic ') {

                $tabbl = $info['BL'];
                $tabber = $info['ber'];
                $tabwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Tablets - Apple ') {

                $apptabbl = $info['BL'];
                $apptabber = $info['ber'];
                $apptabwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Smart Phone ') {

                $spbl = $info['BL'];
                $spber = $info['ber'];
                $spwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Apple Phone ') {

                $apbl = $info['BL'];
                $apber = $info['ber'];
                $apwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Non Smart Phone ') {

                $nonsmartbl = $info['BL'];
                $nonsmartber = $info['ber'];
                $nonsmartwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Printers ') {

                $printbl = $info['BL'];
                $printber = $info['ber'];
                $printwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' MFD Printers ') {

                $mfdmonbl = $info['BL'];
                $mfdmonber = $info['ber'];
                $mfdwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' CRT Monitors / TV') {

                $crtbl = $info['BL'];
                $crtber = $info['ber'];
                $crtwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Scanners ') {

                $scanbl = $info['BL'];
                $scanber = $info['ber'];
                $scanwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Batteries - UPS ') {

                $batupsbl = $info['BL'];
                $batupsber = $info['ber'];
                $batupswight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Batteries - Lead Acid ') {

                $acidbl = $info['BL'];
                $acidber = $info['ber'];
                $acidwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Batteries - Mecury, Nickel ') {

                $nicbl = $info['BL'];
                $nicber = $info['ber'];
                $nicwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Batteries - Non Haz ') {

                $nonhazbl = $info['BL'];
                $nonhazber = $info['ber'];
                $nonhazwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Projectors ') {

                $projbl = $info['BL'];
                $projber = $info['ber'];
                $projwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Thin Clients ') {

                $thinbl = $info['BL'];
                $thinber = $info['ber'];
                $thinwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Switches ') {

                $swibl = $info['BL'];
                $swiber = $info['ber'];
                $swiwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Smartboards ') {

                $smartbl = $info['BL'];
                $smartber = $info['ber'];
                $smartwight = $info['weight'];


            }

            else if($info['PRODUCT'] == ' PDAS ') {

                $pdabl = $info['BL'];
                $pdaber = $info['ber'];
                $pdawight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Sky Boxes ') {

                $skybl = $info['BL'];
                $skyber = $info['ber'];
                $skywight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Game Consoles ') {

                $gamebl = $info['BL'];
                $gameber = $info['ber'];
                $gamewight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Data Tapes ') {

                $databl = $info['BL'];
                $databer = $info['ber'];
                $datawight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Loose Hard Drives ') {

                $hhdbl = $info['BL'];
                $hhdber = $info['ber'];
                $hhdwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Back Up Devices') {

                $devbl = $info['BL'];
                $devber = $info['ber'];
                $devwight = $info['weight'];


            }
            else if($info['PRODUCT'] == ' Ext Hard Drive') {

                $extbl = $info['BL'];
                $extber = $info['ber'];
                $extwight = $info['weight'];


            }

            else if($info['PRODUCT'] == ' Other:') {

                $otherbl = $info['BL'];
                $otherber = $info['ber'];
                $otherwight = $info['weight'];


            }


        }

        $pc = 0;
        $aio = 0;
        $tft = 0;
        $tfttv = 0;
        $lap = 0;
        $lapother  = 0;
        $lapamd = 0;
        $hrd = 0;
        $srv = 0;
        $apptab = 0;
        $tab = 0;
        $sphne = 0;
        $apphne = 0;
        $pri = 0;
        $mfdpri = 0;
        $crt = 0;
        $batups = 0;
        $proj =0;
        $swi = 0;
        $smartb = 0;
        $hdd = 0;
        $o1 = 0;
        $o2 = 0;
        $o3 = 0;


        foreach($res as $resdata){

            if($resdata['Product'] == 'PC - iSeries' || $resdata['Product'] == 'PC - AMD' || $resdata['Product'] == 'PC - OTHER'){
                $pc += $resdata['qty'] ?? '0';
            }
            if($resdata['Product'] == 'Allinone_PC - iSeries' || $resdata['Product'] == 'ALLINONE PC - OTHER' || $resdata['Product'] == 'ALLINONE PC - AMD'){
                $aio += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'TFT'){
                $tft += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'TV'){
                $tfttv += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Laptop - iSeries' || $resdata['Product'] == 'Laptop - iSeries'){
                $lap += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'ALLINONE PC - OTHER'){
                $lapother += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'ALLINONE PC - AMD'){
                $lapamd += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Harddrive'){
                $hrd += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Server'){
                $srv = $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Apple Tablet'){
                $apptab += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Tablet'){
                $tab += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Smart Phone'){
                $sphne += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Apple Phone'){
                $apphne += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'DesktopPrinter'){
                $pri += $resdata['qty'] ?? '0';
            }
            if($resdata['Product'] == 'Standalone_Printer'){
                $mfdpri += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'CRT'){
                $crt += $resdata['qty'] ?? '0';
            }


            if($resdata['Product'] == 'UPS_Small' || $resdata['Product'] == 'UPS_Large'){
                $batups += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Projector'){
                $proj += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Switches'){
                $swi += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'SmartBoard'){
                $smartb += $resdata['qty'] ?? '0';
            }

            if($resdata['Product'] == 'Harddrive'){
                $hdd += $resdata['qty'] ?? '0';
            }


            if($resdata['Product'] == 'Other1'){
                $o1 += $resdata['qty'] ?? '0';
            }


            if($resdata['Product'] == 'Other2'){
                $o2 += $resdata['qty'] ?? '0';
            }


            if($resdata['Product'] == 'Other3'){
                $o3 += $resdata['qty'] ?? '0';
            }

            $to = $o1 + $o2 + $o3;






        }

        $table =  "
<div id='tablezone'>
<table class='table table-striped' border='1' id='intake'>
<col width='210'>

<thead>
<tr >
<td colspan='9 align='center' style='background-color:#f5f5dc !important;'> <h3>Customer</h3> </td>
</tr>
<tr >
<td colspan='8' id='custname'>".$cust."</td> 
</tr>
<tr style=' border: 1px solid black;'>


<td colspan='2' style='background-color:#b7e1f3 !important;'> <strong>RC Number:</strong></td>
<td colspan='2' id='rcnum'> RC-00".$rid." </td>
<td colspan='4' align='center'  style='background-color:#b7e1f3 !important;'> <strong>Driver(s)</strong></td>
</tr>
<tr>
<td colspan='2' style='background-color:#99cc99 !important;'> <strong>WTN:</strong></td>
<td colspan='2' id='wtnnum'> ".$ordnum['wtn']." </td>
<td align='center'  colspan='4' class='block'><input type='text' id='drivername'/> </td>


</tr>

<tr>
<td colspan='2'  style='background-color:#4ca64c !important;'> <strong>Ordernum:</strong></td>
<td colspan='2' id='ordernum'> ".$ordnum['ord']." </td>
<td colspan='4' align='center' class='block'><input type='text' id='drivername2'/> </td>

</tr>

<tr>
<td align='center'  colspan='9' class='block' rowspan='1'></td>
</tr>




<tr>
<th>Product:</th>
<th> TMP REQ:</th>
<th> Request</th>
<th> BL</th>
<th> BER</th>
<th> Weight:</th>
<th> EWC Code</th>
<th> Discrepancies</th>
</tr>
</thead>

<tbody>

<tr>
<th   class='red' id='prod'> TFT Monitors </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number' id='tftmonreq' class='req' value = '".$tft."' /></td>
<td class='red'> <input type='number' id='tftmonbl' class='bl' value='".$tftmonbl."'/> </td>
<td class='red'> <input type='number' id='tftmonber' class='ber' value='".$tftmonber."'/></td>
<td class='red'> <input type='number' id='tftmonwgt' class='wgt' value='".$tftmonwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='tftmondisc' class='dis'/></td>
</tr>

<tr>
<th class='red'  id='prod'> TFT TV Monitors </th>
<td class='red' > Yes</td>
<td class='red' > <input type='number' id='tfttvmonreq'  class='req' value='".$tfttv."'/></td>
<td class='red' > <input type='number' id='tfttvmonbl' class='bl' value='". $tfttvnbl."'/> </td>
<td class='red' > <input type='number' id='tfttvmonber' min='0' class='ber' value='".$tfttvber."'/></td>
<td class='red' > <input type='number' id='tfttvmonwgt' class='wgt' value='".$tfttvwight."'/></td>
<td  class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red' > <input type='number' id='tfttvmondisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> PC - DT/TOWER Generic </th>
<td> Yes</td>
<td> <input type='number' id='pcreq'  class='req' value='".$pc."' /></td>
<td> <input type='number' id='pcbl' class='bl' value='".$pcbl."'/> </td>
<td> <input type='number' id='pcber' class='ber' value='".$pcber."'/></td>
<td> <input type='number' id='pcwgt'  class='wgt' value='".$pcwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='pcdisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> PC - Apple </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='pcappreq' value='0'/></td>
<td> <input type='number' id='pcappbl' class='bl' value='".$pcappmonbl."'/> </td>
<td> <input type='number' id='pcappber' class='ber' value='".$pcappber."'/></td>
<td> <input type='number' id='pcappwgt' class='wgt' value='".$pcappwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='pcappdisc' class='dis'/></td>
</tr>

<tr class='red'>
<th id='prod' class='red'> All in One - Generic </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='pcappreq' value='".$aio."'/></td>
<td class='red'> <input type='number' id='pcappbl' class='bl' value='".$aiobl."'/> </td>
<td class='red'> <input type='number' id='pcappber' class='ber' value='".$aiober."'/></td>
<td class='red'> <input type='number' id='pcappwgt' class='wgt' value='".$aiowight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='pcappdisc' class='dis'/></td>
</tr>

<tr class='red'>
<th id='prod' class='red'> All in One - Apple Imac </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='pcappimreq' value='0'/></td>
<td class='red'>  <input type='number' id='pcappimbl' class='bl' value='".$aioappimacbl."'/> </td>
<td class='red'> <input type='number' id='pcappimber' class='ber' value='".$aioappimacber."'/></td>
<td class='red'> <input type='number' id='pcappimwgt' class='wgt' value='".$aioappimacwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='pcappimdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th id='prod' class='red'> All in One - Apple Emac </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='pcappemreq' value='0'/></td>
<td class='block'> </td>
<td class='red'> <input type='number' id='pcapprmber' class='ber' value='".$aioemacber."'/></td>
<td class='red'> <input type='number' id='pcapprmwgt' class='wgt' value='".$aioemacwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='pcapprmdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th id='prod' class='red'> Laptops </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='lapre' value='".$lap."'/></td>
<td class='red'> <input type='number' id='lapbl' class='bl' value='".$Laptopbl."'/> </td>
<td class='red'> <input type='number' id='lapber' class='ber' value='".$Laptopber."'/></td>
<td class='red'> <input type='number' id='lapwgt' class='wgt' value='".$Laptopwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='lapdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th id='prod' class='red'> Laptop - Apple Macbook </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='lapappreq' value='0'/></td>
<td class='red'> <input type='number' id='lapappbl' class='bl' value='".$applapbl."'/> </td>
<td class='red'> <input type='number' id='lapappber' class='ber' value='".$applapber."'/></td>
<td class='red'> <input type='number' id='lapappwgt' class='wgt' value='".$applapwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='lapappdisc' class='dis'/></td>
</tr>
<tr>
<th id='prod'> Servers </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='srvreq' value='".$srv."'/></td>
<td> <input type='number' id='srvbl' class='bl' value='".$srvbl."'/> </td>
<td class='block'> </td>
<td> <input type='number' id='srvwgt' class='wgt' value='".$srvwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='srvdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th class='red' id='prod'> Tablets - Generic </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='tabreq' value='".$tab."'/></td>
<td class='red'> <input type='number' id='tabbl' class='bl' value='".$tabbl."'/> </td>
<td class='red'> <input type='number' id='tabber' class='ber' value='".$tabber."'/></td>
<td class='red'> <input type='number' id='tabwgt' class='wgt' value='".$tabwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'>  <input type='number' id='tabdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th class='red' id='prod'> Tablets - Apple </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='tabappreq' value='".$apptab."'/></td>
<td class='red'> <input type='number' id='tabappbl' class='bl' value='".$apptabbl."'/> </td>
<td class='red'> <input type='number' id='tabappber' class='ber' value='".$apptabber."'/></td>
<td class='red'> <input type='number' id='tabappwgt' class='wgt' value='".$apptabwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='tabappdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th id='prod' class='red'> Smart Phone </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='smartpreq' value='".$sphne."'/></td>
<td class='red'> <input type='number' id='smartpbl' class='bl' value='".$spbl."'/> </td>
<td class='red'> <input type='number' id='smartpber' class='ber' value='".$spber."'/></td>
<td class='red'> <input type='number' id='smartpwgt' class='wgt' value='".$spwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='smartpdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th class='red' id='prod'> Apple Phone </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='smartpreq' value='".$apphne."'/></td>
<td class='red'> <input type='number' id='smartapbl' class='bl' value='".$apbl."'/> </td>
<td class='red'> <input type='number' id='smartapber' class='ber' value='".$apber."'/></td>
<td class='red'> <input type='number' id='smartapwgt' class='wgt' value='".$apwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='smartpdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th  class='red' id='prod'> Non Smart Phone </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='nsmartpreq' value='0'/></td>
<td class='red'> <input type='number' id='nsmartpbl' class='bl' value='".$nonsmartbl."'/> </td>
<td class='red'> <input type='number' id='nsmartpber' class='ber' value='".$nonsmartber."'/></td>
<td class='red'> <input type='number' id='nsmartpwgt' class='wgt' value='".$nonsmartwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='nsmartpdisc' class='dis'/></td>
</tr>
<tr>
<th id='prod' class='red'> Printers </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='printreq' value='".$pri."'/></td>
<td class='block'> </td>
<td class='red'> <input type='number' id='printber' class='ber' value='".$printber."'/></td>
<td class='red'>  <input type='number' id='printwgt' class='wgt' value='".$printwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td class='red'> <input type='number' id='printdisc' class='dis'/></td>
</tr>
<tr>
<th id='prod'> MFD Printers </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='mfdprintpreq' value='".$mfdpri."'/></td>
<td class='block'> </td>
<td> <input type='number' id='mfdprintber' class='ber' value='".$mfdmonber."'/></td>
<td> <input type='number' id='mfdprintwgt' class='wgt' value='".$mfdwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='mfdprintdisc' class='dis'/></td>
</tr>
<tr class='red'>
<th  class='red' id='prod'> CRT Monitors / TV</th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='crtreq' value='".$crt."'/></td>
<td class='block'> </td>
<td class='red'> <input type='number' id='crtber' class='ber' value='".$crtber."'/></td>
<td class='red'> <input type='number' id='crtwgt' class='wgt' value='".$crtwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='crtdisc' class='dis'/></td>
</tr>
<tr>
<th id='prod'> Scanners </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='scanreq' value='0'/></td>
<td class='block'> </td>
<td> <input type='number' id='scanber' class='ber' value='".$scanber."'/></td>
<td> <input type='number' id='scanwgt' class='wgt' value='".$scanwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='scandisc' class='dis'/></td>
</tr>
<tr class = 'yellow'>
<th  class = 'yellow' id='prod'> Batteries - UPS </th>
<td class = 'yellow'> Yes</td>
<td class = 'yellow'> <input type='number'  class='req' id='battreq' value='".$batups."'/></td>
<td class='block'> </td>
<td class = 'yellow'> <input type='number' id='battber' class='ber' value='".$batupsber."'/></td>
<td class = 'yellow'> <input type='number' id='battwgt' class='wgt' value='".$batupswight."'/></td>
<td class='ewc' style='color:#e6e600 !important;'>20:06:01</td>
<td class = 'yellow'> <input type='number' id='battdisc' class='dis'/></td>
</tr>
<tr class = 'yellow'>
<th class = 'yellow' id='prod'> Batteries - Lead Acid </th>
<td class = 'yellow'> Yes</td>
<td class = 'yellow'> <input type='number'  class='req' id='acidreq' value='0'/></td>
<td class='block'> </td>
<td class = 'yellow'> <input type='number' id='acidber' class='ber' value='".$acidber."'/></td>
<td class = 'yellow'> <input type='number' id='acidwgt' class='wgt' value='".$acidwight."'/></td>
<td class='ewc' style='color:#e6e600 !important;'>20:06:01</td>
<td class = 'yellow'> <input type='number' id='aciddisc' class='dis'/></td>
</tr>

<tr class = 'yellow'>
<th class = 'yellow' id='prod'> Batteries - Mecury, Nickel </th>
<td class = 'yellow'> Yes</td>
<td  class = 'yellow'> <input type='number'  class='req' id='batnicreq' value='0'/></td>
<td  class='block'> </td>
<td class = 'yellow'> <input type='number' class='ber' id='batnicber' value='".$nicber."'/></td>
<td class = 'yellow'> <input type='number' id='batnicwgt' class='wgt' value='".$nicwight."'/></td>
<td class='ewc' style='color:#e6e600 !important; '>20:06:01</td>
<td class = 'yellow'> <input type='number' id='batnicdisc' class='dis'/></td>
</tr>

<tr class='green'>
<th class='green' id='prod'> Batteries - Non Haz </th>
<td class='green'> Yes</td>
<td class='green'> <input type='number'  class='req' id='nonhazreq' value='0'/></td>
<td class='block'> </td>
<td class='green'> <input type='number' id='nonhazber' class='ber' value='".$nonhazber."'/></td>
<td class='green'> <input type='number' id='nonhazwgt'  class='wgt' value='".$nonhazwight."'/></td>
<td class='ewc' style='color:green !important;'>20:01:34</td>
<td class='green'> <input type='number' id='nonhazdisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Projectors </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='projreq' value='".$proj."'/></td>
<td class='block'> </td>
<td> <input type='number' id='projber' class='ber' value='".$projber."'/></td>
<td> <input type='number' id='projwgt'  class='wgt' value='".$projwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='projdisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Thin Clients </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='thinreq' value='0'/></td>
<td class='block'> </td>
<td> <input type='number' id='thinber' class='ber' value='".$thinber."'/></td>
<td> <input type='number' id='thinwgt'  class='wgt' value='".$thinwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='thindisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Switches </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='swireq' value='".$swi."'/></td>
<td class='block'> </td>
<td> <input type='number' id='swiber' class='ber' value='".$swiber."'/></td>
<td> <input type='number' id='swiwgt'  class='wgt' value='".$swiwight."'/></td>
<td class='ewc' style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='swidisc' class='dis'/></td>
</tr>

<tr class='red'>
<th class='red' id='prod'> Smartboards </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='smartbreq' value='".$smartb."'/></td>
<td class='block'> </td>
<td class='red'> <input type='number' id='smartbber' class='ber' value='".$smartber."'/></td>
<td class='red'> <input type='number' id='smartbwgt'  class='wgt' value='".$smartwight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='smartbdisc' class='dis'/></td>
</tr>

<tr>
<th>Miscellaneous</th>
<th> TMP REQ:</th>
<th> Request</th>
<th> BL</th>
<th> BER</th>
<th> Weight:</th>
<th> EWC Code</th>
<th> Discrepancies</th>
</tr>

<tr class='red'>
<th class='red' id='prod'> PDAS </th>
<td class='red'> Yes</td>
<td class='red'> <input type='number'  class='req' id='pdareq' value='0'/></td>
<td class='block'> </td>
<td class='red'> <input type='number' id='pdaber' class='ber' value='".$pdaber."'/></td>
<td class='red'> <input type='number' id='pdawgt'  class='wgt' value='".$pdawight."'/></td>
<td class='ewc' style='color:red !important;'>20:01:35</td>
<td class='red'> <input type='number' id='pdadisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Sky Boxes </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='skyreq' value='0'/></td>
<td class='block'> </td>
<td> <input type='number' id='skyber' class='ber' value='".$skyber."'/></td>
<td> <input type='number' id='skywgt'  class='wgt' value='".$skywight."'/></td>
<td style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='skydisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Game Consoles </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='gamereq' value='0'/></td>
<td class='block'> </td>
<td> <input type='number' id='gameber' class='ber' value='".$gameber."'/></td>
<td> <input type='number' id='gamewgt'  class='wgt' value='".$gamewight."' /></td>
<td style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='gamedisc' class='dis'/></td>
</tr>

<tr>
<th>Data Bearing Items(Non PC)</th>
<th> TMP REQ:</th>
<th> Request</th>
<th> BL</th>
<th> BER</th>
<th> Weight:</th>
<th> EWC Code</th>
<th> Discrepancies</th>
</tr>

<tr>
<th id='prod'> Data Tapes </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='datareq' value='0'/></td>
<td class='block'> </td>
<td> <input type='number' id='databer' class='ber' value='".$databer."'/></td>
<td> <input type='number' id='datawgt'  class='wgt' value='".$datawight."'/></td>
<td style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='datadisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Loose Hard Drives </th>
<td> Yes</td>
<td> <input type='number'  class='req' id='loosereq' value='".$hdd."'/></td>
<td class='block'> </td>
<td> <input type='number' id='looseber' class='ber' value='".$hhdber."'/></td>
<td> <input type='number' id='loosewgt'  class='wgt' value='".$hhdwight."'/></td>
<td style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='loosedisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Back Up Devices</th>
<td> Yes</td>
<td> <input type='number'  class='req' id='backreq' value='0'/></td>
<td class='block'> </td>
<td> <input type='number' id='backber' class='ber' value='".$devber."'/></td>
<td> <input type='number' id='backwgt'  class='wgt' value='".$devwight."'/></td>
<td style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='backdisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Ext Hard Drive</th>
<td> Yes</td>
<td> <input type='number'  class='req' id='extreq' value='0'/></td>
<td class='block'> </td>
<td> <input type='number' id='extber' class='ber' value='".$extber."'/></td>
<td> <input type='number' id='extwgt'  class='wgt' value='".$extwight."'/></td>
<td style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='extdisc' class='dis'/></td>
</tr>

<tr>
<th id='prod'> Other:</th>
<td> Yes</td>
<td> <input type='number'  class='req' id='otherreq' value='".$to."'/></td>
<td class='block'> </td>
<td> <input type='number' id='otherber' class='ber' value='".$otherber."'/></td>
<td> <input type='number' id='otherwgt' class='wgt' value='".$otherwight."'/></td>
<td style='color:blue !important;'>20:01:36</td>
<td> <input type='number' id='otherdisc' class='dis'/></td>
</tr>

<tr>
<td> </td>
<td> <strong>total:</strong></td>
<td> <input type='number' id='totalreq'/> </td>
<td class='block' colspan='4'></td>
<td> <strong>Verified</strong></td>
</tr>

<tr>
<td> Other: </td>
<td colspan='3' id='prodwee'>Scrap Cable </td>
<td>No</td>
<td><input type='number' id='scrapwgt' class='wgt' value='0'/></td>
<td style='color:blue !important;'>20:01:36</td>
<td rowspan='2' align='center'><select id='veri' style='  font-size: large;' selected>
<option><strong>No</strong></option>
<option><strong>Yes</strong></option>
<option><strong>N/A</strong></option>
</select></td>
</tr>

<tr>
<td></td>
<th colspan='3' id='prodwee'>Genreal WEEE </th>
<td>No</td>
<td><input type='number' id='weewgt' class='wgt' value='0'/></td>
<td style='color:blue !important;'>20:01:36</td>
</tr>

<tr>
<th rowspan='2'> Totals </th>
<td><input type='number' id='redtotal'/></td>
<td rowspan='2' align='center'> Units </td>
<td rowspan='2' align='center'><input type='number' id='totunits'/></td>
<td rowspan='2' align='center'> Weight: </td>
<td rowspan='2' align='center'> <input type='number' id='totalwgt'/> </td>
<th colspan='2' style='background-color:#329932 !important'> Comments</th>
</tr>
<tr>
<td><input type='number' id='yeltotal'/></td>
<td colspan = '2' id='comment3'><input type='text' id='com3'></td>
</tr>


<tr>
<td><strong>Counted by:</strong></td>
<td colspan='3' id='countby'><input type='text' id='countby'></td>
<td><strong> Time Start:</strong></td>
<td><input type='time' id='starttime'></td>
<td colspan = '2' id='comment1'><input type='text' id='com1'></td>
</tr>

<tr>
<td><strong>Booked in by:</strong></td>
<td colspan='3'>".$_SESSION['user']['firstname'].' '.$_SESSION['user']['lastname']."</td>
<td><strong> Date:</strong></td>
<td>".date("D M d, Y G:i")."</td>
<td colspan = '2' id='comment2'><input type='text' id='com2'></td>
</tr>

<tr>
<td><strong>Security seal number:</strong></td>
<td><strong>IN</strong></td>
<td colspan='3' id='securin'><input type='text' id='securintxt'></td>
<td><strong>OUT</strong></td>
<td colspan = '2' id='securout'><input type='text' id='securinout'></td>
</tr>



</tbody>


</table>
</div>
<br> 
<input type='button' id='print' class='btn btn-primary'  value='Print'>
<input type='button' id='emailbtn' class='btn btn-success'  value='Email'>




";


        echo $table;





    }else{

        echo "<p>No Data Found!</p>";
    }

    $fh = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/pagehitgoods.txt","a+");
    fwrite($fh,'on goods in'."\n");
    fclose($fh);

    ?>
</div>
</body>
</html>