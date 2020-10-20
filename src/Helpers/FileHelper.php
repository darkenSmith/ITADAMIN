<?php
namespace App\Helpers;

/**
 * Class FileHelper
 * @package App\Helpers
 */
class FileHelper
{
    private static $instance;

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getRealPath(string $path): string
    {
        return is_link($path) ? readlink($path) : $path;
    }
}
