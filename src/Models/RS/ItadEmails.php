<?php

namespace App\Models\RS;

use App\Helpers\Config;
use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use SendGrid;
use SendGrid\Mail\Mail;

/**
 * Class ItadEmails
 * @package App\Models
 */
class ItadEmails extends AbstractModel
{
    public $response;
    private $emailConfig;

    /**
     * ItadEmails constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        $this->emailConfig = Config::getInstance()->get('email');

        parent::__construct();
    }


    public function sendMail()
    {
        Logger::getInstance("itadEmail.log")->debug(
            'email',
            [
                'line' => __LINE__,
                'start' => 'true'
            ]
        );
        $emailadd = $_POST['emailaddress'];
        $lorrytype = $_POST['loorytype'];
        $rid = $_POST['reqid'];
        $datestuff = $_POST['date'];
        $deadlinedate = $_POST['deadlinedateFomat'];
        $manualtime = $_POST['manualtime'];
        $manual = $_POST['manual'];

        $arry = [];
        $arry = explode(" ", $emailadd);
        $fullpath2 = PROJECT_DIR . "assets/attachments/ITADCOVID19terms.pdf";
        $yes = 'Yes';
        $path2 = 'https://itadadmin.stonegroup.co.uk/Conf/thankyoucust/' . $rid;
        $sTime = '';
        $test = strtotime(str_replace('/', '-', $datestuff));
        $name = str_replace('@stonegroup.co.uk', '', $_SESSION['user']['username']);
        $timin = date("y-m-d", $test);
        $emailtime = date("d-m-y", $test);
        $now = time();
        $nowtime = date("Y-m-d h:m", $now);
        $email_1 = '';
        $string = '@stonegroup.co.uk';

        if (isset($_POST['owner'])) {
            if ($_POST['owner'] != 'Sales' || $_POST['owner'] != 'Recycling' || $_POST['owner'] != null) {
                $owner = $_POST['owner'];
                $email_1 = rtrim($owner) . $string;
            } elseif ($_POST['owner'] == 'ITAD') {
                $email_1 = 'StoneITAD' . $string;
            } else {
                $email_1 = 'alex.smith' . $string;
            }
        } else {
            $owner = '';
            $email_1 = 'alex.smith' . $string;
        }

        if ($email_1 == 'Recycling@stonegroup.co.uk') {
            $email_1 = 'alex.smith' . $string;
        } elseif ($email_1 == 'recycling@stonegroup.co.uk') {
            $email_1 = 'alex.smith' . $string;
        } elseif ($email_1 == 'sales@stonegroup.co.uk') {
            $email_1 = 'alex.smith' . $string;
        } elseif ($email_1 == 'Sales@stonegroup.co.uk') {
            $email_1 = 'alex.smith' . $string;
        }

        if ($email_1 == 'ITAD@stonegroup.co.uk') {
            $email_1 = 'StoneITAD@stonegroup.co.uk';
        }

        Logger::getInstance("itadEmail.log")->debug(
            'email',
            [
                'line' => __LINE__,
                'lorrytype' => $lorrytype,
                'manual' => $manual,
                'rid' => $rid,
            ]
        );

        if ($lorrytype == 'b7van' || $lorrytype == 'b72van' || $lorrytype == 'b77lorry' || $lorrytype == 'b714lorry' || $lorrytype == 'b7GB') {
            if ($manual == 0) {
                $sqal2 = " UPDATE Booked_Collections
          SET LorryType = '" . $lorrytype . "',
            Lorry = '" . $yes . "',
            [sentby] = '" . $name . "',
            surveysnet_date = '" . $nowtime . "',
            [SurveySent] = 'Yes',
            email_sent = 'Yes',
            [SurveyComplete] = '',
            emailsentdate = GETDATE(),
            LorryFlag = 1
          WHERE
            RequestID ='" . $rid . "'
            
            
            UPDATE Booked_Collections
          SET 
          survey_deadline = 
          (select  dateadd(hour, 12 , [dbo].[AddBusinessDays](BookedCollectDate, -4))survey_deadline from Booked_Collections where Requestid	='" . $rid . "')
        
          WHERE
            RequestID ='" . $rid . "'";
            } else {
                $sqal2 = " UPDATE Booked_Collections
            SET LorryType = '" . $lorrytype . "',
              Lorry = '" . $yes . "',
              [sentby] = '" . $name . "',
              surveysnet_date = '" . $nowtime . "',
              [SurveySent] = 'Yes',
              email_sent = 'Yes',
              [SurveyComplete] = ' ',
              emailsentdate = GETDATE(),
              LorryFlag = 1
            WHERE
              RequestID ='" . $rid . "'";
            }
            $stmtup2 = $this->sdb->prepare($sqal2);
            $stmtup2->execute();
        } else {
            $sqlcheck = "
          select  (case when [SurveySent] like '%yes%' then 'yes' else 'no' end) ss from Booked_Collections as rt where 
          [SurveySent] like 'yes%' and requestid =  '" . $rid . "'";

            $stmtchk = $this->sdb->prepare($sqlcheck);
            $stmtchk->execute();
            $datack = $stmtchk->fetch(\PDO::FETCH_ASSOC);

            if ($datack['ss'] !== 'yes') {
                $sqal = " UPDATE Booked_Collections
              SET LorryType = '" . $lorrytype . "',
                Lorry = '" . $yes . "',
                [sentby] = '" . $name . "',
                email_sent = 'Yes',
                --[Survey Sent] = 'REG',
                [SurveyComplete] = '',
                emailsentdate = GETDATE(),
                LorryFlag = 1
              WHERE
                RequestID ='" . $rid . "'
                
                UPDATE Booked_Collections
          SET 
          survey_deadline = null
          WHERE
            RequestID ='" . $rid . "'
                ";
            } else {
                $sqal = " UPDATE Booked_Collections
            SET LorryType = '" . $lorrytype . "',
              Lorry = '" . $yes . "',
              [SurveySent] = 'yes',
              [sentby] = '" . $name . "',
              emailsentdate = GETDATE(),
              LorryFlag = 1
            WHERE
              RequestID ='" . $rid . "'";
            }

            $stmtup = $this->sdb->prepare($sqal);
            $stmtup->execute();
        }

        $sqaldead = "(select [dbo].[AddBusinessDays](BookedCollectDate, -4)   as survey_deadline  from Booked_Collections where
         RequestID ='" . $rid . "')";

        $stmtdead = $this->sdb->prepare($sqaldead);
        $stmtdead->execute();
        $datatime = $stmtdead->fetch(\PDO::FETCH_ASSOC);

        if ($manual == 1) {
            $d = str_replace('-', '/', $manualtime);
            $newdeadtime = $d;
            $newDate = date("Y-m-d h:i:s", strtotime($manualtime));

            $upbooking = "
         SET LANGUAGE british
        declare @date datetime 
        set @date = '" . $manualtime . "'
         
         update Booked_Collections
         set survey_deadline = (select format(@date, 'dd-MM-yyyy hh:mm:ss') )
         where RequestID = '" . $rid . "'
         ";

            Logger::getInstance("itadEmail.log")->debug(
                'email',
                [
                    'line' => __LINE__,
                    'upbooking' => $upbooking
                ]
            );

            $stmtbook = $this->sdb->prepare($upbooking);
            $stmtbook->execute();
        } else {
            $datedead = $datatime['survey_deadline'];
            $newdeadtime = date("d/m/y h:i:s", strtotime($datedead));
        }

        Logger::getInstance("itadEmail.log")->debug(
            'email',
            [
                'line' => __LINE__,
                'newdeadtime' => $newdeadtime
            ]
        );

        $newdeadtimetrim = substr($newdeadtime, 0, -3);

        Logger::getInstance("itadEmail.log")->debug(
            'email',
            [
                'line' => __LINE__,
                'emailadd' => $emailadd,
                'arr' => $arry
            ]
        );

        $start = 1;
        //??
        if ($start == 1) {
            $sendgridConfig = $this->emailConfig['sendgrid'];
            try {
                $mail = new Mail();
                $mail->setFrom($_SESSION['user']['username'], 'Stone Computers ITAD System');
                $mail->setSubject("Booked collection confirmation - Request-ID:" . $rid);

                foreach ($arry as $val) {
                    $mail->addTo(rtrim($val));
                }
                $mail->addTo(rtrim($email_1));
                $mail->addTo($_SESSION['user']['username']);

                $att1 = new \SendGrid\Mail\Attachment();
                $att1->setContent(file_get_contents($fullpath2));
                $att1->setType("application/pdf");
                $att1->setFilename("ITADCOVID19terms.pdf");
                $att1->setDisposition("attachment");
                $mail->addAttachment($att1);

                $emailtxt = "";

                $emailHTMLHead = $this->getEmailHead();
                $emailHTMLFooter = $this->getEmailFooter();

                if ($lorrytype == 'sbvan') {
                    $emailtxt = $emailHTMLHead . '
                       <table border="0" cellpadding="0" cellspacing="0" class="body">
                         <tr>
                           <td>&nbsp;</td>
                           <td class="container">
                             <div class="content">
          
                               <!-- START CENTERED WHITE CONTAINER -->
                               <span class="preheader">Stone Recycling Booking</span>
                               <table class="main">
          
                                 <!-- START MAIN CONTENT AREA -->
                                 <tr>
                                   <td class="wrapper">
                                     <table border="0" cellpadding="0" cellspacing="0">
                                       <tr>
                                         <td>
                                           <p>Hi,</p>
                                           <p>You have recently requested a collection for your unwanted IT equipment, 
                                           we have a <strong>Van</strong> in your area on  <strong>' . $timin . '</strong>, please  <a href="' . $path2 . '" target="_blank">Click Here</a> to confirm asap if this is suitable? </p>
                                         
                                           <p> Please note the <strong>Dimensions of the van (H:2.8m x L:7.5m x W:2.5m) and the max Capacity (1 Tonne).</strong> Please advise if you think this will cause any access issues. 
                                           Our Vans do not have tail lifts so please bear this in mind if you have any <strong>2 Man Lift</strong> Items and advise if there will be help on site. </p>
                                           
                                   <p>
                                           Please note that any Collections that are cancelled by the customer within 48 hours/on the day of the collection, 
                                           or are Failed as a result of incorrect or withheld information, may be charged to the customer.
                                            </p>
          
          
                                           </td>
                                           <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                             <tbody>
                                               <tr>
                                                 <td align="left">
                                                   <table border="0" cellpadding="0" cellspacing="0">
                                                   
                                                   <tbody>
                                                   <tr>
                                                    <p> If you have any further  questions please contact:<a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a>.</p>
                                                   </tr>
                                                 </tbody>
                                                   </table>
                                                 </td>
                                               </tr>
                                             </tbody>
                                           </table>
                                           <p>Thank you for using Stone.</p>
                                         </td>
                                       </tr>
                                     </table>
                                   </td>
                                 </tr>
          
                               <!-- END MAIN CONTENT AREA -->
                               </table>
          
                               <!-- START FOOTER -->
                               <div class="footer">
                                 <table border="0" cellpadding="0" cellspacing="0">
                                   <tr>
                                     <td class="content-block">
                                       <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                        
                                     </td>
                                   </tr>
                                   <tr>
                                     <td class="content-block powered-by">
                                       Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                     </td>
                                   </tr>
                                 </table>
                               </div>
                               <!-- END FOOTER -->
          
                             <!-- END CENTERED WHITE CONTAINER -->
                             </div>
                           </td>
                           <td>&nbsp;</td>
                         </tr>
                       </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'sb2van') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                   <tr>
                                     <td>&nbsp;</td>
                                     <td class="container">
                                       <div class="content">
                    
                                         <!-- START CENTERED WHITE CONTAINER -->
                                         <span class="preheader">Stone Recycling Booking</span>
                                         <table class="main">
                    
                                           <!-- START MAIN CONTENT AREA -->
                                           <tr>
                                             <td class="wrapper">
                                               <table border="0" cellpadding="0" cellspacing="0">
                                                 <tr>
                                                   <td>
                                                     <p>Hi,</p>
                                                     <p>You have recently requested a collection for your unwanted IT equipment, 
                                                     we have <strong>2 Vans</strong> in your area on  <strong>' . $timin . '</strong>, please  <a href="' . $path2 . '" target="_blank">Click Here</a> to confirm asap if this is suitable? </p>
                                                   
                                                     <p> Please note the <strong>Dimensions of the van (H:2.8m x L:7.5m x W:2.5m) and the max Capacity (1 Tonne each).</strong> Please advise if you think this will cause any access issues. 
                                                     Our Vans do not have tail lifts so please bear this in mind if you have any <strong>2 Man Lift</strong> Items and advise if there will be help on site. </p>
                                                     
                                                      <p>
                                                     Please note that any Collections that are cancelled by the customer within 48 hours/on the day of the collection, 
                                                     or are Failed as a result of incorrect or withheld information, may be charged to the customer.
                                                      </p>
                    
                    
                                                     </td>
                                                     <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                       <tbody>
                                                         <tr>
                                                           <td align="left">
                                                             <table border="0" cellpadding="0" cellspacing="0">
                                                             
                                                             <tbody>
                                                             <tr>
                                                              <p> If you have any further  questions please contact:<a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a>.</p>
                                                             </tr>
                                                           </tbody>
                                                             </table>
                                                           </td>
                                                         </tr>
                                                       </tbody>
                                                     </table>
                                                     <p>Thank you for using Stone.</p>
                                                   </td>
                                                 </tr>
                                               </table>
                                             </td>
                                           </tr>
                    
                                         <!-- END MAIN CONTENT AREA -->
                                         </table>
                    
                                         <!-- START FOOTER -->
                                         <div class="footer">
                                           <table border="0" cellpadding="0" cellspacing="0">
                                             <tr>
                                               <td class="content-block">
                                                 <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                  
                                               </td>
                                             </tr>
                                             <tr>
                                               <td class="content-block powered-by">
                                                 Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                               </td>
                                             </tr>
                                           </table>
                                         </div>
                                         <!-- END FOOTER -->
                    
                                       <!-- END CENTERED WHITE CONTAINER -->
                                       </div>
                                     </td>
                                     <td>&nbsp;</td>
                                   </tr>
                                 </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'sblorry7') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                   <tr>
                                     <td>&nbsp;</td>
                                     <td class="container">
                                       <div class="content">
                    
                                         <!-- START CENTERED WHITE CONTAINER -->
                                         <span class="preheader">Stone Recycling Booking</span>
                                         <table class="main">
                    
                                           <!-- START MAIN CONTENT AREA -->
                                           <tr>
                                             <td class="wrapper">
                                               <table border="0" cellpadding="0" cellspacing="0">
                                                 <tr>
                                                   <td>
                                                     <p>Hi,</p>
                                                     <p>You have recently requested a collection for your unwanted IT equipment, we have a <strong>7.5 Tonne Lorry</strong> in your area on <strong>' . $timin . '</strong>. 
                                                     please <a href="' . $path2 . '" target="_blank">click here</a> to confirm asap if this is suitable? </p>
                                                     
                                                     <p> Please note the <strong>Dimensions of the 7.5 Tonne Lorry (H:4.2m x L:8m x W:3.2m) and the max Capacity (2 Tonne). </strong> Please advise if you think this will cause any access issues.
                                                      Our Lorries have a tail lift. </p>
                                                    
                                                     <p>
                                                     Please note that any Collections that are cancelled by the customer within 48 hours/on the day of the collection, 
                                                     or are Failed as a result of incorrect or withheld information, may be charged to the customer.
                                                      </p>
                    
                    
                                                     </td>
                                                     <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                       <tbody>
                                                         <tr>
                                                           <td align="left">
                                                             <table border="0" cellpadding="0" cellspacing="0">
                                                             <tbody>
                                                             <tr>
                                                             <p> If you have any further  questions please contact: <a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a>.</p>
                                                             </tr>
                                                           </tbody>
                                                             </table>
                                                           </td>
                                                         </tr>
                                                       </tbody>
                                                     </table>
                                                     <p>Thank you for using Stone.</p>
                                                   </td>
                                                 </tr>
                                               </table>
                                             </td>
                                           </tr>
                    
                                         <!-- END MAIN CONTENT AREA -->
                                         </table>
                    
                                         <!-- START FOOTER -->
                                         <div class="footer">
                                           <table border="0" cellpadding="0" cellspacing="0">
                                             <tr>
                                               <td class="content-block">
                                                 <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                               
                                               </td>
                                             </tr>
                                             <tr>
                                               <td class="content-block powered-by">
                                                 Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                               </td>
                                             </tr>
                                           </table>
                                         </div>
                                         <!-- END FOOTER -->
                    
                                       <!-- END CENTERED WHITE CONTAINER -->
                                       </div>
                                     </td>
                                     <td>&nbsp;</td>
                                   </tr>
                                 </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'sblorry14') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                   <tr>
                                     <td>&nbsp;</td>
                                     <td class="container">
                                       <div class="content">
                    
                                         <!-- START CENTERED WHITE CONTAINER -->
                                         <span class="preheader">Stone Recycling Booking</span>
                                         <table class="main">
                    
                                           <!-- START MAIN CONTENT AREA -->
                                           <tr>
                                             <td class="wrapper">
                                               <table border="0" cellpadding="0" cellspacing="0">
                                                 <tr>
                                                   <td>
                                                     <p>Hi,</p>
                                                     <p>You have recently requested a collection for your unwanted IT equipment, we have a <strong>14 Tonne Lorry</strong> in your area on on <strong>' . $timin . '</strong>, 
                                                     please <a href="' . $path2 . '" target="_blank">click here</a> to confirm asap if this is suitable? </p>
                                                  
                                                     <p> Please note the <strong>Dimensions of the 14 Tonne Lorry(H:4.2m x L:11m x W:3m) and the max Capacity (5-6 Tonne).</strong> Please  advise if you think this will cause any access issues.
                                                      Our Lorries have a tail lift. </p>
                                                     
                                                   
          
                                                    <p>
                                                    Please note that any Collections that are cancelled by the customer within 48 hours/on the day of the collection, 
                                                    or are Failed as a result of incorrect or withheld information, may be charged to the customer.
                                                     </p>
          
                                                     </td>
                                                     <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                       <tbody>
                                                         <tr>
                                                           <td align="left">
                                                             <table border="0" cellpadding="0" cellspacing="0">
                                                             <tbody>
                                                             <tr>
                                                              <p> If you have any further  questions please contact: <a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a>.</p>
                                                             </tr>
                                                           </tbody>
                                                             </table>
                                                           </td>
                                                         </tr>
                                                       </tbody>
                                                     </table>
                                                     <p>Thank you for using Stone.</p>
                                                   </td>
                                                 </tr>
                                               </table>
                                            </td>
                                           </tr>
                    
                                         <!-- END MAIN CONTENT AREA -->
                                         </table>
                    
                                         <!-- START FOOTER -->
                                         <div class="footer">
                                           <table border="0" cellpadding="0" cellspacing="0">
                                             <tr>
                                               <td class="content-block">
                                                 <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                               
                                               </td>
                                             </tr>
                                             <tr>
                                               <td class="content-block powered-by">
                                                 Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                               </td>
                                             </tr>
                                           </table>
                                         </div>
                                         <!-- END FOOTER -->
                    
                                       <!-- END CENTERED WHITE CONTAINER -->
                                       </div>
                                     </td>
                                     <td>&nbsp;</td>
                                  </tr>
                                 </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'GB') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                             <tr>
                                               <td>&nbsp;</td>
                                               <td class="container">
                                                 <div class="content">
                              
                                                   <!-- START CENTERED WHITE CONTAINER -->
                                                   <span class="preheader">Stone Recycling Booking</span>
                                                   <table class="main">
                              
                                                     <!-- START MAIN CONTENT AREA -->
                                                     <tr>
                                                       <td class="wrapper">
                                                         <table border="0" cellpadding="0" cellspacing="0">
                                                           <tr>
                                                             <td>
                                                               <p>Hi,</p>
                                                               <p>
                                                               You have recently requested a collection for your unwanted IT equipment, we have a <strong>Van</strong> in your area on on <strong>' . $timin . '</strong>,
                                                                please <a href="' . $path2 . '" target="_blank">click here</a> to confirm asap if this is suitable? 
                                                                </p>
                                                               <br>
                                                               <p> Please note the collection will be done using a 3rd Party Logistics Partner – Guardian Service, The Drivers are all vetted to SC level or above & wear uniform, vans are double lined, plain white ,
                                                                have two tracking systems & MOD standard Anti Hi-Jack security system. Their vehicles will also be security sealed & witnessed by yourself. They will be using our paperwork.  </p>
                                                                <br> <br>
                                                                <table>
                                                                <thead>
                                                                <TR>
                                                                <th>Vehicle</th>
                                                                <th>Max load weight</th>
                                                                <th>Dimensions<Th>
                                                                </tr>
                                                                <thead>
                                                                <tbody>
                                                                <tr>
                                                                <td>LWB</td>
                                                                <td>1250kg</td>
                                                                <td><strong>6m Long x 2.8m High x 2.1m Wide</strong></td>
                                                                </tr>
                                                                </tr>
                                                                <thead>
                                                                <tbody>
                                                                <tr>
                                                                <td>Berlingo</td>
                                                                <td>400kg</td>
                                                                <td><strong>4.38m Long x 1.9m High x 1.9m Wide</strong></td>
                                                                </tr>
                                                                </tbody>
          
                                                                </table>
                                                                <br> <br>
                                                                <p> The Vans do not have tail lifts so please bear this in mind if you have any <strong>2 Man Lift</strong> Items and advise if there will be help on site.  </p>
                                                                <br>
                                                                <p>Please note that any Collections that are cancelled by the customer within 48 hours/on the day of the collection, or are Failed as a result of incorrect or withheld information, may be charged to the customer. 
                                                                </p>
          
                                                                <br><br>
                                                                <p>Please note that any Collections that are cancelled by the customer within 48 hours/on the day of the collection, 
                                                                or are Failed as a result of incorrect or withheld information, may be charged to the customer.</p>
                                                               
                                                            <br>
                                                              
                              
                                                               </td>
                                                               <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                 <tbody>
                                                                   <tr>
                                                                     <td align="left">
                                                                       <table border="0" cellpadding="0" cellspacing="0">
                                                                       <tbody>
                                                                       <tr>
                                                                        <p> If you have any further  questions please contact: <a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a>.</p>
                                                                       </tr>
                                                                     </tbody>
                                                                       </table>
                                                                     </td>
                                                                   </tr>
                                                                 </tbody>
                                                               </table>
                                                               <p>Thank you for using Stone.</p>
                                                             </td>
                                                           </tr>
                                                         </table>
                                                       </td>
                                                     </tr>
                              
                                                   <!-- END MAIN CONTENT AREA -->
                                                   </table>
                              
                                                   <!-- START FOOTER -->
                                                   <div class="footer">
                                                     <table border="0" cellpadding="0" cellspacing="0">
                                                       <tr>
                                                         <td class="content-block">
                                                           <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                        
                                                         </td>
                                                       </tr>
                                                       <tr>
                                                         <td class="content-block powered-by">
                                                           Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                         </td>
                                                       </tr>
                                                     </table>
                                                   </div>
                                                   <!-- END FOOTER -->
                              
                                                 <!-- END CENTERED WHITE CONTAINER -->
                                                 </div>
                                               </td>
                                               <td>&nbsp;</td>
                                             </tr>
                                           </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'b7van') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                       <tr>
                                                         <td>&nbsp;</td>
                                                         <td class="container">
                                                           <div class="content">
                                        
                                                             <!-- START CENTERED WHITE CONTAINER -->
                                                             <span class="preheader">Stone Recycling Booking</span>
                                                             <table class="main">
                                        
                                                               <!-- START MAIN CONTENT AREA -->
                                                               <tr>
                                                                 <td class="wrapper">
                                                                   <table border="0" cellpadding="0" cellspacing="0">
                                                                     <tr>
                                                                       <td>
                                                                         <p>Hi,</p>
                                                                         <p>We are pleased to confirm that your IT Asset Disposal has been arranged for    <strong>' . $timin . '</strong>.</p>
                                                                        
                                                                         <p>To secure your booking please <a href="' . $path2 . '" target="_blank">click here</a>  and complete the below access survey to ensure our drivers know where to go etc. For request ID please use:  <strong>' . $rid . '</strong></p>
                                                                        
                                                                         <p><a href="https://web2.stonegroup.co.uk/recycling-site-survey" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey</a> <p>
                                                                       
                                                                         <p> If you have not already done so, it is a requirement for us to have photos of the equipment as it is stored. You can upload these to the survey. </p>
                                                                        <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                         <p style="color:red;"> We require these answering by <strong>' . $newdeadtimetrim . ' </strong> in order for collection to take place. </p>
                                                                        
                                                                    <p>It is important for us to have this information to plan collections and deliveries logistically. if we do not have the survey in time, we may have to rearrange your collection.</p>
                                                                   <p> If you have any questions or should any issues arise pending this collection, please <a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a></p>.
          
                                                                          </p>
                                        
                                        
                                                                         </td>
                                                                         <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                           <tbody>
                                                                             <tr>
                                                                               <td align="left">
                                                                                 <table border="0" cellpadding="0" cellspacing="0">
                                                                                 <tbody>
                                                                                 <tr>
                                                                                 
                                                                                 </tr>
                                                                               </tbody>
                                                                                 </table>
                                                                               </td>
                                                                             </tr>
                                                                           </tbody>
                                                                         </table>
                                                                         <p>Thank you for using Stone.</p>
                                                                       </td>
                                                                     </tr>
                                                                   </table>
                                                                 </td>
                                                               </tr>
                                        
                                                             <!-- END MAIN CONTENT AREA -->
                                                             </table>
                                        
                                                             <!-- START FOOTER -->
                                                             <div class="footer">
                                                               <table border="0" cellpadding="0" cellspacing="0">
                                                                 <tr>
                                                                   <td class="content-block">
                                                                     <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                     
                                                                   </td>
                                                                 </tr>
                                                                 <tr>
                                                                   <td class="content-block powered-by">
                                                                     Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                                   </td>
                                                                 </tr>
                                                               </table>
                                                             </div>
                                                             <!-- END FOOTER -->
                                        
                                                           <!-- END CENTERED WHITE CONTAINER -->
                                                           </div>
                                                         </td>
                                                         <td>&nbsp;</td>
                                                       </tr>
                                                     </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'CHSb7van') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                             <tr>
                                                               <td>&nbsp;</td>
                                                               <td class="container">
                                                                 <div class="content">
                                              
                                                                   <!-- START CENTERED WHITE CONTAINER -->
                                                                   <span class="preheader">Stone Recycling Booking</span>
                                                                   <table class="main">
                                              
                                                                     <!-- START MAIN CONTENT AREA -->
                                                                     <tr>
                                                                       <td class="wrapper">
                                                                         <table border="0" cellpadding="0" cellspacing="0">
                                                                           <tr>
                                                                             <td>
                                                                               <p>Hello,</p>
                                                                               <p>Just a gentle reminder that we are awaiting a response to the site survey ahead of your collection on    <strong>' . $timin . '</strong>.</p>
                                                                               <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</p>                                                        
                                                                               <p> We require these answering by  <strong>' . $newdeadtimetrim . ' </strong> or this collection may not proceed as planned. </p>
                                                                               
                                                                               <p>For request ID please use:' . $rid . ' </p>
        
                                                                               <p><a href="https://web2.stonegroup.co.uk/recycling-site-survey" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey</a> <p>
                                                                             
                                                                               <p> Any problems, please dont hesitate to get in touch. </p>
                                                                             
        
                                                                    
                                                                                </p>
                                              
                                              
                                                                               </td>
                                                                               <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                                 <tbody>
                                                                                   <tr>
                                                                                     <td align="left">
                                                                                       <table border="0" cellpadding="0" cellspacing="0">
                                                                                       <tbody>
                                                                                       <tr>
                                                                                       
                                                                                       </tr>
                                                                                     </tbody>
                                                                                       </table>
                                                                                     </td>
                                                                                   </tr>
                                                                                 </tbody>
                                                                               </table>
                                                                               <p>Thank you for using Stone.</p>
                                                                             </td>
                                                                           </tr>
                                                                         </table>
                                                                       </td>
                                                                     </tr>
                                              
                                                                   <!-- END MAIN CONTENT AREA -->
                                                                   </table>
                                              
                                                                   <!-- START FOOTER -->
                                                                   <div class="footer">
                                                                     <table border="0" cellpadding="0" cellspacing="0">
                                                                       <tr>
                                                                         <td class="content-block">
                                                                           <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                           
                                                                         </td>
                                                                       </tr>
                                                                       <tr>
                                                                         <td class="content-block powered-by">
                                                                           Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                                         </td>
                                                                       </tr>
                                                                     </table>
                                                                   </div>
                                                                   <!-- END FOOTER -->
                                              
                                                                 <!-- END CENTERED WHITE CONTAINER -->
                                                                 </div>
                                                               </td>
                                                               <td>&nbsp;</td>
                                                             </tr>
                                                           </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'CHS75T') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                                   <tr>
                                                                     <td>&nbsp;</td>
                                                                     <td class="container">
                                                                       <div class="content">
                                                    
                                                                         <!-- START CENTERED WHITE CONTAINER -->
                                                                         <span class="preheader">Stone Recycling Booking</span>
                                                                         <table class="main">
                                                    
                                                                           <!-- START MAIN CONTENT AREA -->
                                                                           <tr>
                                                                             <td class="wrapper">
                                                                               <table border="0" cellpadding="0" cellspacing="0">
                                                                                 <tr>
                                                                                   <td>
                                                                                     <p>Hello,</p>
                                                                                     <p>Just a gentle reminder that we are awaiting a response to the site survey ahead of your collection on    <strong>' . $timin . '</strong>.</p>
                                                                                     <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                                     <p> We require these answering by  <strong>' . $newdeadtimetrim . ' </strong> or this collection may not proceed as planned. </p>
                                                                                     
                                                                                     <p>For request ID please use:' . $rid . ' </p>
              
                                                                                     <p><a href="https://web2.stonegroup.co.uk/recycling-site-survey-7.5-tonne" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey</a> <p>
                                                                                   
                                                                                     <p> Any problems, please dont hesitate to get in touch. </p>
                                                                                   
              
                                                                          
                                                                                      </p>
                                                    
                                                    
                                                                                     </td>
                                                                                     <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                                       <tbody>
                                                                                         <tr>
                                                                                           <td align="left">
                                                                                             <table border="0" cellpadding="0" cellspacing="0">
                                                                                             <tbody>
                                                                                             <tr>
                                                                                             
                                                                                             </tr>
                                                                                           </tbody>
                                                                                             </table>
                                                                                           </td>
                                                                                         </tr>
                                                                                       </tbody>
                                                                                     </table>
                                                                                     <p>Thank you for using Stone.</p>
                                                                                   </td>
                                                                                 </tr>
                                                                               </table>
                                                                             </td>
                                                                           </tr>
                                                    
                                                                         <!-- END MAIN CONTENT AREA -->
                                                                         </table>
                                                    
                                                                         <!-- START FOOTER -->
                                                                         <div class="footer">
                                                                           <table border="0" cellpadding="0" cellspacing="0">
                                                                             <tr>
                                                                               <td class="content-block">
                                                                                 <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                                 
                                                                               </td>
                                                                             </tr>
                                                                             <tr>
                                                                               <td class="content-block powered-by">
                                                                                 Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                                               </td>
                                                                             </tr>
                                                                           </table>
                                                                         </div>
                                                                         <!-- END FOOTER -->
                                                    
                                                                       <!-- END CENTERED WHITE CONTAINER -->
                                                                       </div>
                                                                     </td>
                                                                     <td>&nbsp;</td>
                                                                   </tr>
                                                                 </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'CHS14T') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                                         <tr>
                                                                           <td>&nbsp;</td>
                                                                           <td class="container">
                                                                             <div class="content">
                                                          
                                                                               <!-- START CENTERED WHITE CONTAINER -->
                                                                               <span class="preheader">Stone Recycling Booking</span>
                                                                               <table class="main">
                                                          
                                                                                 <!-- START MAIN CONTENT AREA -->
                                                                                 <tr>
                                                                                   <td class="wrapper">
                                                                                     <table border="0" cellpadding="0" cellspacing="0">
                                                                                       <tr>
                                                                                         <td>
                                                                                           <p>Hello,</p>
                                                                                           <p>Just a gentle reminder that we are awaiting a response to the site survey ahead of your collection on    <strong>' . $timin . '</strong>.</p>
                                                                                           <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                                           <p> We require these answering by  <strong>' . $newdeadtimetrim . ' </strong> or this collection may not proceed as planned. </p>
                                                                                           
                                                                                           <p>For request ID please use:' . $rid . ' </p>
                    
                                                                                           <p><a href="https://web2.stonegroup.co.uk/recycling-site-survey-14-tonne" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey</a> <p>
                                                                                         
                                                                                           <p> Any problems, please dont hesitate to get in touch. </p>
                                                                                         
                    
                                                                                
                                                                                            </p>
                                                          
                                                          
                                                                                           </td>
                                                                                           <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                                             <tbody>
                                                                                               <tr>
                                                                                                 <td align="left">
                                                                                                   <table border="0" cellpadding="0" cellspacing="0">
                                                                                                   <tbody>
                                                                                                   <tr>
                                                                                                   
                                                                                                   </tr>
                                                                                                 </tbody>
                                                                                                   </table>
                                                                                                 </td>
                                                                                               </tr>
                                                                                             </tbody>
                                                                                           </table>
                                                                                           <p>Thank you for using Stone.</p>
                                                                                         </td>
                                                                                       </tr>
                                                                                     </table>
                                                                                   </td>
                                                                                 </tr>
                                                          
                                                                               <!-- END MAIN CONTENT AREA -->
                                                                               </table>
                                                          
                                                                               <!-- START FOOTER -->
                                                                               <div class="footer">
                                                                                 <table border="0" cellpadding="0" cellspacing="0">
                                                                                   <tr>
                                                                                     <td class="content-block">
                                                                                       <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                                       
                                                                                     </td>
                                                                                   </tr>
                                                                                   <tr>
                                                                                     <td class="content-block powered-by">
                                                                                       Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                                                     </td>
                                                                                   </tr>
                                                                                 </table>
                                                                               </div>
                                                                               <!-- END FOOTER -->
                                                          
                                                                             <!-- END CENTERED WHITE CONTAINER -->
                                                                             </div>
                                                                           </td>
                                                                           <td>&nbsp;</td>
                                                                         </tr>
                                                                       </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'b7van') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                       <tr>
                                                         <td>&nbsp;</td>
                                                         <td class="container">
                                                           <div class="content">
                                        
                                                             <!-- START CENTERED WHITE CONTAINER -->
                                                             <span class="preheader">Stone Recycling Booking</span>
                                                             <table class="main">
                                        
                                                               <!-- START MAIN CONTENT AREA -->
                                                               <tr>
                                                                 <td class="wrapper">
                                                                   <table border="0" cellpadding="0" cellspacing="0">
                                                                     <tr>
                                                                       <td>
                                                                         <p>Hi,</p>
                                                                         <p>We are pleased to confirm that your IT Asset Disposal has been arranged for    <strong>' . $timin . '</strong>.</p>
                                                                        
                                                                         <p>To secure your booking please <a href="' . $path2 . '" target="_blank">click here</a>  and complete the below access survey to ensure our drivers know where to go etc. For request ID please use:  <strong>' . $rid . '</strong></p>
                                                                        
                                                                         <p><a href="https://web2.stonegroup.co.uk/recycling-site-survey" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey</a> <p>
                                                                       
                                                                         <p> If you have not already done so, it is a requirement for us to have photos of the equipment as it is stored. You can upload these to the survey. </p>
                                                                         <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                         <p style="color:red;"> We require these answering by <strong>' . $newdeadtimetrim . ' </strong> in order for collection to take place. </p>
                                                                        
                                                                    <p>It is important for us to have this information to plan collections and deliveries logistically. if we do not have the survey in time, we may have to rearrange your collection.</p>
                                                                   <p> If you have any questions or should any issues arise pending this collection, please <a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a></p>.
          
                                                                          </p>
                                        
                                        
                                                                         </td>
                                                                         <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                           <tbody>
                                                                             <tr>
                                                                               <td align="left">
                                                                                 <table border="0" cellpadding="0" cellspacing="0">
                                                                                 <tbody>
                                                                                 <tr>
                                                                                 
                                                                                 </tr>
                                                                               </tbody>
                                                                                 </table>
                                                                               </td>
                                                                             </tr>
                                                                           </tbody>
                                                                         </table>
                                                                         <p>Thank you for using Stone.</p>
                                                                       </td>
                                                                     </tr>
                                                                   </table>
                                                                 </td>
                                                               </tr>
                                        
                                                             <!-- END MAIN CONTENT AREA -->
                                                             </table>
                                        
                                                             <!-- START FOOTER -->
                                                             <div class="footer">
                                                               <table border="0" cellpadding="0" cellspacing="0">
                                                                 <tr>
                                                                   <td class="content-block">
                                                                     <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                     
                                                                   </td>
                                                                 </tr>
                                                                 <tr>
                                                                   <td class="content-block powered-by">
                                                                     Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                                   </td>
                                                                 </tr>
                                                               </table>
                                                             </div>
                                                             <!-- END FOOTER -->
                                        
                                                           <!-- END CENTERED WHITE CONTAINER -->
                                                           </div>
                                                         </td>
                                                         <td>&nbsp;</td>
                                                       </tr>
                                                     </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'b77lorry') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                                 <tr>
                                                                   <td>&nbsp;</td>
                                                                   <td class="container">
                                                                     <div class="content">
                                                  
                                                                       <!-- START CENTERED WHITE CONTAINER -->
                                                                       <span class="preheader">Stone Recycling Booking</span>
                                                                       <table class="main">
                                                  
                                                                         <!-- START MAIN CONTENT AREA -->
                                                                         <tr>
                                                                           <td class="wrapper">
                                                                             <table border="0" cellpadding="0" cellspacing="0">
                                                                               <tr>
                                                                                 <td>
                                                                                   <p>Hi,</p>
                                                                                   <p>We are pleased to confirm that your IT Asset Disposal has been arranged for   <strong>' . $timin . '</strong></p> 
                                                                                   <p>To secure your booking please  <a href="' . $path2 . '" target="_blank">click here</a>  and complete the below access survey to ensure our drivers know where to go etc. For request ID please use: <strong><strong>' . $rid . '</strong></strong> </p>
                                                                                  <p><a href="https://web2.stonegroup.co.uk/recycling-site-survey-7.5-tonne" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey-7.5-tonne</a></p>
                                                                                   <p> If you have not already done so, it is a requirement for us to have photos of the equipment as it is stored. You can upload these to the survey.</p>
                                                                                   <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                                    <p style="color:red;"> We require these answering by <strong>' . $newdeadtimetrim . '</strong> in order for collection to take place. </p>
                                                                                   
                                                                             
                                                                                   <p> It is really important for us to have this information to plan collections and deliveries logistically. if we do not have the survey in time, we may have to rearrange your collection.</p>
          
                                                                                 <p>  If you have any questions or should any issues arise pending this collection, please <a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a>. </p>
                                                  
                                                  
                                                                                   </td>
                                                                                   <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                                     <tbody>
                                                                                       <tr>
                                                                                         <td align="left">
                                                                                           <table border="0" cellpadding="0" cellspacing="0">
                                                                                           <tbody>
                                                                                           <tr>
                                                                                          
                                                                                           </tr>
                                                                                         </tbody>
                                                                                           </table>
                                                                                         </td>
                                                                                       </tr>
                                                                                     </tbody>
                                                                                   </table>
                                                                                   <p>Thank you for using Stone.</p>
                                                                                 </td>
                                                                               </tr>
                                                                             </table>
                                                                           </td>
                                                                         </tr>
                                                  
                                                                       <!-- END MAIN CONTENT AREA -->
                                                                       </table>
                                                  
                                                                       <!-- START FOOTER -->
                                                                       <div class="footer">
                                                                         <table border="0" cellpadding="0" cellspacing="0">
                                                                           <tr>
                                                                             <td class="content-block">
                                                                               <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                               
                                                                             </td>
                                                                           </tr>
                                                                          <tr>
                                                                             <td class="content-block powered-by">
                                                                               Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>
                                                                             </td>
                                                                           </tr>
                                                                         </table>
                                                                       </div>
                                                                       <!-- END FOOTER -->
                                                  
                                                                     <!-- END CENTERED WHITE CONTAINER -->
                                                                     </div>
                                                                   </td>
                                                                   <td>&nbsp;</td>
                                                                 </tr>
                                                               </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'b72van') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                   <tr>
                                                     <td>&nbsp;</td>
                                                     <td class="container">
                                                       <div class="content">
                                    
                                                         <!-- START CENTERED WHITE CONTAINER -->
                                                         <span class="preheader">Stone Recycling Booking</span>
                                                         <table class="main">
                                    
                                                           <!-- START MAIN CONTENT AREA -->
                                                           <tr>
                                                             <td class="wrapper">
                                                               <table border="0" cellpadding="0" cellspacing="0">
                                                                 <tr>
                                                                   <td>
                                                                     <p>Hi,</p>
                                                                     <p>We are pleased to confirm that your IT Asset Disposal has been arranged for    <strong>' . $timin . '</strong>.</p>
                                                                    
                                                                     <p>To secure your booking please <a href="' . $path2 . '" target="_blank">click here</a>  and complete the below access survey to ensure our drivers know where to go etc. For request ID please use:  <strong>' . $rid . '</strong></p>
                                                                    
                                                                     <p><a href="https://web2.stonegroup.co.uk/recycling-site-survey" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey</a> <p>
                                                                   
                                                                     <p> If you have not already done so, it is a requirement for us to have photos of the equipment as it is stored. You can upload these to the survey. </p>
                                                                     <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                     <p style="color:red;"> We require these answering by <strong>' . $newdeadtimetrim . ' </strong> in order for collection to take place. </p>
                                                                    
                                                                <p>It is important for us to have this information to plan collections and deliveries logistically. if we do not have the survey in time, we may have to rearrange your collection.</p>
                                                               <p> If you have any questions or should any issues arise pending this collection, please <a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a></p>.
        
                                                                      </p>
                                    
                                    
                                                                     </td>
                                                                     <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                       <tbody>
                                                                         <tr>
                                                                           <td align="left">
                                                                             <table border="0" cellpadding="0" cellspacing="0">
                                                                             <tbody>
                                                                             <tr>
                                                                             
                                                                             </tr>
                                                                           </tbody>
                                                                             </table>
                                                                           </td>
                                                                         </tr>
                                                                       </tbody>
                                                                     </table>
                                                                     <p>Thank you for using Stone.</p>
                                                                   </td>
                                                                 </tr>
                                                               </table>
                                                             </td>
                                                           </tr>
                                    
                                                         <!-- END MAIN CONTENT AREA -->
                                                         </table>
                                    
                                                         <!-- START FOOTER -->
                                                         <div class="footer">
                                                           <table border="0" cellpadding="0" cellspacing="0">
                                                             <tr>
                                                               <td class="content-block">
                                                                 <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                 
                                                               </td>
                                                             </tr>
                                                             <tr>
                                                               <td class="content-block powered-by">
                                                                 Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                               </td>
                                                             </tr>
                                                           </table>
                                                         </div>
                                                         <!-- END FOOTER -->
                                    
                                                       <!-- END CENTERED WHITE CONTAINER -->
                                                       </div>
                                                     </td>
                                                     <td>&nbsp;</td>
                                                   </tr>
                                                 </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'b714lorry') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                                           <tr>
                                                                             <td>&nbsp;</td>
                                                                             <td class="container">
                                                                               <div class="content">
                                                            
                                                                                 <!-- START CENTERED WHITE CONTAINER -->
                                                                                 <span class="preheader">Stone Recycling Booking</span>
                                                                                 <table class="main">
                                                            
                                                                                   <!-- START MAIN CONTENT AREA -->
                                                                                   <tr>
                                                                                     <td class="wrapper">
                                                                                       <table border="0" cellpadding="0" cellspacing="0">
                                                                                         <tr>
                                                                                           <td>
                                                                                             <p>Hi,</p>
                                                                                             <p>I am pleased to confirm that your IT Asset Disposal  has been arranged for  <strong>' . $timin . '</strong>.</p>
                                                                                            
                                                                                             <p>To secure your booking please <a href="' . $path2 . '" target="_blank">click here</a>  and complete the below access survey to ensure our drivers know where to go etc. For request ID please use : <strong>' . $rid . '</strong></p>
                                                                                           <p> <a href="https://web2.stonegroup.co.uk/recycling-site-survey-14-tonne" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey-14-tonne</a></p>
                                                                                           
                                                                                             <p> If you have not already done so, it is a requirement for us to have photos of the equipment as it is stored.  You can upload these to the survey.</p>
                                                                                             <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                                              <p style="color:red;"> We require these answering by  <strong>' . $newdeadtimetrim . '</strong> in order for collection to take place.</p>
                                                                                             
                                                                                             
                                                                                             <p>It is really important for us to have this information to plan collections and deliveries logistically. if we do not have the survey in time, we may have to rearrange your collection.</p>
                                                                                             <p>If you have any questions or should any issues arise pending this collection, please don\'t hesitate to contact <a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a>.
                                                                                             </p><br> 
                                                              
                                                                                             </td>
                                                                                             <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                                               <tbody>
                                                                                                 <tr>
                                                                                                   <td align="left">
                                                                                                     <table border="0" cellpadding="0" cellspacing="0">
                                                                                                       <tbody>
                                                                                                         <tr>
                                                                                                         
                                                                                                         </tr>
                                                                                                       </tbody>
                                                                                                     </table>
                                                                                                   </td>
                                                                                                 </tr>
                                                                                               </tbody>
                                                                                             </table>
                                                                                             <p>Thank you for using Stone.</p>
                                                                                           </td>
                                                                                         </tr>
                                                                                       </table>
                                                                                     </td>
                                                                                   </tr>
                                                            
                                                                                 <!-- END MAIN CONTENT AREA -->
                                                                                 </table>
                                                            
                                                                                 <!-- START FOOTER -->
                                                                                 <div class="footer">
                                                                                   <table border="0" cellpadding="0" cellspacing="0">
                                                                                     <tr>
                                                                                       <td class="content-block">
                                                                                         <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                                        
                                                                                       </td>
                                                                                     </tr>
                                                                                     <tr>
                                                                                       <td class="content-block powered-by">
                                                                                         Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                                                       </td>
                                                                                     </tr>
                                                                                   </table>
                                                                                 </div>
                                                                                 <!-- END FOOTER -->
                                                            
                                                                               <!-- END CENTERED WHITE CONTAINER -->
                                                                               </div>
                                                                             </td>
                                                                             <td>&nbsp;</td>
                                                                           </tr>
                                                                         </table>' . $emailHTMLFooter;
                }

                if ($lorrytype == 'b7GB') {
                    $emailtxt = $emailHTMLHead . '<table border="0" cellpadding="0" cellspacing="0" class="body">
                                                                                     <tr>
                                                                                       <td>&nbsp;</td>
                                                                                       <td class="container">
                                                                                         <div class="content">
                                                                      
                                                                                           <!-- START CENTERED WHITE CONTAINER -->
                                                                                           <span class="preheader">Stone Recycling Booking</span>
                                                                                           <table class="main">
                                                                      
                                                                                             <!-- START MAIN CONTENT AREA -->
                                                                                             <tr>
                                                                                               <td class="wrapper">
                                                                                                 <table border="0" cellpadding="0" cellspacing="0">
                                                                                                   <tr>
                                                                                                     <td>
                                                                                                       <p>Hi,</p>
                                                                                                       <p>  You have recently requested a collection for your unwanted IT equipment, we have a Van in your area day  <strong>' . $timin . '</strong>,
                                                                                                        please <a href="' . $path2 . '" target="_blank">Click Here</a> to confirm asap if this is suitable?  </p>
        
                                                                                                        <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                                        
                                                                                                        <p><b> Ahead of your collection, there are a few questions which we require answering ASAP. Just click on the link below.</b></p>
                                                                                                        <p>You will need your Request ID for this.</p>
                                                                                                        
                                                                                                        <p><a href="https://web2.stonegroup.co.uk/recycling-site-survey" target="_blank">https://web2.stonegroup.co.uk/recycling-site-survey</a> </p>
                                                                                                       
                                                                                                        <p> Please note the collection will be done using a 3rd Party Logistics Partner <b>Guardian Service</b>, The Drivers are all vetted to SC level or above & wear uniform, 
                                                                                                        vans are double lined, plain white , have two tracking systems & MOD standard Anti Hi-Jack security system. 
                                                                                                        Their vehicles will also be security sealed & witnessed by yourself. They will be using our paperwork.  </p>
                                                                                                        
                                                                                                        <p> The vehicle will be one of the below dependant on your collection size </p>
                                                                                                        <table class="table">
                                                                                                        <thead>
                                                                                                        <TR>
                                                                                                        <th>Vehicle</th>
                                                                                                        <th>Max load weight</th>
                                                                                                        <th>Dimensions<Th>
                                                                                                        </tr>
                                                                                                        <thead>
                                                                                                        <tbody>
                                                                                                        <tr>
                                                                                                        <td>LWB</td>
                                                                                                        <td>1250kg</td>
                                                                                                        <td>6m Long x 2.8m High x 2.1m Wide</td>
                                                                                                        </tr>
                                                                                                        </tr>
                                                                                                        <thead>
                                                                                                        <tbody>
                                                                                                        <tr>
                                                                                                        <td>Berlingo</td>
                                                                                                        <td>400kg</td>
                                                                                                        <td>4.38m Long x 1.9m High x 1.9m Wide</td>
                                                                                                        </tr>
                                                                                                        </tbody>
                                                  
                                                                                                        </table>
                                                                                                        <br> 
                                                                                                        <p> The Vans do not have tail lifts so please bear this in mind if you have any <strong>2 Man Lift</strong> Items and advise if there will be help on site.  </p>
                                                                                                        <p> <strong>By confirming the date/submitting the survey you are also agreeing to the attached Terms for collection during COVID-19' . htmlentities('-') . ' if you have any questions about this please contact us as below.</strong></p>
                                                                                                        <p>We require full details of parking availability. Please make us aware if allocated parking will not be readily available. 
                                                                                                        If not provided with details (we will have to assume that this will be available) or if provided with the wrong information, 
                                                                                                        a charge may be incurred if this is not the case and leads to a failed/Delayed collection.  </p>
                                                                                                        
                                                                                                         <p> Please note that any Collections that are cancelled by the customer within 48 hours/on the day of the collection, 
                                                                                                         or are Failed as a result of incorrect or withheld information, may be charged to the customer.</p>
                                                                                                       <br>
                                                                                                       <h2>**Important GDPR Compliance ** </h2>
                                                                                                       <p>  In order to process your Recycling request and ensure your compliance with Data Protection legislation,
                                                                                                        a Data Processing Agreement will need to be in place. If you do not already have this agreement in place, we will be in touch to set one up. 
                                                                                                       </p>
                                                                                                      
                                                                      
                                                                                                       </td>
                                                                                                       <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                                                                                         <tbody>
                                                                                                           <tr>
                                                                                                             <td align="left">
                                                                                                               <table border="0" cellpadding="0" cellspacing="0">
                                                                                                               <tbody>
                                                                                                               <tr>
                                                                                                                <p> If you have any further  questions please contact:<a href="mailto:Tracey.Melbourne@stonegroup.co.uk">Tracey Melbourne</a>.</p>
                                                                                                               </tr>
                                                                                                             </tbody>
                                                                                                               </table>
                                                                                                             </td>
                                                                                                           </tr>
                                                                                                         </tbody>
                                                                                                       </table>
                                                                                                       <p>Thank you for using Stone.</p>
                                                                                                     </td>
                                                                                                   </tr>
                                                                                                 </table>
                                                                                               </td>
                                                                                             </tr>
                                                                      
                                                                                           <!-- END MAIN CONTENT AREA -->
                                                                                           </table>
                                                                      
                                                                                           <!-- START FOOTER -->
                                                                                           <div class="footer">
                                                                                             <table border="0" cellpadding="0" cellspacing="0">
                                                                                               <tr>
                                                                                                 <td class="content-block">
                                                                                                   <span class="apple-link">STONE, Granite One Hundred, Acton Gate, Stafford ST18 9AA</span>
                                                                                                   
                                                                                                 </td>
                                                                                               </tr>
                                                                                               <tr>
                                                                                                 <td class="content-block powered-by">
                                                                                                   Powered by <a href="https://www.stonegroup.co.uk">Stone Group</a>.
                                                                                                 </td>
                                                                                               </tr>
                                                                                             </table>
                                                                                           </div>
                                                                                           <!-- END FOOTER -->
                                                                      
                                                                                         <!-- END CENTERED WHITE CONTAINER -->
                                                                                         </div>
                                                                                       </td>
                                                                                       <td>&nbsp;</td>
                                                                                     </tr>
                                                                                   </table>' . $emailHTMLFooter;
                }

                $mail->addContent("text/html", $emailtxt);
                $sendgrid = new SendGrid($sendgridConfig['api']['key']);
                $response = $sendgrid->send($mail);

                Logger::getInstance("itadEmail.log")->debug(
                    'email',
                    [
                        'line' => __LINE__,
                        'emailtxt' => $emailtxt
                    ]
                );
                if ($response->statusCode() !== 202) {
                    Logger::getInstance("itadEmail.log")->error(
                        'email',
                        [
                            'line' => __LINE__,
                            'response->statusCode()' => $response->statusCode(),
                            'response->body()' => $response->body(),
                        ]
                    );
                    throw new \RuntimeException($response->body());
                }
                echo "OK Mail Sent";
            } catch (\Exception $e) {
                echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
                Logger::getInstance("itadEmail.log")->error(
                    'email',
                    [
                        'line' => __LINE__,
                        'Exception' => $e->getMessage()
                    ]
                );
            }
        }
        Logger::getInstance("itadEmail.log")->debug(
            'email',
            [
                'line' => __LINE__,
                'end' => 'true'
            ]
        );
    }

    public function getEmailHead()
    {
        return '<head>
                       <meta name="viewport" content="width=device-width" />
                       <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                       <title>Simple Transactional Email</title>
                       <style>
                         /* -------------------------------------
                             GLOBAL RESETS
                         ------------------------------------- */
                         img {
                           border: none;
                           -ms-interpolation-mode: bicubic;
                           max-width: 100%; }
                         body {
                           background-color: #f6f6f6;
                           font-family: sans-serif;
                           -webkit-font-smoothing: antialiased;
                           font-size: 14px;
                           line-height: 1.4;
                           margin: 0;
                           padding: 0;
                           -ms-text-size-adjust: 100%;
                           -webkit-text-size-adjust: 100%; }
                         table {
                           border-collapse: separate;
                           mso-table-lspace: 0pt;
                           mso-table-rspace: 0pt;
                           width: 100%; }
                           table td {
                             font-family: sans-serif;
                             font-size: 14px;
                             vertical-align: top; }
                         /* -------------------------------------
                             BODY & CONTAINER
                         ------------------------------------- */
                         .body {
                           background-color: #f6f6f6;
                           width: 100%; }
                         .container {
                           display: block;
                           Margin: 0 auto !important;
                           /* makes it centered */
                           max-width: 580px;
                           padding: 10px;
                           width: 580px; }
                         /* This should also be a block element, so that it will fill 100% of the .container */
                         .content {
                           box-sizing: border-box;
                           display: block;
                           Margin: 0 auto;
                           max-width: 580px;
                           padding: 10px; }
                         /* -------------------------------------
                             HEADER, FOOTER, MAIN
                         ------------------------------------- */
                         .main {
                           background: #ffffff;
                           border-radius: 3px;
                           width: 100%; }
                         .wrapper {
                           box-sizing: border-box;
                           padding: 20px; }
                         .content-block {
                           padding-bottom: 10px;
                           padding-top: 10px;
                         }
                         .footer {
                           clear: both;
                           Margin-top: 10px;
                           text-align: center;
                           width: 100%; }
                           .footer td,
                           .footer p,
                           .footer span,
                           .footer a {
                             color: #999999;
                             font-size: 12px;
                             text-align: center; }
                         /* -------------------------------------
                             TYPOGRAPHY
                         ------------------------------------- */
                         h1,
                         h2,
                         h3,
                         h4 {
                           color: #000000;
                           font-family: sans-serif;
                           font-weight: 400;
                           line-height: 1.4;
                           margin: 0;
                           Margin-bottom: 30px; }
                         h1 {
                           font-size: 35px;
                           font-weight: 300;
                           text-align: center;
                           text-transform: capitalize; }
                         p,
                         ul,
                         ol {
                           font-family: sans-serif;
                           font-size: 14px;
                           font-weight: normal;
                           margin: 0;
                           Margin-bottom: 15px; }
                           p li,
                           ul li,
                           ol li {
                             list-style-position: inside;
                             margin-left: 5px; }
                         a {
                           color: #3498db;
                           text-decoration: underline; }
                         /* -------------------------------------
                             BUTTONS
                         ------------------------------------- */
                         .btn {
                           box-sizing: border-box;
                           width: 100%; }
                           .btn > tbody > tr > td {
                             padding-bottom: 15px; }
                           .btn table {
                             width: auto; }
                           .btn table td {
                             background-color: #ffffff;
                             border-radius: 5px;
                             text-align: center; }
                           .btn a {
                             background-color: #ffffff;
                             border: solid 1px #3498db;
                             border-radius: 5px;
                             box-sizing: border-box;
                             color: #3498db;
                             cursor: pointer;
                             display: inline-block;
                             font-size: 14px;
                             font-weight: bold;
                             margin: 0;
                             padding: 12px 25px;
                             text-decoration: none;
                             text-transform: capitalize; }
                         .btn-primary table td {
                           background-color: #3498db; }
                         .btn-primary a {
                           background-color: #3498db;
                           border-color: #3498db;
                           color: #ffffff; }
                         /* -------------------------------------
                             OTHER STYLES THAT MIGHT BE USEFUL
                         ------------------------------------- */
                         .last {
                           margin-bottom: 0; }
                         .first {
                           margin-top: 0; }
                         .align-center {
                           text-align: center; }
                         .align-right {
                           text-align: right; }
                         .align-left {
                           text-align: left; }
                         .clear {
                          clear: both; }
                         .mt0 {
                           margin-top: 0; }
                         .mb0 {
                           margin-bottom: 0; }
                         .preheader {
                           color: transparent;
                           display: none;
                           height: 0;
                           max-height: 0;
                           max-width: 0;
                           opacity: 0;
                           overflow: hidden;
                           mso-hide: all;
                           visibility: hidden;
                           width: 0; }
                         .powered-by a {
                           text-decoration: none; }
                         hr {
                           border: 0;
                           border-bottom: 1px solid #f6f6f6;
                           Margin: 20px 0; }
                         /* -------------------------------------
                             RESPONSIVE AND MOBILE FRIENDLY STYLES
                         ------------------------------------- */
                         @media only screen and (max-width: 620px) {
                           table[class=body] h1 {
                             font-size: 28px !important;
                             margin-bottom: 10px !important; }
                           table[class=body] p,
                           table[class=body] ul,
                           table[class=body] ol,
                           table[class=body] td,
                           table[class=body] span,
                           table[class=body] a {
                             font-size: 16px !important; }
                           table[class=body] .wrapper,
                           table[class=body] .article {
                             padding: 10px !important; }
                           table[class=body] .content {
                             padding: 0 !important; }
                           table[class=body] .container {
                             padding: 0 !important;
                             width: 100% !important; }
                           table[class=body] .main {
                             border-left-width: 0 !important;
                             border-radius: 0 !important;
                             border-right-width: 0 !important; }
                           table[class=body] .btn table {
                             width: 100% !important; }
                           table[class=body] .btn a {
                             width: 100% !important; }
                           table[class=body] .img-responsive {
                             height: auto !important;
                             max-width: 100% !important;
                             width: auto !important; }}
                         /* -------------------------------------
                             PRESERVE THESE STYLES IN THE HEAD
                         ------------------------------------- */
                         @media all {
                           .ExternalClass {
                             width: 100%; }
                           .ExternalClass,
                           .ExternalClass p,
                           .ExternalClass span,
                           .ExternalClass font,
                           .ExternalClass td,
                           .ExternalClass div {
                             line-height: 100%; }
                           .apple-link a {
                             color: inherit !important;
                             font-family: inherit !important;
                             font-size: inherit !important;
                             font-weight: inherit !important;
                             line-height: inherit !important;
                             text-decoration: none !important; }
                           .btn-primary table td:hover {
                             background-color: #34495e !important; }
                           .btn-primary a:hover {
                             background-color: #34495e !important;
                             border-color: #34495e !important; } }
                       </style>
                       
                     </head>
                     <body class="">';
    }

    public function getEmailFooter()
    {
        return '</body></html>';
    }

    public function clean_data($value, $type)
    {
        if ($type == "text") {
            $value = preg_replace("/[^a-zA-Z0-9\-\_\ go]/", "", $value);
        }
        if ($type == "email") {
            $value = preg_replace("/[^a-zA-Z0-9\-\.\@]/", "", $value);
        }
        if ($type == "date") {
            $value = preg_replace("/[^a-zA-Z0-9\-\.\@\\\/]/", "", $value);
        }
        if ($type == "password") {
            $value = preg_replace("/[\'\"\;]/", "", $value);
        }
        if ($type == "number") {
            $value = preg_replace("/![0-9]/", "", $value);
        }
        if ($type == "array") {
            $value = preg_replace("/[^a-zA-Z0-9\,]/", "", $value);
        }
        if ($type == "mac") {
            $value = preg_replace("/[^a-zA-Z0-9\,\:]/", "", $value);
        }
        $clean = $this->santizestring($value);
        return $value;
    }

    public function santizestring($string)
    {
        $clean = filter_var($string, FILTER_SANITIZE_STRING);
        return $clean;
    }
}
