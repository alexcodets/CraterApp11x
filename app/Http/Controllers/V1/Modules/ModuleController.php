<?php

namespace Crater\Http\Controllers\V1\Modules;

use Crater\Http\Controllers\Controller;
use Crater\Models\Modules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(string $name): JsonResponse
    {
        $module = Modules::where('name', $name)->first();

        if (! $module) {
            return abort(404, __('response.modules.general.errors.not_found', ['name' => $name]));
        }

        return response()->json([
            'success' => true,
            'status' => $module->status == 'A',
            'message' => __('response.modules.general.success.found', ['name' => $name]),
            'data' => $module,
            'code' => 200,
        ]);
    }

    public function getModules(Request $request): JsonResponse
    {
        $modules = Modules::whereIn('name', $request->all())->get();

        if ($modules->isEmpty()) {
            return response()->json(
                [
                    'success' => false,
                    'modules' => [],
                    'code' => 404,
                ],
                404// Establecer el código de estado HTTP directamente
            );
        }

        return response()->json(
            [
                'success' => true,
                'modules' => $modules,
                'code' => 200,
            ],
            200// Establecer el código de estado HTTP directamente
        );
    }
}
