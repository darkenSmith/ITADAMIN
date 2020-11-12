<?php

$path = '/var/www/html/itadadmin.stonegroup.co.uk';
require_once $path . '/cron.php';

use App\Models\Company;
use App\Helpers\Logger;

Logger::getInstance("Cron.log")->info('company', ['script called']);
Logger::getInstance("Cron.log")->debug('company', [$argv]);

if (!empty($argv[1]) && $argv[1] === 'refresh') {
// Get companies
    Logger::getInstance("Cron.log")->info('company', ['refresh']);
    try {
        $companies = new Company();
        $companies->syncUpdate();
        $companies->refresh(false);
         $companies->updateCmp();
        Logger::getInstance("Cron.log")->info('company', ['executed']);
    } catch (\Exception $e) {
        Logger::getInstance("Cron.log")->error('company', [$e->getMessage()]);
    }
}

Logger::getInstance("Cron.log")->info('company', ['ended']);
