<?php

namespace Crater\Avalara\Apis;

use Crater\Avalara\DataObject\AvalaraApiDO;
use Crater\Avalara\DMO\AvalaraApiDMO;
use Crater\Avalara\Traits\ApiResponseTrait;
use Crater\Models\AvalaraConfig;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AvalaraApi
{
    use ApiResponseTrait;

    public $dmo;

    private AvalaraApiDO $data;

    public function __construct(AvalaraConfig $config = null)
    {
        $this->data = (new AvalaraApiDMO())->getData($config ?? null);
    }

    public function changeConfig(AvalaraConfig $config): void
    {
        $this->data = (new AvalaraApiDMO())->getData($config);
    }

    public function getServiceInfo(): array
    {
        try {
            return $this->getRequest('serviceinfo');
        } catch (Exception $e) {
            return ['error' => __($e->getMessage()), 'code' => $e->getCode() == 0 ? 500 : $e->getCode()];
        }
    }

    /**
     * @throws Exception
     */
    private function getRequest(string $action, array $params = []): array
    {
        Log::debug('Entro creo a get request');

        try {
            $request = $this->buildRequest()->get($this->data->url.$this->data->actions[$action], $params);
        } catch (ConnectException $ex) {
            Log::debug($ex->getMessage());
            if (str_contains($ex->getMessage(), 'cURL error 28')) {
                throw new Exception(__('exception.curl.28'), 500);
            }

            throw $ex;
        } catch (Exception $th) {
            Log::debug($th->getMessage());

            throw $th;
        }

        //Log::debug($request->effectiveUri());
        return $this->validateRequest($request);
    }

    /**
     * Add BasicAuth, header and anything else that is need for a    request.
     */
    private function buildRequest(): PendingRequest
    {
        return Http::withBasicAuth($this->data->user_name, $this->data->password)
            ->withHeaders(array_filter([
                'client_id' => $this->data->client_id,
                'Content-Type' => 'application/json',
                'client_profile_id' => $this->data->profile_id,
            ]))->timeout(4)->retry(2, 100);
    }

    /**
     * @throws Exception
     */
    private function validateRequest(\Illuminate\Http\Client\Response $request): array
    {
        if ($request->successful()) {
            //Log::debug($request->json());
            $response = $request->json();
            if (is_null($response)) {
                throw new Exception('Null body', $request->status() ?? Response::HTTP_NOT_FOUND);
            }

            return $request->json();
        }

        $error = $request->body() ? $request->body() : [];
        $head = $request->header('error');
        Log::debug($request->json());

        if ($head) {
            throw new Exception($head, $request->status() ?? Response::HTTP_NOT_FOUND);
        }

        if ($request->status() == Response::HTTP_UNAUTHORIZED) {
            throw new Exception($error ?? 'Unauthorized, The credentials are incorrect', $request->status() ?? Response::HTTP_UNAUTHORIZED);
        }

        if ($request->status() == Response::HTTP_FORBIDDEN) {
            throw new Exception($error ?? 'Forbidden', $request->status() ?? Response::HTTP_FORBIDDEN);
        }

        if ($request->status() == Response::HTTP_NOT_FOUND) {
            throw new Exception($error ?? 'Not Found, the route is incorrect', $request->status() ?? Response::HTTP_NOT_FOUND);
        }

        if ($request->clientError()) {
            throw new Exception($error ?? __('avalara.error.client'), $request->status() ?? Response::HTTP_BAD_REQUEST);
        }

        if ($request->serverError()) {
            throw new Exception($error ?? __('avalara.error.server'), $request->status() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (isset($error['err'])) {
            Log::error($error['err'][0]);

            throw new Exception($error['err'][0]['msg'], $error['err'][0]['code'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (isset($error['error'])) {
            //Log::debug('Error Check Inside Validate Request');
            Log::error($error['error']);

            throw new Exception($error["error"]['error'], $error["error"]['code'] ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return ['error' => 'avalara.error.general', 'code' => 500];
    }

    /**
     * @return array
     */
    public function isHealthy(): array
    {
        $response = $this->getServerHealth();
        if (isset($response['error'])) {
            Log::debug($response['error']);

            return $this->errorResponse($response['error'] ?? 'avalara.error.no_expected', $response, $response['code'] ?? 500);
        }
        //Log::debug('error Client block');
        if ($response['Status'] == 'Healthy') {
            return $this->successResponse('avalara.success.server_up');
        }

        return $this->errorResponse('avalara.error.server_down');
    }

    private function getServerHealth(): array
    {
        try {
            return $this->getRequest('health');
        } catch (Exception $e) {
            return ['error' => $e->getMessage(), 'code' => $e->getCode() == 0 ? 500 : $e->getCode()];
        }
    }

    public function getTaxes(array $params): array
    {
        try {
            $response = $this->calcTaxes($params);
        } catch (Throwable $th) {
            return $this->errorResponse($th->getMessage(), [$th->getMessage()], $th->getCode() ?? 500);
        }

        if (isset($response['err'])) {
            Log::debug('First check Inside Get Taxes');

            return $this->errorResponse($response["err"][0]['msg'], $response["err"][0], $response["err"][0]['code'] ?? 500);
        }

        if (isset($response['error'])) {
            Log::debug('Second check Inside Get Taxes');
            //Log::debug($response);

            return $this->errorResponse('avalara.error.no_expected', $response, $response['code'] ?? 500);
        }

        if (isset($response['inv'][0]['itms'][0]['err'])) {
            Log::debug('Third check Inside Get Taxes');

            return $this->errorResponse($response['inv'][0]['itms'][0]['err'][0]['msg'], $response['inv'][0]['itms'][0]['err'], $response['inv'][0]['itms'][0]['err'][0]['code'] ?? 500);
        }

        if (isset($response['inv'][0]['err'])) {
            Log::debug('forth check Inside Get Taxes');
            //Log::debug($response['inv'][0]['itms'][0]['err'][0]);

            return $this->errorResponse($response['inv'][0]['err'][0]['msg'], $response['inv'][0]['err'], $response['inv'][0]['err'][0]['code']);
        }

        return $this->successResponse('avalara.success.taxes', $response);
    }

    /**
     * @throws Exception
     */
    private function calcTaxes(array $params): array
    {
        return $this->postRequest('calcTaxes', $params);
    }

    /**
     * @throws Exception
     */
    private function postRequest(string $action, array $params = []): array
    {
        try {
            $request = $this->buildRequest()->post($this->data->url.$this->data->actions[$action], $params);

        } catch (ConnectException $ex) {
            Log::debug($ex->getMessage());
            if (str_contains($ex->getMessage(), 'cURL error 28')) {
                throw new Exception(__('exception.curl.28'), 500);
            }

            throw $ex;
        } catch (Exception $th) {
            Log::debug($th->getMessage());

            throw $th;
        }

        return $this->validateRequest($request);

    }

    public function unCommitDoc(string $code = null, array $opt = []): array
    {
        $response = $this->commit($code, false, $opt);
        if (isset($response['ok']) && $response['ok']) {
            return $this->successResponse('avalara.success.uncommit');
        }
        if (isset($response['error'])) {
            return $this->errorResponse('avalara.error.no_expected', $response, $response['code'] ?? 500);
        }

        return $this->errorResponse($response['err'][0]['msg'] ?? 'Error');
    }

    private function commit(string $doc, bool $commit = true, array $values = []): array
    {
        try {
            return $this->postRequest('commit', ['doc' => $doc, 'cmmt' => $commit]);
        } catch (Exception $e) {
            return ['error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    public function commitDoc(string $code = null, array $opt = []): array
    {
        $response = $this->commit($code, true, $opt);
        if (isset($response['ok']) && $response['ok']) {
            return $this->successResponse('avalara.success.commit');
        }
        if (isset($response['error'])) {
            return $this->errorResponse('avalara.error.no_expected', $response, $response['code'] ?? 500);
        }

        return $this->errorResponse($response['err'][0]['msg'] ?? 'Error', $response['err'], $response['err'][0]['code']);
    }

    public function getCustomization($profile_id, string $item_type = 'Configuration'): array
    {
        $response = $this->getProfile($profile_id, $item_type);
        if (isset($response['error'])) {
            return $this->errorResponse('avalara.error.no_expected', $response, $response['code'] ?? 500);
        }
        if ($response[0]['Error']) {
            return $this->errorResponse($response[0]['Error'], [], $response[0]['ResponseErrorCode'] ?? 400);
        }
        if (isset($response['err'])) {
            return $this->errorResponse('There was a unexpected error', $response['code'] ?? 500);
        }

        return $this->successResponse('Ok', $response[0]);

    }

    private function getProfile($profile_id = null, string $item_type = null): array
    {
        if ($this->data->client_id) {
            $queryParams['RequestedClientId'] = $this->data->client_id;
        }
        if ($profile_id) {
            $queryParams['RequestedProfileId'] = $profile_id;
        }
        if ($item_type) {
            $queryParams['ItemType'] = $item_type;
        }

        try {
            return $this->getRequest('profile', $queryParams ?? []);
        } catch (Exception $e) {
            return ['error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    public function locationPCode(array $code): array
    {
        try {
            $response = $this->postRequest('pcode', $code);

            if (isset($response['error'])) {
                return $this->errorResponse($response['error'], $response, $response['code'] ?? 500);
            }
            if ($response['MatchCount'] == 0) {
                return $this->errorResponse('avalara.error.pcode', $response, 404);
            }

            return $this->successResponse('avalara.success.pcode', $response['LocationData']);
        } catch (Exception $e) {
            return ['error' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    public function getTSPair(): array
    {
        try {
            $response = $this->getRequest('tsPair');

            //Log::debug($response);
            return $this->successResponse('avalara.success.tspair', $response);
        } catch (Throwable $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }

    public function getTaxTypes($type = '*'): array
    {
        try {
            $response = $this->getRequest('taxTypes', ['taxType' => $type]);

            //Log::debug($response);
            return $this->successResponse('avalara.success.taxtypes', $response);
        } catch (Throwable $th) {
            return ['error' => $th->getMessage(), 'code' => $th->getCode()];
        }
    }
}
