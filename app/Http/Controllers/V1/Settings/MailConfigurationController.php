<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\MailEnvironmentRequest;
use Crater\Mail\TestMail;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Setting;
use Crater\Space\EnvironmentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mail;

class MailConfigurationController extends Controller
{
    /**
     * @var EnvironmentManager
     */
    protected $environmentManager;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->environmentManager = $environmentManager;
    }

    /**
     *
     * @param MailEnvironmentRequest $request
     * @return JsonResponse
     */
    public function saveMailEnvironment(MailEnvironmentRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "MailConfigurationController", "saveMailEnvironment");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $setting = Setting::getSetting('profile_complete');
        $results = $this->environmentManager->saveMailVariables($request);

        if ($setting !== 'COMPLETED') {
            Setting::setSetting('profile_complete', 4);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => $results, "message" => "MailConfigurationController"];
        LogsDev::finishLog($log, $res, $time, 'D', "MailConfigurationController");
        /////////////////////////////////////////
        // Logs por modulo
        LogsModule::createLog("Mail Configuration", "Update", "admin/settings/mail-configuration", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Save email configuration");

        return response()->json($results);
    }

    public function getMailEnvironment(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "MailConfigurationController", "getMailEnvironment");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $MailData = [
            'mail_driver' => config('mail.driver'),
            'mail_host' => config('mail.host'),
            'mail_port' => config('mail.port'),
            'mail_username' => config('mail.username'),
            'mail_password' => config('mail.password'),
            'mail_encryption' => config('mail.encryption'),
            'from_name' => config('mail.from.name'),
            'from_mail' => config('mail.from.address'),
            'mail_mailgun_endpoint' => config('services.mailgun.endpoint'),
            'mail_mailgun_domain' => config('services.mailgun.domain'),
            'mail_mailgun_secret' => config('services.mailgun.secret'),
            'mail_ses_key' => config('services.ses.key'),
            'mail_ses_secret' => config('services.ses.secret'),
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => $MailData, "message" => "MailConfigurationController"];
        LogsDev::finishLog($log, $res, $time, 'D', "MailConfigurationController");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Mail Configuration", "View", "admin/settings/mail-configuration", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Get email configuration");

        return response()->json($MailData);
    }

    /**
     *
     * @return JsonResponse
     */
    public function getMailDrivers(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "MailConfigurationController", "getMailDrivers");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $drivers = [
            'smtp',
            'mail',
            'sendmail',
            'mailgun',
            'ses',
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => $drivers, "message" => "GetUserSettingsController"];
        LogsDev::finishLog($log, $res, $time, 'D', "GetUserSettingsController");
        /////////////////////////////////////////
        // Logs por modulo
        LogsModule::createLog("Mail Configuration", "View", "admin/settings/mail-configuration", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Get email configuration");

        return response()->json($drivers);
    }

    public function testEmailConfig(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "MailConfigurationController", "testEmailConfig");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $this->validate($request, [
            'to' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        Mail::to($request->to)->send(new TestMail($request->subject, $request->message));

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "GetUserSettingsController"];
        LogsDev::finishLog($log, $res, $time, 'D', "MailConfigurationController");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Mail Configuration", "Test", "admin/settings/mail-configuration", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Test email configuration");


        return response()->json([
            'success' => true,
        ]);
    }
}
