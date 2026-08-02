<?php

namespace App\Controllers\Api;

use App\Helpers\Response;
use App\Services\GalleryService;

class GalleryController
{
    public function index(): void
    {
        $service = new GalleryService();
        Response::json(['success' => true, 'data' => $service->getActive()]);
    }
}
