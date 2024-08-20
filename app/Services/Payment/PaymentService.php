<?php

namespace Crater\Services\Payment;

use Carbon\Carbon;
use Crater\Authorize\Models\Authorize;
use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentDO;
use Crater\DataObject\PaymentDTO;
use Crater\DataObject\UserData;
use Crater\Mail\FailedPaymentMail;
use Crater\Models\AuxVault;
use Crater\Models\BalanceCustomer;
use Crater\Models\CompanySetting;
use Crater\Models\FailedPaymentHistory;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\PaymentAccount;
use Crater\Models\PaymentGateways;
use Crater\Models\PaymentMethod;
use Crater\Models\User;
use Crater\Services\Payment\Authorize\AuthorizePaymentService;
use Crater\Services\Payment\Authorize\PaymentAuthorizeDO;
use Crater\Services\Payment\AuxVault\AuxVaultData;
use Crater\Services\Payment\AuxVault\AuxVaultPaymentService;
use Crater\Services\Payment\AuxVault\TransactionResponseData;
use Exception;
use Illuminate\Support\Facades\Log;
use Mail;
use Throwable;

class PaymentService
{
    private User $user;

    private PaymentAccount $paymentAccount;

    private ?int $invoiceID;

    private bool $isRecharge;

    public function __construct(User $user, PaymentAccount $paymentAccount)
    {
        $this->user = $user;
        $this->paymentAccount = $paymentAccount;
    }

    public function payInvoice(Invoice $invoice): array
    {
        return $this->handle($invoice->due_amount, $invoice);
    }

    public function rechargeBalance(int $amount): array
    {
        return $this->handle($amount);
    }

    private function handle($amount, ?Invoice $invoice = null): array
    {
        Log::debug('Inicio del método handle --pay invoice');

        $this->invoiceID = $invoice->id ?? null;
        $this->isRecharge = ! $invoice;
        $paymentData = PaymentAccountData::fromModel($this->paymentAccount);

        if ($invoice) {
            $paymentData->setInvoiceId($invoice->id);
            $paymentData->setInvoiceNumber($invoice->invoice_number);
        }

        $paymentDo = new PaymentDTO(
            $paymentData,
            UserData::fromModel($this->user),
            $amount
        );

        try {
            switch ($this->paymentAccount->payment_account_type) {
                case 'CC':
                    Log::debug('Es tipo tarjeta de crédito');

                    $type = PaymentMethod::where('status', 'A')->where('account_accepted', 'C')->whereNULL('deleted_at')->first();
                    if (is_null($type)) {
                        Log::info('No se encontró un método de pago válido para tarjeta de crédito');

                        return ['success' => false, 'message' => 'No se encontró un método de pago'];
                    }

                    $response = $this->processCreditCard($paymentDo);
                    $response['payment_method_id'] = $type->id;

                    break;
                case 'ACH':
                    Log::debug('Es tipo ACH');

                    $type = PaymentMethod::where('status', 'A')->where('account_accepted', 'A')->whereNULL('deleted_at')->first();
                    if (is_null($type)) {
                        Log::info('No se encontró un método de pago válido para ACH');

                        return ['success' => false, 'message' => 'No se encontró un método de pago'];
                    }

                    $response = $this->chargeAch($paymentDo);
                    $response['payment_method_id'] = $type->id;
                    Log::debug('Respuesta del método de pago: '.$response['payment_method_id']);

                    break;
                default:
                    Log::info('No se seleccionó un método de pago');

                    return [
                        'success' => false,
                        'message' => 'Transacción fallida',
                    ];
            }
        } catch (Throwable $th) {
            Log::error('Error durante el proceso de pago');
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());

            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }

        if (! $response['success']) {
            Log::debug('El proceso de pago no fue exitoso');

            return $response;
        }

        // Si el pago fue exitoso, guarda el pago en la tabla payments
        $response['invoice_id'] = $invoice->id ?? null;
        $response['note'] = $this->isRecharge ? 'Recharge to restore credit' : 'Payment with: '.$this->paymentAccount->payment_account_type;
        Log::debug('Guardando el pago');
        $responseSavePayment = $this->saveAccountPayment($response, $paymentDo->getBaseDO());

        Log::debug('Fin del método handle con éxito');

        return [
            'success' => true,
            'message' => 'Exitoso',
            'payment_id' => $responseSavePayment->id,
            'payment_method_id' => $response['payment_method_id'],
        ];
    }

    public function getPrefix()
    {
        return CompanySetting::where('option', 'payment_prefix')
            ->where('company_id', $this->user->company_id)->first()->value ?? 'PAY';
    }

    // Guarda el pago en la tabla authorize si es exitoso
    public function saveChargeAuthorize(array $values, PaymentAuthorizeDO $authorizeData)
    {
        $authorize = Authorize::create($authorizeData->saveChargeAuthorize($values));

        return $authorize->id;
    }

    public function saveChargeAux(TransactionResponseData $data, AuxVaultData $auxVaultData)
    {
        $auxVault = AuxVault::create($auxVaultData->saveChargeAux($data));

        return $auxVault->id;
    }

    /**
     * @throws Exception
     */
    public function processCreditCard(PaymentDTO $paymentDTO): array
    {
        Log::debug('Inicio del método processCreditCard');

        $paymentGateway = PaymentGateways::where('company_id', $this->user->company_id)
            ->where('default', 1)
            ->where('status', 'A')
            ->first();

        if (is_null($paymentGateway)) {
            Log::info('No se encontró la pasarela de pago predeterminada');

            return ['success' => false, 'message' => 'No se encontró la pasarela de pago predeterminada'];
        }

        Log::debug("Pasarela de pago seleccionada: {$paymentGateway->name}");

        try {
            switch ($paymentGateway->name) {
                case 'Paypal':
                    Log::debug('Iniciando proceso de pago con Paypal');
                    $paymentDO = $paymentDTO->getPaypalDO();
                    $response = Paypal\PaypalPaymentServiceOld::handleCreditCard($paymentDO);
                    $response['invoice_id'] = $this->invoiceID;

                    if (! $response['success']) {
                        Log::info('Error al procesar el pago con Paypal Pro');
                        $extraData = $paymentDO->failureResponse($response);
                        Log::debug('Respuesta de error de Paypal: '.json_encode($response));

                        break;
                    }

                    $response['payment_paypal_id'] = $response['data']['id'];
                    Log::debug('Pago con Paypal Pro procesado correctamente: '.json_encode($response));

                    return $response;

                case 'Authorize':
                    Log::debug('Iniciando proceso de pago con Authorize.net');
                    $paymentDO = $paymentDTO->getAuthorizeDO();
                    $response = AuthorizePaymentService::handleCreditCard($paymentDO);
                    $response['invoice_id'] = $this->invoiceID;

                    if (! $response['success']) {
                        Log::info('Error al procesar el pago con Authorize.net');
                        $extraData = $response;
                        Log::debug('Respuesta de error de Authorize.net: '.json_encode($response));

                        break;
                    }

                    Log::debug('Pago con Authorize.net procesado correctamente: '.json_encode($response));
                    $response['authorize_id'] = $this->saveChargeAuthorize($response, $paymentDO);

                    return $response;

                case 'AuxVault':
                    Log::debug('Iniciando proceso de pago con AuxVault');
                    $paymentDO = $paymentDTO->getAuxVaultDO();
                    $serviceResponse = AuxVaultPaymentService::handleCreditCard($paymentDO, 'model');
                    $response = $paymentDO->successResponse($serviceResponse);

                    if (! empty($this->invoiceID)) {
                        $serviceResponse->setInvoiceId($this->invoiceID);
                    }

                    Log::debug('Pago con AuxVault procesado correctamente: '.json_encode($response));
                    $response['aux_id'] = $this->saveChargeAux($serviceResponse, $paymentDO);

                    return $response;

                default:
                    Log::info('No se encontró una pasarela de pago activa por defecto');
                    Log::debug('Método de pago no soportado: '.$paymentGateway->name);

                    return ['success' => false];
            }
        } catch (Throwable $th) {
            Log::error('Error durante el procesamiento de la tarjeta de crédito: '.$th->getMessage());

            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        } finally {
            if (isset($extraData) && isset($paymentDO)) {
                Log::debug('Enviando correo de pago fallido');

                try {
                    Mail::to($this->user->company->main_email)->send(new FailedPaymentMail($this->user, $paymentDO->getMailData($extraData)));
                    Log::debug('Correo de pago fallido enviado correctamente');
                } catch (Exception $e) {
                    Log::error('Error al enviar correo de pago fallido: '.$e->getMessage());
                }
                $this->saveFailedPaymentHistory($extraData);
            }
        }

        Log::debug('Fin del método processCreditCard');

        return $response ?? ['success' => false];
    }

    // Procesa el pago por ACH usando Authorize.net y el metodo del controlador
    public function chargeAch(PaymentDTO $authorizeDTO): array
    {
        $authorizeDO = $authorizeDTO->getAuthorizeDO();
        Log::debug('Is Authorize type');
        $responseAuthorize = AuthorizePaymentService::handleCreditAch($authorizeDO);
        $responseAuthorize['invoice_id'] = $this->invoiceID;

        if ($responseAuthorize['success']) {
            Log::debug('The operation was a success');
            $responseAuthorize['authorize_id'] = $this->saveChargeAuthorize($responseAuthorize, $authorizeDO);

            return $responseAuthorize;
        }

        Log::debug('Response Authorize');
        Log::debug($responseAuthorize);

        Log::debug('Before Mail');
        Mail::to($this->user->company->main_email)->send(new FailedPaymentMail($this->user, $authorizeDO->getMailData($responseAuthorize)));
        Log::debug('AfterMail');
        $this->saveFailedPaymentHistory($responseAuthorize);

        return ['success' => false];
    }

    // guarda los pagos fallidos
    public function saveFailedPaymentHistory(array $data)
    {
        $date = Carbon::now()->format('Y-m-d');
        $failed = FailedPaymentHistory::create([
            'payment_gateway' => $data['payment_gateway'],
            'transaction_number' => $data['transaction_number'] ?? null,
            'date' => $date,
            'amount' => $data['amount'],
            'customer_id' => $data['customer_id'],
            'description' => $data['description'],
            'invoice_id' => $data['invoice_id'] ?? null,
            'error_description' => $data['error_description'] ?? null,
        ]);

        Log::info('Failed payment history saved');
        Log::info($failed);
    }

    // guarda los pagos exitosos

    /**
     * Store The recharge of credit to the user account
     */
    public function saveAccountPayment($response, PaymentDO $paymentDO): Payment
    {
        Log::debug('*** saveAccountPayment***');
        $payment = $paymentDO->generateSuccessPayment($response);

        try {
            $payment->sendSuccessPaymentMail();
        } catch (Throwable $th) {
            //throw $th;
            Log::debug('There was a error while trying to send success');
            Log::debug($th->getMessage());
        }

        if ($this->isRecharge) {
            BalanceCustomer::create($paymentDO->getAddBalanceCustomer($payment->id));
        }

        Log::debug('Save Success Payment');

        return $payment;

    }
}
