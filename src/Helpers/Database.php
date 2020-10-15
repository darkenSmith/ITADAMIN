<?php

namespace App\Helpers;

use \PDO as PDO;

/**
 * Class Database
 * @package App\Helpers
 */
class Database
{
    private static $instance = null;

    /**
     * @param string|null $type
     * @return PDO|null
     */
    public static function getInstance(?string $type = null)
    {
        try {
            $databaseConfig = Config::getInstance()->get('database');

            if (!$type && $databaseConfig) {
                $type = $databaseConfig['default'];
            }

            $database = $databaseConfig['instance'][$type] ?? null;

            if (!$database) {
                return self::$instance;
            }

            if ($database['driver'] === 'sqlsrv') {
                $driver = sprintf('%s:%s', $database['driver'], 'Server');
                $dbName = sprintf('%s=%s', 'Database', $database['dbname']);
                $port = '';
            } else {
                $driver = sprintf('%s:%s', $database['driver'], 'host');
                $dbName = sprintf('%s=%s', 'dbname', $database['dbname']);
                $port = sprintf('%s=%s;', 'port', $database['port']);
            }

            $dsn = sprintf(
                '%s=%s;%s;%s',
                $driver,
                $database['host'],
                $dbName,
                $port
            );

            self::$instance = new \PDO(
                $dsn,
                $database['user'],
                $database['pass']
            );
            self::$instance ->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            Logger::getInstance("Database.log")->info('connected', [$type]);

        } catch (\PDOException $e) {
            Logger::getInstance("Database.log")->error($type, [$e->getMessage()]);
            echo "server connection issue please contact MIS";
            die;
        }
        return self::$instance;
    }
}
