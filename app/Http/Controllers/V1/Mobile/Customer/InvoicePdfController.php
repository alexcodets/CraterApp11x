<?php

namespace Crater\Http\Controllers\V1\Mobile\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Mail\InvoiceViewedMail;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\User;
use Crater\Traits\SendEmailsTrait;
//Traits
use Illuminate\Http\Request;

class InvoicePdfController extends Controller
{
    use SendEmailsTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Invoice $invoice)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge([' Invoice' => $invoice]);
        $log = LogsDev::initLog($request, "", "D", "InvoicePdfController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($invoice && ($invoice->status == Invoice::STATUS_SENT || $invoice->status == Invoice::STATUS_DRAFT)) {
            $invoice->status = Invoice::STATUS_VIEWED;
            $invoice->viewed = true;
            $invoice->save();
            $notifyInvoiceViewed = CompanySetting::getSetting(
                'notify_invoice_viewed',
                $invoice->company_id
            );

            if ($notifyInvoiceViewed == 'YES') {
                $data['invoice'] = Invoice::findOrFail($invoice->id)->toArray();
                $data['user'] = User::find($invoice->user_id)->toArray();
                $notificationEmail = CompanySetting::getSetting(
                    'notification_email',
                    $invoice->company_id
                );

                \Mail::to($notificationEmail)->send(new InvoiceViewedMail($data));


                // correo bbc
                $bbcmail = CompanySetting::select('value', 'id')->where('option', 'invoice_bbc_email')->where('company_id', $invoice->company_id)->first();

                if ($bbcmail != null) {
                    if ($bbcmail->value != "") {
                        \Mail::to($bbcmail->value)->send(new InvoiceViewedMail($data));

                    }

                }

                // consultar customer
                $customer = User::findOrFail($invoice->user_id);
                $settings = CompanySetting::select('value', 'id')->where('option', 'estimate_auto_generate')->where('company_id', $invoice->company_id)->first();
                $mailable_id = $settings->id;
                $message = $settings->value;
                // save emails logs
                // $emailTrait = new SendEmailsTrait;
                //$emailTrait->saveEmailLog($customer->email, $subject = 'invoice generate pdf', $message, $mailable_id, $invoice->company_id, $customer->id);
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => "invoice pdf", "message" => "invoice pdf"];
        LogsDev::finishLog($log, $res, $time, 'D', "invoice pdf");
        /////////////////////////////////////////

        return $invoice->getGeneratedPDFOrStream('invoice');
    }
}
