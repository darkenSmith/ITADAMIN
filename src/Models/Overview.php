<?php

namespace App\Models;

/**
 * Class Overview
 * @package App\Models
 */
class Overview extends AbstractModel
{

    public $company;
    public $page = null;
    private $unallocated;

    /**
     * Overview constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function route(): void
    {
        $role = $_SESSION['user']['role_id'];

        if (!empty($role) && $role == 1) {
            //stone admin
            $company = new Company();
            $company->getCustomers();
            $company->getUnallocated();

            $this->page = 'overview/stone-admin';
            $this->company = $company->customers;
            $this->unallocated = $company->unallocated;
        } elseif (!empty($role) && $role == 2) {
            //stone staff
            //load customers by bdm
            $company = new Company();
            $company->getCustomers();
            $company->getUnallocated();
            $this->page = 'overview/stone-staff';
            $this->company = $company->customers;
            $this->unallocated = $company->unallocated;
        } elseif (!empty($role)) {
            $company = new Company();
            $company->getCompany();
            $this->page = 'pages/home';
            $this->company = $company;
        } else {
            exit;
        }
    }
}
