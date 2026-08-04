<?php

namespace App\Controllers\Api;

use App\Helpers\Response;
use App\Services\DestinationService;

class DestinationController
{
    public function index(): void
    {
        $service = new DestinationService();
        Response::json(['success' => true, 'data' => $service->getActive()]);
    }
}
