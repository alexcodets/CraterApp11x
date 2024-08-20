<?php

namespace Crater\Services\Bandwidth\Traits;

use Crater\Services\Bandwidth\DataTransferObjects\AccountDTO;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait ApiTrait
{
    /**
     * Add BasicAuth, header and anything else that is need for a request.
     */
    private function buildRequest(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withBasicAuth($this->userName, $this->password)
            ->withHeaders([
                'Content-Type' => 'text/xml; charset=UTF8',
            ]);
    }

    /**
     * Send Request type get
     *
     * @param string $action
     * @param array $params
     * @return array
     * @throws \Exception
     */
    private function getRequest(string $action, array $params = []): array
    {
        $request = $this->buildRequest()->get($this->url.$action, $params);

        //Log::debug($request->effectiveUri());
        return $this->validateRequest($request);
    }

    /**
     * Check If the response was a success and respond according.
     *
     * @param \Illuminate\Http\Client\Response $request
     * @return array
     * @throws \Exception
     */
    private function validateRequest(\Illuminate\Http\Client\Response $request): array
    {
        /*
        //Log::debug("Json");
        //Log::debug($request->json());
        //Log::debug("Body");
        //Log::debug($request->body());
        //Log::debug("Client");
        //Log::debug($request->clientError());
        //Log::debug("Headers");
        //Log::debug($request->headers());
        //Log::debug("Status");
        //Log::debug($request->status());
         */
        if ($request->successful()) {
            //Log::debug("success");
            return $this->xmlToArray($request->body());
        }

        if ($request->status() == '401') {
            throw new Exception("Unauthorized, The credentials are incorrect", $request->status());
        }

        if ($request->status() == '403') {
            throw new Exception("Forbidden, the account id is incorrect", $request->status());
        }

        if ($request->status() == '404') {
            throw new Exception("Not Found, the route is incorrect", $request->status());
        }

        $error = $request->body() ? $request->body() : null;

        if ($request->clientError()) {
            throw new Exception($error ?? 'Client side error', $request->status());
        }

        if ($request->serverError()) {
            throw new Exception($error ?? 'Server side error', $request->status());
        }

        throw new Exception($error ?? 'Unnexpected error', $request->status() ?? 999);
    }

    public function postRequest()
    {
        throw new Exception('Method not implemented');
    }

    //Tools

    /**
     * Convert the body response to a array.
     *
     * @param string $xmlstring
     * @return array
     */
    public function xmlToArray(string $xmlstring): array
    {
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        return $array;
    }

    public function responseError(\Exception $e)
    {
        return ['success' => false, 'data' => ['error' => $e->getMessage(), 'code' => $e->getCode() == 0 ? 500 : $e->getCode()]];
    }

    public function responseSuccess($data)
    {
        return ['success' => true, 'data' => ['response' => $data]];
    }

    public function updateConfig(AccountDTO $dto)
    {

        $this->userName = $dto->userName;
        $this->password = $dto->password;
        $this->base_url = $dto->url;
        $this->url = "{$dto->url}/{$dto->accountId}/";
        $this->accountId = $dto->accountId;
    }
}
