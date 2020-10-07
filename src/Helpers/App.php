<?php

namespace App\Helpers;

use \PDO;

/**
 * Class App
 * @package App\Helpers
 */
final class App
{
    public $version;
    public $updates = array();
    public $updateAvailable = 0;
    public $updateVersion = 0;
    public $controllers = array();
    public $controller = '';
    public $action = '';
    public $layout = '';
    public $user = '';
    public $role = '';
    public $structureId = '';
    public $headerCo = '';
    public $customerCo = '';
    public $container = '';

    private $rdb;

    public function __construct()
    {
        $this->rdb = Database::getInstance('recycling');
        $this->currentVersion();
        if (!$this->user && isset($_SESSION['user']['id'])) {
            $this->user = $_SESSION['user']['id'];
        }
        if (!$this->role && isset($_SESSION['user']['role_id'])) {
            $this->role = $_SESSION['user']['role_id'];
        }
    }

    public function canUpload()
    {
        if ($_SESSION['user']['role_id'] == 1 || $_SESSION['user']['role_id'] == 2) {
            return true;
        } else {
            return false;
        }
    }

    public function isLoggedIn()
    {
        if (isset($_SESSION['user']['id'])) {
            return true;
        } else {
            return false;
        }
    }

    public function isAllowed()
    {
        if (!isset($_SESSION['user']['id'])) {
            $sql = 'SELECT unrestricted FROM recyc_structure WHERE id = :structure';
            $query = $this->rdb->prepare($sql);
            $query->execute(array(':structure' => $this->structureId));
            $unrestricted = $query->fetch(\PDO::FETCH_COLUMN);
            if ($unrestricted == 1) {
                return true;
            } else {
                $qs = '?redirect=' . $this->controller . '/' . $this->action;
                header('Location: /login/' . $qs);
                exit;
            }
        } else {
            $sql = 'SELECT * FROM recyc_permissions WHERE role_id = :role AND structure_id = :structure';
            $query = $this->rdb->prepare($sql);
            $query->execute(array(':role' => $this->role, ':structure' => $this->structureId));
            $permissions = $query->fetch(\PDO::FETCH_OBJ);
            if ($permissions) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function run()
    {
        $this->getControllers();
    }

    public function getHeader()
    {
        $sql = 'SELECT id,company_name from recyc_company_list cl
				left join recyc_bdm_to_company bc on cl.id = bc.company_id
				where user_id = :user';
        $query = $this->rdb->prepare($sql);
        $query->execute(array(':user' => $this->user));
        $this->headerCo = $query->fetchAll(\PDO::FETCH_OBJ);

        $sql = 'SELECT cl.id, cl.company_name FROM `recyc_customer_links_to_company` l 
				LEFT JOIN recyc_company_list cl on l.company_id = cl.id where user_id = :user';
        $query = $this->rdb->prepare($sql);
        $query->execute(array(':user' => $this->user));
        $this->customerCo = $query->fetchAll(\PDO::FETCH_OBJ);
    }

    public function getControllers()
    {
        $sql = 'SELECT controller, action from recyc_structure where active = 1';
        $query = $this->rdb->prepare($sql);
        $query->execute();
        $controllers = $query->fetchAll(\PDO::FETCH_OBJ);

        foreach ($controllers as $controller) {
            $results[$controller->controller][] = $controller->action;
        }
        $this->controllers = $results;
    }

    /**
     * @param string $controller
     * @param string $action
     */
    public function setView(string $controller = 'pages', string $action = 'home')
    {
        $this->controller = $controller;
        $this->action = $action;

        $sql = 'SELECT id,layout,container FROM recyc_structure WHERE controller = :controller AND action= :action';
        $query = $this->rdb->prepare($sql);
        $query->execute(array(':controller' => $this->controller, ':action' => $this->action));
        $result = $query->fetch(\PDO::FETCH_OBJ);
        if ($result) {
            $this->layout = $result->layout;
            $this->structureId = $result->id;
            $this->container = $result->container;
        } else {
            $this->layout = 1;
            $this->structureId = 1;
            $this->container = 'container';
        }
    }

    /**
     * @return mixed
     */
    public function currentVersion()
    {
        $sql = 'SELECT value from recyc_config where path = "version"';
        $query = $this->rdb->prepare($sql);
        $query->execute();
        $result = $query->fetch(\PDO::FETCH_OBJ);
        if ($result) {
            $this->version = $result->value;
        } else {
            $this->version = 0;
        }
    }

    public function checkUpdates()
    {
        $directory = 'upgrade\\';
        $files = array_diff(scandir($directory), array('..', '.'));
        foreach ($files as $update) {
            $parts = explode('-', $update);
            if ($parts[1] == $this->version) {
                $this->updateAvailable = 1;
                $this->updateVersion = str_replace('.php', '', $parts[2]);
                $filepath = $_SERVER["DOCUMENT_ROOT"] . '\\upgrade\\' . $update;
                include_once($filepath);
                if (isset($updates)) {
                    foreach ($updates as $update) {
                        $this->updates[] = $update;
                    }
                }
                break;
            }
        }
    }
}
