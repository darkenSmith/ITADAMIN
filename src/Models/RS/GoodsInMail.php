<?php

namespace App\Models\RS;

use App\Helpers\Config;
use App\Helpers\Database;
use App\Helpers\Logger;
use App\Models\AbstractModel;
use Psr\Log\NullLogger;
use SendGrid;
use SendGrid\Mail\Mail;

class GoodsInMail extends AbstractModel
{
    private $emailConfig;

    /**
     * GoodInData constructor.
     */
    public function __construct()
    {
        $this->sdb = Database::getInstance('sql01');
        $this->emailConfig = Config::getInstance()->get('email');

        parent::__construct();
    }

    public function process()
    {
        Logger::getInstance("goodsInMail.log")->debug(
            'process - start'
        );

        $content1 = $_POST['array1'];
        $content2 = $_POST['array2'];

        $arr = json_decode(json_encode($content1), true);
        $arrfinal = json_decode($arr, true);

        $arr2 = json_decode(json_encode($content2), true);
        $arrfinal2 = json_decode($arr2, true);

        $ordernum = '';

        Logger::getInstance("goodsInMail.log")->debug(
            'process - json decoded encoded'
        );

        foreach ($arrfinal2 as $output2) {
            Logger::getInstance("goodsInMail.log")->debug(
                'process - foreach',
                [$output2]
            );

            $ord = array_slice($output2, 0, 1);
            $id = array_slice($output2, 1, 1);
            $id = !empty($id) ? $id : null;
            $driver = array_slice($output2, 2, 1);
            $driver2 = array_slice($output2, 3, 1);
            $scrap = array_slice($output2, 4, 1);
            $wee = array_slice($output2, 5, 1);
            $totalred = array_slice($output2, 6, 1);
            $totalyells = array_slice($output2, 7, 1);
            $totalunits = array_slice($output2, 8, 1);
            $totalweights = array_slice($output2, 9, 1);
            $cust = array_slice($output2, 10, 1);
            $veri = array_slice($output2, 11, 1);
            $comments = array_slice($output2, 12, 1);

            $sqlch = ' SELECT COUNT(*) AS CH FROM [RECwarehouse_collection] WHERE REQUESTID =' . implode(",", $id);
            $stmt = $this->sdb->prepare($sqlch);
            $stmt->execute();
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);

            Logger::getInstance("goodsInMail.log")->debug(
                'process - foreach data.ch',
                [$data]
            );

            try {
                if ($data['CH'] == 0) {
                    $sqlin = "insert into [RECwarehouse_collection]( [RequestID]  ,[ordernum] ,[Driver1] ,[Driver2] ,[verfied]  ,[TotalUnits]  ,[Totalweights] ,[TotalReds] ,[TotalYellow] ,[Comments] ,[Countby], [Bookedin by], [TimeOf_Process] ,[Customer])
        values('" . implode(",", $id) . "', '" . implode(",", $ord) . "', '" . implode(",", $driver) . "', '" . implode(",", $driver2) . "', 'NO', '" . implode(",", $totalunits) . "', '" . implode(",", $totalweights) . "', '" . implode(",", $totalred) . "', '" . implode(",", $totalyells) . "', '', '', '" . $_SESSION['user']['lastname'] . "', getdate(), '" . implode(",", $cust) . "' )
        ";
                    $stmtIN = $this->sdb->prepare($sqlin);
                    $stmtIN->execute();
                    $_SESSION['ORDD'] = implode(",", $ord);
                } else {
                    $sqlin = "
      update [RECwarehouse_collection] 
      set Driver1 = '" . implode(",", $driver) . "',
          Driver2 = '" . implode(",", $driver2) . "',
          [verfied] = '" . implode(",", $veri) . "',
          [TotalUnits] ='" . implode(",", $totalunits) . "',
          Totalweights = '" . implode(",", $totalweights) . "',
          TotalReds = '" . implode(",", $totalred) . "',
          TotalYellow = '" . implode(",", $totalyells) . "',
          Comments = '',
          Countby = '',
          [Bookedin by] ='" . $_SESSION['user']['lastname'] . "'
          where RequestID = '" . implode(",", $id) . "'
      ";
                    $stmtIN = $this->sdb->prepare($sqlin);
                    $stmtIN->execute();
                    $_SESSION['ORDD'] = implode(",", $ord);
                }
            } catch (\Exception $e) {
                Logger::getInstance("goodsInMail.log")->error(
                    'process - foreach insert-update',
                    [$e->getMessage()]
                );
            }

            $table = "
<!DOCTYPE html>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' integrity='sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO' crossorigin='anonymous'>
<table class='table' cellspacing='3'>
<tr>
<th> Ordernumber </th>
<th> RequestID </th>
<th> Driver name 1 </th>
<th> Driver name 2 </th>
<th> Scrap Cable(wgt) </th>
<th> Genreal Wee(wgt) </th>
<th> total Units </th> 
<th> total Weights </th>
<th>total RED</th>
<th>total YELLOW </th>
</tr>
<tr>
<td>" . implode(",", $ord) . "</td> 
<td>" . implode(",", $id) . "</td> 
<td>" . implode(",", $driver) . "</td> 
<td>" . implode(",", $driver2) . "</td> 
<td>" . implode(",", $scrap) . "</td> 
<td>" . implode(",", $totalunits) . "</td> 
<td>" . implode(",", $totalweights) . "</td> 
<td>" . implode(",", $totalred) . "</td> 
<td>" . implode(",", $totalyells) . "</td> 
</tr>
</table>

<table class = 'table'> 
<thead>
<tr> 
<th> product </th>
<th> Request </th>
<th> BL </th>
<th> BER </th>
<th> Weight </th>
<th> Discrepancies </th>
</tr>
</thead>";
        }

        foreach ($arrfinal as $output) {
            Logger::getInstance("goodsInMail.log")->debug(
                'process - foreach 2',
                [$output]
            );
            $name = array_slice($output, 0, 1);
            $req = array_slice($output, 1, 1);
            $bl = array_slice($output, 2, 1);
            $ber = array_slice($output, 3, 1);
            $wgt = array_slice($output, 4, 1);
            $dis = array_slice($output, 5, 1);
            $table .= "<tbody>
                        <tr>
                        <td> <b>" . implode(",", $name) . "</b></td>
                        <td> " . implode(",", $req) . "</td>
                        <td> " . implode(",", $bl) . "</td>
                        <td> " . implode(",", $ber) . "</td>
                        <td> " . implode(",", $wgt) . "</td>
                        <td> " . implode(",", $dis) . "</td>";
        }

        $table .= "</tr></tbody></table></html>";

        $rid = $_POST['rid'] ?? null;

        Logger::getInstance("goodsInMail.log")->debug(
            'process - sendEmail called',
            [$rid]
        );
        $this->sendEmail($table, $rid);
        Logger::getInstance("goodsInMail.log")->debug(
            'process - sendEmail passed',
            [$rid]
        );

        foreach ($arrfinal as $output) {
            Logger::getInstance("goodsInMail.log")->debug(
                'process - foreach 3',
                [$output]
            );
            $name = array_slice($output, 0, 1);
            $req = array_slice($output, 1, 1);
            $bl = array_slice($output, 2, 1);
            $ber = array_slice($output, 3, 1);
            $wgt = array_slice($output, 4, 1);
            $dis = array_slice($output, 5, 1);

            $sqlcd = ' SELECT COUNT(*) AS CD FROM RECWharehouse_detail WHERE REQUESTID =' . implode(",", $id);
            $stmt2 = $this->sdb->prepare($sqlcd);
            $stmt2->execute();
            $datacd = $stmt2->fetch(\PDO::FETCH_ASSOC);

            if ($datacd['CD'] == 0) {
                $sqldelin = "
    insert into ITADsys.[dbo].[RECWharehouse_detail](Product, Request, BL, BER, Weight, Discrep, RequestID, Ordernum)
    values('" . implode(",", $name) . "', '" . implode(",", $req) . "', '" . implode(",", $bl) . "', '" . implode(",", $ber) . "', '" . implode(",", $wgt) . "', '" . implode(",", $dis) . "', '" . implode(",", $id) . "','" . $_SESSION['ORDD'] . "')
    ";
                $stmtindet = $this->sdb->prepare($sqldelin);
                $stmtindet->execute();
            } else {
                $sqlupdate = "
      update ITADsys.[dbo].[RECWharehouse_detail]
      set Request = '" . implode(",", $req) . "',
      BL = '" . implode(",", $bl) . "',
      BER = '" . implode(",", $ber) . "',
      Weight = '" . implode(",", $wgt) . "',
      Discrep = '" . implode(",", $dis) . "'
      where RequestID = '" . implode(",", $id) . "' and Product = '" . implode(",", $name) . "'

      ";
                $stmtupdate = $this->sdb->prepare($sqlupdate);
                $stmtupdate->execute();
            }
        }
    }

    private function sendEmail($table, $rid)
    {
        $sendgridConfig = $this->emailConfig['sendgrid'];
        try {
            $email = new Mail();

            $email->setFrom($_SESSION['user']['username'], 'Stone Computers Recycling System');
            $email->setSubject("Collection intake Sheet  - Request-ID:" . $rid);
            $email->addTo($sendgridConfig['from']['ITADSystem']);
            $email->addContent("text/html", $table);
            $sendgrid = new SendGrid($sendgridConfig['api']['key']);
            $response = $sendgrid->send($email);

            Logger::getInstance("goodsInMail.log")->debug(
                'sendEmail - statusCode',
                [$response->statusCode()]
            );

            if ($response->statusCode() !== 202) {
                throw new \RuntimeException($response->body());
            }
            echo "OK Mail Sent";
        } catch (\Exception $e) {
            Logger::getInstance("goodsInMail.log")->error(
                'sendEmail',
                [$e->getMessage()]
            );
            echo 'Message could not be sent. Mailer Error: ', $e->getMessage();
        }
    }
}
