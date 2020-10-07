<?php

namespace App\Models\RS;

use App\Helpers\Database;
use App\Models\AbstractModel;

/**
 * Class AddRebate
 * @package App\Models
 */
class AddRebate extends AbstractModel
{

    public $response;

    /**
     * AddRebate constructor.
     */
    public function __construct()
    {
        $this->gdb =  Database::getInstance('greenoak');
        parent::__construct();
    }
}
