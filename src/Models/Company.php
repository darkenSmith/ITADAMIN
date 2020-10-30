<?php

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;
use Exception;

/**
 * Class Company
 * @package App\Models
 */
class Company extends AbstractModel
{
    public $userId;
    public $companies;
    public $unallocated;
    public $auth = true;
    public $customers;

    /**
     * Company constructor.
     */
    public function __construct()
    {
        if (isset($_SESSION['user']['id'])) {
            $this->userId = $_SESSION['user']['id'];
            $this->userRole = $_SESSION['user']['role_id'];
        }

        $this->sdb = Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak', false);

        parent::__construct();
    }

    public function getCompany()
    {
        $sql = 'SELECT company_id from recyc_customer_links_to_company where user_id = :user';
        $result = $this->rdb->prepare($sql);
        $result->execute(array(':user' => $this->userId));
        $this->companies = $result->fetchAll(\PDO::FETCH_OBJ);

        if ($this->companies) {
            foreach ($this->companies as $company) {
                $data = $this->loadById($company->company_id);
                foreach ($data as $key => $value) {
                    $company->{$key} = $value;
                }
                $this->checkFiles($company->collections);
            }
        }
        unset($this->rdb);
    }


    public function updateCmps(){

        $sql = 'select ordernum from Collections_Log';
        $result = $this->sdb->prepare($sql);
        $result->execute();
        $this->ordernumbers = $result->fetchAll(\PDO::FETCH_OBJ);

        foreach($this->ordernumbers as $orders){

            $sql = "select crmnumber as cmp  from [greenoak].[we3recycler].[dbo].SalesOrders as so with(nolock)  
            join [greenoak].[we3recycler].[dbo].company as c on 
            c.companyid = so.companyid where replace(so.salesordernumber, 'ORD-', '') =:ord";
            $result = $this->gdb->prepare($sql);
            $result->execute(array(':ord' => $orders->ordernum));
            $cmpnumbers = $result->fetch(\PDO::FETCH_OBJ);

            if($cmpnumbers){
                        $sql = "update Collections_Log
                        set cmp_num = '".$cmpnumbers->cmp."'
                        where ordernum = :ord";
                        $result = $this->sdb->prepare($sql);
                        $result->execute(array(
                            ':cmp' => $cmpnumbers->cmp,
                            ':ord' => $orders->ordernum
                         ));
            }else{
                    $sql = "update Collections_Log
                    set cmp_num = 'NOT FOUND'
                    where ordernum =".$orders->ordernum;
                    $result = $this->sdb->prepare($sql);
                    $result->execute();
            }
 
        }

    }

    public function loadById($id, $return = false)
    {
        $results = new \stdClass();

        if ($_SESSION['user']['role_id'] == 1 || $_SESSION['user']['role_id'] == 2) {
            $sql = 'SELECT cl.* FROM recyc_company_list cl
						left join recyc_bdm_to_company bc on cl.id = bc.company_id 
						WHERE id = :company and user_id = :user';
        } elseif ($_SESSION['user']['role_id'] == 3 || $_SESSION['user']['role_id'] == 4) {
            $sql = 'SELECT * FROM recyc_company_list cl
						LEFT JOIN recyc_customer_links_to_company c on cl.id = c.company_id 
						WHERE c.company_id = :company 
						and user_id = :user';
        } else {
            return 'Not Authorised';
        }

        $result = $this->rdb->prepare($sql);
        $result->execute(array(':company' => $id, ':user' => $_SESSION['user']['id']));
        $results->data = $result->fetch(\PDO::FETCH_OBJ);

        if ($results->data) {
            $results->summary = $this->getSummary($id);
            $results->collections = $this->getCollections($id);

            if ($return) {
                foreach ($results as $key => $value) {
                    $this->{$key} = $value;
                }
                $this->checkFiles($this->collections);
            } else {
                return $results;
            }
        } else {
            $this->auth = false;
        }
    }

    public function getSummary($id)
    {
        $sql = 'select location_name, address1,address2,address3,address4, postcode,
				telephone,count(location_id) as "collections" 
				from recyc_order_information where company_id = :company 
				GROUP BY location_id';
        $result = $this->rdb->prepare($sql);
        $result->execute(array(':company' => $id));
        $data = $result->fetchAll(\PDO::FETCH_OBJ);
        return $data;
    }

    public function getCollections($id)
    {
        $sql = 'SELECT * FROM `recyc_order_information` WHERE `company_id` = :company ORDER BY actual_delivery_date desc';
        $result = $this->rdb->prepare($sql);
        $result->execute(array(':company' => $id));
        $data = $result->fetchAll(\PDO::FETCH_OBJ);

        return $data;
    }

    public function checkFiles($collections)
    {
        foreach ($collections as $collection) {
            $files = array(
                'asset' => 'Asset-Management-Report.pdf',
                'disposal' => 'Certificate-of-Disposal.pdf',
                'rebate' => 'Rebate-Report.pdf'
            );
            //loop through, add to collection
            foreach ($files as $dir => $file) {
                $path = PROJECT_DIR . 'uploads/' . $dir . '/';
                $filename = $collection->sales_order_number . '-' . $file;
                $altFilename = $collection->sales_order_number . '-' . str_replace('-', ' ', $file);
                if (file_exists($path . $filename)) {
                    $collection->files[$dir] = $collection->sales_order_number . $file;
                } elseif (file_exists($path . $altFilename)) {
                    $collection->files[$dir] = $altFilename;
                }
            }
            $sql = 'SELECT * FROM recyc_uploads WHERE order_id = :id and downloadable = 1';
            $result = $this->rdb->prepare($sql);
            $result->execute(array(':id' => $collection->id));
            $newFiles = $result->fetchAll(\PDO::FETCH_OBJ);

            foreach ($newFiles as $newFile) {
                if ($newFile->file_type == 'Asset Management Report') {
                    $dir = 'asset';
                } elseif ($newFile->file_type == 'Rebate Report') {
                    $dir = 'rebate';
                } elseif ($newFile->file_type == 'Certificate of Disposal') {
                    $dir = 'disposal';
                } else {
                    continue;
                }
                $collection->files[$dir] = $newFile->filename;
            }
            $e = 123;
        }
    }

    public function getCustomers()
    {
        $sql = 'SELECT * from recyc_company_list cl
				left join recyc_bdm_to_company bc on cl.id = bc.company_id 
				where user_id = :user';
        $result = $this->rdb->prepare($sql);
        $result->execute(array(':user' => $this->userId));
        $this->customers = $result->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getUnallocated()
    {
        $sql = "SELECT * from recyc_company_list cl
				left join recyc_bdm_to_company bc on cl.id = bc.company_id
				where (user_id = '' OR user_id IS NULL) 
				and portal_requirement = 1";
        $result = $this->rdb->prepare($sql);
        $result->execute(array(':user' => $this->userId));
        $this->unallocated = $result->fetchAll(\PDO::FETCH_OBJ);

        Logger::getInstance("Company.log")->info('getUnallocated', [$this->unallocated]);
    }

    public function claim()
    {
        $id = $_POST['company'];
        $bdm = $_SESSION['user']['id'];
        $sql = 'INSERT INTO recyc_bdm_to_company (user_id,company_id) VALUES  (:bdm , :company)';
        $result = $this->rdb->prepare($sql);
        $result->execute(array(':company' => $id, ':bdm' => $bdm));
    }

    public function refresh($return = true)
    {
        $count = 0;
        try {
            $sql = "SELECT convert(varchar(255),[CompanyID]) as 'company_id', CompanyName as 'compname', CompanyDescription, CRMNumber as 'ccmp', InvoiceAddressPostCode as 'postcode' FROM [dbo].[Company]";
            $result = $this->gdb->query($sql);
            $data = $result->fetchAll(\PDO::FETCH_OBJ);

            Logger::getInstance("CompanyRefresh.log")->debug(
                'data',
                ['line' => __LINE__, count($data)]
            );
        } catch (\Exception $e) {
            Logger::getInstance("CompanyRefresh.log")->error(
                'refresh-exception',
                ['line' => __LINE__, $e->getMessage()]
            );
        }

        if (isset($data)) {
            foreach ($data as $webUser) {
                try {
                    $sql = "SELECT * from recyc_company_sync WHERE greenoak_id = :greenoak";
                    $result = $this->rdb->prepare($sql);
                    $result->execute(array(':greenoak' => $webUser->company_id));
                    $exists = $result->fetchAll(\PDO::FETCH_OBJ);

                    $sql2 = "SELECT * from companies WHERE CMP = :cmpnum";
                    $result2 = $this->sdb->prepare($sql2);
                    $result2->execute(array(':cmpnum' => $webUser->ccmp));
                    $exists2 = $result2->fetchall(\PDO::FETCH_OBJ);

                    Logger::getInstance("CompanyRefresh.log")->debug(
                        'exists2',
                        [
                            'line' => __LINE__,
                            [
                                ':greenoak' => $webUser->company_id

                            ],
                            [
                                ':cmpnum' => $webUser->ccmp

                            ]
                        ]
                    );
                } catch (\Exception $e) {
                    Logger::getInstance("CompanyRefresh.log")->error(
                        '2-select-errored',
                        ['line' => __LINE__, $e->getMessage()]
                    );
                }

                if (empty($exists2)) {
                    try {
                        $sql3 = "INSERT into companies(CompanyName, Location, cmp, dateadded, Department, owner )
                        VALUES (:name,:loc,:cmp, GETDATE(), 'new', 'new')";
                        $result2 = $this->sdb->prepare($sql3);
                        $result2->execute(
                            [
                                ':name' => $webUser->compname,
                                ':loc' => $webUser->postcode,
                                ':cmp' => $webUser->ccmp
                            ]
                        );
                        Logger::getInstance("CompanyRefresh.log")->info(
                            'insert',
                            ['line' => __LINE__, 'sql' => $sql3]
                        );
                    } catch (\Exception $e) {
                        Logger::getInstance("CompanyRefresh.log")->error(
                            'insert',
                            ['line' => __LINE__, $e->getMessage()]
                        );
                    }

                    Logger::getInstance("CompanyRefresh.log")->info('getUnallocated', [$this->unallocated]);
                }

                /**
                 * UPDATES CMP IN WEB DB
                 */
                if (!empty($exists)) {
                    try {
                        $sql = "UPDATE recyc_company_sync
					SET CMP = :cmp
					WHERE greenoak_id = :greenoak";
                        $result = $this->rdb->prepare($sql);
                        $result->execute(array(':greenoak' => $webUser->company_id, ':cmp' => $webUser->ccmp));

                        Logger::getInstance("CompanyRefresh.log")->info(
                            'update',
                            ['line' => __LINE__,
                                [':greenoak' => $webUser->company_id, ':cmp' => $webUser->ccmp],
                                'sql' => $sql
                            ]
                        );
                    } catch (\Exception $e) {
                        Logger::getInstance("CompanyRefresh.log")->error(
                            'update errored below !empty($exists)',
                            ['line' => __LINE__, $e->getMessage()]
                        );
                    }
                }

                if (empty($exists)) {
                    //get all the company data we need to set them up
                    try {
                        $sql = "SELECT CompanyName, PrimaryAddressLine1,PrimaryAddressLine2,PrimaryAddressLine3,PrimaryAddressLine4 ,PrimaryAddressTown, PrimaryAddressPostCode, Telephone, Email, SiteCode, SICCode, CRMNumber
                                FROM [dbo].[Company] WHERE [CompanyID] = :greenoak";
                        $result = $this->gdb->prepare($sql);
                        $result->execute(array(':greenoak' => $webUser->company_id));
                        $data = $result->fetch(\PDO::FETCH_OBJ);
                        Logger::getInstance("CompanyRefresh.log")->info(
                            'select',
                            [
                                'line' => __LINE__,
                                [':greenoak' => $webUser->company_id],
                                'sql' => $sql,
                                count($data)
                            ]
                        );
                    } catch (\Exception $e) {
                        Logger::getInstance("CompanyRefresh.log")->error(
                            'select errored below empty($exists)',
                            ['line' => __LINE__, $e->getMessage()]
                        );
                    }

                    if (isset($data)) {
                        //add first to company table, and then once we have the company ID add to the sync table.
                        try {
                            $sql = "INSERT INTO recyc_company_list (company_name, portal_requirement) VALUES (:company, 1)";
                            $result = $this->rdb->prepare($sql);
                            $result->execute(array(':company' => $data->CompanyName));

                            $comId = $this->rdb->lastInsertId();
                            Logger::getInstance("CompanyRefresh.log")->info(
                                'insert',
                                ['line' => __LINE__,
                                    'comId' => $comId,
                                    [':company' => $data->CompanyName],
                                    'sql' => $sql
                                ]
                            );
                        } catch (\Exception $e) {
                            Logger::getInstance("CompanyRefresh.log")->error(
                                'insert errored below isset($data)',
                                ['line' => __LINE__, $e->getMessage()]
                            );
                        }
                        if ($comId != 0) {
                            try {
                                $sql = "INSERT INTO recyc_company_sync (company_id, greenoak_id, company_name, CMP) VALUES (:recyc,:greenoak,:company,:cmp)";
                                $result = $this->rdb->prepare($sql);
                                $execute = [
                                    ':recyc' => $comId,
                                    ':greenoak' => $webUser->company_id,
                                    ':company' => $data->CompanyName,
                                    ':cmp' => $data->CRMNumber
                                ];
                                $result->execute($execute);
                                Logger::getInstance("CompanyRefresh.log")->info(
                                    'insert',
                                    ['line' => __LINE__, 'comId' => $comId, $execute, 'sql' => $sql]
                                );
                            } catch (\Exception $e) {
                                Logger::getInstance("CompanyRefresh.log")->error(
                                    'insert 1 errored below comId != 0',
                                    ['line' => __LINE__, $e->getMessage()]
                                );
                            }
                            try {
                                $sql = "INSERT INTO recyc_bdm_to_company (user_id,company_id) VALUES (1,:company)";
                                $result = $this->rdb->prepare($sql);
                                $result->execute(array(':company' => $comId));

                                Logger::getInstance("CompanyRefresh.log")->info(
                                    'insert',
                                    ['line' => __LINE__, 'comId' => $comId, array(':company' => $comId), 'sql' => $sql]
                                );
                            } catch (\Exception $e) {
                                Logger::getInstance("CompanyRefresh.log")->error(
                                    'insert 2 errored below comId != 0',
                                    ['line' => __LINE__, $e->getMessage()]
                                );
                            }

                            $owner = $webUser->CompanyDescription;
                            $parts = explode('#', $owner);
                            $owner = isset($parts[1]) ? $parts[1] : 'ITAD@stonegroup.co.uk';
                            if (isset($owner)) {
                                $sql = "SELECT id from recyc_users WHERE username = :owner";
                                $result = $this->rdb->prepare($sql);
                                $result->execute(array(':owner' => $owner));
                                $ownerId = $result->fetch(\PDO::FETCH_COLUMN);

                                if (isset($ownerId)) {
                                    try {
                                        $sql = "INSERT INTO recyc_bdm_to_company (user_id,company_id) VALUES (:owner,:company)";
                                        $result = $this->rdb->prepare($sql);
                                        $result->execute([':company' => $comId, ':owner' => $ownerId]);
                                        Logger::getInstance("CompanyRefresh.log")->info(
                                            'insert',
                                            [
                                                'line' => __LINE__,
                                                [':company' => $comId, ':owner' => $ownerId],
                                                'sql' => $sql
                                            ]
                                        );
                                    } catch (\Exception $e) {
                                        Logger::getInstance("CompanyRefresh.log")->error(
                                            'insert errored below isset($ownerId)',
                                            ['line' => __LINE__, $e->getMessage()]
                                        );
                                    }
                                }
                            }

                            $count++;
                        } else {
                            Logger::getInstance("CompanyRefresh.log")->error(
                                'error adding',
                                ['line' => __LINE__, $data->CompanyName]
                            );
                            echo 'error adding ' . $data->CompanyName . '<br>';
                        }
                    }
                } else {
                    Logger::getInstance("CompanyRefresh.log")->error(
                        'error adding',
                        ['line' => __LINE__, $webUser->CompanyName . ' exists']
                    );
                }
            }
        }
        if ($return) {
            echo $count . ' New Companies created<br>';
            Logger::getInstance("CompanyRefresh.log")->error(
                'NEW COMPANY CREATED',
                ['line' => __LINE__, 'count' => $count]
            );
        }
    }

    /**
     * @return bool
     */
    public function company_sync()
    {
        try {
            $sql = "SELECT g.company_number, s.company_id from recyc_company_sync as s
            join company_greenoak_links as g on
           s.company_id = g.company_id where s.greenoak_id = 'AWAITING UPDATE'";
            $result = $this->rdb->query($sql);
            $data = $result->fetchAll(\PDO::FETCH_OBJ);

            $cNumber = [];
            $q = '';
            foreach ($data as $company) {
                $cNumber[$company->company_number] = $company->company_id;
                $q .= "'" . $company->company_number . "',";
            }

            $q = rtrim($q, ',');
            $sql = "SELECT convert(varchar(255),[CompanyID]) as 'company_id', CompanyName as 'compname', CompanyDescription, CRMNumber as 'cmp', InvoiceAddressPostCode as 'postcode', CompanyRegNo  as 'companynum' FROM [dbo].[Company] WHERE CompanyRegNo IN (" . $q . ")";
            $result = $this->gdb->query($sql);
            $data2 = $result->fetchAll(\PDO::FETCH_OBJ);

            foreach ($data2 as $greenCompany) {
                $company_id = $cNumber[$greenCompany->companynum];

                $sql = " UPDATE recyc_company_sync
					SET CMP = :cmp,
                    greenoak_id = :greenoak_id
					WHERE company_id = :company_id";
                $result = $this->rdb->prepare($sql);
                $result->execute([
                    ':company_id' => $company_id,
                    ':greenoak_id' => $greenCompany->company_id,
                    ':cmp' => $greenCompany->cmp
                ]);
            }

            Logger::getInstance("CompanySync.log")->debug(
                'Company Synced',
                [
                    'line' => __LINE__,
                    'data' => count($data),
                    'data2' => count($data2)
                ]
            );

            return true;
        } catch (Exception $e) {
            Logger::getInstance("CompanySync.log")->error(
                'Update Failed',
                ['line' => __LINE__, $e->getMessage()]
            );
        }
        return false;
    }
}
