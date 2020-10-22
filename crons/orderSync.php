<?php

use App\Models\Company;
use App\Models\OrderSync;

define('PROJECT_DIR', __DIR__ . '/');
define('VIEW_DIR', __DIR__ . '/views/');
define('TEMPLATE_DIR', VIEW_DIR . 'template/');
define('LAYOUT_DIR', VIEW_DIR . 'layout/');

require_once('../vendor/autoload.php');


if (!empty($argv[1]) && $argv[1] === 'sync') {
// Get companies
    $companies = new Company();
    $companies->refresh(false);

// Get orders
    $sync = new OrderSync();
    $sync->start();

    if ($sync->orders) {
        $sync->process();
    }
}
