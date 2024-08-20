<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerTicket;
use Crater\Models\Estimate;
use Crater\Models\Expense;
use Crater\Models\ExpenseTemplate;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\User;
use Illuminate\Http\Request;

class NextNumberController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // Inicializar log si es necesario
        // $time = microtime(true);
        // $log = LogsDev::initLog($request, "", "D", "NextNumberController", "__invoke");

        // Validar la clave y obtener el prefijo
        $key = $request->key;
        $company = $request->header('company');
        $val = $key.'_prefix';
        $prefix = CompanySetting::getSetting($val, $company) ?? 'CUST'; // Valor predeterminado para 'customer'

        // Obtener el siguiente número basado en la clave
        switch ($key) {
            case 'invoice':
            case 'core_pos': // Combinar casos con la misma lógica
                return response()->json([
                    'nextNumber' => Invoice::getNextInvoiceNumber($prefix),
                    'prefix' => $prefix,
                ]);

            case 'customer':
                return response()->json([
                    'nextNumber' => User::getNextCustomerNumber($prefix),
                    'prefix' => $prefix,
                ]);

            case 'estimate':
                return response()->json([
                    'nextNumber' => Estimate::getNextEstimateNumber($prefix),
                    'prefix' => $prefix,
                ]);

            case 'payment':
                return response()->json([
                    'nextNumber' => Payment::getNextPaymentNumber($prefix),
                    'prefix' => $prefix,
                ]);

            case 'expense':
                return response()->json([
                    'nextNumber' => Expense::getNextExpenseNumber($prefix),
                    'prefix' => $prefix,
                ]);

            case 'expense_template':
                return response()->json([
                    'nextNumber' => ExpenseTemplate::getNextExpenseTemplateNumber($prefix),
                    'prefix' => $prefix,
                ]);

            case 'TTW':
                return response()->json([
                    'nextNumber' => CustomerTicket::getNextTicketNumber($prefix),
                    'prefix' => $prefix,
                ]);

            default:
                return response()->json([
                    'nextNumber' => null,
                    'prefix' => null,
                ]);
        }

        // Finalizar log si es necesario
        // $res = ["success" => true, "response" => ["datamesage" => [
        //     'nextNumber' => $nextNumber,
        //     'prefix' => $prefix,
        // ], "message" => "NextNumberController invoke"]];
        // LogsDev::finishLog($log, $res, $time, 'D', "NextNumberController invoke");
    }
}
