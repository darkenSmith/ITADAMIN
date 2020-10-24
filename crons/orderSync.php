<?php

$path = '/var/www/html/itadadmin.stonegroup.co.uk';
require_once $path . '/cron.php';

use App\Models\Company;
use App\Models\OrderSync;

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
