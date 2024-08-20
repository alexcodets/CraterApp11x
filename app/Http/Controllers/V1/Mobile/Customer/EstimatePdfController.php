<?php

namespace Crater\Http\Controllers\V1\Mobile\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Mail\EstimateViewedMail;
use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\LogsDev;
use Crater\Models\User;
use Crater\Traits\SendEmailsTrait;
//Traits
use Illuminate\Http\Request;

class EstimatePdfController extends Controller
{
    use SendEmailsTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Estimate $estimate)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['Estimate' => $estimate]);
        $log = LogsDev::initLog($request, "", "D", "EstimatePdfController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($estimate && ($estimate->status == Estimate::STATUS_SENT || $estimate->status == Estimate::STATUS_DRAFT)) {
            $estimate->status = Estimate::STATUS_VIEWED;
            $estimate->save();
            $notifyEstimateViewed = CompanySetting::getSetting(
                'notify_estimate_viewed',
                $estimate->company_id
            );

            if ($notifyEstimateViewed == 'YES') {
                $data['estimate'] = Estimate::findOrFail($estimate->id)->toArray();
                $data['user'] = User::find($estimate->user_id)->toArray();
                $notificationEmail = CompanySetting::getSetting(
                    'notification_email',
                    $estimate->company_id
                );


                \Mail::to($notificationEmail)->send(new EstimateViewedMail($data));

                //envio bbc

                // correo bbc
                $bbcmail = CompanySetting::select('value', 'id')->where('option', 'estimate_bbc_email')->where('company_id', $estimate->company_id)->first();

                if ($bbcmail != null) {
                    if ($bbcmail->value != "") {
                        \Mail::to($bbcmail->value)->send(new EstimateViewedMail($data));
                    }

                }
                // consultar customer
                $customer = User::findOrFail($estimate->user_id);
                $settings = CompanySetting::select('value', 'id')->where('option', 'estimate_auto_generate')->where('company_id', $estimate->company_id)->first();
                $mailable_id = $settings->id;
                $message = $settings->value;
                // save emails logs
                //$emailTrait = new SendEmailsTrait;
                //$emailTrait->saveEmailLog($customer->email, $subject = 'estimate generate pdf', $message, $mailable_id, $estimate->company_id, $customer->id);
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => "Estimado pdf", "message" => "Estimado pdf"];
        LogsDev::finishLog($log, $res, $time, 'D', "Estimado pdf");
        /////////////////////////////////////////


        return $estimate->getGeneratedPDFOrStream('estimate');
    }
}
