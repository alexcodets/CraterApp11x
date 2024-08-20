<?php

namespace Crater\Http\Controllers\V1\Expense;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\Expense;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class UploadReceiptController extends Controller
{
    /**
     * Upload the expense receipts to storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Expense $expense)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UploadReceiptController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $data = json_decode($request->attachment_receipt);

        if ($data) {
            if ($request->type === 'edit') {
                $expense->clearMediaCollection('receipts');
            }

            $expense->addMediaFromBase64($data->data)
                ->usingFileName($data->name)
                ->toMediaCollection('receipts', 'local');
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Expense receipts uploaded successfully',
        ], "message" => "Expense receipts uploaded successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Expense receipts uploaded successfully");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Expenses", "Upload", "admin/expenses/id/edit", $expense->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense: ".$expense->name);

        return response()->json([
            'success' => 'Expense receipts uploaded successfully',
        ]);
    }
}
