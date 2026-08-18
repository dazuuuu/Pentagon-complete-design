<?php

namespace App\Controllers\Api;

use App\Helpers\Response;
use App\Services\TourService;

class TourController
{
    public function index(): void
    {
        $service = new TourService();
        Response::json(['success' => true, 'data' => $service->getActive()]);
    }
}
