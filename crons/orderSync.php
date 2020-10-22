<?php

use App\Models\Company;
use App\Models\OrderSync;

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
