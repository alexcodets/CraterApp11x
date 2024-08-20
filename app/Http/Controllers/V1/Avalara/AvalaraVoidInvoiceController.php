<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Artisan;
use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraInvoice;
use Illuminate\Http\Request;

class AvalaraVoidInvoiceController extends Controller
{
    public function void(Request $request, $invoice_id): array
    {
        $status = Artisan::call('pbx:avalaraVoidInvoice', ['invoice' => $invoice_id]);

        return [
            'success' => $status == 0,
            'message' => str_replace("\n", ' - ', Artisan::output()),
            'invoice_id' => $invoice_id,
        ];
    }

    public function status(Request $request, $id)
    {
        $status_avalara_invoice = AvalaraInvoice::where('invoice_id', '=', $id)->first('status');

        return [
            'response' => $status_avalara_invoice
        ];
    }
}
