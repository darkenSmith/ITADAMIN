<?php

namespace App\Controllers;

use App\Models\Collection;

/**
 * Class BookingController
 * @package App\Controllers
 */
class BookingController extends AbstractController
{

    public function index(): void
    {
        $collections = new Collection();
        $collections->getCollections();
        $collections = $collections->collections;

        $this->template->view(
            'collection/list',
            [
                'collections' => $collections
            ]
        );
    }

    public function request(): void
    {
        echo "<h1>New Collection Request</h1>";
        echo "<iframe src='https://stoneitad.stonegroup.co.uk/itadbooking/' frameborder='0' width='100%' height='2000'></iframe>";
    }
}
