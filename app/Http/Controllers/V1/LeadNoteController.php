<?php

namespace Crater\Http\Controllers\V1;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Lead;
use Crater\Models\LeadNote;
use Crater\Models\LogsDev;
use Illuminate\Http\Request;

class LeadNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $limit = $request->input('limit', 10);
        $lead_id = $request->input('lead_id', 0);

        $leadnotes = LeadNote::applyFilters($request->only([
            'orderByField',
            'orderBy',

        ]))->where("lead_id", $lead_id)->whereNULL("deleted_at")->whereCompany($request->header('company'))->latest()->paginateData($limit);

        return response()->json([
            'leadnotes' => $leadnotes,
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
    {
        //
        try {

            $data = $request->all();
            $data['company_id'] = auth()->user()->company->id;
            $data['creator_id'] = auth()->user()->id;
            $data['leadnote_number'] = $this->getNextLeadNoteNumber();
            \Log::debug($data);
            LeadNote::create($data);

            $lead = Lead::where('id', $data['lead_id'])->first();

            // Si se encuentra el registro, actualizar el campo last_contacted_date
            if ($lead) {
                $lead->last_contacted_date = Carbon::now()->format('Y-m-d');
                if (! is_null($data['followdate']) && strtotime($data['followdate']) !== false) {
                    $lead->followup_date = date('Y-m-d', strtotime($data['followdate']));
                }
                $lead->save();
            }

            return response()->json([
                'success' => true,
            ]);
        } catch (\throwable $th) {
            \Log::debug($th);

            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function getNextLeadNoteNumber()
    {
        // Obtener el mayor valor numérico del campo leadnote_number
        $maxNumber = LeadNote::whereNotNull('leadnote_number')
            ->selectRaw('MAX(CAST(SUBSTRING(leadnote_number, 4) AS UNSIGNED)) as max_number')
            ->pluck('max_number')
            ->first();

        // Si no hay ningún número, empezar desde 0
        $nextNumber = $maxNumber ? $maxNumber + 1 : 1;

        // Formatear el número con ceros a la izquierda para que tenga 5 caracteres
        $formattedNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Concatenar el prefijo 'LN-' con el número formateado
        return 'LN-'.$formattedNumber;
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\LeadNote  $leadNote
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        \Log::debug($id);

        $time = microtime(true);

        $leadnote = LeadNote::where('id', $id)->first();

        $log = LogsDev::initLog($request, "", "D", "CustomerNoteController", "show");

        //Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'leadnote' => $leadnote,
        ], "message" => "customerNote show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "customerNote show");
        //Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'leadnote' => $leadnote,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\LeadNote  $leadNote
     * @return \Illuminate\Http\Response
     */
    public function edit(LeadNote $leadNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\LeadNote  $leadNote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LeadNote $leadNote)
    {
        //
        \Log::debug("update leadnote");
        \Log::debug($request->input());
        \Log::debug($leadNote);
        $data = $request->input();
        $leadNote->update($data);

        return response()->json([

            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\LeadNote  $leadNote
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeadNote $leadNote)
    {
        //
    }

    public function delete(Request $request)
    {
        // Obtener el id del request
        $id = $request->input('id');

        // Buscar el registro en el modelo LeadNote
        $leadNote = LeadNote::find($id);

        // Si se encuentra el registro, marcarlo como eliminado
        if ($leadNote) {
            $leadNote->deleted_at = Carbon::now();
            $leadNote->save();

            return response()->json([
                'success' => true,
            ]);
        }

        // Si no se encuentra el registro, responder con un mensaje de error
        return response()->json([
            'message' => 'not found',
            'success' => false,
        ]);
    }
}
