<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\NotesRequest;
use Crater\Models\LogsDev;
use Crater\Models\Note;
use Illuminate\Http\Request;
use Log;

class NotesController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "NotesController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->limit ?? 10;
        $notes = Note::applyFilters($request->only(['type', 'search', 'orderByField', 'orderBy']))
            ->latest()
            ->paginate($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'notes' => $notes,
        ], "message" => "Index note"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Index note");
        /////////////////////////////////////////

        return response()->json([
            'notes' => $notes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotesRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "NotesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $note = Note::create($request->validated());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'note' => $note,
        ], "message" => "store note"]];
        LogsDev::finishLog($log, $res, $time, 'D', "store note");
        /////////////////////////////////////////

        return response()->json([
            'note' => $note,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Note $note)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "NotesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'note' => $note,
        ], "message" => "show note"]];
        LogsDev::finishLog($log, $res, $time, 'D', "show note");
        /////////////////////////////////////////

        return response()->json([
            'note' => $note,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(NotesRequest $request, Note $note)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "NotesController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $note->update($request->validated());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'note' => $note,
        ], "message" => "update note"]];
        LogsDev::finishLog($log, $res, $time, 'D', "update note");

        /////////////////////////////////////////
        return response()->json([
            'note' => $note,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Note $note)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "NotesController", "destroy");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $note->delete();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "Destroy note"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Destroy note");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }
}
