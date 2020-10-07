<?php

namespace App\Controllers;

use App\Models\User;

/**
 * Class UserController
 * @package App\Controllers
 */
class UserController extends AbstractController
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
            'user/edit',
            [
                'id' => $id,
                'data' => $data,
                'customer' => $data->customers,
                'user' => $user
            ]
        );
    }
}
