<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
// request
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\MobileSettingsRequest;
// models
use Crater\Models\CompanySetting;
use Crater\Models\Currency;
use Crater\Models\LogsDev;
use Crater\Models\MobileSettings as ModelsMobileSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

class MobileSettings extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MobileSettingsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "MobileSettingsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resMobileSet = ModelsMobileSettings::createMobileSetting($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'mobileSettings' => $resMobileSet,
        ], "message" => "MobileSettingsController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "MobileSettingsController store");
        /////////////////////////////////////////

        return response()->json([
            'mobileSettings' => $resMobileSet,
            'success' => true,
            'message' => 'Mobile settings saved successfully',
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($idCompany, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "MobileSettingsController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        //Log::debug(gettype($idCompany));
        if ($idCompany === 'null') {
            $idCompany = Auth::user()->company_id;
        }

        $mobileSetting = ModelsMobileSettings::select('*')->where('company_id', $idCompany)->get();

        foreach ($mobileSetting as $setting) {
            $setting['horizontal_menu'] = json_decode($setting['horizontal_menu']);
            $setting['vertical_menu'] = json_decode($setting['vertical_menu']);
            $setting['dashboard'] = json_decode($setting['dashboard']);
            $setting['color_palette'] = str_replace('"', '', $setting['color_palette']);
            $setting['dark_color_palette'] = str_replace('"', '', $setting['dark_color_palette']);

            $media = $setting->getMedia('logo-mobile');
            foreach ($media as $file) {
                $setting['file_url'] = url($file->getUrl());
                if(Storage::disk('public')->has($file->id."/".$file->file_name)) {
                    $imageData = base64_encode(Storage::get($file->id."/".$file->file_name));
                    $setting['logo_base64'] = 'data:'.$file->mime_type.';base64,'.$imageData;
                } else {
                    $setting['logo_base64'] = null;
                }

            }
        }

        $currencyt = CompanySetting::where('option', 'currency')->where('company_id', $idCompany)->first();

        $currencysymbol = "$";
        if ($currencyt != null) {
            $money = Currency::where("id", $currencyt->value)->first();
            if($money != null) {
                $currencysymbol = $money->symbol;
            }
        }
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = [
            "success" => true,
            "response" => [
                'mobileSetting' => $mobileSetting,
                'currency' => $currencysymbol,
            ],
            "message" => "mobile setting show",
        ];
        LogsDev::finishLog($log, $res, $time, 'D', "Mobile settings show");
        /////////////////////////////////////////

        return response()->json([
            'mobileSetting' => $mobileSetting,
            'currency' => $currencysymbol,
            "success" => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
