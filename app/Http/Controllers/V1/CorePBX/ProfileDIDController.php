<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
// request
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\ProfileDIDRequest;
use Crater\Models\AditionalCharges;
// models
use Crater\Models\CompanySetting;
use Crater\Models\LogsDev;
use Crater\Models\PbxPackages;
use Crater\Models\ProfileDID;
use Illuminate\Http\Request;

class ProfileDIDController extends Controller
{
    /**
     * add profile did / aditional charges
     */
    public function addAditionalCharges($charge, $profile_did_id, $company_id, $user_id)
    {
        $res = [];

        if (! empty($charge['amount'])) {
            $newCharge = new AditionalCharges();
            $newCharge->company_id = $company_id;
            $newCharge->creator_id = $user_id;
            $newCharge->profile_extension_id = null;
            $newCharge->description = $charge['description'];
            $newCharge->amount = $charge['amount'];
            $newCharge->status = $charge['status'];
            $newCharge->profile_did_id = $profile_did_id;
            $res[] = $newCharge->save();
        }

        return $res;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resProfileDID = ProfileDID::applyFilters($request->only([
            'search',
            'name',
            'did_number',
            'orderByField',
            'orderBy',
        ]))
        ->select('profile_did.*')
        ->paginate(10);

        // recorrer respuesta
        foreach ($resProfileDID as $did) {
            // formatear fecha creacion
            $date = Carbon::parse($did->created_at)->format('Y-m-d');
            $did['created_at_no_timezone'] = $date;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileDID' => $resProfileDID,
        ], "message" => "ProfileDIDController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileDIDController index");
        /////////////////////////////////////////

        return response()->json([
            'profileDID' => $resProfileDID,
        ]);
    }

    public function indexnp(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $resProfileDID = ProfileDID::applyFilters($request->only([
            'search',
            'name',
            'did_number',
            'orderByField',
            'orderBy',
        ]))
        ->select('profile_did.*')
        ->get();

        // recorrer respuesta
        foreach ($resProfileDID as $did) {
            // formatear fecha creacion
            $date = Carbon::parse($did->created_at)->format('Y-m-d');
            $did['created_at_no_timezone'] = $date;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileDID' => $resProfileDID,
        ], "message" => "ProfileDIDController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileDIDController index");
        /////////////////////////////////////////

        return response()->json([
            'profileDID' => $resProfileDID,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\ProfileDIDRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProfileDIDRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();
        $prefix = CompanySetting::where("option", "=", 'did_pbx_prefix')->where('company_id', "=", Auth::user()->company_id)->first();
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $data['company_id'] = $company_id;
        $data['creator_id'] = $user_id;

        $resProfileDID = ProfileDID::create($data);
        $profile_did_id = $resProfileDID->id;

        if ($request->has('item_groups')) {
            ProfileDID::createItemGroups($resProfileDID, $request);
        }

        $prefixtext = "DID";
        if($prefix != null) {
            $prefixtext = $prefix->value;
        }

        $did_number = '';
        if($resProfileDID->id > 0 && $resProfileDID->id < 10) {
            $did_number = $prefixtext.'-00000'.$resProfileDID->id;
        }
        if($resProfileDID->id > 9 && $resProfileDID->id < 100) {
            $did_number = $prefixtext.'-0000'.$resProfileDID->id;
        }
        if($resProfileDID->id > 99 && $resProfileDID->id < 1000) {
            $did_number = $prefixtext.'-000'.$resProfileDID->id;
        }
        if($resProfileDID->id > 999 && $resProfileDID->id < 10000) {
            $did_number = $prefixtext.'-00'.$resProfileDID->id;
        }
        if($resProfileDID->id > 9999 && $resProfileDID->id < 100000) {
            $did_number = $prefixtext.'-0'.$resProfileDID->id;
        }
        if($resProfileDID->id > 99999) {
            $did_number = $prefixtext.'-'.$resProfileDID->id;
        }
        $resProfileDID->did_number = $did_number;
        $resProfileDID->save();

        if (! $resProfileDID) {
            return response()->json([
                'profileDID' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false
            ]);
        }
        if ($profile_did_id) {
            foreach ($data['aditional_charges'] as $charge) {
                // print_r($tax);
                // $charge_bd = AditionalCharges::find($charge['id']);
                // Packages Tax Types
                $this->addAditionalCharges($charge, $profile_did_id, $company_id, $user_id);
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileDID' => $resProfileDID,
        ], "message" => "ProfileDIDController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileDIDController store");
        /////////////////////////////////////////

        return response()->json([
            'profileDID' => $resProfileDID,
            'message' => '¡Successful registration!',
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Requests\ProfileDIDRequest $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, ProfileDIDRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // $resProfileDID = ProfileDIDRequest::find($id);
        $resProfileDID = ProfileDID::with('aditionalCharges')->with('itemGroups')
            ->select('profile_did.*')
            ->find($id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileDID' => $resProfileDID,
        ], "message" => "ProfileDIDController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileDIDController show");
        /////////////////////////////////////////

        return response()->json([
            'profileDID' => $resProfileDID,
            'message' => 'Profile Extension succesfully',
            'success' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Requests\ProfileDIDRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProfileDIDRequest $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $data = $request->validated();
        $profileDID = ProfileDID::find($id);
        $profileDID->update($data);

        // Eliminar los grupos asociados
        $itemG = ProfileDID::with('itemGroups')->find($id);
        ProfileDID::deleteItemGroups($itemG);

        if ($request->has('item_groups')) {
            // Asociar nuevos grupos
            ProfileDID::createItemGroups($profileDID, $request);
        }

        // eliminar cargo adicional
        AditionalCharges::where('profile_did_id', $id)->delete();
        // recorrer nuevos cargos adicionales
        foreach ($data['aditional_charges'] as $charge) {
            // aditional charges
            $this->addAditionalCharges($charge, $id, $company_id, $user_id);
        }



        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileDID' => $profileDID,
        ], "message" => "ProfileDIDController put"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileDIDController update");
        /////////////////////////////////////////

        return response()->json([
            'profileDID' => $profileDID,
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $pbx_package = PbxPackages::where('template_did_id', $id)->first();

        if($pbx_package) {
            return response()->json([
                'message' => 'It cannot be removed, it is related to a package',
                'success' => false
            ], 406);
        }


        $profileDID = ProfileDID::find($id);
        if ($profileDID) {
            $itemG = ProfileDID::with('itemGroups')->find($id);
            ProfileDID::deleteItemGroups($itemG); // Eliminar los grupos asociados
            $profileDID->delete();
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
