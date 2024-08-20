<?php

namespace Crater\Services\Payment\Authorize;

use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\controller\base\IApiOperation;
use net\authorize\util\HttpClient;
use net\authorize\util\Mapper;
use ReflectionClass;

class BaseApiService implements IApiOperation
{
    private AnetApiRequestType $apiRequest;

    protected ?AnetApiResponseType $apiResponse = null;

    private string $apiResponseType;

    public AuthorizeApiClient $httpClient;

    /**
     * @throws InvalidArgumentException if invalid request
     */
    public function __construct(\DesolatorMagno\AuthorizePhp\Api\Contract\V1\AnetApiRequestType $request, string $responseType)
    {
        if (null == $responseType || '' == $responseType) {
            throw new InvalidArgumentException("responseType cannot be null or empty");
        }

        if (null != $this->apiResponse) {
            throw new InvalidArgumentException("response has to be null");
        }

        $this->apiRequest = $request;
        $this->validate();

        $this->apiResponseType = $responseType;
        $this->httpClient = new AuthorizeApiClient();
        //$this->httpClient = new HttpClient;

        /*Log::withContext([
            'request-id' => $requestId
        ]);*/
    }

    public function getApiResponse(): ?AnetApiResponseType
    {
        return $this->apiResponse;
    }

    public function executeWithApiResponse($endPoint = ANetEnvironment::CUSTOM): ?AnetApiResponseType
    {
        $this->execute($endPoint);

        return $this->apiResponse;
    }

    public function execute($endPoint = ANetEnvironment::CUSTOM)
    {
        $this->beforeExecute();

        $this->apiRequest->setClientId("sdk-php-".ANetEnvironment::VERSION);

        Log::channel('authorize')->info('Request Creation Begin');
        //Log::channel('authorize')->debug($this->apiRequest->jsonSerialize());

        $mapper = Mapper::Instance();
        $requestRoot = $mapper->getXmlName((new ReflectionClass($this->apiRequest))->getName());

        $requestArray = [$requestRoot => $this->apiRequest];

        Log::channel('authorize')->info('Request  Creation End');

        $this->httpClient->setPostUrl($endPoint);

        //$jsonResponse = $this->httpClient->_sendRequest(json_encode($requestArray));
        $requestData = json_encode($requestArray);
        $jsonResponse = $this->httpClient->sendRequest($requestData);
        Log::channel('authorize')->info('Request Data');
        Log::channel('authorize')->info($jsonResponse);
        Log::channel('authorize')->info($requestData);

        if (is_null($jsonResponse)) {
            Log::channel('authorize')->error('Error getting response from API');
            $this->apiResponse = null;
            $this->afterExecute();

            return;
        }

        $response = json_decode($jsonResponse, true);
        Log::channel('authorize')->info(gettype($response));
        //Log::channel('authorize')->info($response);

        $this->apiResponse = new $this->apiResponseType();
        $this->apiResponse->set($response);
        Log::channel('authorize')->info($this->apiResponse->jsonSerialize());
        Log::channel('authorize')->info(serialize($this->apiResponse));


        $this->afterExecute();
    }

    public function executeOld($endPoint)
    {
        $this->beforeExecute();

        $this->apiRequest->setClientId("sdk-php-".ANetEnvironment::VERSION);

        Log::channel('authorize')->info('Request Creation Begin');
        Log::channel('authorize')->debug($this->apiRequest->jsonSerialize());

        $mapper = Mapper::Instance();
        $requestRoot = $mapper->getXmlName((new ReflectionClass($this->apiRequest))->getName());

        $requestArray = [$requestRoot => $this->apiRequest];

        Log::channel('authorize')->info('Request  Creation End');

        $this->httpClient->setPostUrl($endPoint);

        $jsonResponse = $this->httpClient->sendRequest(json_encode($requestArray));
        if (is_null($jsonResponse)) {
            Log::channel('authorize')->error('Error getting response from API');
            $this->apiResponse = null;
            $this->afterExecute();

            return;
        }

        if ($jsonResponse != null) {
            //decoding json and removing bom
            $possibleBOM = substr($jsonResponse, 0, 3);
            $utfBOM = pack("CCC", 0xef, 0xbb, 0xbf);

            if (0 === strncmp($possibleBOM, $utfBOM, 3)) {
                $response = json_decode(substr($jsonResponse, 3), true);
            } else {
                $response = json_decode($jsonResponse, true);
            }
            $this->apiResponse = new $this->apiResponseType();
            $this->apiResponse->set($response);
        }

        $this->afterExecute();

    }

    private function validate()
    {
        $merchantAuthentication = $this->apiRequest->getMerchantAuthentication();
        if (null == $merchantAuthentication) {
            throw new InvalidArgumentException("MerchantAuthentication cannot be null");
        }

        $this->validateRequest();
    }

    protected function beforeExecute()
    {
    }

    protected function afterExecute()
    {
    }

    protected function validateRequest()
    {
    } //need to make this abstract

    protected function now(): string
    {
        return now()->format(DATE_RFC2822);
    }
}
