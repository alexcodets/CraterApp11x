<?php

namespace Crater\Http\Controllers\V1\Expense;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\Expense;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;
use Log;

class ShowDocsController extends Controller
{
    /**
     * Retrieve details of an expense receipt from storage.
     *
     * @param   \Crater\Models\Expense $expense
     * @return  \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Expense $expense)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ShowDocsController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $imagePath = null;
        $files = [];

        if ($expense) {
            $media = $expense->getMedia('docs');
            if ($media) {
                foreach ($media as $file) {
                    $filePath = $file->getPath();
                    $type = \File::mimeType($filePath);
                    if($type == "application/octet-stream") {
                        $type = "application/pdf";
                    }
                    $file->base64 = 'data:'.$type.';base64,'.base64_encode(file_get_contents($filePath));
                    array_push($files, $file);
                }

            } else {
                return response()->json([
                    'error' => 'docs_does_not_exist',
                ]);
            }
        }

        // $type = \File::mimeType($imagePath);
        //Log::debug(".....filex");
        //Log::debug($imagePath);
        //Log::debug(  $type );
        // $image = 'data:' . $type . ';base64,' . base64_encode(file_get_contents($imagePath));

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        /*  $res = ["success" => true, "response" => ["datamesage" => [
             'image' => $image,
             'type' => $type,
         ], "message" => "ShowReceipt  __invoke"]]; */
        //  LogsDev::finishLog($log, $res, $time, 'D', "ShowReceipt __invoke");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Expenses", "View", "admin/expenses/id/edit", $expense->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense: ".$expense->name);

        return response()->json([
            'files' => $files
        ]);
    }

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function download($expense_id, $doc_id)
    {
        $expense = Expense::find($expense_id);
        if($expense != null) {
            $media = $expense->getExpenseDocById('docs', $doc_id);
            if ($media) {
                $imagePath = $media->getPath();
                $response = \Response::download($imagePath, $media->file_name);

                if (ob_get_contents()) {
                    ob_end_clean();
                }

                return $response;
            }

            return response()->json([
                'error' => 'doc_not_found',
            ]);
        }
    }
}
