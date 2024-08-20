<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\ProfileExtensionsRequest;
use Crater\Models\AditionalCharges;

// models
use Crater\Models\CompanySetting;
use Crater\Models\LogsDev;
use Crater\Models\PbxPackages;
use Crater\Models\ProfileExtensions;
use Illuminate\Http\Request;
use Log;

class ProfileExtensionsController extends Controller
{
    /**
     * add profile did / aditional charges
     */
    public function addAditionalCharges($charge, $profile_ext_id, $company_id, $user_id)
    {
        $res = [];

        if (! empty($charge['amount'])) {
            $newCharge = new AditionalCharges();
            $newCharge->company_id = $company_id;
            $newCharge->creator_id = $user_id;
            $newCharge->profile_extension_id = $profile_ext_id;
            $newCharge->description = $charge['description'];
            $newCharge->amount = $charge['amount'];
            $newCharge->status = $charge['status'];
            $newCharge->profile_did_id = null;
            $res[] = $newCharge->save();
        }

        return $res;
    }

    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileExtensionsController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resProfileExt = ProfileExtensions::applyFilters($request->only([
            'search',
            'name',
            'extensions_number',
            'orderByField',
            'orderBy',
        ]))
        ->select('profile_extensions.*')
        ->paginate(10);

        foreach ($resProfileExt as $extension) {
            // formatear fecha creacion
            $date = Carbon::parse($extension->created_at)->format('Y-m-d');
            $extension['created_at_no_timezone'] = $date;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileExtensions' => $resProfileExt,
        ], "message" => "ProfileExtensionsController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileExtensionsController index");
        /////////////////////////////////////////

        return response()->json([
            'profileExtensions' => $resProfileExt,
        ]);
    }

    public function indexnp(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileExtensionsController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resProfileExt = ProfileExtensions::whereNull("deleted_at");
        // recorrer respuesta



        if(request("name")) {

            $resProfileExt = $resProfileExt->where("name", "LIKE", '%'.$request->name.'%');
        }

        if(request("extensions_number")) {
            $resProfileExt = $resProfileExt->where("extensions_number", "LIKE", '%'.$request->extensions_number.'%');
        }

        $resProfileExt = $resProfileExt->get();

        foreach ($resProfileExt as $extension) {
            // formatear fecha creacion
            $date = Carbon::parse($extension->created_at)->format('Y-m-d');
            $extension['created_at_no_timezone'] = $date;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileExtensions' => $resProfileExt,
        ], "message" => "ProfileExtensionsController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileExtensionsController index");
        /////////////////////////////////////////

        return response()->json([
            'profileExtensions' => $resProfileExt,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\ProfileExtensionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProfileExtensionsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileExtensionsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();
        $prefix = CompanySetting::where("option", "=", 'extension_pbx_prefix')->where('company_id', "=", Auth::user()->company_id)->first();
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $data['company_id'] = $company_id;
        $data['creator_id'] = $user_id;
        $data['minutes_increments'] = intval(preg_replace('/[^0-9]/', '', $data['minutes_increments'])); // extraer numeros
        // print_r($data);
        $resProfileExtension = ProfileExtensions::create($data);
        $profile_ext_id = $resProfileExtension->id;

        $prefixtext = "EXT";
        if($prefix != null) {
            $prefixtext = $prefix->value;
        }

        $extensions_number = '';
        if($resProfileExtension->id > 0 && $resProfileExtension->id < 10) {
            $extensions_number = $prefixtext.'-00000'.$resProfileExtension->id;
        }
        if($resProfileExtension->id > 9 && $resProfileExtension->id < 100) {
            $extensions_number = $prefixtext.'-0000'.$resProfileExtension->id;
        }
        if($resProfileExtension->id > 99 && $resProfileExtension->id < 1000) {
            $extensions_number = $prefixtext.'-000'.$resProfileExtension->id;
        }
        if($resProfileExtension->id > 999 && $resProfileExtension->id < 10000) {
            $extensions_number = $prefixtext.'-00'.$resProfileExtension->id;
        }
        if($resProfileExtension->id > 9999 && $resProfileExtension->id < 100000) {
            $extensions_number = $prefixtext.'-0'.$resProfileExtension->id;
        }
        if($resProfileExtension->id > 99999) {
            $extensions_number = $prefixtext.'-'.$resProfileExtension->id;
        }
        $resProfileExtension->extensions_number = $extensions_number;
        $resProfileExtension->save();


        if (! $resProfileExtension) {
            return response()->json([
                'profileExtensions' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false
            ]);
        }
        if ($profile_ext_id) {
            foreach ($data['aditional_charges'] as $charge) {
                // aditional charges
                $this->addAditionalCharges($charge, $profile_ext_id, $company_id, $user_id);
            }
        }



        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileExtension' => $resProfileExtension,
        ], "message" => "ProfileExtensionsController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileExtensionsController store");
        /////////////////////////////////////////

        return response()->json([
            'profileExtension' => $resProfileExtension,
            'message' => '¡Registro Exitoso!',
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Requests\ProfileExtensionsRequest $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, ProfileExtensionsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileExtensionsController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resProfileExt = ProfileExtensions::with('aditionalCharges')
            ->select('profile_extensions.*')
            ->find($id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileExtension' => $resProfileExt,
        ], "message" => "ProfileExtensionsController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileExtensionsController show");
        /////////////////////////////////////////



        return response()->json([
            'profileExtension' => $resProfileExt,
            'message' => 'Profile Extension succesfully',
            'success' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Requests\ProfileExtensionsRequest $request
     * @param  int  $id
     * @param  \Crater\Models\ProfileExtensions $ProfileExtensions
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileExtensionsRequest $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileExtensionsController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $data = $request->validated();
        $profileExt = ProfileExtensions::find($id);
        $profileExt->update($data);
        // eliminar cargo adicional
        AditionalCharges::where('profile_extension_id', $id)->delete();
        // recorrer nuevos cargos adicionales
        foreach ($data['aditional_charges'] as $charge) {
            // aditional charges
            $this->addAditionalCharges($charge, $id, $company_id, $user_id);
        }


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileExt' => $profileExt,
        ], "message" => "ProfileExtensionsController put"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileExtensionsController update");
        /////////////////////////////////////////

        return response()->json([
            'profileExt' => $profileExt,
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Crater\Models\ProfileExtensions $ProfileExtensions
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileExtensionsController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // verificar si esta realacionado con pbx_packages
        $pbx_package = PbxPackages::where('template_extension_id', $id)->first();

        if($pbx_package) {
            return response()->json([
                'message' => 'It cannot be removed, it is related to a package',
                'success' => false
            ], 406);
        }

        $profileExt = ProfileExtensions::find($id);
        if ($profileExt) {
            $profileExt->delete();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No existe el registro'
            ]);
        }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "soft delete success"];
        LogsDev::finishLog($log, $res, $time, 'D', "soft delete success");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado con éxito.'
        ]);
    }
}
