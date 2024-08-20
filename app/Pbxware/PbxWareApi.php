<?php

namespace Crater\Pbxware;

// models
use Crater\Avalara\Traits\ApiPbxResponseTrait;
use Crater\Models\PbxServers;
use Crater\Traits\PbxApiTrait;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PbxWareApi
{
    use ApiPbxResponseTrait;
    use PbxApiTrait;

    public const VERSION_API_TENANT_STATUS_CHANGE = '6.6.1.0';

    public $host;

    public $apiKey;

    public $port;

    private ?int $id = null;

    public function __construct(?PbxServers $pbxServer = null)
    {
        //Se debe dejar el null para permitir retrocompatibilidad con otras rutas.
        if ($pbxServer != null) {
            $this->host = $pbxServer->hostname;
            $this->apiKey = $pbxServer->api_key;
            $this->port = $pbxServer->ssl_port;
            $this->id = $pbxServer->id;
        }
    }

    public function getCDR(string $tenant, string $start, string $starttime, string $end, string $endtime, string $limit, string $status, int $page, array $array = []): array
    {
        $fields = [
            'server' => $tenant,
            'start' => $start,
            'starttime' => $starttime,
            'end' => $end,
            'endtime' => $endtime,
            'limit' => $limit,
            'status' => $status,
            'page' => $page,
            //'timezone' => 'UTC/GMT',
            //'timezone' => 'America/New_York',
            //'timezone' => 'Africa/Djibouti',
        ];
        $fields = array_merge($fields, $array);

        try {
            $response = $this->getRequest('pbxware.cdr.download', $fields);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de CDR', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    /**
     *
     * @param string $action
     * @param array $queryParams
     * @return array
     */
    public function getRequest(string $action, array $queryParams = []): array
    {
        $query = $queryParams;
        $query['apikey'] = $this->apiKey;
        $query['action'] = $action;

        try {
            $response = Http::timeout(3)->retry(2, 100)->get($this->host, $query);

            /* //Log::debug('response---');*/
            //            $response->status();
            //            Log::debug('Get Data', [
            //                'status'  => $response->status(),
            //                'data'    => $response->json(),
            //                'cookies' => $response->cookies(),
            //                'body'    => $response->body(),
            //                'headers' => $response->headers(),
            //                'query'   => $query,
            //
            //            ]);
            //Log::debug($response);
            return $this->validateRequest($response);

        } catch (Throwable $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    /**.
     *
     * @param Response $request
     * @return array
     */
    public function validateRequest(Response $request): array
    {
        //  //Log::debug($request->effectiveUri());
        $response = $request->json();
        /*Log::debug('Json - ');
        Log::debug($request->json());
        Log::debug('Body - ');
        Log::debug($request->body());
        Log::debug('Header - ');
        Log::debug($request->headers());
        Log::debug('Status - ' . $request->status());
        Log::debug('Response original: ', $request->json()); */
        if (isset($response['error'])) {
            //Log::debug($response['error']);
            return ['error' => $response['error'], 'code' => 501];
        }

        $body = $request->body();
        if ($response == null && str_contains($body, 'error')) {
            return ['error' => 'Server Not Found', 'code' => 400];
        }

        if ($response == null && str_contains($body, 'warning')) {
            return ['success' => 'The query was made but a warning was returned by the PBX systems', 'warning' => $body];
        }

        if ($response == null && (str_contains($body, 'Notice'))) {
            return ['success' => 'Se realizo la consulta pero se retorno un warning por parte del sistema PBX', 'warning' => $body];
        }

        if ($request->successful()) {
            return $response ?? [];
        }

        $error = $request->body() ? $request->body() : null;

        if ($request->clientError()) {
            return ['error' => $error ?? 'avalara.error.client', 'code' => $request->status()];
        }

        if ($request->serverError()) {
            return ['error' => $error ?? 'avalara.error.server', 'code' => $request->status()];
        }

        return ['error' => 'avalara.error.general', 'code' => 500];
    }

    public function getTrunks(array $extraData = []): array
    {
        try {
            $response = Cache::remember('getTrunks_'.$this->id, $seconds = 120, function () use ($extraData) {
                return $this->getRequest('pbxware.trunk.list', $extraData);
            });

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de Trunks', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    /**
     * Get DIDs from PBXware server
     *
     */
    public function didList(string $tenant): array
    {
        try {
            $response = $this->getRequest('pbxware.did.list', ['server' => $tenant]);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de DID por tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    public function didStore(string $tenant, array $values): array
    {
        try {

            $response = $this->getRequest('pbxware.did.add', array_merge(['server' => $tenant], $values));

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu Creacion de Tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }

    }

    public function didUpdate(string $tenant, array $values): array
    {
        try {

            $response = $this->getRequest('pbxware.did.edit', array_merge(['server' => $tenant], $values));

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu Edicion de Did', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }

    }

    /**
     * Get Extensions from PBXware server
     *
     */
    public function extensionConfiguration(string $tenant, int $id): array
    {
        try {
            $response = $this->getRequest('pbxware.ext.configuration', ['server' => $tenant, 'id' => $id]);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('La configuracion de la Ext del tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    /**
     * Get Extensions from PBXware server
     *
     * @param mixed $tenant
     * @return array
     */
    public function extensionsList(string $tenant): array
    {
        try {
            $response = $this->getRequest('pbxware.ext.list', ['server' => $tenant]);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de Ext por tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    public function extensionStore(string $tenant, array $values): array
    {
        try {

            $response = $this->getRequest('pbxware.ext.add', array_merge(['server' => $tenant], $values));

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu Edicion de Tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }

    }

    public function extensionUpdate(string $tenant, array $values): array
    {
        try {

            $response = $this->getRequest('pbxware.ext.edit', array_merge(['server' => $tenant], $values));

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu Edicion de Extension', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }

    }

    public function tenantList(): array
    {
        try {
            $response = $this->getRequest('pbxware.tenant.list');

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de Tenants', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    public function tenantStore(int $tenant, array $values): array
    {
        try {
            $response = $this->getRequest('pbxware.tenant.add', array_merge(['server' => $tenant], $values));
            //Log::debug('Response Add Tenant:', $response);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tenant Almacenado con éxito', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    public function tenantUpdate(int $tenant, array $values): array
    {
        try {

            $response = $this->getRequest('pbxware.tenant.edit', array_merge(['id' => $tenant], $values));

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu Edition de Tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    public function tenantConfiguration(int $tenant = 1): array
    {
        try {
            $server = $this->getRequest('pbxware.tenant.configuration', ['id' => $tenant]);

            if (isset($server['error']) || $server['server_name'] == null) {
                return $this->errorResponse($server);
            }

            return $this->successResponse('pbxserver.success.tenant_configuration', $server);

        } catch (Exception $e) {
            return $this->errorResponse($server ?? [], $e->getMessage());
        }
    }

    /**
     *
     * Only usable when tenant mode disabled
     */
    public function getServerConfiguration(): array
    {
        try {
            $server = $this->getRequest('pbxware.tenant.configuration', ['id' => 1]);

            if (isset($server['error']) || $server['server_name'] == null) {
                return $this->errorResponse($server);
            }

            return $this->successResponse('pbxserver.success.tenant_configuration', $server);

        } catch (Exception $e) {
            return $this->errorResponse($server ?? [], $e->getMessage());
        }
    }

    /**
     * Check Api Connection
     *
     */
    public function checkConnection($id = 1): array
    {
        /*//Log::debug('id----');
        //Log::debug($id);*/
        try {
            $server = $this->getRequest('pbxware.tenant.configuration', ['id' => $id]);
            //Log::debug($server);

            if (isset($server['error']) || $server['server_name'] == null) {
                //Log::debug('error server');
                return $this->errorResponse($server);
            }

            return $this->successResponse('pbxserver.success.check');

        } catch (Exception $e) {
            /*//Log::debug('error exception');
            //Log::debug($e->getMessage());*/

            return $this->errorResponse($server ?? [], $e->getMessage());
        }
    }

    public function getApps(int $tenant): array
    {
        try {
            $response = $this->getRequest('pbxware.apps.list', ['server' => $tenant]);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('pbxserver.success.apps', $response);

        } catch (Exception $e) {
            return $this->errorResponse($response ?? [], $e->getMessage());
        }
    }

    public function getLicenseInfo(): array
    {
        try {
            $response = $this->getRequest('pbxware.license.info');

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('pbxserver.success.license.info', $response);

        } catch (Exception $e) {
            return $this->errorResponse($response ?? [], $e->getMessage());
        }

    }

    public function getDidGroups(): array
    {
        try {
            $response = $this->getRequest('pbxware.didgroup.list');

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de DID Group por tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }

    }

    public function getRingGroups(int $tenant): array
    {
        try {
            $response = $this->getRequest('pbxware.ring_group.list', ['server' => $tenant]);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de Ring Group por tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }

    }

    public function getUserAgentDevices(int $tenant): array
    {
        try {
            $response = $this->getRequest('pbxware.uads.list', ['server' => $tenant]);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de User Agent Devices', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }

    }

    public function getUserAgentDevicesEnable(int $tenant): array
    {
        $response = $this->getUserAgentDevices($tenant);

        if ($response['success']) {
            $response['data'] = array_filter($response['data'], fn ($item) => $item['enabled'] !== false);
        }

        return $response;
    }

    public function getIvr(int $tenant): array
    {
        try {
            $response = $this->getRequest('pbxware.ivr.list', ['server' => $tenant]);

            if (isset($response['error'])) {
                return $this->errorResponse($response);
            }

            return $this->successResponse('Tu listado de Ivr Group por tenant', $response);

        } catch (Exception $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }

    }

    public function getTenantPackage(): array
    {
        try {
            $server = $this->getRequest('pbxware.package.list');

            if (isset($server['error'])) {
                return $this->errorResponse($server);
            }

            return $this->successResponse('pbxserver.success.tenant_package', $server);

        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return $this->errorResponse($server ?? [], $e->getMessage());
        }
    }

    public function getRoutes(): array
    {
        try {
            $server = $this->getRequest('pbxware.route.list');

            if (isset($server['error'])) {
                return $this->errorResponse($server);
            }

            return $this->successResponse('pbxserver.success.routes', $server);

        } catch (Exception $e) {
            Log::debug($e->getMessage());

            return $this->errorResponse($server ?? [], $e->getMessage());
        }
    }
}
