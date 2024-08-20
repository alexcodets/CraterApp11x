<?php

namespace Crater\Http\Controllers\V1\Auth;

use Crater\Http\Controllers\Controller;

use Crater\Models\LogsDev;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

//// Models
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    use SendsPasswordResetEmails;

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['response' => $response]);
        $log = LogsDev::initLog($request, "", "D", "ForgotPasswordController", "sendResetLinkResponse");
        ///////////////////////////////////////

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'message' => 'Password reset email sent.',
            'data' => $response,
        ], "message" => "Correo enviado"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Correo enviado");
        /////////////////////////////////////////

        return response()->json([
            'message' => 'Password reset email sent.',
            'data' => $response,
        ]);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['response' => $response]);
        $log = LogsDev::initLog($request, "", "D", "ForgotPasswordController", "sendResetLinkFailedResponse");
        ///////////////////////////////////////

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'message' => 'Password reset email sent.',
            'data' => $response,
        ], "message" => "Correo no  enviado"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Correo no enviado");

        /////////////////////////////////////////
        return response('Email could not be sent to this email address.', 403);
    }
}
