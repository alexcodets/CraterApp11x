<?php

namespace Crater\Http\Controllers\V1\Modules;

use Crater\Avalara\DataObject\AvalaraCompanyModelDO;
use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraConfig;
use Crater\Models\Modules;
use Illuminate\Http\JsonResponse;
use TypeError;

class AvalaraModuleController extends Controller
{
    public function index(): JsonResponse
    {
        if (Modules::where('name', '=', 'avalara')->doesntExist()) {
            return response()->json($this->error(__('response.modules.avalara.errors.not_found'), 404));
        }

        $config = AvalaraConfig::active()->first();

        if ($config == null) {
            return response()->json($this->error(__('response.modules.avalara.errors.no_config'), 404));
        }

        try {
            $company = new AvalaraCompanyModelDO($config);
        } catch (TypeError $th) {
            return response()->json($this->error(__('avalara.error.company_model.required.base')));
        } catch (\Throwable $th) {
            return response()->json($this->error($th->getMessage()));
        }

        $validar = $company->checkItems($config);
        if (! $validar['success']) {
            return response()->json($this->error($validar['msg']));
        }

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => __('response.modules.avalara.success'),
            'data' => [],
        ]);

    }

    private function error(string $message, int $code = 422): array
    {
        return [
            'success' => false,
            'message' => __($message),
            'data' => [],
            'code' => $code
        ];

    }
}
