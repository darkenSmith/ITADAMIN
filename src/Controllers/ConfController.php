<?php

namespace App\Controllers;

use App\Models\Conf\Confirm;
use App\Models\Conf\UnBook;
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
        //require_once('./RECbooking/thankyou.php');
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
        $data = new user();
        $data->getRoles();
        $roles = $data->roles;
        $confdata = new Confirm();
        $confirm = $confdata->confirmlist();

        $data->getCustomers();
        $customers = $data->customers;
        //require_once( './RECbooking/confirmmutli.php');
    }

    public function unconfmulti()
    {
        $data = new user();
        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;
        $this->template->view('RECBooking/pages/unconfirmmutli', $this->getCommonData());
        //require_once( './RECbooking/unconfirmmutli.php');
    }

    public function unbookmulti()
    {
        return (new UnBook())->unbookrequest();
    }
}
