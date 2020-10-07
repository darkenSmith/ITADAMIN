<?php

namespace App\Controllers;

use App\Models\Permissions;
use App\Models\User;

/**
 * Class AdminController
 * @package App\Controllers
 */
class AdminController extends AbstractController
{
    public function profile(): void
    {
        $id = $_SESSION['user']['id'];
        $data = new User();
        $data->get($id);
        $data->getRoles();
        $user = $data->user;

        $data->getCustomers();

        $user->page = 'profile';

        $this->template->view(
            'admin/edit',
            [
                'id' => $id,
                'data' => $data,
                'customer' => $data->customers,
                'user' => $user
            ]
        );
    }

    public function add()
    {
        $data = new User();
        $data->getRoles();
        $roles = $data->roles;

        $data->getCustomers();
        $customers = $data->customers;

        $this->template->view(
            'admin/adduser',
            [
                'customers' => $customers,
                'data' => $data,
                'roles' => $roles
            ]
        );
    }

    public function edit()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $data = new User();
            $data->get($id);
            $data->getRoles();
            $user = $data->user;
            $user->page = 'edit';
            $roles = $data->roles;

            // Change tag: addCustomer
            $data->getCustomers();
            $customers = $data->customers;

            $this->template->view(
                'admin/edit',
                [
                    'id' => $id,
                    'data' => $data,
                    'roles' => $roles,
                    'customers' => $customers,
                    'user' => $user
                ]
            );
        } else {
            header('Location: /admin/users');
        }
    }

    public function users()
    {
        $data = new User();
        $data->get();

        $this->template->view(
            'admin/list',
            [
                'data' => $data
            ]
        );
    }

    public function editUserPost()
    {
        if (isset($_POST['id'])) {
            $id = stripslashes($_POST['id']);
            $data = new user();
            $data->updateUser($id);
            echo $data->response;
        }
    }

    public function managePermissions()
    {
        if ($_POST) {
            $original = $_SESSION['allowed'];
        }
    }

    public function permissions()
    {
        $permissions = new Permissions();
        $permissions->load();
        $allowed = $permissions->permissions;
        $sections = $permissions->section;
        $_SESSION['allowed'] = $permissions->permissions;


        $this->template->view(
            'admin/permissions',
            [
                'permissions' => $permissions,
                'allowed' => $allowed,
                'sections' => $sections
            ]
        );
    }
}
