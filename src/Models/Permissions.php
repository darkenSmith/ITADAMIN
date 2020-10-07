<?php

namespace App\Models;

/**
 * Class Permissions
 * @package App\Models
 */
class Permissions extends AbstractModel
{
    public $content;
    public $permissions;

    /**
     * Permissions constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function load()
    {
        $sql = 'SELECT id, controller, action, friendly 
				FROM `recyc_structure` 
				where unrestricted = 0 and layout != 0 
				ORDER BY controller ASC';
        $query = $this->rdb->prepare($sql);
        $query->execute();
        $structure = $query->fetchAll(\PDO::FETCH_OBJ);

        foreach ($structure as $item) {
            $this->section[$item->controller][$item->id] = $item;
        }
        $sql = 'SELECT structure_id FROM recyc_permissions WHERE role_id = 1';
        $query = $this->rdb->prepare($sql);
        $query->execute();
        $this->permissions = $query->fetchAll(\PDO::FETCH_COLUMN);
    }
}
