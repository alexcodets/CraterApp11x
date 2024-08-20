<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\TaxAgencyRequest;
use Crater\Models\Address;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\TaxAgency;
use Crater\Models\TaxType;
use Illuminate\Http\Request;
use Log;

class TaxAgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxAgencyController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $taxAgencies = TaxAgency::select('*')
            //->latest()
            ->with('taxAgencyAddress')
            ->applyFilters($request->only([
                'name',
                'number',
                'country_id',
                'state_id',
                'orderBy',
            ]))
            ->paginateData($limit);


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxAgencies' => $taxAgencies,
        ], "message" => "TaxAgencyController"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxAgencyController");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Tax Agencies", "List", "admin/settings/tax-agencies", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'taxAgencies' => $taxAgencies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxAgencyRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxAgencyController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $data = $request->validated();

        $taxAgency = TaxAgency::create($data);

        if ($taxAgency) {
            // salvar address
            $dataAddress = [];
            $dataAddress['name'] = $request->billing['name'];
            $dataAddress['address_street_1'] = $request->billing['address_street_1'];
            $dataAddress['address_street_2'] = $request->billing['address_street_2'];
            $dataAddress['city'] = $request->billing['city'];
            $dataAddress['state_id'] = $request->billing['state_id'];
            $dataAddress['country_id'] = $request->billing['country_id'];
            $dataAddress['county'] = $request->billing['county'];
            $dataAddress['zip'] = $request->billing['zip'];
            $dataAddress['phone'] = $request->billing['phone'];
            $dataAddress['type'] = $request->billing['type'];

            $dataAddress['company_id'] = $company_id;
            $dataAddress['user_id'] = $user_id;
            $dataAddress['tax_agency_id'] = $taxAgency->id;

            try {
                $taxAgencyAddress = Address::create($dataAddress);

            } catch (Exception $e) {
                echo 'Excepción capturada: ',  $e->getMessage(), "\n";

                $taxAgency->delete();

                //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
                $res = ["success" => false, "response" => [
                    'taxAgency' => [],
                ], "message" => "TaxAgencyController"];
                LogsDev::finishLog($log, $res, $time, 'D', "TaxAgencyController");
                /////////////////////////////////////////

                return response()->json([
                    'success' => false,
                    'taxAgency' => [],
                    'message' => $e->getMessage()
                ]);
            }
        }

        // Logs por modulo
        LogsModule::createLog("Tax Agency", "Create", "admin/settings/tax-agency", $taxAgency->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax Agency: ".$taxAgency->name);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxAgency' => $taxAgency,
        ], "message" => "TaxAgencyController"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxAgencyController");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
            'taxAgency' => $taxAgency,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TaxAgency $taxAgency, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(TaxAgency' => $taxAgency]);
        $log = LogsDev::initLog($request, "", "D", "TaxAgencyController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // //Log::debug('update agency ---------');
        // //Log::debug($id);
        //Log::debug($taxAgency);

        $taxAgency = TaxAgency::find($taxAgency['id']);
        $address = Address::select('*')->where('tax_agency_id', $taxAgency->id)->whereNull('deleted_at')->first();
        if ($address) {
            $taxAgency->address = $address;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxAgency' => $taxAgency,
        ], "message" => "TaxAgencyController show"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxAgencyController show");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Tax Agency", "View", "admin/settings/tax-agency", $taxAgency->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax type: ".$taxAgency->name);

        return response()->json([
            'taxAgency' => $taxAgency,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\TaxAgencyRequest  $request
     * @param  \Crater\Models\TaxAgency  $taxAgency
     * @return \Illuminate\Http\Response
     */
    public function update(TaxAgencyRequest $request, TaxAgency $taxAgency)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(TaxAgency' => $taxAgency]);
        $log = LogsDev::initLog($request, "", "D", "TaxAgencyController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();

        /* //Log::debug('--- update ---');
        //Log::debug($request);
        //Log::debug($data); */

        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $taxAgency = TaxAgency::find($request['id']);
        $taxAgency->name = $request['name'];
        $taxAgency->number = $request['number'];
        $taxAgency->email = $request['email'];
        $taxAgency->phone = $request['phone'];
        $taxAgency->website = $request['website'];
        $taxAgency->note = $request['note'];
        $taxAgency->company_id = $company_id;
        $taxAgency->creator_id = $user_id;
        $taxAgency->save();
        //Log::debug($taxAgency->id);


        if ($taxAgency) {
            $taxAgencyAddress = Address::select('*')->where('tax_agency_id', $taxAgency->id)->first();

            //Log::debug(gettype($taxAgencyAddress));


            try {
                if (isset($taxAgencyAddress)) {
                    // address
                    $taxAgencyAddress['name'] = $request->billing['name'];
                    $taxAgencyAddress['address_street_1'] = $request->billing['address_street_1'];
                    $taxAgencyAddress['address_street_2'] = $request->billing['address_street_2'];
                    $taxAgencyAddress['city'] = $request->billing['city'];
                    $taxAgencyAddress['state_id'] = $request->billing['state_id'];
                    $taxAgencyAddress['country_id'] = $request->billing['country_id'];
                    $taxAgencyAddress['county'] = $request->billing['county'];
                    $taxAgencyAddress['zip'] = $request->billing['zip'];
                    $taxAgencyAddress['phone'] = $request->billing['phone'];
                    $taxAgencyAddress['type'] = $request->billing['type'];
                    $taxAgencyAddress['company_id'] = $company_id;
                    $taxAgencyAddress['user_id'] = $user_id;
                    $taxAgencyAddress['tax_agency_id'] = $taxAgency->id;
                    //Log::debug(' --- tax agency address ---');
                    $taxAgencyAddress->save();
                } else {
                    // address
                    $dataAddress['name'] = $request->billing['name'];
                    $dataAddress['address_street_1'] = $request->billing['address_street_1'];
                    $dataAddress['address_street_2'] = $request->billing['address_street_2'];
                    $dataAddress['city'] = $request->billing['city'];
                    $dataAddress['state_id'] = $request->billing['state_id'];
                    $dataAddress['country_id'] = $request->billing['country_id'];
                    $dataAddress['county'] = $request->billing['county'];
                    $dataAddress['zip'] = $request->billing['zip'];
                    $dataAddress['phone'] = $request->billing['phone'];
                    $dataAddress['type'] = $request->billing['type'];
                    $dataAddress['company_id'] = $company_id;
                    $dataAddress['user_id'] = $user_id;
                    $dataAddress['tax_agency_id'] = $taxAgency->id;
                    // Address::create($taxAgencyAddress);
                    $taxAgency->address = Address::create($dataAddress);
                }

            } catch (Exception $e) {
                echo 'Excepción capturada: ',  $e->getMessage(), "\n";

                $taxAgency->delete();

                //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
                $res = ["success" => false, "response" => [
                    'taxAgency' => [],
                ], "message" => "TaxAgencyController"];
                LogsDev::finishLog($log, $res, $time, 'D', "TaxAgencyController");
                /////////////////////////////////////////

                return response()->json([
                    'success' => false,
                    'taxAgency' => [],
                    'message' => $e->getMessage()
                ]);
            }
        }


        // Logs por modulo
        LogsModule::createLog("Tax Agency", "Update", "admin/settings/tax-agency", $taxAgency->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax agency: ".$taxAgency->name);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'taxAgency' => $taxAgency,
        ], "message" => "TaxAgencyController update"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxAgencyController update");
        /////////////////////////////////////////

        return response()->json([
            'taxAgency' => $taxAgency,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\TaxAgency  $taxAgency
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxAgency $taxAgency, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['(TaxAgency' => $taxAgency]);
        $log = LogsDev::initLog($request, "", "D", "TaxAgencyController", "destroy");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //Log::debug($taxAgency->id);
        $TaxTypeAgency = TaxType::select('*')->where('tax_agency_id', $taxAgency->id)->get();
        //Log::debug('--- count ---');
        //Log::debug(count($TaxTypeAgency));
        if (count($TaxTypeAgency) > 0) {

            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => [
                'success' => false,
            ], "message" => "destroy failed"];
            LogsDev::finishLog($log, $res, $time, 'D', "destroy failed");
            /////////////////////////////////////////

            return response()->json([
                'success' => false,
            ]);
        }


        // Logs por modulo
        LogsModule::createLog("Tax Agencies", "delete", "admin/settings/tax-agency", $taxAgency->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Tax agency: ".$taxAgency->name);

        $taxAgency->delete();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "destroy succes"];
        LogsDev::finishLog($log, $res, $time, 'D', "destroy success");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);

    }
}
