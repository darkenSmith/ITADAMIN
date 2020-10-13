<?php

namespace App\Models;

use App\Helpers\Database;

/**
 * Class ModelAbstract
 * @package App\Models
 */
abstract class AbstractModel
{
    public $rdb;
    public $gdb;
    public $ldb;
    public $sdb;

    /**
     * AbstractModel constructor.
     */
    public function __construct()
    {
        $this->rdb = Database::getInstance('recycling');
        $this->sdb = Database::getInstance('sql01');
        $this->gdb = Database::getInstance('greenoak');
    }
}
