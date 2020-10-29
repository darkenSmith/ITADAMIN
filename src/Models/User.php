<?php

namespace App\Models;

use App\Helpers\Config;
use App\Helpers\Logger;

/**
 * Class User
 * @package App\Models
 */
class User extends AbstractModel
{
    public $stoneUsers;
    public $users;
    public $user;
    public $roles;
    public $heading;
    private $emailConfig;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->heading = 'Users';
        $this->emailConfig = Config::getInstance()->get('email');

        parent::__construct();
    }

    // Called by: controllers/admin_controller.php > edit.php
    // Called by: addUser()
    // Called by: updateUser()

    public function getRoles()
    {
        $sql = 'SELECT * FROM recyc_roles';
        $result = $this->rdb->prepare($sql);
        $result->execute();

        $this->roles = $result->fetchAll(\PDO::FETCH_OBJ);
    }

    // Called by: controllers/admin_controller.php > edit.php
    // Called by: controllers/admin_controller.php > profile()

    public function ajaxLogin()
    {
        $username = stripslashes($_POST['username']);
        $password = md5(stripslashes($_POST['password']));

        if (!empty($password) && !empty($username)) {
            $sql = 'SELECT * FROM recyc_users WHERE username = :user AND password = :pass AND active = 1 AND role_id <> 3 LIMIT 1';
            $result = $this->rdb->prepare($sql);
            $values = array(':user' => $username, ':pass' => $password);
            $result->execute($values);
            $result = $result->fetch(\PDO::FETCH_ASSOC);
        }

        if (!empty($result['id'])) {
            $_SESSION['user'] = $result;
            return 1;
        } else {
            $sql = 'SELECT username, active FROM recyc_users  WHERE username = :user';
            $query = $this->rdb->prepare($sql);
            $values = array(':user' => $username);
            $query->execute($values);
            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if ($result) {
                if (empty($result['username'])) {
                    return 'alert("User does not exist");';
                } elseif ($result['active'] == 0) {
                    return 'alert("Your account is not active.");';
                }
            } else {
                return 'alert("User does not exist");';
            }
        }

        return 'alert("User does not exist.");';
    }

    // Called by: addUser()

    public function login()
    {
        /// loads new companies
        /////////
        // $company = new company();
        // $company->refresh(true);
        ////////////

        $username = stripslashes(strtolower($_POST['username']));
        $password = md5(stripslashes($_POST['password']));

        if (!empty($password) && !empty($username)) {
            $sql = 'SELECT * FROM recyc_users WHERE username = :user AND password = :pass AND active = 1 AND role_id <> 3 LIMIT 1';

            $result = $this->rdb->prepare($sql);
            $values = array(':user' => $username, ':pass' => $password);
            $result->execute($values);
            $result = $result->fetch(\PDO::FETCH_ASSOC);
        }

        if (!empty($result['id'])) {
            $_SESSION['user'] = $result;
            $alert = 1;
        } else {
            $sql = 'SELECT username, active, password FROM recyc_users  WHERE username = :user LIMIT 1';
            $query = $this->rdb->prepare($sql);
            $values = array(':user' => $username);
            $query->execute($values);
            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if ($result) {
                if (empty($result['username'])) {
                    $alert = "User does not exist";
                } elseif ($result['active'] == 0) {
                    $alert = 'Your account is not active.';
                } elseif ($result['password'] != $password) {
                    $alert = 'Incorrect Password';
                } else {
                    $alert = 'YUser does not exist';
                }
            } else {
                $alert = 'User does not exist';
            }
        }
        echo $alert;
    }

    public function resetPassword($token)
    {
        $sql = 'SELECT * FROM recyc_users WHERE token = :token AND token_expires >= NOW() LIMIT 1';
        $result = $this->rdb->prepare($sql);
        $result->execute(array(':token' => $token));
        $this->user = $result->fetch(\PDO::FETCH_OBJ);
    }

    // Called by: login_controller.php -> ajax()

    public function update($type, $value)
    {
        if ($type == 'password') {
            $sql = 'UPDATE recyc_users SET password = :pw, token = null, token_expires = null WHERE id = :id LIMIT 1';
            $values = array(':pw' => $value, ':id' => $this->user->id);
            $result = $this->rdb->prepare($sql);
            $result->execute($values);
        } elseif ($type == 'full') {
            //@todo
        }
    }

    // Called by: login_controller.php -> forgot()
    // Called by: addUser()

    public function updateUser($id)
    {

        $this->get($id);

        // Change tag : updateUser
        // Prepare values for update
        $values = array(
            ':firstname' => $_POST["firstname"],
            ':lastname' => $_POST["lastname"],
            ':email' => $_POST["email"],
            ':id' => $id
        );
        // End Change tag : updateUser

        // Check for password change
        if ($_POST['password'] != "" && $_POST["confirmation"] != "" && ($_POST['password'] == $_POST['confirmation'])) {
            unset($_POST['confirmation']);
            $_POST['password'] = md5($_POST['password']);

            // Change tag : updateUser
            // Assign password change to $values array for update
            $values += [":password" => $_POST["password"]];
        }

        // Check for Role ID
        if (isset($_POST["role_id"]) && $_POST["role_id"] != "") {
            $values += [':role_id' => $_POST["role_id"]];
        }

        try {
            // Change tag : updateUser
            // Update user record
            $sql = "UPDATE
				recyc_users
			SET
				firstname = :firstname,
				lastname	= :lastname,
				email			= :email
			";
            if ($_POST["password"] != "") {
                $sql .= ", password		= :password";
            }
            if (isset($_POST["role_id"]) && $_POST["role_id"] != "") {
                $sql .= ", role_id		= :role_id ";
            }

            $sql .= "
			WHERE
				id = :id";
            $result = $this->rdb->prepare($sql);
            $result->execute($values);

            // Check if updating a user with a customer
            if (isset($_POST["role_id"]) && ($_POST["role_id"] == "3" || $_POST["role_id"] == "4") && count($_POST["customer_id"]) > 0) {
                // Remove all existing customer associations
                $result = $this->rdb->query("DELETE FROM recyc_customer_links_to_company WHERE user_id = " . $id);
                $result->execute();

                // Add each custome to the user
                foreach ($_POST["customer_id"] as $customer_id) {
                    // Check if user already linked with customer
                    $sql = "SELECT * FROM recyc_customer_links_to_company WHERE user_id = " . $id . " AND company_id = " . $customer_id;
                    $result = $this->rdb->query($sql);
                    $exists = $result->fetch(\PDO::FETCH_OBJ);

                    if (!$exists) {
                        // Insert record
                        $sql = "INSERT INTO recyc_customer_links_to_company (user_id, company_id) VALUES (:id,:customer_id)";
                        $statement = $this->rdb->prepare($sql);
                        $values = array(':id' => $id, ':customer_id' => $customer_id);
                        $statement->execute($values);

                        $company = $this->getCompanyById($customer_id);
                        if ($company) {
                            $sql = "INSERT INTO recyc_company_sync (company_id, greenoak_id, company_name, CMP) VALUES (:recyc,:greenoak,:company,:cmp)";
                            $result = $this->rdb->prepare($sql);
                            $result->execute(
                                [
                                    ':recyc' => $company->id,
                                    ':greenoak' => 'AWAITING UPDATE',
                                    ':company' => $company->company_name,
                                    ':cmp' => null
                                ]
                            );
                        }
                    }
                }
            }
            // End change tag updateUser

            $this->response = '
			<div class="alert alert-success fade-in" id="reset-container" >
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Profile Updated</h4>
				<p>User updated successfully</p>
			</div>
			';
        } catch (\Exception $e) {
            $this->response = '
			<div class="alert alert-danger fade-in" id="reset-container" >
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Error</h4>
				<p>There was a problem saving the user.  Please try again.</p>
				<p>' . $e->getMessage() . '</p>
			</div>
			';
        }
    }

    // Called by: addUser()

    public function get($id = false)
    {

        if ($id) {
            $sql = 'SELECT
				id,
				username,
				firstname,
				lastname,
				email,
				active,
				role_id,
				password
			FROM
				recyc_users
			WHERE
				id = :id
			';
            $result = $this->rdb->prepare($sql);
            $result->execute(array(':id' => $id));
            $this->user = $result->fetch(\PDO::FETCH_OBJ);

            // $sql   = 'SELECT structure_id FROM recyc_permissions WHERE role_id = :role';
            // $result = $this->rdb->prepare( $sql );
            // $result->execute(array(':role' => $this->user->role_id));
            // $this->user->permissions = $result->fetchAll(\PDO::FETCH_OBJ );

            $sql = 'SELECT
				c.id,
				c.company_name
			FROM
				recyc_customer_links_to_company cc
			left join
				recyc_company_list c on cc.company_id = c.id
			where
				user_id = :id
			';
            $result = $this->rdb->prepare($sql);
            $result->execute(array(':id' => $id));
            $this->user->companies = $result->fetchAll(\PDO::FETCH_OBJ);

            $sql = 'SELECT
				c.id,
				c.company_name
			from
				recyc_customer_links_to_company cc
			left join
				recyc_company_list c on cc.company_id = c.id
			where
				user_id = :id
			';
            $result = $this->rdb->prepare($sql);
            $result->execute(array(':id' => $id));
            $this->user->customers = $result->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $sql = 'SELECT * FROM recyc_users WHERE (role_id = 1 OR role_id = 2)';
            $result = $this->rdb->prepare($sql);
            $result->execute();
            $this->stoneUsers = $result->fetchAll(\PDO::FETCH_OBJ);

            $sql = 'SELECT * FROM recyc_users WHERE (role_id = 3 OR role_id = 4)';
            $result = $this->rdb->prepare($sql);
            $result->execute();
            $this->users = $result->fetchAll(\PDO::FETCH_OBJ);
        }
    }

    public function getCompanyById($companyId)
    {
        $sql = 'SELECT id, company_name FROM `recyc_company_list` WHERE id = :companyId';
        $result = $this->rdb->prepare($sql);
        $result->execute(array(':companyId' => $companyId));
        $company = $result->fetch(\PDO::FETCH_OBJ);


        if (!empty($company->id)) {
            return $company;
        }

        return false;
    }

    // Called by: controllers/login_controller.php -> reset()

    public function addUser()
    {
        $values = array();

        try {
            $email = stripslashes(strtolower($_POST['email']));
            $this->loadByEmail($email);

            // Check if user already exists or not
            if (!$this->user) {
                // Build up array from posted values
                foreach ($_POST as $k => $v) {
                    if ($k != "customer_id") {
                        $values[':' . $k] = stripslashes($v);
                    }
                }

                // Create record for user in recyc_users return the new user id
                $sql = 'INSERT INTO recyc_users (username, firstname,lastname, email, active, role_id) VALUES (:username,:firstname, :lastname,:email, 1, :role_id)';
                $result = $this->rdb->prepare($sql);
                $result->execute($values);

                // Get user id just created and init $this->user to stop empty object warning message
                $this->user = new \stdClass();
                $this->user->id = $this->rdb->lastInsertId();

                // Check if a user id was returned by the insert
                if ($this->user->id != 0) {
                    // Change tag: addCustomer
                    // Check if adding a user with a customer
                    if (($_POST["role_id"] == "3" || $_POST["role_id"] == "4") && count($_POST["customer_id"]) > 0) {
                        foreach ($_POST["customer_id"] as $customerId) {
                            $sql = "INSERT INTO recyc_customer_links_to_company (user_id, company_id) VALUES (:id,:customer_id)";
                            $values = array(':id' => $this->user->id, ':customer_id' => $customerId);

                            Logger::getInstance("User.log")->debug(
                                'insert-recyc_customer_links_to_company',
                                [$sql, $values, $_POST["customer_id"], $customerId]
                            );

                            $statement = $this->rdb->prepare($sql);
                            $statement->execute($values);

                            $company = $this->getCompanyById($customerId);
                            if ($company) {
                                $sql = "INSERT INTO recyc_company_sync (company_id, greenoak_id, company_name, CMP) VALUES (:recyc,:greenoak,:company,:cmp)";
                                $result = $this->rdb->prepare($sql);
                                $result->execute(
                                    [
                                        ':recyc' => $company->id,
                                        ':greenoak' => 'AWAITING UPDATE',
                                        ':company' => $company->company_name,
                                        ':cmp' => null
                                    ]
                                );
                            }
                        }
                    }
                    // End change tag addCustomer

                    // Generate token and send password change email
                    $this->get($this->user->id);
                    $this->generateToken();
                    $this->sendReminder("new");
                } else {
                    // Show error

                    $this->response = '
					<div class="alert alert-danger fade-in" id="reset-container" >
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<h4>Error</h4>
						<p>Unable to save user due to a bad data array.</p>
					</div>
					';
                }

                $this->response = '
				<div class="alert alert-success fade-in" id="reset-container" >
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<h4>Profile Updated</h4>
					<p>User updated successfully</p>
				</div>
				';
            } else {
                $this->response = '
				<div class="alert alert-danger fade-in" id="reset-container" >
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<h4>Error</h4>
					<p>A user with this email address already exists.</p>
				</div>
				';
            }
        } catch (\Exception $e) {
            $this->response = '
			<div class="alert alert-danger fade-in" id="reset-container" >
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Error</h4>
				<p>A user with this email address already exists.</p>
				<p>' . $e->getMessage() . '</p>
			</div>
			';
        }
    }

    // Called by: controllers/login_controller.php -> change()

    public function loadByEmail($email)
    {

        $email = strtolower($email);

        if ($email) {
            $sql = 'SELECT * FROM recyc_users WHERE email = :email';
            $result = $this->rdb->prepare($sql);
            $result->execute(array(':email' => $email));
            $this->user = $result->fetch(\PDO::FETCH_OBJ);
        }
    }

    // Called by: controllers/admin_controller.php -> editUserPost()

    public function generateToken()
    {
        $length = 32;
        $this->token = bin2hex(random_bytes($length));
        $expires = new \DateTime();
        $expires->modify('+2 days');
        $this->expiry = $expires->format('Y-m-d H:i:s');

        $sql = 'UPDATE recyc_users SET token = :token, token_expires = :expiry WHERE id = :id';
        $values = array(':token' => $this->token, ':expiry' => $this->expiry, ':id' => $this->user->id);
        $result = $this->rdb->prepare($sql);
        $result->execute($values);
    }

    // Called by: controllers/admin_controller.php > addUserPost()
    // Calls: loadByEmail()
    // Calls: generateToken()
    // Calls: sendReminder()

    public function sendReminder($type = null)
    {
        // Change tag: passwordChange
        if ($type == "new") {
            $content = '<p>Hi ' . $this->user->firstname . ',</p><p>Your account with Stone Computers Recycling Portal has been created.</p>';
            $content .= '<p>Before you can login you need to create a password, please click on the link below.</p>';
            $method = 'create';
        } else {
            $content = '<p>Hi ' . $this->user->firstname . ',</p><p>We have received a password reset request for your account on the Stone Computers Recycling Portal.</p>';
            $content .= '<p>To reset your password, please click on the link below.</p>';
            $method = 'reset';
        }

        $content .= '<p>This link is valid for 48 hours.</p>';
        $content .= '<p><a href="https://itadadmin.stonegroup.co.uk/login/reset/' . $this->token . '">Click here to ' . $method . ' your password</a></p>';
        $content .= '<p>Alternatively copy and paste this into the address bar of your internet browser:</p>';
        $content .= '<p>https://itadadmin.stonegroup.co.uk/login/reset/' . $this->token . '</p>';
        $content .= '<p>If you have any problems resetting your password, please reply to this email.</p>';
        $content .= '<p>Thanks</p>';
        $content .= '<p>Stone Computers</p>';

        $sendgridConfig = $this->emailConfig['sendgrid'];

        $useremail = strtolower($this->user->email);

        $email = new \SendGrid\Mail\Mail();

        try {
            $email->setFrom($sendgridConfig['from']['ITADSystem'], "ITAD System");
            $email->setSubject("ITAD System - Password Reset");
            $email->addTo($useremail);
            $email->addContent("text/html", $content);
            $sendgrid = new \SendGrid($sendgridConfig['api']['key']);
            $response = $sendgrid->send($email);

            if ($response->statusCode() !== 202) {
                throw new \RuntimeException($response->body());
            }

            Logger::getInstance("Mail_send_success.log")->info(
                "\n-\nEmail to " . $useremail . " has been sent\n\n" . $response->statusCode() . "\n" .
                "\n-\nBody " . $response->body() . "\n" .
                "\n-\nHeader " . json_encode($response->headers()) . "\n"
            );
        } catch (\Exception $e) {
            echo 'Message could not be sent. Error: ', $e->getMessage();

            Logger::getInstance("Mail_send_failureuser.log")->warning(
                "\n-\nEmail to " . $useremail . "  could not be sent\n\n" . $e->getMessage() . "\n"
            );
        }
    }

    public function getCustomers()
    {
        $sql = 'SELECT
			recyc_company_list.id,
			recyc_company_list.company_name
		from
			recyc_bdm_to_company
		left join
			recyc_company_list on recyc_bdm_to_company.company_id = recyc_company_list.id
		WHERE
			portal_requirement = 1
		GROUP BY
			recyc_bdm_to_company.company_id
		ORDER BY
			company_name
		';
        $result = $this->rdb->prepare($sql);
        $result->execute();
        $this->customers = $result->fetchAll(\PDO::FETCH_OBJ);
    }

    public function create()
    {
    }

    public function delete()
    {
    }
}
