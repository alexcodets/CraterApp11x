<?php

namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAdmissionListController extends Controller
{
    public function index(Request $request)
    {
        // Utilizar el método when para simplificar la consulta condicional
        $permissions = DB::table('user_permisions')
            ->when($request->module == 'COMPLETE_ALL', function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id);
            }, function ($query) use ($request) {
                return $query->where('user_id', $request->user()->id)->where('module', $request->module)->limit(1);
            })
            ->get();

        // Verificar si el usuario es super administrador
        $isSuperAdmin = $request->user()->role == 'super admin' && $request->user()->role2 == null;

        // Construir la respuesta JSON directamente con la colección
        return response()->json([
            'message' => 'Admission List Completed Successfully',
            'module' => $request->module,
            'exist' => $permissions->isNotEmpty(),
            'permissions' => $permissions,
            'super_admin' => $isSuperAdmin,
        ]);
    }
}
