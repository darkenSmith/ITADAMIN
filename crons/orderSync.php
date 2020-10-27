<?php

$path = '/var/www/html/itadadmin.stonegroup.co.uk';
require_once $path . '/cron.php';

use App\Models\OrderSync;
use App\Helpers\Logger;

Logger::getInstance("Cron.log")->info('orderSync', ['script called']);
Logger::getInstance("Cron.log")->debug('orderSync', [$argv]);

if (!empty($argv[1]) && $argv[1] === 'sync') {
    Logger::getInstance("Cron.log")->info('orderSync', ['sync']);
    try {
        $sync = new OrderSync();
        $sync->start();
        Logger::getInstance("Cron.log")->info('orderSync', ['executed - start']);
        if ($sync->orders) {
            Logger::getInstance("Cron.log")->debug('orderSync', ['sync->orders', $sync->orders]);
            $sync->process();
            Logger::getInstance("Cron.log")->info('orderSync', ['sync->process executed']);
        }
        Logger::getInstance("Cron.log")->info('orderSync', ['completed']);
    } catch (\Exception $e) {
        Logger::getInstance("Cron.log")->error('orderSync - start - process', [$e->getMessage()]);
    }
}

Logger::getInstance("Cron.log")->info('orderSync', ['ended']);
