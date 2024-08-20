<?php

namespace Crater\Services\Payment\Authorize;

use DesolatorMagno\AuthorizePhp\Api\Contract\V1\AnetApiRequestType;
use net\authorize\api\constants\ANetEnvironment;

class CreateTransactionService extends BaseApiService
{
    public function __construct(AnetApiRequestType $request)
    {
        $responseType = \Crater\Services\Payment\Authorize\TransactionResponse::class;
        parent::__construct($request, $responseType);
    }

    public function getApiResponse(): ?TransactionResponse
    {
        return $this->apiResponse;
    }

    public function executeWithApiResponse($endPoint = ANetEnvironment::CUSTOM): ?TransactionResponse
    {
        $this->execute($endPoint);

        return $this->apiResponse;
    }

    protected function validateRequest()
    {
        //validate required fields of $this->apiRequest->

        //validate non-required fields of $this->apiRequest->
    }
}
