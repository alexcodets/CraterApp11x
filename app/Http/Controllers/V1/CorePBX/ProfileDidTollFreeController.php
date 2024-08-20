<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\ProfileDidTollFree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/* use Auth; */
use Log;

class ProfileDidTollFreeController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "ProfileDidTollFreeController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $resProfileDIDTOLLFREE = ProfileDidTollFree::applyFilters($request->only([
            'status',
            'prefijo',
            'toll_free_category_id',
            'orderByField',
            'orderBy',
        ]))
        ->select('profile_did_toll_frees.*')
        ->latest()
        ->paginateData($limit);


        /*  $resProfileDIDTOLLFREE = ProfileDidTollFree::all(); */
        /* $resProfileDIDTOLLFREE = ProfileDidTollFree::all(); */



        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileDIDTOLLFREE' => $resProfileDIDTOLLFREE,
            'profileDIDTOLLFREETotalCount' => ProfileDidTollFree::whereStatus('A')->count(),
        ], "message" => "ProfileDidTollFreeController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileDidTollFreeController index");
        /////////////////////////////////////////

        return response()->json([
            'profileDIDTOLLFREE' => $resProfileDIDTOLLFREE,
            'profileDIDTOLLFREETotalCount' => ProfileDidTollFree::whereStatus('A')->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   /* ProfileDidTollFree */

        $data["status"] = $request->status;
        $data["prefijo"] = $request->prefijo;
        $val = Validator::make($data, [
            'prefijo' => ['nullable','integer'],
            'status' => ['nullable'],
            'toll_free_category_id' => ['nullable']
        ]);

        $time = microtime(true);
        // Init log
        $log = LogsDev::initLog($request, "", "D", "ProfileDidTollFreeController", "store");

        // Create options groups
        /*  $data = $request->validated(); */
        $ProfileDidTollFree = ProfileDidTollFree::createProfileDidTollFree($request);



        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'ProfileDidTollFree' => $ProfileDidTollFree,
                    'success' => true,
                ],
                "message" => "Guardado el did toll free"
            ]
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'ProfileDidTollFree' => $ProfileDidTollFree,
        ], "message" => "DidtollFree Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "DidtollFree Index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'ProfileDidTollFree' => $ProfileDidTollFree,
            'message' => '¡Successful registration!',
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\ProfileDidTollFree  $profileDidTollFree
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // $resProfileDID = ProfileDIDRequest::find($id);
        $ProfileDidTollFree = ProfileDidTollFree::find($id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileDID' => $ProfileDidTollFree,
        ], "message" => "ProfileDIDController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileDIDController show");
        /////////////////////////////////////////

        return response()->json([
            'ProfileDidTollFree' => $ProfileDidTollFree,
            'success' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\ProfileDidTollFree  $profileDidTollFree
     * @return \Illuminate\Http\Response
     */
    public function edit(ProfileDidTollFree $profileDidTollFree)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\ProfileDidTollFree  $profileDidTollFree
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $data["toll_free_category_id"] = $request->toll_free_category_id;
        $data["status"] = $request->statu['value'];
        $data["prefijo"] = $request->prefijo;
        $data["rate_per_minute"] = $request->rate_per_minutes;
        $val = Validator::make($data, [
            'prefijo' => ['nullable','integer'],
            'status' => ['nullable'],
            'toll_free_category_id' => ['nullable']
        ]);

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo


        /* $data = $request->validated(); */
        $ProfileDidTollFree = ProfileDidTollFree::find($id);
        $ProfileDidTollFree->update($data);




        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'profileDID' => $ProfileDidTollFree,
        ], "message" => "ProfileDIDController put"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfileDIDController update");

        /////////////////////////////////////////
        return response()->json([
            'ProfileDidTollFree' => $ProfileDidTollFree,
            'message' => 'Profile Did Toll Free succesfully',
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\ProfileDidTollFree  $profileDidTollFree
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ProfileDIDController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $ProfileDidTollFree = ProfileDidTollFree::find($id);
        if ($ProfileDidTollFree) {
            $ProfileDidTollFree->delete();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No existe el registro'
            ]);
        }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "Delete success"];
        LogsDev::finishLog($log, $res, $time, 'D', "Delete success");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado con éxito.'
        ]);
    }
}
