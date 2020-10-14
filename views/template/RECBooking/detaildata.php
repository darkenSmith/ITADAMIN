<?php
if(!isset($_SESSION)) {
  session_start();
}
/*----------------------------------------------------------------------------------------------------
Change Log
Date			tag						Ticket				By						Description
------------------------------------------------------------------------------------------------------
23/08/18	Created											Alex.Smith		Created
07/09/18	tidy                        Neil.Baker    Remove HTML Headers and include of db.php
------------------------------------------------------------------------------------------------------*/
$rowid = isset($_GET['rowid']) ? " WHERE Request_ID = ".$_GET["rowid"] : "";
$rowid2 = isset($_GET['rowid']) ? " WHERE RequestID = '".$_GET["rowid"]."'" : "";
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
where request_id = ".$justID."

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
(SELECT [survey_deadline] FROM Booked_Collections ".$rowid2.")  as survdue,
(SELECT [surveysnet_date] FROM Booked_Collections ".$rowid2.")  as survsenttime,
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

 from request".$rowid;



 $fn = fopen($_SERVER["DOCUMENT_ROOT"]."/RS_Files/stringquery.txt","a+");
 fwrite($fn,$sql."\n");
 fclose($fn);
  

$stmt = $this->sdb->prepare($sql);
if (!$stmt) {
  echo "\nPDO::errorInfo():\n";
  print_r($this->sdb->errorInfo());
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
where rt.request_id = ".$justID."

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



  $stmt2 = $this->sdb->prepare($sqlextra);
if (!$stmt2) {
  echo "\nPDO::errorInfo():\n";
  print_r($this->sdb->errorInfo());
  die();
}
$stmt2->execute();

$data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);





foreach($data  as $row) {

$sqlnotes = "
SET NOCOUNT on
declare @postcode varchar(10)

set @postcode='".$row['postcode']."'";
}


$sqlnotes .="select top 1
C.gdpr as gdpr, 
c.is_AMR as amrc,
isnull(replace(c.location, ' ', ''), 'null'), 
isnull(max(c.notes), 'no notes') as notes
from request as rt2
full join companies as c on
 Location = @postcode and 
 Customer_name = [CompanyName]

 where  Location = '".$row['name']."'+@postcode
group by isnull(replace(c.location, ' ', ''), 'null')
, C.gdpr
,c.is_AMR



order by isnull(replace(c.location, ' ', ''), 'null')
";



  $stmtnote = $this->sdb->prepare($sqlnotes);
if (!$stmtnote) {
  echo "\nPDO::errorInfo():\n";
  print_r($this->sdb->errorInfo());
  die();
}
$stmtnote->execute();
$datanote = $stmtnote->fetchAll(PDO::FETCH_ASSOC);







//var_dump($sqlnotes);

$stmttotal = $this->sdb->prepare($totalswu);
if (!$stmttotal) {
  echo "\nPDO::errorInfo():\n";
  print_r($this->sdb->errorInfo());
  die();
}
$stmttotal->execute();

$datar = $stmttotal->fetchAll(PDO::FETCH_ASSOC);

///// end of image upload///



////// start of bottom feilds /////

 
        // while($row = sqlsrv_fetch_array($stmt)){
        foreach($datar as $roww) {





echo "
<tr>
<th> Total Units </th>
<td>". $roww['totalunits'] ."</td>
</tr>
<th> Total Weight </th>
<td>". $roww['totalweight'] ."</td>
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
  foreach($data2 as $row) {
    
    //Creates a loop to loop through results
    if ($row['asset'] == 1) {
      $nd="checked";
      $ass = 1;
    }else{
      $nd = '';
      $ass = 0;
    }
    if ($row['wip'] == 1) {
      $nd2="checked";
      $wip = 1;
    }else{
      $nd2 = '';
      $wip = 0;
    }


    //<td hidden><input type='checkbox' id='w' name='wipe' class='group1'  ".$nd2." disabled></td>
  //  <td hidden><input hidden type='checkbox' id='v'   class='group1'  ".$nd ." disabled></td>

    echo "
    <tr id=row".$row['prodid'].">
   <td id=".$row['prodid']." class='partis'>".$row['prodname']."</td>
   <td  hidden><input type='text' class='idp' value='".$row['prodid']."' ></td>
   <td><input type='text' class='workin  group1' value='". $row['w'] ."' disabled></td>
   <td class='delline'><a href='javascript:void(0);'  class='remove'><span class='glyphicon glyphicon-remove'></span></a></td>
   </tr>
";

   if($row['prodname']  == 'Other1'){
   echo "
   <td> <input type='text' id='other1' class='group1' value='". $row['o1'] ."' disabled></td>";
   }
   else if($row['prodname']  == 'Other2'){
    echo "
    <td> <input type='text' id='other2' class='group1' value='". $row['o2'] ."' disabled></td>";
    }
    else if($row['prodname']  == 'Other3'){
      echo "
      <td> <input type='text' id='other3' class='group1' value='". $row['o3'] ."' disabled></td>";
    }
  
       if($row['prodname']  == 'Other4'){
        echo "
        <td> <input type='text' id='other4' class='group1' value='". $row['o4'] ."' disabled></td>";
        }
        else if($row['prodname']  == 'Other5'){
         echo "
         <td> <input type='text' id='other5' class='group1' value='". $row['o5'] ."' disabled></td>";
         }
         else if($row['prodname']  == 'Other6'){
           echo "
           <td> <input type='text' id='other6' class='group1' value='". $row['o6'] ."' disabled></td>";
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
<input type='hidden' id='rowid' value='".$rowid."'>
<input type='hidden' id='justID' value='".$justID."'>
";





$addsql = "
select product_ID , product  from productlist where product_id not in(19, 21, 23)
and active = 1
 order by [order] asc

";



  $stmtadd = $this->sdb->prepare($addsql);
if (!$stmtadd) {
  echo "\nPDO::errorInfo():\n";
  print_r($this->sdb->errorInfo());
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

foreach($adddata as $op) {




echo "
    <option value='".$op['product_ID']."'>".$op['product']."</option>

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





  $(document).ready(function(){


    var updateall = 0;


   $('.adduser').hide();
   $('#subuser').hide();
   $('#compa').hide();


   $('#roles').on('change', function(){
  if($('#roles option:selected').val() == 3 ||$('#roles option:selected').val() == 4){

    $('#compa').show();
   }else{
    $('#compa').hide();
   }

   });


   $('#apsupdate').hide();

   $('#apsedit').on('click', function(){

    $('#apsupdate').show();
    $('#apsedit').hide();
   });

 






  $('#subuser').on('click', function(){


  var name = $('#name_ed').val();
  var namesp = name.split(" ");
  var firstname = namesp[0];
  var lastname = namesp[1];
  var email = $('#emailuser option:selected').val();
  var role_id = $('#roles option:selected').val();
  var customer_id = $('#companies option:selected').val();
  
  username = email;

  if(role_id == 0 || customer_id == 0){

    alert('please select role type');
  }else{


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




      if($('#portalstat').text() == 'Customer has portal access'){

       $('#addport').hide();

      }



      $('#addport').on('click', function(){

       // alert('hello');
        $('#addport').hide();

        $('#subuser').show();
        $('.adduser').show();

      });


   // $("#lorrytype").hide();
   $('#othername').hide();

    $(".btnbuttondoneID").hide();
    $(".buttonIDed").click(function(){
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





    $(".btnbuttondoneID").click(function(){

   



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
      var reqby = <?php echo "'".str_replace('@stonegroup.co.uk', '', $_SESSION['user']['username']), '', ''."'" ?>;
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

      if($('#survdead2').val() === '01-01-70 00:00:00'){

        $('#survdead2').val('');

      }
      




      if(gdprselect.length <= 0){

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

 
 if($("#ntb #row"+value+" #v").prop("checked") == true){
          assettick = 1;
             
               
           }
          else if($("#ntb #row"+value+" #v").prop("checked") == false){
            assettick = 0;

          }
                           if($("#ntb #row"+value+" #w").prop("checked") == true){
                wipetick = 1;
             
               
           }
          else if($("#ntb #row"+value+" #w").prop("checked") == false){
            wipetick = 0;
           
               
            }

        var id =$("#ntb #row"+value).find(".idp").val();
        var working =$("#ntb #row"+value).find(".workin").val();
        //var notworking =$("#ntb #row"+value).find(".notworkin").val();

       
        //alert(id);
        work.push({id : id, working : working, asset : assettick, wipe : wipetick});
         reqlines = JSON.stringify(work);  
      console.log(reqlines);

          });

    $.ajax({
    type: "POST",
        url: "/RS/updatadata/",
        data:{
          other1name : other1name,
          other2name : other2name,
          other3name : other3name,
          other4name : other4name,
          other5name : other5name,
          other6name : other6name,
          reqlines : reqlines,
          email : email,
          name : name,
          address1 : address1,
          address2 : address2,
          address3 : address3,
          tel : tel,
          consi : consi,
          ram : ram,
          consw : consw,
          condep : condep,
          conam : conam,
          ordnum : ordnum,
          manord : manord,
          pos : pos,
          biopass : biopass,
          colinstruct : colinstruct,
          colldate : colldate,
          upnotes : upnotes,
          reqby : reqby,
          reqid : reqid,
          twn : twn,
          postcode : postcode,
          custphone : custphone, 
          reqstat : reqstat,
          reqowner : reqowner,
          dtype : dtype,
          previousorders : previousorders,
          approved : approved,
          process : process,
          gdprselect : gdprselect,
          deadline : deadline,
          emailsent : emailsent,
          avoid : avoid,
          emailsentdate : emailsentdate
        },
  
        success: function(data){
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


    


 $("#buttondelID, btndel").click(function(){
//alert("hello");
var del = 1;
var rid = $("#justID").val();


  $.ajax({
  type: "POST",
  url: "/RS/delrequest/",
  data: {
    del : del,
    rid : rid
  },
  success: function(data) {
   // alert(data);
   // alert("success!");
    //alert("con7: " + con7 + "rowid" + rowid);
   // alert(rid);
  }
    });

    });
    var lo = '';
    var flag = 0
    
    if($('#lorry').prop("checked") == true){
               // alert("Checkbox is checked.");
                 flag = 1;
                lo = 'Yes';
                //alert(lo);
                $("#lorrytype").show();
            }
            if($('#lorry').prop("checked") == false){
               // alert("Checkbox is unchecked.");
                lo = 'No';
                 flag = 0;
               // alert(lo);
                //$("#lorrytype").hide();
            }
       


    $('#ntb input[type="checkbox"]').click(function(){

            if($('#lorry').prop("checked") == true){
               //6 alert("Checkbox is checked.");
                 flag = 1;
                lo = 'Yes';
               // alert(lo);
                $("#lorrytype").show();
            }
            else if($('#lorry').prop("checked") == false){
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


    $('#apsedit').on('click', function(){

      clicks++;

      if(clicks == 1){
      $("input.group2").attr('disabled', false);
      $("input.group1").attr('disabled', false);
      $("select.group1").attr('disabled', false);
       $("textarea.group2").attr('disabled', false);
      }if(clicks == 2){

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

        var  deadline = $('#survdead2').val();

      //   alert(deadline);
      // }

//alert(deadline);
     
      

      $.ajax({
        type: "POST",
        url: "/RS/UpdateAPS/",
        data: {
          bookingstatus :bookingstatus,
          drivername : drivername,
          vehicreg : vehicreg,
          constat : constat,
          connotes : connotes,
          confemail : confemail,
          sursent : sursent,
          surcomp : surcomp,
          sentby : sentby,
          jobno : jobno,
          accup : accup,
          a_status : a_status,
          p_status : p_status,
          s_status : s_status,
          notes_status : notes_status,
          rowid : rowid,
          lo : lo,
          flag : flag,
          owner : owner,
          deadline : deadline,
          help : help,
          early : early,
          parking : parking,
          lift : lift, 
          ground : ground,
          steps : steps,
          twoman : twoman

        },
        success: function(data) {
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

function reload(){
      if(updateall == 2){

      location.reload(true);
    }
}


  });

 




</script>

<div id="result"></div>


