<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Models\User;
use Illuminate\Http\JsonResponse;

class CustomerPushNotificationLogController extends Controller
{
    public function index(User $customer): JsonResponse
    {
        return response()->json($customer->pushNotificationLogs()->orderByDesc('created_at')->paginate(10));
    }
}
