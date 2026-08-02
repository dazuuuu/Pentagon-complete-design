<?php

namespace App\Controllers\Api;

use App\Helpers\Response;
use App\Services\EnquiryService;

class ContactController
{
    public function store(): void
    {
        $service = new EnquiryService();
        $result = $service->submit($_POST);
        $status = $result['success'] ? 200 : 422;
        Response::json($result, $status);
    }
}
