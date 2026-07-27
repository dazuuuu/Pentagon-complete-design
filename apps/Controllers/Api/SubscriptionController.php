<?php

namespace App\Controllers\Api;

use App\Helpers\Response;
use App\Services\SubscriptionService;

class SubscriptionController
{
    public function store(): void
    {
        $service = new SubscriptionService();
        $result = $service->subscribe($_POST['email'] ?? '');
        $status = $result['success'] ? 200 : 422;
        Response::json($result, $status);
    }
}
