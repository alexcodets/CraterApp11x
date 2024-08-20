<?php

namespace Crater\Http\Controllers\V1\Address;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\AddressRequest;
use Crater\Models\Address;
// models
use Crater\Models\AvalaraLocation;
use Crater\Models\CustomerPackage;
use Crater\Models\LogsDev;

use Crater\Models\LogsModule;
use Crater\Models\PbxServices;

use Illuminate\Http\Request;

class AddressController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "AddressController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        // //Log::debug($request->customer_id);

        if ($request->customer_id > 0) {
            $customerAddress = Address::select('*')->applyFilters($request->only([

                'orderByField',
                'orderBy',

            ]))
            ->where('user_id', $request->customer_id)->with('country', 'state', 'user')->paginateData($limit);
            ;

            foreach($customerAddress  as $cAddress) {
                $cAddress["avalaraLocation"] = AvalaraLocation::where("addresses_id", $cAddress->id)->first();
            }
            $count = Address::where('user_id', $request->customer_id)->count();

        } else {
            $customerAddress = Address::select('*')->with('country', 'state', 'user')->paginateData($limit);
            ;
            foreach($customerAddress  as $cAddress) {
                $cAddress["avalaraLocation"] = AvalaraLocation::where("addresses_id", $cAddress->id)->first();
            }
            $count = Address::count();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customerAddress' => $customerAddress,
            'customerAddressTotalCount' => $count,
        ], "message" => "Listado de Address"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Listado de notas");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'customerAddress' => $customerAddress,
            'customerAddressTotalCount' => $count,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AddressController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;

        $customerAddress = Address::create($data);

        if ($customerAddress) {
            $response = [
                'status' => 200,
                'response' => 'Address saved correctly',
                'success' => true,
                'customerAddress' => $customerAddress
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error saving Address',
                'success' => false
            ];
        }

        // Logs por modulo
        LogsModule::createLog("Address", "store", "admin/customer/address", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        LogsDev::finishLog($log, $response, $time, 'D', "Address store");

        return response()->json($response, $response['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AddressController", "show");

        $customerAddress = Address::find($id);

        if ($customerAddress) {
            $response = [
                'status' => 200,
                'response' => 'Getting Address correctly',
                'success' => true,
                'customerAddress' => $customerAddress
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error getting Address',
                'success' => false
            ];
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        LogsDev::finishLog($log, $response, $time, 'D', "AddressController show");

        // Logs por modulo
        LogsModule::createLog("AddressController", "show", "admin/customer/address", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AddressController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $address = Address::where('user_id', $request->input('user_id'))->where('type', $request->input('type'))->whereNull('deleted_at')->first();

        if ($request->input('user_id') == 1) {
            $address = Address::where('id', 1)->first();
        }


        //Log::debug('request---');
        //Log::debug($request);

        if ($request->has('id')) {
            //Log::debug('tiene id');
            $address = Address::find($request->id);
        }

        // $address->address_street_1 = $request->address_street_1;
        // $address->address_street_2 = $request->address_street_2;
        $address->country_id = $request->country_id;
        $address->county = $request->county;
        $address->city = $request->city;
        $address->state_id = $request->state_id;
        $address->type = $request->type;
        $address->pcode = $request->pcode;
        $address->company_id = Auth::user()->company_id;

        $updated = $address->save();

        if ($updated) {
            $response = [
                'status' => 200,
                'response' => 'Address update correctly',
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error saving Address',
            ];
        }

        LogsDev::finishLog($log, $response, $time, 'D', "Address update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Address", "Update", "admin/customer/address", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AddressController", "delete");

        $pbxServices = PbxServices::where('addresses_id', $request->id)->get()->count();

        $customerPackage = CustomerPackage::where('addresses_id', $request->id)->get()->count();

        if ($pbxServices == 0 and $customerPackage == 0) {
            $address = Address::find($request->id);
            $address->delete();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'It cannot be removed, it is related to a pbx services or a customer package',
            ]);
        }

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'success' => true,
                ],
                "message" => "Address delete"
            ]
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        LogsDev::finishLog($log, $res, $time, 'D', "CustomerAddress deleted successfully");

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);

    }
}
