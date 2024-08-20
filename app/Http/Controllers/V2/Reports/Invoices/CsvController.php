<?php

namespace Crater\Http\Controllers\V2\Reports\Invoices;

use Crater\Http\Requests\InvoiceToCsvRequest;
use Crater\Services\Reports\InvoicesToCsvService;
use Log;

class CsvController
{
    public function __invoke(InvoiceToCsvRequest $request)
    {
        $user = auth('sanctum')->user();
        $service = new InvoicesToCsvService();
        Log::debug($request->input());

        return $service->handle($user, $request->validated());
    }
}
