<?php

namespace Crater\Http\Controllers\V1\Expense;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\Expense;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class DownloadReceiptController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Expense $expense
     * @param   string $hash
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Expense $expense)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);

        $request->merge(['Expense' => $expense]);
        $log = LogsDev::initLog($request, "", "D", "DownloadReceiptController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        if ($expense) {
            \Log::debug("antes del get first");
            $media = $expense->getFirstMedia('receipts');
            if ($media) {
                // Logs por modulo
                LogsModule::createLog("Expenses", "Download", "admin/expenses/id/edit", $expense->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense: ".$expense->name);

                $imagePath = $media->getPath();
                $response = \Response::download($imagePath, $media->file_name);
                //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
                $res = ["success" => true, "response" => ["datamesage" => $response, "message" => "receipt found"]];
                LogsDev::finishLog($log, $res, $time, 'D', "receipt found");
                /////////////////////////////////////////

                if (ob_get_contents()) {
                    ob_end_clean();
                }

                return $response;
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'error' => 'receipt_not_found',
        ], "message" => "receipt_not_found"]];
        LogsDev::finishLog($log, $res, $time, 'D', "receipt_not_found");
        /////////////////////////////////////////

        return response()->json([
            'error' => 'receipt_not_found',
        ]);
    }
}
