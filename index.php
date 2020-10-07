<?php

use App\Helpers\App;
use App\Helpers\Config;

session_start();

define('PROJECT_DIR', __DIR__ . '/');
define('VIEW_DIR', __DIR__ . '/views/');
define('TEMPLATE_DIR', VIEW_DIR . 'template/');
define('LAYOUT_DIR', VIEW_DIR . 'layout/');

require_once('vendor/autoload.php');

try {
    $app = new App();
    $app->run();

    $app->getHeader();
    if (!isset($_SESSION['user']['id'])) {
        if (isset($_GET['controller'], $_GET['action'])) {
            $redir = $_GET['controller'] . '/' . $_GET['action'];
        } else {
            $redir = false;
        }

        if (isset($_GET['controller'], $_GET['action'])) {
            $app->setView($_GET['controller'], $_GET['action']);
        } else {
            $app->setView('login', 'index');
        }

        if ($app->isAllowed()) {
            if ($app->layout == 0) {
                require_once(VIEW_DIR . 'routes.php');
            } else {
                $layout = $app->layout;
                require_once(LAYOUT_DIR . 'layout.php');
            }
        }

    } else {
        if (isset($_GET['controller'], $_GET['action'])) {
            $app->setView($_GET['controller'], $_GET['action']);
        } else {
            $app->setView();
        }
        if ($app->isAllowed()) {
            if ($app->layout == 0) {
                require_once(VIEW_DIR . 'routes.php');
            } else {
                $layout = $app->layout;
                require_once(LAYOUT_DIR . 'layout.php');
            }
        } else {
            $layout = 0;
            require_once(LAYOUT_DIR . 'layout.php');
        }
    }

} catch (Exception $e) {
    var_dump($e);
    die;
}
