<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "SearchController", "__invoke");
        //////////////////

        $customers = User::where('role', 'customer')
            ->applyFilters($request->only(['search']))
            ->latest()
            ->paginate(10);

        if (Auth::user()->role == 'super admin') {
            $users = User::where('role', 'admin')
                ->applyFilters($request->only(['search']))
                ->latest()
                ->paginate(10);
        }

        $response = [
            'customers' => $customers,
            'users' => $users ?? [],
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => $response, "message" => "Invoke Search"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoke Search");

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        return response()->json($response);
    }
}
