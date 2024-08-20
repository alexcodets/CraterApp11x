<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\AvalaraLocationsRequest;
// requests
use Crater\Models\Address;
use Crater\Models\AvalaraLocation;
use Crater\Models\LogsDev;
// models
use Crater\Models\User;
use Illuminate\Http\Request;
use Log;

class AvalaraLocationsController extends Controller
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
    public function store(AvalaraLocationsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraLocationsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        /* //Log::debug('request to save----');
        //Log::debug($request); */

        $data = $request->validated();

        $user = User::find($request->user_id);
        if (is_null($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid User',
            ]);
        }

        $data['user_id'] = $user->id;
        $data['company_id'] = $user->company->id;

        //$avalaraLocation = AvalaraLocation::create($data);

        /* //Log::debug('data to save----');
        //Log::debug($data);
        //Log::debug($request); */

        if (! isset($request['avalara_location_id']) || $request['avalara_location_id'] == null) {
            // create
            $avalaraLocation = AvalaraLocation::create($data);
        } else {
            $avalaraLocation = AvalaraLocation::findOrFail($request['avalara_location_id']);
            // update
            $avalaraLocation = $avalaraLocation->update($data);
        }

        if (! $avalaraLocation) {
            return response()->json([
                'avalaraLocation' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false,
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'avalaraLocation' => $avalaraLocation,
        ], "message" => "AvalaraLocationsController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "AvalaraLocationsController store");
        /////////////////////////////////////////

        return response()->json([
            'avalaraLocation' => $avalaraLocation,
            'message' => '¡Registro Exitoso!',
            'success' => true,
        ]);
    }

    public function updateLocationCustomer(AvalaraLocationsRequest $request)
    {
        $dataAvalaraLocation = $request->all();
        $dataAddress = $request->dataAddress;
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "updateLocationCustomer", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        /* //Log::debug('request to save----');
        //Log::debug($request); */


        $user = User::find($dataAvalaraLocation['user_id']);
        if (is_null($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid User',
            ]);
        }


        $dataAvalaraLocation['user_id'] = $user->id;
        $dataAvalaraLocation['company_id'] = $user->company->id;

        //$avalaraLocation = AvalaraLocation::create($data);

        /* //Log::debug('data to save----');
        //Log::debug($data);
        //Log::debug($request); */

        $avalaraLocation = AvalaraLocation::find($user['avalara_location_id']);
        if($avalaraLocation) {
            $avalaraLocation = $avalaraLocation->update($dataAvalaraLocation);
        } else {
            $avalaraLocation = AvalaraLocation::create($dataAvalaraLocation);
            $user->avalara_location_id = $avalaraLocation->id;
            $user->save();
        }


        if (! $avalaraLocation) {
            return response()->json([
                'avalaraLocation' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false,
            ]);
        }

        // update Address customer
        $addressUpdate = Address::find($dataAddress['id']);
        $addressUpdate->update($dataAddress);

        // if ( !isset($request['avalara_location_id']) ||  $request['avalara_location_id'] == null){
        //     // create
        //     $avalaraLocation = AvalaraLocation::create($data);
        // } else {
        //     $avalaraLocation = AvalaraLocation::findOrFail($request['avalara_location_id']);
        //     // update
        //     $avalaraLocation = $avalaraLocation->update($data);
        // }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'avalaraLocation' => $avalaraLocation,
        ], "message" => "AvalaraLocationsController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "AvalaraLocationsController store");
        /////////////////////////////////////////

        return response()->json([
            'avalaraLocation' => $avalaraLocation,
            'message' => '¡Registro Exitoso!',
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraLocationsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        /* //Log::debug('request to save----');
        //Log::debug($request); */

        $data = $request->validated();
        $data['user_id'] = User::findOrFail($request->user_id)->id;

        /* //Log::debug('data to save----');
        //Log::debug($data);
        //Log::debug($request); */

        if (! isset($request['avalara_location_id']) || $request['avalara_location_id'] == null) {
            // create
            $avalaraLocation = AvalaraLocation::create($data);
        } else {
            $avalaraLocation = AvalaraLocation::findOrFail($request['avalara_location_id']);
            // update
            $avalaraLocation = $avalaraLocation->update($data);
        }

        if (! $avalaraLocation) {
            return response()->json([
                'avalaraLocation' => null,
                'message' => 'Ha ocurrido un error',
                'status' => 406,
                'success' => false,
            ]);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'avalaraLocation' => $avalaraLocation,
        ], "message" => "AvalaraLocationsController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "AvalaraLocationsController store");
        /////////////////////////////////////////

        return response()->json([
            'avalaraLocation' => $avalaraLocation,
            'message' => '¡Registro Exitoso!',
            'success' => true,
        ]);
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
