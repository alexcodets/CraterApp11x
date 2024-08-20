<?php

namespace Crater\Services\Payment\Authorize;

use Crater\Authorize\Models\Authorize;
use Crater\Authorize\Models\AuthorizeSetting;
use Crater\Services\Payment\Traits\VoidTrait;
use DesolatorMagno\AuthorizePhp\Api\Contract\V1 as AnetAPI;
use DesolatorMagno\AuthorizePhp\Api\Controller\CreateTransactionController;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class AuthorizeRefundService
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

    public function handle(Authorize $authorize, $request, $amount): array
    {

        //        if (!$request->has('transaction_status') || $request['transaction_status'] !== 'Refunded' && $request['status_with_authorize'] !== true) {
        //            return [];
        //        }

        $authorize_setting = AuthorizeSetting::where('status', 'A')->first();
        $url = $authorize_setting['test_mode'] ? self::URL_TEST : self::URL_PRODUCTION;

        $refId = 'ref'.time();
        $this->refId = $refId;

        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($authorize_setting['login_id']);
        $merchantAuthentication->setTransactionKey($authorize_setting['transaction_key']);

        switch ($request->payment_type) {
            case 'CC':
                try {
                    $paymentOne = $this->getCreditCard($authorize);
                } catch (Throwable $th) {
                    \Log::debug($th->getTraceAsString());

                    return [
                        'success' => false,
                        'description' => $th->getMessage(),
                        'status' => 422,
                    ];
                }

                break;
            case 'ACH':

                $paymentOne = $this->getAccount($authorize);

                break;
            default:
                return [
                    'success' => false,
                    'description' => 'The payment type is invalid',
                    'status' => 422,
                ];
        }


        //create a transaction
        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType('refundTransaction');
        $transactionRequest->setAmount($amount / 100);
        $transactionRequest->setPayment($paymentOne);
        $transactionRequest->setRefTransId($authorize->transaction_id);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequest);
        $controller = new CreateTransactionController($request);
        $response = $controller->executeWithApiResponse($url);
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

    /**
     * @throws Exception
     */
    public function getCreditCard(Authorize $authorize): AnetAPI\PaymentType
    {
        $cc = $authorize->decrypted_credit_card;
        $length = strlen($cc);
        if (! $cc) {
            throw new Exception('Card Number is required');
        }
        if (! is_numeric(preg_replace('/\s+/', '', $cc))) {
            throw new Exception('Card Number can only contain digits.');
        }
        if ($length < 12 || $length >= 20) {
            throw new Exception("{$length} digits is outside the normal rank: (12-18).");
        }

        $expirationDate = $authorize->decrypted_expiration_date;
        if (! $expirationDate) {
            throw new Exception('Expiration date is required');
        }
        if (now()->format('Y-m') >= $expirationDate) {
            throw new Exception('Expiration date has already pass');
        }

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cc);
        $creditCard->setExpirationDate($expirationDate);
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        return $paymentOne;

    }

    public function getAccount(Authorize $authorize): AnetAPI\PaymentType
    {
        $account = new AnetAPI\BankAccountType();
        $account->setAccountType('ACH');
        $account->setAccountNumber(Crypt::decryptString($authorize->account_number));
        $account->setNameOnAccount(Crypt::decryptString($authorize->name_on_account));
        $account->setRoutingNumber(Crypt::decryptString($authorize->routing_number));
        $account->setBankName(Crypt::decryptString($authorize->bank_name));
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setBankAccount($account);

        return $paymentOne;
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
