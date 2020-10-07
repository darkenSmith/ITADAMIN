<?php

namespace App\Controllers;

use App\Models\User;

/**
 * Class LoginController
 * @package App\Controllers
 */
class LoginController extends AbstractController
{

    // Calls: models/user.php > login()
    public function ajax()
    {
        if (isset($_GET['isAjax'])) {
            $user = new User();
            echo $user->ajaxLogin();
            exit();
        }

        $user = new User();
        $user->login();
    }

    // Calls: models/user.php -> update()
    public function change()
    {
        if (isset($_POST)) {
            $password = md5(stripslashes($_POST['password']));
            $data = new User();

            $data->get($_POST['user']);
            $data->update('password', $password);

            echo '
			<div class="alert alert-success fade-in" id="reset-container" >
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Password Changed</h4>
				<p>Please login using your new password.</p>
			</div>';
        } else {
            echo 'Unable to change Password';
        }
    }

    // Calls: models/user.php -> sendReminder()
    public function forgot()
    {
        if (isset($_POST['email'])) {
            $email = stripslashes(strtolower($_POST['email']));
            $user = new User();
            $user->loadByEmail($email);

            if ($user->user) {
                $user->generateToken();

                // Change tag: reminderMsg
                $user->sendReminder("change");

                echo '
				<div class="alert alert-success fade-in" id="reset-container" >
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<h4>Password Reset</h4>
					<p>Please check your email for details on how to reset your password.</p>
				</div>
				';
            } else {
                echo '
				<div class="alert alert-danger fade-in" id="reset-container" >
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<h4>Not Registered</h4>
					<p>That email address has not been registered. Please contact your account manager.</p>
				</div>
				';
            }
        } else {
            echo 'Invalid Email Address';
        }
    }

    public function index()
    {
        if (isset($_SESSION['user']['id'])) {
            header('Location: /');
        } else {
            if (isset($_GET['controller']) && isset($_GET['action'])) {
                $redirect = '/' . $_GET['controller'] . '/' . $_GET['action'];
            } else {
                $redirect = false;
            }


            $this->template->view(
                'login/index',
                [
                    'redirect' => $redirect
                ]
            );
        }
    }

    public function logout()
    {
        unset($_SESSION);
        session_destroy();
        header('Location: /');
    }

    // Calls: models/user.php -> resetPassword()
    public function reset()
    {
        unset($_SESSION);
        session_destroy();
        if (isset($_GET['id'])) {
            $token = $_GET['id'];
            $data = new User();
            $data->resetPassword($token);
            $user = $data->user;

            $this->template->view(
                'login/reset',
                [
                    'token' => $token,
                    'data' => $data,
                    'user' => $user,
                ]
            );
        } else {
            header('Location: /admin/users');
        }
    }
}
