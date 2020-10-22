<?php

namespace App\Controllers;

use App\Models\Company;

/**
 * Class CompanyController
 * @package App\Controllers
 */
class CompanyController extends AbstractController
{
    public function index()
    {
    }

    public function view()
    {
        $data = null;
        $summary = null;
        $collections = null;

        if (isset($_GET['id'])) {
            $company = new Company();
            $company->loadById($_GET['id'], true);

            if ($company->auth) {
                $data = $company->data;
                $summary = $company->summary;
                $collections = $company->collections;
            }

            $this->template->view(
                'pages/company',
                [
                    'data' => $data,
                    'id' => $_GET['id'],
                    'company' => $company,
                    'summary' => $summary,
                    'collections' => $collections
                ]
            );
        } else {
            echo 'Unable to load Company';
        }
    }

    public function claim()
    {
        if (isset($_POST)) {
            $upload = new Company();
            $upload->claim();
            header('Location: /');
        }
    }

    public function refresh()
    {
        //@todo - this requires config on the server for mssql and ssh access.
        $company = new Company();
        $company->refresh();
    }
}
