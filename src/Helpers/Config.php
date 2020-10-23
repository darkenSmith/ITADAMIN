<?php

namespace App\Helpers;

/**
 * Class Config
 * @package App\Helpers
 */
class Config
{
    private static $instance;
    public $config = [];

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
     * @param string|null $type
     * @return array|false|mixed
     */
    public function get(?string $type = null)
    {

        if (file_exists(PROJECT_DIR . "app.dev.ini")) {
            $this->config = $this->parse(PROJECT_DIR . "app.dev.ini", true);
        } elseif (file_exists(PROJECT_DIR . ".app.ini")) {
            $this->config = $this->parse(PROJECT_DIR . ".app.ini", true);
        }

        if (!empty($type)) {
            return $this->config[$type] ?? [];
        }

        return $this->config;
    }

    /**
     * @param string $file
     * @param bool $process_sections
     * @param bool|int $scanner_mode
     * @return array|null
     */
    private function parse(string $file, bool $process_sections = false, bool $scanner_mode = INI_SCANNER_NORMAL): ?array
    {
        $explode_str = '.';
        $escape_char = "'";
        // load ini file the normal way
        $data = parse_ini_file($file, $process_sections, $scanner_mode);
        if (!$process_sections) {
            $data = array($data);
        }
        foreach ($data as $section_key => $section) {
            // loop inside the section
            foreach ($section as $key => $value) {
                if (strpos($key, $explode_str)) {
                    if ($key[0] !== $escape_char) {
                        // key has a dot. Explode on it, then parse each subkeys
                        // and set value at the right place thanks to references
                        $sub_keys = explode($explode_str, $key);
                        $subs =& $data[$section_key];
                        foreach ($sub_keys as $sub_key) {
                            if (!isset($subs[$sub_key])) {
                                $subs[$sub_key] = [];
                            }
                            $subs =& $subs[$sub_key];
                        }
                        // set the value at the right place
                        $subs = $value;
                        // unset the dotted key, we don't need it anymore
                        unset($data[$section_key][$key]);
                    } else {
                        $new_key = trim($key, $escape_char);
                        $data[$section_key][$new_key] = $value;
                        unset($data[$section_key][$key]);
                    }
                }
            }
        }
        if (!$process_sections) {
            $data = $data[0];
        }
        return $data;
    }
}
