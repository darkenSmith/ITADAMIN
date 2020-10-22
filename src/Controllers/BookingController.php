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
    }

    public function upload()
    {
        $collections = new Collection();
        echo $collections->upload();
    }
}
