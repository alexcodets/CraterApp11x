<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\GetSettingsRequest;
use Crater\Models\CompanySetting;
use Crater\Models\User;

class GetCompanySettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(GetSettingsRequest $request)
    {
        // Inicializar log si es necesario
        // $time = microtime(true);
        // $log = LogsDev::initLog($request, "", "D", "GetCompanySettingsController", "__invoke");

        // Determinar el ID de la compañía
        $companyId = $request->header('company') ?: 1;

        // Obtener configuraciones de la compañía
        $settings = CompanySetting::getSettings($request->settings, $companyId);

        // Obtener el correo electrónico del 'super admin'
        $defaultEmail = User::where('role', 'super admin')->value('email');
        $settings["default_email"] = $defaultEmail;

        // Finalizar log si es necesario
        // $res = ["success" => true, "response" => $settings, "message" => "GetCompanySettingsController"];
        // LogsDev::finishLog($log, $res, $time, 'D', "GetCompanySettingsController");

        // Devolver la respuesta
        return response()->json($settings);
    }
}
