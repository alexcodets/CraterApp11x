<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UpdateSettingsRequest;
use Crater\Models\CompanySetting;
use Crater\Models\LogsDev;
use Crater\Models\PbxPackages;
use Crater\Models\ProfileDID;
use Crater\Models\ProfileExtensions;
use Crater\Models\Provider;
use Log;

class UpdateCompanySettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\UpdateSettingsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(UpdateSettingsRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UpdateCompanySettingsController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        //Log::debug($request->settings);
        CompanySetting::setSettings($request->settings, $request->header('company'));
        if(isset($request->settings["packages_prefix_general"]) && $request->settings["packages_prefix_general"]) {
            $pack = PbxPackages::all();
            foreach($pack as $item) {
                $packages_number = '';
                if($item->id > 0 && $item->id < 10) {
                    $packages_number = $request->settings["packages_pbx_prefix"].'-00000'.$item->id;
                }
                if($item->id > 9 && $item->id < 100) {
                    $packages_number = $request->settings["packages_pbx_prefix"].'-0000'.$item->id;
                }
                if($item->id > 99 && $item->id < 1000) {
                    $packages_number = $request->settings["packages_pbx_prefix"].'-000'.$item->id;
                }
                if($item->id > 999 && $item->id < 10000) {
                    $packages_number = $request->settings["packages_pbx_prefix"].'-00'.$item->id;
                }
                if($item->id > 9999 && $item->id < 100000) {
                    $packages_number = $request->settings["packages_pbx_prefix"].'-0'.$item->id;
                }
                if($item->id > 99999) {
                    $packages_number = $request->settings["packages_pbx_prefix"].'-'.$item->id;
                }
                $company = PbxPackages::where("id", "=", $item->id)->first();
                $company->packages_number = $packages_number;
                $company->save();
            }
        }

        if(isset($request->settings["did_prefix_general"]) && $request->settings["did_prefix_general"]) {
            $pack = ProfileDID::all();
            foreach($pack as $item) {
                $did_number = '';
                if($item->id > 0 && $item->id < 10) {
                    $did_number = $request->settings["did_pbx_prefix"].'-00000'.$item->id;
                }
                if($item->id > 9 && $item->id < 100) {
                    $did_number = $request->settings["did_pbx_prefix"].'-0000'.$item->id;
                }
                if($item->id > 99 && $item->id < 1000) {
                    $did_number = $request->settings["did_pbx_prefix"].'-000'.$item->id;
                }
                if($item->id > 999 && $item->id < 10000) {
                    $did_number = $request->settings["did_pbx_prefix"].'-00'.$item->id;
                }
                if($item->id > 9999 && $item->id < 100000) {
                    $did_number = $request->settings["did_pbx_prefix"].'-0'.$item->id;
                }
                if($item->id > 99999) {
                    $did_number = $request->settings["did_pbx_prefix"].'-'.$item->id;
                }
                $company = ProfileDID::where("id", "=", $item->id)->first();
                $company->did_number = $did_number;
                $company->save();
            }
        }

        if(isset($request->settings["extension_prefix_general"]) && $request->settings["extension_prefix_general"]) {
            $pack = ProfileExtensions::all();
            foreach($pack as $item) {
                $extensions_number = '';
                if($item->id > 0 && $item->id < 10) {
                    $extensions_number = $request->settings["extension_pbx_prefix"].'-00000'.$item->id;
                }
                if($item->id > 9 && $item->id < 100) {
                    $extensions_number = $request->settings["extension_pbx_prefix"].'-0000'.$item->id;
                }
                if($item->id > 99 && $item->id < 1000) {
                    $extensions_number = $request->settings["extension_pbx_prefix"].'-000'.$item->id;
                }
                if($item->id > 999 && $item->id < 10000) {
                    $extensions_number = $request->settings["extension_pbx_prefix"].'-00'.$item->id;
                }
                if($item->id > 9999 && $item->id < 100000) {
                    $extensions_number = $request->settings["extension_pbx_prefix"].'-0'.$item->id;
                }
                if($item->id > 99999) {
                    $extensions_number = $request->settings["extension_pbx_prefix"].'-'.$item->id;
                }
                $company = ProfileExtensions::where("id", "=", $item->id)->first();
                $company->extensions_number = $extensions_number;
                $company->save();
            }
        }

        if(isset($request->settings["provider_prefix_general"]) && $request->settings["provider_prefix_general"]) {
            $pack = Provider::all();
            foreach($pack as $item) {
                $providers_number = '';
                if($item->id > 0 && $item->id < 10) {
                    $providers_number = $request->settings["prov_prefix"].'-00000'.$item->id;
                }
                if($item->id > 9 && $item->id < 100) {
                    $providers_number = $request->settings["prov_prefix"].'-0000'.$item->id;
                }
                if($item->id > 99 && $item->id < 1000) {
                    $providers_number = $request->settings["prov_prefix"].'-000'.$item->id;
                }
                if($item->id > 999 && $item->id < 10000) {
                    $providers_number = $request->settings["prov_prefix"].'-00'.$item->id;
                }
                if($item->id > 9999 && $item->id < 100000) {
                    $providers_number = $request->settings["prov_prefix"].'-0'.$item->id;
                }
                if($item->id > 99999) {
                    $providers_number = $request->settings["prov_prefix"].'-'.$item->id;
                }
                $company = Provider::where("id", "=", $item->id)->first();
                $company->providers_number = $providers_number;
                $company->save();
            }
        }


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
           'success' => true
        ] , "message" => "GetUserSettingsController"];
        LogsDev::finishLog($log, $res, $time, 'D', "GetUserSettingsController");
        /////////////////////////////////////////

        return response()->json([
            'success' => true
        ]);
    }
}
