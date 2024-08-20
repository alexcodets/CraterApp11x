<?php

namespace Crater\Http\Controllers\V1\Expense;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\Expense;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;
use Response;

class ShowReceiptController extends Controller
{
    /**
     * Retrieve details of an expense receipt from storage.
     *
     * @param   \Crater\Models\Expense $expense
     * @return  \Illuminate\Http\JsonResponse
     */
    public function __invoke($id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense) {
            $filename = $expense->getFirstMedia('receipts');
            if ($filename) {
                $path = $filename->getPath();
            } else {
                return response()->json([
                    'error' => 'receipt_does_not_exist',
                ]);
            }
        }

        return Response::make(file_get_contents($path), 200, [
         'Content-Type' => 'application/pdf',
         'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }

    public function view(Request $request, Expense $expense)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ShowReceiptController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $imagePath = null;
        $files = [];

        if ($expense) {
            $media = $expense->getMedia('receipts');
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

        // Logs por modulo
        LogsModule::createLog("Expenses", "View", "admin/expenses/id/view", $expense->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense: ".$expense->name);

        return response()->json([
            'files' => $files
        ]);
    }
}
