<?php

namespace Crater\Http\Controllers\V2\Invoice;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\InvoiceImportRequest;
use Crater\Services\Reports\InvoicesImportService;

class InvoiceImportController extends Controller
{
    public function __invoke(InvoiceImportRequest $request, InvoicesImportService $service)
    {
        $auth = auth('sanctum')->user();

        return $service->handle($auth, $request->file('file'), $request->input('date_format', 'd/m/Y'));
    }
}
