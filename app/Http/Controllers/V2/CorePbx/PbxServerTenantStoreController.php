<?php

namespace Crater\Http\Controllers\V2\CorePbx;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\TenantRequest;
use Crater\Http\Resources\PbxServerTenantResource;
use Crater\Models\PbxServers;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxTenant;
use Crater\Pbxware\PbxWareApi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PbxServerTenantStoreController extends Controller
{
    public function __invoke(TenantRequest $request, PbxServers $pbxServer)
    {
        //\Log::debug('Inicio del método __invoke.');

        $api = new PbxWareApi($pbxServer);
        // \Log::debug('Instancia de PbxWareApi creada.');

        try {
            $tenants = collect($api->tenantList()['data'] ?? []);
        } catch (Throwable $th) {
            // \Log::debug('Error al obtener tenants: ', ['error' => $th->getMessage()]);
            return response()->json(['error' => $th->getMessage(), 'code' => $th->getCode()], 500);
        }

        $errors = $this->getErrors($tenants, $request);
        //\Log::debug('Errores de validación: ', ['errors' => $errors]);

        if ($errors) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'error' => $errors,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], 422);
        }

        [$response, $tenantId] = $this->getTenantId($api, $request);
        // \Log::debug('Respuesta y tenantId obtenidos: ', ['response' => $response, 'tenantId' => $tenantId]);

        if (! $response['success'] && is_null($tenantId)) {
            return response()->json($response, $response['status'] ?? 501);
        }

        if (is_null($tenantId)) {
            //   \Log::debug('TenantId es nulo.');
            return response()->json([
                'success' => false,
                'message' => 'Api error: Tenant not found in api',
                'code' => Response::HTTP_BAD_REQUEST,
            ], Response::HTTP_BAD_REQUEST);
        }

        $tenant = new PbxServerTenantResource(PbxServerTenant::create([
            'name' => $request->tenant_name,
            'tenant_code' => $request->tenant_code,
            'creator_id' => auth()->id(),
            'pbx_server_id' => $pbxServer->id,
            'status' => PbxTenant::STATUS_INCOMPLETE,
            'company_id' => $pbxServer->company_id,
            'tenant_id' => $tenantId,
        ]));
        //\Log::debug('Nuevo tenant creado: ', ['tenant' => $tenant]);

        Artisan::queue('tenant:pending-activation-notified', [
            '--id' => $tenant->id,
        ]);
        //\Log::debug('Comando tenant:pending-activation-notified encolado.');

        //\Log::debug('Fin del método __invoke.');
        return response()->json([
            'success' => true,
            'message' => 'Tenant Almacenado',
            'status' => 200,
            'data' => $tenant,
        ]);

    }

    private function getErrors(Collection $tenants, TenantRequest $request): array
    {
        // \Log::debug('Inicio del método getErrors.');

        $errors = [];

        if ($tenants->contains('name', $request->tenant_name)) {
            $errors['name'][] = "The name: {$request->tenant_name}, is already in use.";
            //   \Log::debug('Error encontrado en el nombre del tenant.', ['tenant_name' => $request->tenant_name]);
        }

        if ($tenants->contains('tenantcode', $request->tenant_code)) {
            $errors['tenant_code'][] = "The tenant code: {$request->tenant_code}, is already in use.";
            // \Log::debug('Error encontrado en el código del tenant.', ['tenant_code' => $request->tenant_code]);
        }

        //\Log::debug('Errores encontrados en getErrors.', ['errors' => $errors]);

        return $errors;
    }

    private function getTenantId(PbxWareApi $api, TenantRequest $request): array
    {
        $response = $api->tenantStore(array_filter($request->validated()));
        $code = $request->tenant_code;
        //\Log::debug('Código del tenant:', ['tenant_code' => $code]);

        $tenantId = null;

        $tenants = collect($api->tenantList()['data']);
        $tenants->each(function (array $item, int $key) use ($code, &$tenantId) {
            //   \Log::debug('Revisando tenant:', ['item' => $item, 'key' => $key]);
            if ($item['tenantcode'] == $code) {
                $tenantId = $key;

                // \Log::debug('Tenant encontrado, asignando ID:', ['tenantId' => $tenantId]);
                return false;
            }

            return $item;
        });

        if (is_null($tenantId)) {
            // \Log::debug('Tenant ID es nulo, no se encontró el tenant con el código proporcionado.');
            $response['code'] = $response['status'];

            return [$response, null];
        }

        //\Log::debug('Proceso completado, retornando respuesta y tenant ID.', ['response' => $response, 'tenantId' => $tenantId]);
        return [$response, $tenantId];
    }
}
