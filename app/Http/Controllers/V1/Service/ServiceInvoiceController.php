<?php

namespace Crater\Http\Controllers\V1\Service;

use Crater\Http\Controllers\Controller;
use Crater\Models\CustomerPackage;
use Illuminate\Http\JsonResponse;

class ServiceInvoiceController extends Controller
{
    public function index(CustomerPackage $service): JsonResponse
    {
        return response()->json($service->invoices()->orderByDesc('created_at')->paginate(10));
    }
}
