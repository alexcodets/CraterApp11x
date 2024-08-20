<?php

namespace Crater\Services\Payment\Authorize;

use Crater\Authorize\Models\AuthorizeSetting;
use DesolatorMagno\AuthorizePhp\Api\Contract\V1 as AnetAPI;
use DesolatorMagno\AuthorizePhp\Api\Controller\CreateTransactionController;
use Log;
use Throwable;

class AuthorizePaymentService
{
    public const URL_TEST_XML = 'https://apitest.authorize.net/xml/v1/request.api';
    public const URL_TEST = 'https://apitest.authorize.net';
    public const URL_PRODUCTION = 'https://api2.authorize.net';
    public const URL_PRODUCTION_XML = 'https://api.authorize.net/xml/v1/request.api';

    public static function handleCreditCard(PaymentAuthorizeDO &$authorizeDO): array
    {
        return self::handleAll($authorizeDO, 'CreditCard');
    }

    public static function handleCreditAch(PaymentAuthorizeDO &$authorizeDO): array
    {
        return self::handleAll($authorizeDO, 'ACH');
    }

    protected static function getCreditCardPaymentType(array $data): AnetAPI\PaymentType
    {
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($data['card_number']);
        $creditCard->setExpirationDate($data['expiration_date']);
        $creditCard->setCardCode($data['cvv']);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();

        return $paymentOne->setCreditCard($creditCard);

    }

    protected static function getBankAccountPaymentType(array $data): AnetAPI\PaymentType
    {
        $account = new AnetAPI\BankAccountType();
        $account->setAccountType($data['ACH_type']);
        $account->setAccountNumber($data['account_number']);
        $account->setNameOnAccount($data['name_on_account']);
        $account->setRoutingNumber($data['routing_number']);
        $account->setBankName($data['bank_name']);

        // see eCheck documentation for proper echeck type to use for each situation
        //$bankAccount->setEcheckType('WEB');

        $paymentOne = new AnetAPI\PaymentType();

        return $paymentOne->setBankAccount($account);
    }

    protected static function handleAll(PaymentAuthorizeDO &$authorizeDO, string $type): array
    {
        Log::debug('inicio de handleAll');
        $data = $authorizeDO->getAuthorizeData();
        $authorize_setting = AuthorizeSetting::whereNull('deleted_at')->where('is_default', 1)->first();

        if (is_null($authorize_setting)) {
            Log::debug('No authorize setting found');

            return $authorizeDO->failResponse(
                'A valid Authorize setting couldn\'t be found ',
                404
            );
        }
        $authorizeDO->setting_id = $authorize_setting->id;
        $url = $authorize_setting['test_mode'] ? self::URL_TEST : self::URL_PRODUCTION;

        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($authorize_setting['login_id']);
        $merchantAuthentication->setTransactionKey($authorize_setting['transaction_key']);

        //Log::debug('Merchant Auth');
        //Log::debug($merchantAuthentication->jsonSerialize());

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);

        switch ($type) {
            case 'CreditCard':
                Log::debug('Entro a getCreditCardPaymentType');
                $paymentOne = self::getCreditCardPaymentType($authorizeDO->getCreditCardData());

                break;
            case 'ACH':
                Log::debug('Entro a getBankAccountPaymentType');
                $paymentOne = self::getBankAccountPaymentType($authorizeDO->getBankAccountData());

                break;
            default:
                return $authorizeDO->failResponse('Incorrect payment method (Only CC or ACH)');
        }

        Log::debug('paso type');
        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($data['invoice_number']);
        $order->setDescription($data['description']);

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($data['name']);
        $customerAddress->setLastName($data['last_name']);
        $customerAddress->setCompany($data['company_name']);
        $customerAddress->setAddress($data['address_street_1']);
        $customerAddress->setCity($data['city']);
        $customerAddress->setState($data['state']);
        $customerAddress->setZip($data['zip']);
        $customerAddress->setCountry($data['country']);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType($data['type_client']);
        $customerData->setId($data['custom_code']);
        $customerData->setEmail($data['email']);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName('duplicateWindow');
        $duplicateWindowSetting->setSettingValue('0');

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        /*$merchantDefinedField1 = new AnetAPI\UserFieldType();
        $merchantDefinedField1->setName("customerLoyaltyNum");
        $merchantDefinedField1->setValue("1128836273");*/

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();

        //authOnlyTransaction
        $transactionRequestType->setTransactionType('authCaptureTransaction');
        $transactionRequestType->setAmount($data['amount_total']);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        //$transactionRequestType->addToUserFields($merchantDefinedField1);
        //$transactionRequestType->addToUserFields($merchantDefinedField2);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($data['transaction_id']);
        $request->setTransactionRequest($transactionRequestType);

        $service = new CreateTransactionController($request);

        try {
            $response = $service->executeWithApiResponse($url);
        } catch (Throwable $th) {
            Log::error('Error while running executeWithApiResponse');
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());
            $response = null;
        }

        //TODO: Response from Guzzle Error, and another errors.

        if (is_null($response)) {
            $error = 'There was not response from the Authorize api';

            //Log::debug($error);
            return $authorizeDO->failResponse($error);
        }

        Log::debug('***********************************');
        Log::debug('Authorize Api Response');
        //Log::debug($response->jsonSerialize());
        Log::debug(serialize($response));
        Log::debug('***********************************');

        // Check to see if the API request was not successfully received and acted upon
        return self::getPrettyRes($response, $authorizeDO);

    }

    public static function getPrettyRes(AnetAPI\ANetApiResponseType $response, PaymentAuthorizeDO $authorizeDO): array
    {
        $transactionResponse = $response->getTransactionResponse();

        if ($response->getMessages() == null) {
            Log::debug('Transaction failed');
            if ($transactionResponse != null && $transactionResponse->getErrors() != null) {
                Log::debug($transactionResponse->getErrors()[0]->getErrorText());

                return $authorizeDO->failResponse(
                    $transactionResponse->getErrors()[0]->getErrorText(),
                    $transactionResponse->getErrors()[0]->getErrorCode()
                );
            }

            Log::debug('Failed with not message from the Authorize api');

            return $authorizeDO->failResponse('Failed with not message from the Authorize api', 404);

        }

        if (is_null($transactionResponse)) {
            Log::debug('Transaction failed');
            Log::debug('Failed with not response from the Authorize api');

            return $authorizeDO->failResponse('Failed with not response from the Authorize api', 404);
        }

        if ($response->getMessages()->getResultCode() != 'Ok') {

            //Log::debug("Transaction Failed \n");

            if ($transactionResponse->getErrors() != null) {

                return $authorizeDO->failResponse(
                    $transactionResponse->getErrors()[0]->getErrorText(),
                    $transactionResponse->getErrors()[0]->getErrorCode()
                );

            }

            return $authorizeDO->failResponse(
                $response->getMessages()->getMessage()[0]->getText(),
                $response->getMessages()->getMessage()[0]->getCode()
            );
        }

        // Since the API request was successful, look for a transaction response
        // and parse it to display the results of authorizing the card
        if ($transactionResponse->getMessages() == null) {
            //Log::debug('Transaction failed');
            if ($transactionResponse->getErrors() != null) {
                return $authorizeDO->failResponse(
                    $transactionResponse->getErrors()[0]->getErrorText(),
                    $transactionResponse->getErrors()[0]->getErrorCode()
                );
            }
            //Log::debug('Failed with not message from the Authorize api');

            return $authorizeDO->failResponse('Failed with not message from the Authorize api', 404);

        }

        return $authorizeDO->successResponse($transactionResponse);
    }
}
