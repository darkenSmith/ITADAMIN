
<Style>


#load{
   margin-left:30em;

}

#btn-group{

  padding-left:50px;
}

#load p{
  position:absolute;

}
.loader {
  position:absolute;
    border: 16px solid #f3f3f3; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

#company {

  margin-left:50px;

    margin-right:50px;

    padding: 50px;
}

#company tr {

margin-top:10px;
margin-bottom:10px;

}
.nav-item{

}
#addnew{
  margin-left:50px;

}

/* for headerfilter */

table, th, td {
      /* border: 1px solid black; */
    }

    #company  th {
    background-color: #eee;
    border-top: 1px solid #fff;
}
#company  th:hover {
    background-color: #ccc;
}
#company  th {
    background-color: #fff;
}

#company  th:hover {
    cursor: pointer;
}

/* Sortable tables */
table.sortable thead {
    background-color:#eee;
    color:#666666;
    font-weight: bold;
    cursor: default;
}


</style>







<script>

function ready() {

  var del = 0;
  $(".loader").remove();
 $("#load").remove();

 $("#load").stop( true, true ).fadeOut();
 $(".loader").stop( true, true ).fadeOut();


  // image is not yet loaded (unless was cached), so the size is 0x0
  
}

document.addEventListener("DOMContentLoaded", ready);




  $(document).ready(function(){
    $("a.tooltipLink").tooltip();
        var d = 22;

     document.getElementById("ref").onclick = function () {
        location.href = "/RS/companynote";
    };
//     $(".loader").remove();
//  $("#load").remove();

    var i = 0;


    var arrcomp = [];
    var idarray = []
    var delid =0;

  

   

      $('#company .trstuff .checkboxes').on('click', function(){


if ($(this).prop('checked')){
req = $(this).val();
           // alert(req);

            idarray.push(req);


}else{

  req2 = $(this).val();
  var index = idarray.indexOf(req2);
    if (index > -1) {

      idarray.splice(index, 1);

    }
 // alert('unhit');
}


  console.log(idarray);
});


$('#tstbtn').click(function(){

  ////alert('hit');

  var del = 1;
  var type = 'multi';

  $.ajax({
  type: "POST",
  url: "/RS/companyupdate/",
  data: {
    del : del,
    idarray : idarray,
    type : type

  },
  success: function(data) {
    //alert(data);
    //alert("success!");
    ////alert("con7: " + con7 + "rowid" + rowid);
   // //alert(rid);
   location.reload(true);
  }

        });

});





  

 



    $("#company .trstuff #btnns #del").click(function(){

     

        var id = $(this).closest("tr").find("#compid").html();

        del = 1;

        var type = 'delete';



       $.ajax({
  type: "POST",
  url: "/RS/companyupdate/",
  data: {
    del : del,
    id : id,
    type : type

  },
  success: function(data) {
    //alert(data);
    //alert("success!");
    ////alert("con7: " + con7 + "rowid" + rowid);
   // //alert(rid);
   location.reload(true);
  }

        });




   });
    

   
   $("#company .trstuff #btnns #edi").click(function(){
//alert("hello");
   $("#company .trstuff #btnns #edi").prop("disabled", true);
      $(this).closest("tr").find(".editgroup").prop("disabled", false);
      
    
      $(this).closest("tr").find("#btnns #sav").css('visibility', 'visible');

  $(this).hide();
      


   });


  $("#sub").click(function(){
    //alert("hello");
    $('#addnew').removeClass('hidden');
    $("#sub").hide();

  });

  $("#upsub").click(function(){

    var compname = $("#comnamep").val();
    var locat = $("#locat").val();
    var deptpart = $("#deptent option:selected").val();

    //changed owner to grab text.
    var own = $("#own option:selected").text();
    var gdprcon = $("#gdprcon").val();
    var amrstat = $("#amrst").val();
    var rebatestat = $("#rebstat").val();
    var rebatestat2 = $("#rebstat2").val();
    var sector = $("#sec").val();
    var type = $("#typi").val();
    var notes = $("#not").val();
    var prevown = $("#prev").val();
    var dataadd = $("#dadd").val();
    var crmid = $("#crmid").val();
    var cmpid = $("#cmpid").val();
    var letter = $("#lett").val();
    var shrdwith = $("#shrdwith").val();

    //alert("New Entry Added");

    //alert(notes);



      
  $.ajax({
  type: "POST",
  url: "/RS/newcompany/",
  data: {
    compname : compname,
    locat : locat,
    deptpart : deptpart,
    own : own,
    gdprcon : gdprcon, 
    amrstat : amrstat,
    rebatestat : rebatestat,
    sector : sector,
    type : type,
    notes : notes,
    prevown : prevown,
    dataadd : dataadd,
    crmid : crmid,
    cmpid : cmpid,
    letter : letter,
    rebatestat2 : rebatestat2,
    shrdwith : shrdwith
  },
  success: function(data) {
    alert(data);
    //alert("success!");
    ////alert("con7: " + con7 + "rowid" + rowid);
   // //alert(rid);

  }

    });


    $('#addnew').addClass('hidden');
    $("#sub").show();
    location.reload(true);
  });


    $("#company .trstuff #btnns #sav").click(function(){

        $(this).closest("tr").find("#btnns #sav").css('visibility', 'hidden');

     $(this).closest("tr").find("#btnns #edi").show();

      $("#company .trstuff #btnns #edi").prop("disabled", false);
       $(this).closest("tr").find(".editgroup").prop("disabled", true);
     
       crm = $(this).closest("tr").find("#crmnum").val();
       cmp = $(this).closest("tr").find("#cmpnum").val();
      name =  $(this).closest("tr").find("#comname").val();
      dept =  $(this).closest("tr").find("#depname").val();
      loc =  $(this).closest("tr").find("#locname").val();
      owner =  $(this).closest("tr").find("#ownname").val();
      gdpr =  $(this).closest("tr").find("#gdprst").val();
      amr =  $(this).closest("tr").find("#isamr").val();
      reb =  $(this).closest("tr").find("#isamr").val();
      notes =  $(this).closest("tr").find("#notestuff").val();
      shared = $(this).closest("tr").find("#shrw").val();
      report = $(this).closest("tr").find("#isreb").val();
      id =  $(this).closest("tr").find("#compid").text();


      //alert(notes);

    //   //alert(name + dept + owner + gdpr + amr + reb + id);



  $.ajax({
  type: "POST",
  url: "/RS/companyupdate/",
  data: {
    crm : crm,
    cmp : cmp,
    name : name,
    dept : dept,
    notes : notes,
    loc : loc,
    owner : owner,
    gdpr : gdpr, 
    amr : amr,
    reb : reb,
    id : id,
    shared : shared, 
    report : report
  },
  success: function(data) {
    //alert(data);
    //alert("success!");
    ////alert("con7: " + con7 + "rowid" + rowid);
   // //alert(rid);
   location.reload(true);
  }
    });
   


  });
  

         $('#search').keyup(function(){  
              search_table($(this).val());  
         });  
         function search_table(value){  
              $('#company tbody tr').each(function(){  
                   var found = 'false';  
                   $(this).each(function(){  
                        if($(this).text().toLowerCase().indexOf(value.toLowerCase()) >= 0)  
                        {  
                             found = 'true';  
                        }  
                   });  
                   if(found == 'true')  
                   {  
                        $(this).show();  
                   }  
                   else  
                   {  
                        $(this).hide();  
                   }  
              });  
         }  



    $("select.filter").change(function(){
      var selectedCountry = $(".filter option:selected").val();
    });
  });
</script>


 <div class="container">
 <br>
 <Br>
 <br>
 <Br>

    <ul class="nav nav-tabs">
         <li class="nav-item">
        <a class="nav-link" href="/RS/booking/">Booked Collection</a>
      </li>
      <li class="nav-item">
        <a class="nav-link"  href="/RS/arc/">Collected</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="/RS/companynote/">Company Notes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/RS/RGR/">Recycling Goods Receipting </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/RS/rebatepage/">Rebates</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href=".\admin\home\">Back</a>
      </li>
    </ul>
  <br> <br>
  <div id='load'>
    <div class="loader">
    </div>
    <p> loading... </p>
    </div>

    <h1 class="page-header"> Stone Computers: Recycling System <small>test</small></h1>
   

    <hr>
    <br>
    <br>


       <div class="form-group col-xs-12">


    
    <div class="centered"> <label> Search  </label></div><br> 


    <form method='post' action="/RS/companynote/">
      Company Name:<br>
      <input type="text" class="form-control" name="ent" value="">
      <br>
      <br>
    



      postcode:<br>
      <input type="text" class="form-control" name="postcode" value="">
      <br>


      Owner:<br>
      <input type="text" class="form-control" name="owner" value="">
      <br>

        Type:<br>
      <input type="text" class="form-control" name="type" value="">
      <br>

           AMR status:<br>
      <input type="text" class="form-control" name="amrst" value="">
      <br>
      <div hidden align="center">  <label> Filter Search Results <a data-toggle="tooltip" class="tooltipLink" data-original-title="filter's data in visble table.">
  <span class="glyphicon glyphicon-info-sign"></span>
</a></label>
                     <input type="text" name="search" id="search" class="form-control" />  
                </div>
                <br>

      <br>

      <div align="center"> <label>Order By</label></div>
      <select  class="form-control" name="filter">
        <option value="[CompanyName]">Name</option>
        <option value="Location">Location</option>
        <option value="Department">Department</option>
        <option value="Owner">Collection Date</option>
        <option value="GDPR">email</option>
        <option value="is_AMR">AMR</option>
        <option value="Rebate">Rebate</option>
      </select>

      <div>

      <select  class="form-control" name="deptfilter">
        <option value="%">Select Dept</option>

        <?php

 

foreach($depts as $v){

  echo "<option value='".$v['Department']."'>".$v['Department']."</option> ";


}



        ?>
  
      </select>
</div>
      <select  class="form-control" name="ownfilter">
        <option value="%">Select Owner</option>

        <?php

foreach($owners as $v){

  echo "<option>".$v['name']."</option> ";


}    ?>
  
      </select>

      <br>
      <input type="submit" class="btn btn-primary" value="Submit">
      </form>
      </div>
      
      <br> 
      <br>
   
  
  </div>
  <br>
  <br>
  <div id="btn-group">
  <button type='submit' id='sub' CLASS="btn btn-primary"> Add new </button>
  <button type='submit' id='ref' CLASS="btn btn-secondary"> Refresh </button>
  <button type='submit' id='tstbtn' CLASS="btn btn-danger"> Delete </button>


  </div>



  <div id='addnew' class='hidden'>

  <table class='table tb1'>
  <thead>
  <tr>
  <th> Company name </th>
  <th> Location </th>
  <th> Department </th>
  <th> Owner </th>
  <th hidden> GDPR Contract </th>
  <th hidden> AMR Status </th>
  <th> Shared With </th>
  </tr>
  </thead>
  <tbody>
  <tr>
  <td><input type='text' id='comnamep'> </td>
  <td><input type='text' id='locat'> </td>
  <td>
  <select id='deptent'>
 <option value=''> select a department </option>
  <?php
  


  foreach($depts as $dept){

    echo "

  <option value='".$dept['Department']."'>".$dept['Department']."</option>
  ";
  


  }
  
  ?>
  
  </select>
  </td>
  <td>
  <select id='own'> 
  <option selected value=''> Select a Owner </option>
  <?php




foreach($owners as $owner){

  echo "

<option value='".$owner['name']."'>".$owner['name']."</option>
";



}
  
  ?>
  </select>
  </td>
  <td hidden><input type='text' id='gdprcon'> </td>
  <td hidden><input type='text' id='amrst'> </td>
  <td><input type='text' id='shrdwith'> </td>
  </tr>
  </tbody>
  </table>
  <br>



  <table class='table  tb2'>
  <thead>
  <tr>
  <th> Report:Rebate </th>
  <th hidden> Report:AMR </th>
  <th> Notes </th>
 
 
  <th hidden> CRM </th>
  <th hidden> CMP </th>
  </tr>
  </thead>
  <tbody>
  <tr>
  <td><input type='text' id='rebstat'> </td>
  <td hidden><input type='text' id='rebstat2'> </td>
  <td><input type='text' id='not'></td>
  <td hidden><input type="date" class="form-control" id="dadd" value="" ></td>
  <!----<td><input type='text' id='dadd'> </td>--->
  <td hidden><input type='text' id='crmid'> </td>
  <td hidden><input type='text' id='cmpid'> </td>

  </tr>
  </tbody>
  </table>
  <br>

  <button type='submit' id='upsub' class="btn btn-primary"> submit </button>
  </div>
  <br>





  <?php 


  echo"
  <table class='table table-hover sortable table' id='company'>
  <thead>
  <tr>
  <th>  </th>
  <th>  </th>
  <th hidden> CMP </th>
  <th hidden> CRM </th>
  <th> Company name </th>
  <th> Location </th>
  <th> Department </th>
  <th> Owner </th>
  <th > Shared With </th>
  <th hidden> GDPR Contract</th>
  <th hidden> AMR Status </th>
  <th> Report </th>
  <th hidden> Sector </th>
  <th hidden> Type </th>
  <th> Notes </th>
  <th hidden> Date Added </th>
  <th hidden> Letter </th>
  <th hidden> Comp ID </th>
  </tr>
  </thead> 
  ";

  //include("companiesdata.php");

  echo $table;


  ?>







 

    

