<?php

$path = '/var/www/html/itadadmin.stonegroup.co.uk';
require_once $path . '/cron.php';

use App\Models\Company;

if (!empty($argv[1]) && $argv[1] === 'refresh') {
// Get companies
    $companies = new Company();
    $companies->refresh(true);
}
