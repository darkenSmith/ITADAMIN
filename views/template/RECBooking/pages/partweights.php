

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <script src="/inc/js/jquery-3.3.1.js"></script>
  <script src="/inc/js/bootstrap.js"></script>
  <script src="/inc/js/popper.js"></script>
  <link href="/inc/css/bootstrap-datepicker.min.css" rel="stylesheet">
  <link href="/inc/css/imgupload.css" rel="stylesheet">
  <title>Recycling Goods Receipting</title>
</head>
<style>
  table, th, td {
  border: 1px solid black;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>
<body>


<script type='text/javascript'>
$(document).ready(function(){

  $('#amr').hide();
   $('#rebh').hide();
   $('#codpap').hide();
   $('#rpt').hide();

  $("#berupdate").hide();
  $("#tb2").hide();

  function reload_totals() {
  var totalwgt = 0;
   var totalber = 0;
   var totalqty = 0;
   var overralltot = 0;

    $('#tb .wgt').each(function() {

        var val = $(this).val();

        if(!isNaN(val) && val.length != 0) {
          totalwgt += parseFloat(val);
    }
     });


     $('#tb .ber').each(function() {

var val2 = $(this).val();

if(!isNaN(val2) && val2.length != 0) {
  totalber += parseFloat(val2);
}
});


$('#tb .qty').each(function() {

var val3 = $(this).val();

if(!isNaN(val3) && val3.length != 0) {
  totalqty += parseFloat(val3);
}
});

    var totalwgtdec = totalwgt.toFixed(2);

    $('#totalwe').val(totalwgtdec);
    $('#totalbr').val(totalber);
    $('#totalbl').val(totalqty);
   
     overralltot =  totalber + totalqty;

      $('#overtotal').val(overralltot);


 
  }


  reload_totals();



  $('#tb').on('input ', function(){

    reload_totals();

  });


  $("input[type=number]").on('change keyup', function() {

    console.log('change');
    $(this).css("font-weight", "900");

  });


  $('#tb').find(':input[type="number"]').each(function() {


   console.log($(this).val());

   if($(this).val() == '0' || $(this).val() == '.00'){

    $(this).val(' ');
   }
      
//     if($(this).find('.qty').val() == ''){
     
//       $(this).find('.qty').val(0);
//   }

//   if($(this).find('.ber').val() == ''){
     
//      $(this).find('.ber').val(0);
//  }

//  if($(this).find('.wgt').val() == ''){
     
//      $(this).find('.wgt').val(0);
//  }



  });

  $("#updatewgt").on("click", function(){

    var flag = 0

      //alert("hello");

  $('#tb tbody td').each(function() {

    
    
    // var keval = $(this).find(".group1").val();
    // console.log(keval);
    if($(this).find('.group1').val() == ''){
     
      flag = 1;
   }

  });

  if(flag == 1){
    alert('Input can not be left blank');
    $('#error').after('<p>Input can not be left blank</p>');
   }else{

    $('#error').after('<p></p>');


 

    var pcqty = Number($("#col2q").val());
   var pcber = Number($("#ber2col").val());
   var pcwgt = parseFloat($("#col2").val());

   var pcotherqty = Number($("#col2qother").val());
   var pcotherber = Number($("#ber2colother").val());
   var pcotherwgt = parseFloat($("#col2other").val());

   

   var pctotal = pcqty + pcber;
   var pcothertotal = pcotherqty + pcotherber;
  


   var totalpcwgt = pcwgt + pcotherwgt;

  //  var lapappleqty = Number($("#col4qother").val());
  //  var lapappleber = Number($("#ber4colother").val());
  //  var lapapplewgt = parseFloat($("#col4other").val());


  // alert(pcber);
  // alert(pctotal);


   var aioqty = Number($("#col3").val());
   var aiober = Number($("#ber3col").val());
   var aiowgt = parseFloat($("#col3q").val());

   var aiootherqty = Number($("#col3other").val());
   var aiootherber = Number($("#ber3colother").val());
   var aiootherwgt = parseFloat($("#col3qother").val());



   var aiototal = aioqty + aiober;
   var aioothertotal = aiootherqty + aiootherber;

   var totalaiowgt = aiowgt + aiootherwgt;

   var aioappqty = Number($("#appaio").val());
   var aioappber = Number($("#berappaio").val());
   var aioappwgt = parseFloat($("#wgtaio").val());

   var aioapptotal = aioappqty + aioappber;




   var lapqty = Number($("#col4q").val());
   var lapber = Number($("#ber4col").val());
   var lapwgt = parseFloat($("#col4").val());

   
   var lapotherqty = Number($("#col4qother").val());
   var lapotherber = Number($("#ber4colother").val());
   var lapotherwgt = parseFloat($("#col4other").val());



   var totallapwgt = lapwgt + lapotherwgt;

   var lapothertotal = lapotherber + lapotherqty;
   var laptotal = lapber + lapqty;
   

   var spqty = Number($("#col7q").val());
   var spber = Number($("#berspcol").val());
   var spwgt = parseFloat($("#col7").val());

   var sptotal = spqty + spber;

   var nspqty = Number($("#colnonq").val());
   var nspber = Number($("#bernonspcol").val());
   var nspwgt = parseFloat($("#colnon7").val());


   var nsptotal = nspqty + nspber;

   var appspqty = Number($("#colapp2q").val());
   var appspber = Number($("#berapp2col").val());
   var appspwgt = parseFloat($("#colapp2").val());


   var appsptotal = appspqty + appspber;

  var apptabqty = Number($("#colapptab2q").val());
   var apptabber = Number($("#berapptab2col").val());
   var apptabwgt = parseFloat($("#colapptab2").val());

   var apptabtotal = apptabqty + apptabber;

   var tabqty = Number($("#col6q").val());
   var tabber = Number($("#bertab2col").val());
   var tabwgt = parseFloat($("#col6").val());

   var tabtotal = tabqty + tabber;
 
   var tftqty = Number($("#colq").val());
   var tftber = Number($("#tftber2col").val());
   var tftwgt = parseFloat($("#col").val());

   var tfttotal = tftqty + tftber;

   var tfttvber = Number($("#ber1col").val());
   var tfttvwgt = parseFloat($("#col1").val());
    
   var srvqty = Number($("#col5q").val());
   var srvber = Number($("#berservcol").val());
   var srvwgt = parseFloat($("#col5").val());

   var srvtotal = srvqty + srvber;

 
   var swber = Number($("#ber17col").val());
   var swwgt = parseFloat($("#col17").val());

   var smber = Number($("#ber8col").val());
   var smwgt = parseFloat($("#col8").val());

   var priber = Number($("#ber9col").val());
   var priwgt = parseFloat($("#col9").val());


   var mfdber = Number($("#ber10col").val());
   var mfdwgt = parseFloat($("#col10").val());

 

   var bathazber = Number($("#ber23col").val());
   var bathazwgt = parseFloat($("#col23").val());

   var batnonhazber = Number($("#ber19col").val());
   var batnonhazwgt = parseFloat($("#col19").val());

   var projber = Number($("#ber15col").val());
   var projwgt = parseFloat($("#col15").val());

   var crtber = Number($("#ber20col").val());
   var crtwgt = parseFloat($("#col11").val());

   var thinber = Number($("#ber16col").val());
   var thinwgt = parseFloat($("#col1thin").val());

   var scanber = Number($("#ber13col").val());
   var scanwgt = parseFloat($("#col13").val());


   var pdaber = Number($("#col30ber").val());
   var pdawgt = parseFloat($("#col30").val());

   var skyber = Number($("#col29ber").val());
   var skywgt = parseFloat($("#col29").val());

   var gameber = Number($("#col28ber").val());
   var gamewgt = parseFloat($("#col28").val());


   var databer = Number($("#col27ber").val());
   var datawgt = parseFloat($("#col27").val());

   var looseber = Number($("#col13q").val());
   var loosewgt = parseFloat($("#col1hd").val());

   var backupber = Number($("#col31ber").val());
   var backupwgt = parseFloat($("#col31").val());


   var exhardber = Number($("#col32ber").val());
   var exhardwgt = parseFloat($("#col32").val());

   var otherber = Number($("#col19ber").val());
   var otherwgt = parseFloat($("#col1other").val());

   var scrapwgt = parseFloat($("#col24").val());

   var sweewgt = parseFloat($("#col20").val());

   var totalwe =  Number($('#totalwe').val());
   var totalBER =  Number($('#totalbr').val());
   var totalunits = Number($('#overtotal').val());

   var dept = $('#dept').val();
   //var rpt = $('#rpt').val();
  var accmanager = $('#accman').val();
  var codpaper = $('#codpap').val();
  var datecoll = $('#datec').val();
    var Consign = $('#conignnum').val();
    var shared = $('#swith').val();
    var nodays = $('#nodays').val();


    var amr = $('#amr').val();
    var rebate = $('#rebh').val();
    var enchanced = $('#enchan').val();



  




   var ord = <?php 
   if(isset($_GET['ye'])){

  


    strip_tags($_GET['ye']);


         $ord = filter_var ($_GET['ye'], FILTER_SANITIZE_STRING); 

    echo $ord;
    
       }else{
         echo "Enter a Order Number";
       }
    ?>;

   //alert(ord);



   
        $.ajax({
          type: "POST",
            url: "/RS/updatecol/",
            data: {
              pcqty : pcqty,
              pcotherqty : pcotherqty, 
              pcotherber : pcotherber,
              pcber : pcber,
              pcotherwgt : pcotherwgt, 
              totalpcwgt : totalpcwgt,
              pcwgt : pcwgt,
              aioqty : aioqty,
              aiootherqty : aiootherqty,
              aiootherber : aiootherber,
              aiober : aiober,
              aiowgt : aiowgt,

              aiootherwgt : aiootherwgt,

              totalaiowgt : totalaiowgt,
              aioappqty : aioappqty,
              aioappber : aioappber,
              aioappwgt : aioappwgt,
              lapqty : lapqty,
              lapotherqty : lapotherqty,
              lapber : lapber,
              lapotherber : lapotherber,
              totallapwgt : totallapwgt,

              lapotherwgt : lapotherwgt,
              lapwgt : lapwgt,
              spqty : spqty,
              spber : spber,
              spwgt : spwgt,
              appspqty : appspqty,
              appspber : appspber,
              appspwgt : appspwgt,
              apptabqty : apptabqty,
              apptabber : apptabber,
              apptabwgt : apptabwgt,
              tabqty : tabqty,
              tabber : tabber,
              tabwgt : tabwgt,
              tftqty : tftqty,
              tftber : tftber,
              tftwgt : tftwgt,
              tfttvber : tfttvber,
              tfttvwgt : tfttvwgt,
              srvqty : srvqty,
              srvber : srvber,
              srvwgt : srvwgt,
              nspqty : nspqty,
              nspber : nspber,
              nspwgt : nspwgt,
              swber : swber,
              swwgt : swwgt,
              smber : smber,
              smwgt : smwgt,
              priber : priber,
              priwgt : priwgt,
              mfdber : mfdber,
              mfdwgt : mfdwgt,
              bathazber : bathazber,
              bathazwgt : bathazwgt,
              batnonhazber : batnonhazber,
              batnonhazwgt : batnonhazwgt,
              projber : projber,
              projwgt : projwgt,
              crtber : crtber,
              crtwgt : crtwgt, 
              thinber : thinber,
              thinwgt : thinwgt,
              scanber : scanber,
              scanwgt : scanwgt,
              pdaber : pdaber,
              pdawgt : pdawgt,
              skyber : skyber,
              skywgt : skywgt,
              gameber : gameber,
              gamewgt : gamewgt,
              databer : databer,
              datawgt : datawgt,
              looseber : looseber,
              loosewgt : loosewgt,
              backupber : backupber,
              backupwgt : backupwgt,
              exhardber : exhardber,
              exhardwgt : exhardwgt,
              otherber : otherber,
              otherwgt : otherwgt,
              scrapwgt : scrapwgt,
              sweewgt : sweewgt,
              totalwe : totalwe,
              totalBER : totalBER,
              pctotal : pctotal,
              pcothertotal : pcothertotal,
              aiototal : aiototal,
              aioothertotal : aioothertotal,
              laptotal : laptotal,
              lapothertotal : lapothertotal,
              sptotal : sptotal,
              nsptotal : nsptotal,
              appsptotal : appsptotal,
              apptabtotal : apptabtotal,
              aioapptotal : aioapptotal,
              tabtotal : tabtotal,
              tfttotal : tfttotal,
              srvtotal : srvtotal,
              ord :ord,
              dept : dept,
             // rpt : rpt,
              accmanager : accmanager,
              codpaper : codpaper,
              datecoll : datecoll,
              Consign : Consign,
              shared : shared,
              nodays : nodays,
              amr : amr,
              rebate : rebate,
              enchanced : enchanced,
              totalunits : totalunits






              },
            success: function(data)
            {
             // alert(data);
              //  alert("success!");
            }
        });
}

location.reload('http://recwebtest.stonegroup.co.uk/RS/RGR/?ye=');
  });

});

</script>







<div class = "container">
  <ul class="nav nav-tabs">
 
    <li class="nav-item">
      <a class="nav-link" href="/RS/booking/">Booked Collection</a>
    </li>
     <li class="nav-item">
      <a class="nav-link active" href="">RGR</a>
    </li>
    <li class="nav-item">
        <a class="nav-link"  href="/RS/arc/">Collected</a>
      </li>
    <li class="nav-item">
    <a class="nav-link" href="/RS/companynote/">Company Notes</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/RS/rebatepage/">Rebates</a>
    </li>
   
  </ul>
  <hr>
  <h1 class="page-header"> RGR <small>Recycling Goods Receipting</small></h1>
  <br>
  <hr>
  <label> Enter Ordernum </label>

  <form method='get'action="/RS/RGR/" id="form">
    <input type="text"  name="ye" id="ord" value=""/>
    <input type="submit" value="Submit">

  </form>

 <!--- <button type="button" id="updatewgt">Update</button>--->






<div id="error" style="display:none">add some text</div>






  <?php

if(isset($_SESSION['customer'])){
  $ord = $_GET['ye'];
   $name = $_SESSION['customer'];
}else{
  $ord = "";
  $name = "";
}


 
   echo "<h1>".$name."</h1>";
   echo $ord;
  echo $table;
  ?>


  



  


</body>
</html>
