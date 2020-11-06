<?php

namespace App\Helpers;

use Monolog\Handler\StreamHandler;

/**
 * Class Logger
 * @package App\Helpers
 */
class Logger
{
    /**
     * @var \Monolog\Logger
     */
    private static $instance;
    public const DEFAULT_LOG_PATH = PROJECT_DIR . 'logs/';
    public const DEFAULT_LOG_FILE = 'log.log';

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * Logger constructor.
     */
    final private function __construct()
    {
    }

    /**
     * @return \Monolog\Logger
     */
    public static function getInstance(string $logFile = null)
    {
        $logFile = !empty($logFile) ? $logFile : self::DEFAULT_LOG_FILE;

        static::$instance = new \Monolog\Logger('ITAD-Admin');

        static::$instance->pushHandler(
            new StreamHandler(
                self::DEFAULT_LOG_PATH . date('Ymd-') . $logFile,
                \Monolog\Logger::DEBUG,
                true
            )
        );

        return static::$instance;
    }

    final private function __clone()
    {
    }
}
