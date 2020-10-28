<?php

namespace App\Controllers;

use App\Models\RS\AddRebate;
use App\Models\RS\ApprovData;
use App\Models\RS\Arcdata;
use App\Models\RS\BdmDetail;
use App\Models\RS\BdView;
use App\Models\RS\Bookingdata;
use App\Models\RS\Charge;
use App\Models\RS\Download;
use App\Models\RS\GoodInData;
use App\Models\RS\GoodsInMail;
use App\Models\RS\GoodsOutData;
use App\Models\RS\IsDone;
use App\Models\RS\Pdfmaker;
use App\Models\RS\Rebate;
use App\Models\RS\Rgrdata;
use App\Models\RS\UpdateCollog;
use App\Models\RS\Companynotes;
use App\Models\RS\CompanyUpdate;
use App\Models\RS\NewCompany;
use App\Models\RS\OnHold;
use App\Models\RS\DeleteReq;
use App\Models\RS\UpdateDataDoc;
use App\Models\RS\UpdateAps;
use App\Models\RS\AddLineitem;
use App\Models\RS\ItadEmails;
use App\Models\RS\UploadImage;
use App\Models\RS\UnDone;
use App\Models\RS\ARCUpdates;
use App\Models\RS\Updaterebate;
use App\Models\User;

/**
 * Class RSController
 * @package App\Controllers
 */
class RSController extends AbstractController
{
    public function index()
    {
        $this->booking();
    }



    public function booking()
    {
        $bookdata = new Bookingdata();

        $booklist = $bookdata->getdata();
        $arealist = $bookdata->getareas();

        $this->template->view(
            'RECBooking/pages/bookedcollection',
            array_merge(
                [
                    'bookList' => $booklist,
                    'arealist' => $arealist
                ],
                $this->getCommonData()
            )
        );
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
        $arc= new Arcdata();
        $arcData= $arc->getdata();
        $areas = $arc->getareas();
        $this->template->view(
            'RECBooking/pages/Archive',
            array_merge(['arcData' => $arcData, 'areas' => $areas], $this->getCommonData())
        );
    }

    // Checked
    public function updatadata()
    {
        $update = new UpdateDataDoc();
        $update->update();
        //$this->template->view('RECBooking/pages/update', $this->getCommonData());
    }

    // Checked
    public function isEmail()
    {
        $this->template->view('RECBooking/pages/isEmail', $this->getCommonData());
    }

    // Checked
    public function isPdf()
    {
        echo (new Pdfmaker())->printdoc();
    }

    // Checked
    public function isDone()
    {
        $isdonedata = new IsDone();
        $isdonedata->tocollected();
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
        $apsupdate = new UpdateAps();
        $apsupdate->update();
    }

    // Checked
    public function rgr()
    {
        $getrgr = new Rgrdata();
        $data = $getrgr->getdata();
        $this->template->view(
            'RECBooking/pages/partweights',
            array_merge(['table' => $data], $this->getCommonData())
        );
    }

    public function updateCol()
    {
        $updatecol = new UpdateCollog();
        $updatecol->update();
    }

    public function delRequestMulti()
    {
        $del = new DeleteReq();
        $del->deletelist();
    }

    public function emailConf()
    {
        $emaildoc = new ItadEmails();
        $emaildoc->sendMail();
    }

    public function testArc()
    {
        $this->template->view('RECBooking/pages/arctest', $this->getCommonData());
    }

    public function testArcData()
    {
        $this->template->view('RECBooking/pages/arctestdata', $this->getCommonData());
    }

    public function imageupload()
    {
        $uploadimg = new UploadImage();
        $uploadimg->upload();
    }

    public function companyNote()
    {
        $companydata = new Companynotes();
        $table = $companydata->getdata();
        $areas = $companydata->getareas();
        $depts = $companydata->getdept();
        $owners = $companydata->getowners();
        $this->template->view(
            'RECBooking/pages/companies',
            array_merge(
                ['table' => $table,
                 'areas' => $areas,
                 'depts' => $depts,
                 'owners' => $owners
                ],
                $this->getCommonData()
            )
        );
    }

    public function companyUpdate()
    {
        $update = new CompanyUpdate();
        $update->update();
    }

    public function delLine()
    {
        $this->template->view('RECBooking/pages/delline', $this->getCommonData());
    }

    public function addNewline()
    {
        $addnew = new AddLineitem();
        $addnew->addline();
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
        $hold = new OnHold();
        $hold->onholdlist();
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
        $addcomp = new NewCompany();
        $addcomp->add();
    }

    public function addToRebate()
    {
        echo (new Rebate())->add();
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
        $updatereb = new Rebate();
        $updatereb->update();
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
        $undonereq = new unDone();
        $undonereq->undo();
    }

    public function amrs()
    {

        $amrsup = new ARCUpdates();
        $amrsup->update();
    }

    public function goodsInMail()
    {
        $goodsInMail = new GoodsInMail();
        echo $goodsInMail->process();
    }

    public function goodsIn()
    {
        $data = new User();
        $data->getRoles();
        $roles = $data->roles;
        $rid = null;

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
                'info' => $info ?? null,
                'check' => $check ?? null,
            ]
        );
    }

    public function goodsOut()
    {
        $data = new User();
        $palletinfo = new GoodsOutData();
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
        $goodsOutData = new GoodsOutData();
        echo $goodsOutData->goodsInAdd();
    }

    public function closeLoad()
    {
        $goodsOutData = new GoodsOutData();
        echo $goodsOutData->closeLoad();
    }

    public function toggleCharge()
    {
        $data = new User();
        $toggl = new Charge();
        $data->getRoles();
        $roles = $data->roles;
        $toggl->toggle();
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

    public function invRebate()
    {
        echo (new Rebate())->invoice();
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
