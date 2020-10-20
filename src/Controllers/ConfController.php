<?php

namespace App\Controllers;

use App\Models\Conf\Confirm;
use App\Models\Conf\UnBook;
use App\Models\Conf\UnConf;
use App\Models\User;

/**
 * Class ConfController
 * @package App\Controllers
 */
class ConfController
{
    public function thankyoucust()
    {
        $data = new user();
        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;
        $this->template->view('RECBooking/pages/thankyou', $this->getCommonData());
    }

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

    public function confmulti()
    {
        $confdata = new Confirm();
        $confdata->confirmlist();
    }

    public function unconfmulti()
    {
        $unconf = new UnConf();
        $unconf->unconfirmlist();
    }

    public function unbookmulti()
    {
        $confdata = new UnBook();
        $confdata->unbookrequest();
    }
}
