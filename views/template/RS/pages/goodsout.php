
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
  $(document).ready(function($) {


     $('#loadclose').hide();


     $('#close').on('click', function(){

        $('#adding').hide();
        $('#loadclose').show();
        $('#add').hide();



     });

     $('#btncloseload').on('click', function(){

         var loadid = $('#loadnumid').val();
         var despatchdate = $('#desp').val();
         var company = $('#company').val();


         
      $.ajax({
              url: "/RS/closeload/", // Url to which the request is send
              type: "POST",             // Type of request to be send, called as method
              data: {
               loadid : loadid,
               despatchdate : despatchdate,
               company : company

               // notworkingqty : notworkingqty,
           
              }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    // To send DOMDocument or non processed data file it is set to false

              success: function(data){   // A function to be called if request succeeds
          
             alert('success');
             console.log(data);
                
             location.reload("http://recwebtest.stonegroup.co.uk/RS/Goodsout/");
                // $("#image_preview").html(data);
              }



     });

     
   });




     $('#add').on('click', function(){


        var loadnum = $('#loadnum').val();
        var wgt = $('#wgt').val();
        var Type = $('#Type').val();
        var Supplier = $('#Supplier').val();
        var palletnum = $('#Pallet-num').val();


   


      $.ajax({
              url: "/RS/Goodsinadd/", // Url to which the request is send
              type: "POST",             // Type of request to be send, called as method
              data: {
               loadnum : loadnum,
               wgt : wgt,
               Type : Type,
               Supplier : Supplier,
               palletnum : palletnum
               // notworkingqty : notworkingqty,
           
              }, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
                    // To send DOMDocument or non processed data file it is set to false

              success: function(data){   // A function to be called if request succeeds
          
             alert('success');
             console.log(data);
                
             location.reload("http://recwebtest.stonegroup.co.uk/RS/Goodsout/");
                // $("#image_preview").html(data);
              }
            });

     });

    

  });

  </script>
  
  <body>

  <div class="container">

<?php




echo "<br><br><table class='table'> 
<thead> 
<tr> 
<th> Loadnum </th>
<th> Wgt </th>
<th> Type </th>
<th> Supplier </th>
<th> Pallet-num </th>
</tr>
</thead>
<tbody>";

foreach($totalloads as $pallet){

  echo "
  <tr> 
  <td> ".$pallet['loadnum']." </td>
  <td> ".$pallet['wgt']." </td>
  <td> ".$pallet['type']." </td>
  <td> ".$pallet['supplier']." </td>
  <td> ".$pallet['pallets']." </td>

  </tr>
  
  ";
}
echo "

   </tbody> 
   </table>
";
   echo "
   <table class='table'>
   <thead>
   <tr>";
foreach(	$loadlist as $loaddet){

echo "   
  
<th> ".$loaddet['loadnum']." </th>
";


}

echo "</tr>
   <tbody>
   <tr>";


   foreach(	$loadlist as $loaddet){

      echo "   
        
      <td> ".$loaddet['totalwgt']." </td>
      ";
      
      
      }


      echo "</tr><tr>";

      foreach(	$loadlist as $loaddet){
         echo "   
        
         <td> ".$loaddet['company']." </td>
         ";

      }


      echo "</tr><tr>";

      foreach(	$loadlist as $loaddet){
         echo "   
        
         <td> ".$loaddet['despatch_date']." </td>
         ";

      }


      echo "</tr><tr>";

      foreach(	$loadlist as $loaddet){
         echo "   
        
         <td> ".$loaddet['staus']." </td>
         ";

      }

echo "</tr>
      </tbody> 
      </table>"


//////////////////////////////////////////////////////////////






?>



<br> 

<table class='table' id='adding'>
<thead> 
<tr> 
<th> Loadnum </th>
<th> Wgt </th>
<th> Type </th>
<th> Supplier </th>
<th> Pallet-num </th>
</tr>
</thead>
<tbody>
<tr>
<td> <input type='number' id='loadnum'/></td>
<td> <input type='number' id='wgt'/></td>
<td> <input type='text' id='Type'/></td>
<td> <input type='text' id='Supplier'/></td>
<td> <input type='number' id='Pallet-num'/></td>
</tr>
</tbody>
</thead>
</table>
<input type='submit' id='add' value='Add' class='btn btn-success'/>
<input type='submit' id='close' value='Close Load' class='btn btn-warning'/>



<table class='table' id='loadclose'>
<thead> 
<tr> 
<th> Loadnum </th>
<th> Company </th>
<th> despatch Date </th>
</tr>
</thead>
<tbody>
<tr>
<td> <input type='number' id='loadnumid'/></td>
<td> <input type='text' id='company'/></td>
<td> <input type='date' id='desp'/></td>
<td> <input type='button' value='submit' class='btn btn-success' id='btncloseload'/>
</tr>
</tbody>
</thead>
</table>


</div>


</body> 
</html>