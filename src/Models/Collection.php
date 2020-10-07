<?php

namespace App\Models;

/**
 * Class Collection
 * @package App\Models
 */
class Collection extends AbstractModel
{

    public $collections;
    public $heading;

    /**
     * Collection constructor.
     */
    public function __construct()
    {
        $this->heading = 'Collections';

        parent::__construct();
    }

    public function getCollections()
    {
        $sql = 'SELECT
					Request_ID,
					date(Request_date_added),
					Request_Customer_contact,
					Request_Customer_email,
					Request_Customer_contact,
					Request_Customer_phone,
					Request_Customer_name,
					Request_town,
					Request_county,
					Request_postcode,
					Request_total_weight,
					Request_TotalUnits
				FROM
					`recyc_collection_requests`
				WHERE
				Request_is_done = 0
				and Request_deleted = 0';
        $result = $this->rdb->prepare($sql);
        $result->execute();
        $this->collections = $result->fetchAll(\PDO::FETCH_OBJ);
    }
}
