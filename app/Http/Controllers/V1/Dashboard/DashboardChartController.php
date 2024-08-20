<?php

namespace Crater\Http\Controllers\V1\Dashboard;

use Crater\Http\Controllers\Controller;
use Crater\Models\Expense;
use Illuminate\Http\Request;

class DashboardChartController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        try {
            // Código para guardar un log, la parte inicial siempre debe ir al principio del método
            $time = microtime(true);
            $log = LogsDev::initLog($request, "", "D", "DashboardChartController", "__invoke");
            /////////////////

            $expensesCategories = Expense::with('category')
                ->whereCompany($request->header('company'))
                ->expensesAttributes()
                ->get();

            $amounts = $expensesCategories->pluck('total_amount');
            $names = $expensesCategories->pluck('category.name');

            // Preparar respuesta
            $response = [
                'amounts' => $amounts,
                'categories' => $names,
            ];

            // Fin de registro de log, debe guardarse inmediatamente antes de un return
            $res = [
                "success" => true,
                "response" => ["datamesage" => $response],
                "message" => "Invoke Dashboard",
            ];
            LogsDev::finishLog($log, $res, $time, 'D', "Invoke Dashboard chart");
            // Fin de registro de log, debe guardarse inmediatamente antes de un return

            // Retornar respuesta JSON
            return response()->json($response);
        } catch (\Throwable $th) {
            \Log::debug('dashboard charts', ['error' => $th]);

            // Considerar retornar una respuesta JSON en caso de error también
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}
