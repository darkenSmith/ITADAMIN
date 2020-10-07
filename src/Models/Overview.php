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

        if (!empty($role)) {
            $company = new Company();
            $company->getCompany();
            $this->page = 'pages/home';
            $this->company = $company;
        }
    }
}
