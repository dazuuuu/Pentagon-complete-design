<?php

namespace App\Controllers\Api;

use App\Helpers\Response;
use App\Services\TestimonialService;

class TestimonialController
{
    public function index(): void
    {
        $service = new TestimonialService();
        Response::json(['success' => true, 'data' => $service->getActive()]);
    }
}
