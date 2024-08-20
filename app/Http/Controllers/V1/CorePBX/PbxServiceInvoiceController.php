<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxServices;

class PbxServiceInvoiceController extends Controller
{
    public function index(PbxServices $pbxService)
    {
        return $pbxService->invoices()->orderByDesc('created_at')->paginate(10);
    }
}
