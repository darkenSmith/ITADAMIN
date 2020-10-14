<?php

namespace App\Controllers;

use App\Models\RS\AddRebate;
use App\Models\RS\ApprovData;
use App\Models\RS\BdmDetail;
use App\Models\RS\BdView;
use App\Models\RS\GoodInData;
use App\Models\RS\GoodSoutData;
use App\Models\RS\Bookingdata;
use App\Models\RS\Isdone;
use App\Models\RS\Pdfmaker;
use App\Models\RS\download;
use App\Models\RS\Rebate;
use App\Models\User;

/**
 * Class RSController
 * @package App\Controllers
 */
class RSController extends AbstractController
{
    public function index()
    {
        header("Location:/RS/booking/");
    }

    public function booking()
    {
        $data = new User();
        $bookdata = new Bookingdata();
        $data->getRoles();
        $roles = $data->roles;

        $booklist = $bookdata->getdata();
        $arealist = $bookdata->getareas();
        
       
        $this->template->view('RECBooking/pages/bookedcollection', array_merge(['bookList' => $booklist,
                               'arealist'=>$arealist], $this->getCommonData()));
        
    }

    // Checked
    private function getCommonData()
    {
        $data = new User();
        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;

        return [
            'customers' => $customers,
            'data' => $data,
            'roles' => $roles,
        ];


    }

    // Checked
    public function detialdoc()
    {
        $this->template->view('RECBooking/pages/detaildoc', $this->getCommonData());
    }


    // Checked
    public function arc()
    {
        $this->template->view('RECBooking/pages/archive', $this->getCommonData());
    }

    // Checked
    public function updatadata()
    {
        $this->template->view('RECBooking/pages/update', $this->getCommonData());
    }

    // Checked
    public function isEmail()
    {
        $this->template->view('RECBooking/pages/isEmail', $this->getCommonData());
    }

    // Checked
    public function isPdf()
    {
        $data = new User();
        $ispdfmaker = new Pdfmaker();
        $data->getRoles();
        $roles = $data->roles;
        $pdf = $ispdfmaker->printdoc();
        //$this->template->view('RECBooking/pages/pdfMaker', $this->getCommonData());
    }

    // Checked
    public function isDone()
    {

        $data = new User();
        $isdonedata = new isDone();
        $data->getRoles();
        $roles = $data->roles;
        $isdone = $isdonedata->tocollected();
       
    }

    // Checked
    public function ordCheck()
    {
        $this->template->view('RECBooking/pages/Ordernumcheck', $this->getCommonData());
    }

    // Checked
    public function bookingLog()
    {
        $this->template->view('RECBooking/pages/bookcollection_log', $this->getCommonData());
    }

    // Checked
    public function updateAPS()
    {
        $this->template->view('RECBooking/pages/updateAPSnew', $this->getCommonData());
    }

    // Checked
    public function rgr()
    {
        $this->template->view('RECBooking/pages/partweights', $this->getCommonData());
    }


    public function updateCol()
    {
        $this->template->view('RECBooking/pages/updatewgt_col_log', $this->getCommonData());
    }

    public function delRequest()
    {
        $this->template->view('RECBooking/pages/delete', $this->getCommonData());
    }

    public function delRequestMulti()
    {
        $this->template->view('RECBooking/pages/deletemutli', $this->getCommonData());
    }

    public function emailConf()
    {
        $this->template->view('RECBooking/pages/bookingConf', $this->getCommonData());
    }

    public function testArc()
    {
        $this->template->view('RECBooking/pages/arctest', $this->getCommonData());
    }


    public function testArcData()
    {
        $this->template->view('RECBooking/pages/arctestdata', $this->getCommonData());
    }


    public function companyNote()
    {
        $this->template->view('RECBooking/pages/companies', $this->getCommonData());
    }

    public function companyData()
    {
        $this->template->view('RECBooking/pages/companiesdata', $this->getCommonData());
    }


    public function companyUpdate()
    {
        $this->template->view('RECBooking/pages/updatecomps', $this->getCommonData());
    }

    public function delLine()
    {
        $this->template->view('RECBooking/pages/delline', $this->getCommonData());
    }

    public function addNewLine()
    {
        $this->template->view('RECBooking/pages/addnewline', $this->getCommonData());
    }


    public function updateConf()
    {
        $this->template->view('RECBooking/pages/emailcustomerconf', $this->getCommonData());
    }

    public function downloadCsv()
    {
        $data = new User();
        $isdownload = new Download();
        $data->getRoles();
        $roles = $data->roles;
        $file = $isdownload->getfile();
    }

    public function onHold()
    {
        $this->template->view('RECBooking/pages/holdon', $this->getCommonData());
    }


    public function amrApdate()
    {
        $this->template->view('RECBooking/pages/AMRupdate', $this->getCommonData());
    }


    public function bookStat()
    {
        $this->template->view('RECBooking/pages/bookstatupdate', $this->getCommonData());
    }


    public function newCompany()
    {
        $this->template->view('RECBooking/pages/addcomps', $this->getCommonData());
    }


    public function newRebate()
    {
        echo (new Rebate())->create();
    }

    public function rebatePage()
    {
        $data = (new Rebate())->getData();
        $this->template->view(
            'RECBooking/pages/rebate-page',
            array_merge(['rebateData' => $data], $this->getCommonData())
        );
    }

    public function rebateData()
    {
        $data = (new Rebate())->getData();
        $this->template->view(
            'RECBooking/pages/rebate-page',
            array_merge(['rebateData' => $data], $this->getCommonData())
        );
    }

    public function updateRebate()
    {
        echo (new Rebate())->create();
    }

    public function createBER()
    {
        $this->template->view('RECBooking/pages/createBER', $this->getCommonData());
    }

    public function getdataBER()
    {
        $this->template->view('RECBooking/pages/BERdata', $this->getCommonData());
    }

    public function berUpdate()
    {
        $this->template->view('RECBooking/pages/BERupdate', $this->getCommonData());
    }

    public function unDoneBtn()
    {
        $this->template->view('RECBooking/pages/Undonemulti', $this->getCommonData());
    }

    public function amrs()
    {
        $this->template->view('RECBooking/pages/amrmessage', $this->getCommonData());
    }

    public function goodsiInMail()
    {
        $this->template->view('RECBooking/pages/goodsinemail', $this->getCommonData());
    }

    public function goodsIn()
    {
        $data = new User();
        $data->getRoles();
        $roles = $data->roles;
        $rid = '';

        if (isset($_POST['rid'])) {
            $rid = $_POST['rid'];

            $info = new GoodInData();
            $check = $info->getcount($rid);

            if ($check !== 0) {
                $res = $info->getdata($rid);
                $name = $info->getname($rid);
                $ordnum = $info->getord($rid);
                $subinfo = $info->getsubinfo($rid);
            } else {
                $data->getCustomers();
                $customers = $data->customers;
                $this->template->view(
                    'RS/pages/goodins',
                    [
                        'data' => $data,
                        'customers' => $customers,
                        'roles' => $roles,
                        'rid' => $rid,
                        'info' => $info,
                        'check' => $check
                    ]
                );
            }
        }

        $data->getCustomers();
        $customers = $data->customers;

        $this->template->view(
            'RS/pages/goodins',
            [
                'data' => $data,
                'customers' => $customers,
                'roles' => $roles,
                'rid' => $rid ?? null,
                'res' => $res ?? null,
                'name' => $name ?? null,
                'ordnum' => $ordnum ?? null,
                'subinfo' => $subinfo ?? null
            ]
        );
    }

    public function goodsOut()
    {
        $data = new User();
        $palletinfo = new GoodSoutData();
        $palletlist = $palletinfo->getpallets();
        $loadlist = $palletinfo->getloads();
        $totalloads = $palletinfo->getloadtotals();
        $data->getRoles();
        $roles = $data->roles;


        $data->getCustomers();
        $customers = $data->customers;

        $this->template->view(
            'RS/pages/goodsout',
            [
                'data' => $data,
                'customers' => $customers,
                'roles' => $roles,
                'palletinfo' => $palletinfo,
                'palletlist' => $palletlist,
                'loadlist' => $loadlist,
                'totalloads' => $totalloads
            ]
        );
    }

    public function goodsInAdd()
    {
        $this->template->view('RECBooking/pages/goodsinUpdate', $this->getCommonData());
    }

    public function closeLoad()
    {
        $this->template->view('RECBooking/pages/loadclose', $this->getCommonData());
    }

    public function toggleCharge()
    {
        $this->template->view('RECBooking/pages/ischarge', $this->getCommonData());
    }

    public function bdmView()
    {
        if (isset($_POST['own'])) {
            $own = $_POST['own'];
        } else {
            $own = '%';
        }

        if (isset($_POST['filterstatus'])) {
            $filter = $_POST['filterstatus'];
        } else {
            $filter = '';
        }

        $data = new User();
        $bdmdata = new BdView();
        $bdmviewreport = $bdmdata->getdata($own, $filter);
        $listowners = $bdmdata->getowners();

        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;

        $this->template->view(
            'pages/viewbdmreport',
            [
                'data' => $data,
                'customers' => $customers,
                'roles' => $roles,
                'bdmviewreport' => $bdmviewreport,
                'listowners' => $listowners,
                'own' => $own,
                'filter' => $filter
            ]
        );
    }

    public function bdmDetail()
    {
        $data = new user();
        $prods = new BdmDetail();
        $rid = $_GET['reqid'];
        if (!isset($_GET['reqid'])) {
            $rid = '';
        } elseif ($_GET['reqid'] == 'de') {
            $rid = '';
        }

        $reqprodlist = $prods->getrequestdata($rid);
        $reqprodtotals = $prods->totals($rid);
        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;

        $this->template->view(
            'pages/bdmdetailpage',
            [
                'data' => $data,
                'customers' => $customers,
                'roles' => $roles,
                'reqprodlist' => $reqprodlist,
                'reqprodtotals' => $reqprodtotals,
                'prods' => $prods,
                'rid' => $rid
            ]
        );
    }

    public function addToRebate()
    {
        $data = new User();
        $reb = new AddRebate();

        $reb->insert();
        echo $reb->response;
    }

    public function invRebate()
    {
        $data = new User();
        $rebinv = new AddRebate();
        $data->getRoles();

        $data->getCustomers();


        $rebinv->invoicerebate();
        echo $rebinv->response;
    }

    public function approvedList()
    {
        $data = new User();
        $applist = new ApprovData();
        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;
        $list = $applist->getlist();
       
        $this->template->view(
            'RS/pages/approved-list-page',
            [
                'data' => $data,
                'customers' => $customers,
                'roles' => $roles,
                'list' => $list,
                'applist' => $applist,
            ]
        );
    }

    public function approvedUpdate()
    {
        $data = new User();
        $updateapp = new ApprovData();
        $data->getRoles();

        $data->getCustomers();
        $customers = $data->customers;
        $updateapp->updateapp();
    }

    public function changePasswordApp()
    {
        $data = new User();
        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;
        $this->template->view('pages/chanagepassmob', $this->getCommonData());
    }
}
