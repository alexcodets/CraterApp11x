<?php

namespace Crater\Services\Payment\Authorize;

use Crater\Authorize\Models\Authorize;
use Crater\Authorize\Models\AuthorizeSetting;
use Crater\Services\Payment\Traits\VoidTrait;
use DesolatorMagno\AuthorizePhp\Api\Contract\V1 as AnetAPI;
use DesolatorMagno\AuthorizePhp\Api\Controller\CreateTransactionController;
use net\authorize\api\constants\ANetEnvironment;

class AuthorizeVoidService
{
    use VoidTrait;
    public const URL_TEST_XML = 'https://apitest.authorize.net/xml/v1/request.api';
    public const URL_TEST = 'https://apitest.authorize.net';
    public const URL_PRODUCTION = 'https://api2.authorize.net';
    public const URL_PRODUCTION_XML = 'https://api.authorize.net/xml/v1/request.api';

    public function __construct()
    {
        $this->name = 'Authorize';
    }

    public function handle(Authorize $authorize): array
    {

        $transactionId = rand(100000000, 999999999);

        $authorize_setting = AuthorizeSetting::where('status', 'A')->first();
        $url = $authorize_setting['test_mode'] ? self::URL_TEST : self::URL_PRODUCTION;

        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($authorize_setting['login_id']);
        $merchantAuthentication->setTransactionKey($authorize_setting['transaction_key']);

        // Set the transaction's refId
        $refId = 'ref'.time();
        $this->refId = $refId;

        //create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType('voidTransaction');
        $transactionRequestType->setRefTransId($authorize->transaction_id);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        $controller = new CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);

        if (is_null($response)) {
            return $this->errorResponse(
                0,
                'There was not response from authorize',
                'Transaction Failed(2): no response'
            );
        }

        if ($response->getMessages()->getResultCode() == 'Ok') {
            $transactionResponse = $response->getTransactionResponse();

            if ($transactionResponse != null && $transactionResponse->getMessages() != null) {
                return $this->successResponse(
                    $transactionResponse->getMessages()[0]->getCode(),
                    $transactionResponse->getMessages()[0]->getDescription(),
                    'success',
                    $transactionResponse->getTransId()
                );

            } else {
                if ($transactionResponse->getErrors() != null) {
                    return $this->errorResponse(
                        $transactionResponse->getErrors()[0]->getErrorCode(),
                        $transactionResponse->getErrors()[0]->getErrorText(),
                        'Transaction Failed: with errors'
                    );
                }

                return $this->errorResponse(
                    0,
                    'Transaction Failed, but did no return error',
                    'Transaction Failed: with no errors'
                );

            }
        } else {
            $transactionResponse = $response->getTransactionResponse();
            if ($transactionResponse != null && $transactionResponse->getErrors() != null) {
                return $this->errorResponse(
                    $transactionResponse->getErrors()[0]->getErrorCode(),
                    $transactionResponse->getErrors()[0]->getErrorText(),
                    'Transaction Failed(2): with errors'
                );
            } else {
                return $this->errorResponse(
                    $response->getMessages()->getMessage()[0]->getCode(),
                    $response->getMessages()->getMessage()[0]->getText(),
                    'Transaction Failed(2): with errors(2)'
                );
            }
        }

    }

    /** @return array{'success': false, 'gateway': string, 'transaction_id': string, 'ref_id': string, 'code': string, 'description': string, 'note': string } */
    public function errorResponse(string $code, string $description, string $note, ?string $transactionId = null): array
    {
        return $this->response(false, $code, $description, $note, $transactionId);
    }

    /** @return array{'success': bool, 'gateway': string, 'transaction_id': string, 'ref_id': string, 'code': string, 'description': string, 'note': string } */
    public function response(bool $success, string $code, string $description, string $note, ?string $transactionId = null): array
    {
        return [
            'success' => $success,
            'gateway' => 'Authorize',
            'transaction_id' => $transactionId,
            'ref_id' => $this->refId,
            'code' => $code,
            'description' => $description,
            'note' => $note,
        ];
    }

    /** @return array{'success': true, 'gateway': string, 'transaction_id': string, 'ref_id': string, 'code': string, 'description': string, 'note': string } */
    public function successResponse(string $code, string $description, string $note, ?string $transactionId = null): array
    {
        return $this->response(true, $code, $description, $note, $transactionId);
    }
}
