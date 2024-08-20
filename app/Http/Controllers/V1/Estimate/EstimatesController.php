<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeleteEstimatesRequest;
use Crater\Http\Requests\EstimatesRequest;
use Crater\Jobs\GenerateEstimatePdfJob;
use Crater\Models\Estimate;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\User;
use Illuminate\Http\Request;
use Log;

class EstimatesController extends Controller
{
    public function index(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "EstimatesController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->input('limit', 10);

        $estimates = Estimate::with([
            'items',
            'user',
            'estimateTemplate',
            'taxes',
            'creator',
            'assingUsersGroups',
            'assingUser',
        ])
            ->join('users', 'users.id', '=', 'estimates.user_id')
            ->applyFilters($request->only([
                'status',
                'customer_id',
                'customcode',
                'estimate_id',
                'estimate_number',
                'from_date',
                'to_date',
                'search',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($request->header('company'))
            ->select('estimates.*', 'users.name', 'users.customcode')
            ->latest()
            ->paginateData($limit);

        $siteData = [
            'estimates' => $estimates,
            'estimateTotalCount' => Estimate::count(),
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => $siteData, "message" => "__invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "__invoke");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Estimates", "List", "/admin/estimates", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($siteData);
    }

    public function store(EstimatesRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "EstimatesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $estimate = Estimate::createEstimate($request);

        if ($request->has('estimateSend')) {
            $estimate->send($request->title, $request->body);
        }

        GenerateEstimatePdfJob::dispatch($estimate);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'estimate' => $estimate,
        ], "message" => "store estimate"]];
        LogsDev::finishLog($log, $res, $time, 'D', "store estimate");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Estimates", "Create", "admin/estimates/create", $estimate->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Estimate: ".$estimate->estimate_number);

        return response()->json([
            'estimate' => $estimate,
        ]);
    }

    public function show(Request $request, Estimate $estimate)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['Estimate' => $estimate]);
        $log = LogsDev::initLog($request, "", "D", "EstimatesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $estimate->load([
            'items',
            'items.taxes',
            'user',
            'estimateTemplate',
            'creator',
            'taxes',
            'taxes.taxType',
            'fields.customField',
            'assingUsersGroups',
            'assingUser',
        ]);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'estimate' => $estimate,
            'nextEstimateNumber' => $estimate->getEstimateNumAttribute(),
            'estimatePrefix' => $estimate->getEstimatePrefixAttribute(),
        ], "message" => "show estimate"]];
        LogsDev::finishLog($log, $res, $time, 'D', "show estimate");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Estimates", "View", "admin/estimates/id/view", $estimate->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Estimate: ".$estimate->estimate_number);

        return response()->json([
            'estimate' => $estimate,
            'nextEstimateNumber' => $estimate->getEstimateNumAttribute(),
            'estimatePrefix' => $estimate->getEstimatePrefixAttribute(),
        ]);
    }

    public function update(EstimatesRequest $request, Estimate $estimate)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['Estimate' => $estimate]);
        $log = LogsDev::initLog($request2, "", "D", "EstimatesController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $estimate = $estimate->updateEstimate($request);

        GenerateEstimatePdfJob::dispatch($estimate, true);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'estimate' => $estimate,
        ], "message" => "update estimate"]];
        LogsDev::finishLog($log, $res, $time, 'D', "update estimate");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Estimates", "Update", "admin/estimates/id/edit", $estimate->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Estimate: ".$estimate->estimate_number);

        return response()->json([
            'estimate' => $estimate,
        ]);
    }

    public function delete(DeleteEstimatesRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "EstimatesController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        foreach ($request->ids as $id) {
            $estimate = Estimate::find($id);
            if ($estimate) {
                $estimate->delete();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe el registro'
                ]);
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "delete estimate"]];
        LogsDev::finishLog($log, $res, $time, 'D', "delete estimate");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    public function getListUsers()
    {

        $users = User::where('role', '!=', 'customer')->select('id', 'first_name', 'last_name', 'name', 'email')->get();

        return response()->json([
            'data' => $users,
            'message' => 'Users list',
        ]);
    }

    public function approval(Request $request, $inique_hash)
    {


        $estimate = Estimate::with('user:id,authentication')->where('unique_hash', $inique_hash)->first();
        $login = false;

        if(! $estimate) {
            return response()->json([
                'success' => false,
                'message' => 'Estimate not found',
            ]);
        }

        // validar si el usuario tiene authentication activado
        if($estimate->user->authentication == 1) {
            $login = true;
        }

        // validar si esta aprovado
        if($estimate->status == 'ACCEPTED') {
            return response()->json([
                'success' => true,
                'message' => 'The estimate with the number '.$estimate->estimate_number.' has already been approved',
                'login' => $login
            ]);
        }

        // validar que no este expirado
        if($estimate->expiry_date < Carbon::now()) {
            return response()->json([
                'success' => false,
                'message' => 'The estimate with the number '.$estimate->estimate_number.' has expired',
            ]);
        }

        $estimate->update([
            'status' => 'ACCEPTED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'The estimate with the number '.$estimate->estimate_number.' has been approved',
            'login' => $login
        ]);
    }
}
