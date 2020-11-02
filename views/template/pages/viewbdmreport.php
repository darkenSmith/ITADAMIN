

 <script src="/inc/js/bootstrap.js"></script>

 <script>
 jQuery(document).ready(function($) {
  $("a.tooltipLink").tooltip();
  $('#search').keyup(function(){  
                search_table($(this).val());  
           });  
           function search_table(value){  
                $('.table tbody tr').each(function(){  
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

 });

           </script>

<div class="bdm">


<h1 class="page-header"> Stone Computers: BDM view <small>front screen</small></h1>
 
  

 <form method='post' action='/RS/BDMview/'>
<select name='own'>
    <option id='de'> Please select owners</option>

    <?php foreach($listowners as $owners){

        echo "<option id ='".$owners['id']."'>".$owners['name']."</option>";
    }  ?>
   
</select>
<select name="filterstatus">
                    <option selected value="and (laststatus not like 'On-Hold' or  laststatus in('Request', 'confirmed', 'booked', 'cancelled')) ">All</option>
                    <option value="and been_collected = 1 AND collection_date IS NOT NULL and confirmed = 1 and( laststatus  like 'Confirmed')">Confirmed</option>
            <option value="and collection_date IS NOT NULL and confirmed = 0 and ( laststatus = 'booked')">Booked</option>
            <option value="and (been_collected = 0 or been_collected is null ) AND collection_date IS NULL and (laststatus not like 'unbooked' and laststatus not like 'On-Hold')">Requests</option>
            <option value="and (been_collected = 0 or been_collected is null ) AND collection_date IS NULL and (laststatus like 'unbooked')">Unbooked</option>
            <option value="and laststatus like 'On-Hold'">On-Hold</option>
          </select>

<input type="submit" class='btn btn-sucess' value="Submit">
<form>
<div >  <label> Search         <a data-toggle="tooltip" class="tooltipLink" data-original-title="search  any field. just type what your looking for.">
  <span class="glyphicon glyphicon-info-sign"></span>
</a></label> <input type="text" name="search" id="search" class="form-control searchbar" />  
 </div>
<?php


///var_dump($bdmviewreport);


$headers = array(

    'requestdate' => 'reqdate',
    'req' => 'RequestID',
    'ordernum' => 'Ordernum',
    'gdpr' => 'GDPR',
    'stat' => 'Status',
    'typ' => 'vehicle',
    'coldate'=> 'Collection date',
    'name' => 'Customer',
    'town' => ' Town',
    'postcod' => 'PostCode',
    'totalweight' => 'Total Weight',
    'totalunits' => 'Total Units',
    'qualify' => 'Qualifying',
    'charge' => 'Charge',
    'instructions' => 'Instructions',
    'Requestcoldate' => 'Access times',
    'approved' => 'Approved',
    'process' => 'Process',
    'survey_deadline' => 'Survey Deadline',
    'surveycomp' => 'Survey Complete', 
    'prev' => 'Prev Date',
    'owner' => 'owners',
    'contact' => 'Contact name',
    'tel'=> 'telphone',
    'email' => 'Email',
    

);

  $content = "<table class='sortable table table-striped'><tr>";
  

    foreach($headers as $h){

    $content .= "<th>".$h."</th>";

        }
  $content .= "</tr>";

   foreach($bdmviewreport as $val){


 

  //echo  var_dump($val);
$prev = $val['prev'];
//   $suveycomp = $p['scomp'];
//   $owner = $p['Owner'];
//   $dept = $p['dept'];
//   $units = $p['totalunits'];
//   $wgt = $val['totalweight'];
//   $comis = $val['commisionable']; 
//   $approved = $val['approved']; 
//   $process = $val['process']; 
//   $order = $val['ordern']; 
//   $Stat = $val['Stat'];
//   $requestdate = $val['requestdate'];
//   $coldate = $val['coldate'];
//   $name = $val['name'];
//   $email = $val['email'];
//   $contact = $val['contact'];
//   $tel = $val['tel'];
//   $typ = $val['typ'];
//   $upnotes = $val['upnotes'];
//   $county = $val['county'];
//   $charge = $val['charge'];
//   $survey_deadline = $val['survey_deadline'];
//   $instructions = $val['instructions'];
//   $request_col_date = $val['request_col_date'];
//   $gdpr = $val['gdpr'];
//   $postcode = $val['postcode'];

$charge = 'yes';


if($val['charge'] == 0){
  $charge = '-';

}


  $content .= "<tr><td>".$val['requestdate']."</td>";
  $content .= "<td id='ids' name='reqid'><a href='/RS/bdmdetail?reqid=".$val["id"]."''>".$val['id']."</a></td>";
  $content .= "<td>".$val['ordern']."</td>";
  $content .= "<td>".$val['gdpr']."</td>";
  $content .= "<td>".$val['Stat']."</td>";
  $content .= "<td>".$val['typ']."</td>";
  $content .= "<td>".$val['coldate']."</td>";
  $content .= "<td>".$val['name']."</td>";
  $content .= "<td>".$val['town']."</td>";
  $content .= "<td>".$val['postcode']."</td>";
  $content .= "<td>".$val['totalweight']."</td>";
  $content .= "<td>".$val['totalunits']."</td>";
  $content .= "<td>".$val['commisionable']."</td>";
  $content .= "<td>".$charge."</td>";
  $content .= "<td><textarea row='4' col='6'>".$val['instructions']."</textarea></td>";
  $content .= "<td><textarea row='4' col='6'>".$val['request_col_date']."</textarea></td>";
  $content .= "<td><textarea row='4' col='6'>".$val['approved']."</textarea></td>";
  $content .= "<td><textarea row='4' col='6'>".$val['process']."</textarea></td>";
  $content .= "<td>".$val['survey_deadline']."</td>";
  $content .= "<td>".$val['scomp']."</td>";
  $content .= "<td><textarea row='4' col='6'>".$val['prev']."</textarea></td>";
  $content .= "<td>".$val['Owner']."</td>";
  $content .= "<td>".$val['contact']."</td>";
  $content .= "<td>".$val['tel']."</td>";
  $content .= "<td>".$val['email']."</td>";




   }
    $content .= "</tr></table>";

   echo $content;




//////////////////////////////////////////////////////////////






?>



<br> 


</div>


