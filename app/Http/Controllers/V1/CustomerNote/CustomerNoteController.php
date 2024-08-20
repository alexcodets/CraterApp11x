<?php

namespace Crater\Http\Controllers\V1\CustomerNote;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CustomerNoteRequest;
use Crater\Http\Requests\DeleteNoteRequest;
use Crater\Models\CustomerNote;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class CustomerNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomerNoteController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        $customerNote = CustomerNote::applyFilters(
            $request->only([
                'summary',
                'note',
                'orderByField',
                'orderBy'
            ])
        )
            ->select('customer_notes.*', 'users.name', 'users.id as userid')
            ->where('user_id', $request->customer_id)
            ->join('users', 'customer_notes.creator_id', '=', 'users.id')
            ->paginateData($limit);

        $count = CustomerNote::where('user_id', $request->customer_id)->count();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customerNote' => $customerNote,
            'customerNoteTotalCount' => $count,
            /* 'providerTotalCount' => Provider::whereStatus('A')->count(), */
        ], "message" => "Listado de notas"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Listado de notas");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'customerNote' => $customerNote,
            'customerNoteTotalCount' => $count,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerNoteRequest $request)
    {

        $time = microtime(true);
        // Init log
        $log = LogsDev::initLog($request, "", "D", "CustomerNoteController", "store");

        // Create options groups
        $customerNote = CustomerNote::createCustomerNote($request);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customerNote' => $customerNote,
                    'success' => true,
                ],
                "message" => "Guardada la nota",
            ],
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customerNote' => $customerNote,
        ], "message" => "CustomerNote Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "CustomerNote Index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'customerNote' => $customerNote,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\CustomerNote  $customerNote
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $time = microtime(true);

        $customerNote = CustomerNote::where('id', $id)
            ->select('*')->first();

        $log = LogsDev::initLog($request, "", "D", "CustomerNoteController", "show");

        //Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customerNote' => $customerNote,
        ], "message" => "customerNote show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "customerNote show");
        //Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'customerNote' => $customerNote,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\CustomerNote  $customerNote
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerNote $customerNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\CustomerNote  $customerNote
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerNoteRequest $request, CustomerNote $customerNote)
    {
        $time = microtime(true);

        // Init log
        $log = LogsDev::initLog($request, "", "D", "ItemGroupController", "update");

        // Update options groups
        $customerNote = CustomerNote::updateCustomerNote($request, $customerNote);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'customerNote' => $customerNote,
                    'success' => true,
                ],
                "message" => "Actualizacion grupo de items",
            ],
        ];

        // Finish log
        LogsDev::finishLog($log, $res, $time, 'D', "Actualizacion grupo de items");

        // Module log
        LogsModule::createLog(
            "CustomerNote",
            "Update",
            "/admin/customers/id/id1/edit-note",
            $customerNote->id,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id,
            "ItemsGroup: ".$customerNote->name
        );

        return response()->json([
            'customerNote' => $customerNote,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\CustomerNote  $customerNote
     * @return \Illuminate\Http\Response
     */
    public function delete(DeleteNoteRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomerNoteController", "destroy");
        //////////////////

        CustomerNote::destroy($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'CustomerNote deleted successfully',
        ], "message" => "CustomerNote deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "CustomerNote deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'CustomerNote deleted successfully',
        ]);
    }
}
