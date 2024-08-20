<?php

namespace Crater\Services\Payment;

use Crater\Authorize\Models\Authorize;
use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\PaymentAmountData;
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
use Crater\Models\PaymentGateways;
use Crater\Models\PaymentMethod;
use Crater\Models\TransactionFees;
use Crater\Models\User;
use Crater\Services\Payment\Authorize\AuthorizePaymentService;
use Crater\Services\Payment\Authorize\PaymentAuthorizeDO;
use Crater\Services\Payment\AuxVault\AuxVaultData;
use Crater\Services\Payment\AuxVault\AuxVaultPaymentService;
use Crater\Services\Payment\AuxVault\TransactionResponseData;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Mail;
use Throwable;

class MultiPurposePaymentService
{
    private PaymentAccountData $paymentData;

    private UserData $user;

    private ?int $invoiceID = null;

    private bool $isRecharge;

    public function __construct(PaymentAccountData $paymentData, UserData $user)
    {
        $this->paymentData = $paymentData;
        $this->user = $user;
        Log::debug('user 41 payment service', ['user' => $user, 'payment data' => $paymentData]);
    }

    public function payInvoice(Invoice $invoice, PaymentAmountData $amount): array
    {
        $this->paymentData->setInvoiceId($invoice->id);
        $this->paymentData->setInvoiceNumber($invoice->invoice_number);

        return $this->handle($amount, $invoice);
    }

    public function handleMultiple(Collection $invoices, PaymentAmountData $total): array
    {

        $paymentDo = new PaymentDTO(
            $this->paymentData,
            $this->user,
            $total
        );

        $response = $this->processPayment($paymentDo);

        $this->isRecharge = false;

        if (! $response['success']) {
            return $response;
        }

        $balance = $total->amount;
        $payment = null;

        $invoices->each(function (Invoice $invoice) use (&$balance, $response, $paymentDo, &$payment) {

            if ($balance >= $invoice->due_amount) {
                $toPay = $invoice->due_amount;
                $balance -= $invoice->due_amount;
            } else {
                $toPay = $balance;
                $balance = 0;
            }

            $response['invoice_id'] = $invoice->id;
            $response['note'] = 'Payment with: '.$this->paymentData->payment_account_type;

            $paymentDo->updateAmount($toPay);
            $payment = $this->saveAccountPayment($response, $paymentDo->getBaseDO());

            if ($toPay === $invoice->due_amount) {
                $invoice->due_amount = 0;
                $invoice->paid_status = Invoice::STATUS_PAID;
                $invoice->status = Invoice::STATUS_COMPLETED;
            } else {
                $invoice->due_amount -= $toPay;
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
            }

            $invoice->save();
        });

        $response['payment_id'] = $payment->id;

        return $response;

    }

    private function handle(PaymentAmountData $amount, ?Invoice $invoice = null): array
    {
        $this->generateStartLog($amount, $invoice);

        $this->invoiceID = $invoice->id ?? null;
        $this->isRecharge = ! $invoice;

        $paymentDo = new PaymentDTO(
            $this->paymentData,
            $this->user,
            $amount
        );

        $response = $this->processPayment($paymentDo);

        if (! $response['success']) {
            return $response;
        }

        // si fue exitoso guarda el pago en la tabla payments
        $response['invoice_id'] = $invoice->id ?? null;
        $response['note'] = $this->isRecharge ? 'Charge to restore credit' : 'Payment with: '.$this->paymentData->payment_account_type;
        Log::channel('payment')->debug('Saving Payment');
        $responseSavePayment = $this->saveAccountPayment($response, $paymentDo->getBaseDO());

        return [
            'success' => true,
            'message' => 'Successful',
            'payment_id' => $responseSavePayment->id,
            'payment_method_id' => $response['payment_method_id'],
            'payment_account_type' => $this->paymentData->payment_account_type,
            'gateway' => $response['payment_gateway'],
        ];

    }

    public function processPayment(PaymentDTO $paymentDo): array
    {
        $response = [];

        try {
            switch ($this->paymentData->payment_account_type) {
                case 'CC':
                    Log::channel('payment')->debug('Is credit card type');
                    $type = $this->getType('C', $this->paymentData->payment_method_id);

                    if (is_null($type)) {
                        return ['success' => false, 'message' => 'There are no active payment methods for CC'];
                    }

                    $response = $this->processCreditCard($paymentDo);
                    $response['payment_method_id'] = $type->id;

                    break;
                case 'ACH':
                    Log::channel('payment')->debug('Is ACH type');
                    $type = $this->getType('A', $this->paymentData->payment_method_id);

                    if (is_null($type)) {
                        return ['success' => false, 'message' => 'There was not PaymentMethod for ACH'];
                    }

                    $response = $this->chargeAch($paymentDo);
                    $response['payment_method_id'] = $type->id;
                    Log::channel('payment')->debug('Response Payment_method'.$response['payment_method_id']);

                    break;
                default:
                    Log::channel('payment')->info("{$this->paymentData->payment_account_type} is not a valid payment method. We only accept credit cards (CC) and ACH.");

                    return [
                        'success' => false,
                        'message' => "Transaction failed. {$this->paymentData->payment_account_type} is not a valid payment method. We only accept credit cards (CC) and ACH.",
                    ];
            }
        } catch (Throwable $th) {
            //throw $th;
            Log::channel('payment')->debug($th->getMessage());
            Log::channel('payment')->debug($th->getTraceAsString());

            return [
                'success' => false,
                'message' => $th->getMessage(),
            ];
        }

        return $response;

    }

    public function generateStartLog($amount, ?Invoice $invoice): void
    {
        Log::channel('payment')->info('<-----------------------------Start---------------------------------->');
        $metaData = [
            'user_name' => $this->user->first_name,
            'user_id' => $this->user->id,
            'system_user' => (bool)$this->user->id,
            'amount' => $amount,
            'payment_type' => $invoice ? 'Invoice' : 'Recharge',
        ];
        if ($invoice) {
            $metaData['invoice'] = [
                'id' => $invoice->id,
                'due_amount' => $invoice->due_amount,
                'due_date' => $invoice->formatted_due_date,
            ];
        }
        Log::channel('payment')->info('Process MetaData', $metaData);
    }

    private function getType($typeMethod, ?int $id = null): ?PaymentMethod
    {
        return PaymentMethod::where('status', 'A')
            ->where('account_accepted', $typeMethod)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', $id);
            })
            ->whereNULL('deleted_at')
            ->first();
    }

    // Guarda el pago en la tabla authorize si es exitoso

    /**
     * @throws Exception
     */
    public function processCreditCard(PaymentDTO $paymentDTO): array
    {

        $paymentGateway = $this->getPaymentGateways();

        if (is_null($paymentGateway)) {
            Log::channel('payment')->info('Default payment gateway not found');

            return ['success' => false, 'message' => 'Error: Payment gateway not found for credit card.'];
        }

        Log::channel('payment')->debug($paymentGateway);

        switch ($paymentGateway->name) {
            case 'Paypal':

                try {
                    $paymentDO = $paymentDTO->getPaypalData();
                    $response = Paypal\PaypalPaymentService::handleCreditCard($paymentDO);
                    $response['invoice_id'] = $this->invoiceID;

                    if ($response['success']) {
                        $response['payment_paypal_id'] = $response['data']['id'];

                        return $response;
                    }

                    $extraData = $paymentDO->failureResponse($response);
                    Log::channel('payment')->info('error al procesar el pago Paypal Pro');
                    Log::channel('payment')->info('response: ', $response);

                    break;

                } catch (Throwable $th) {
                    //throw $th;
                    Log::channel('payment')->error($th->getMessage());
                    Log::channel('payment')->error($th->getTraceAsString());

                    return [
                        'success' => false,
                        'message' => $th->getMessage(),
                        'error_description' => $th->getMessage(),
                        'code_error' => $th->getCode(),
                        'invoice_id' => $this->invoiceID,
                        'payment_gateway' => $paymentGateway->name,
                    ];
                }

            case 'Authorize':
                Log::channel('payment')->debug('Is Authorize type');

                $paymentDO = $paymentDTO->getAuthorizeDO();
                $response = AuthorizePaymentService::handleCreditCard($paymentDO);
                $response['invoice_id'] = $this->invoiceID;

                if ($response['success']) {
                    Log::channel('payment')->debug('The operation was a success');
                    $response['authorize_id'] = $this->saveChargeAuthorize($response, $paymentDO);

                    return $response;
                }
                Log::channel('payment')->debug('Response Authorize');
                Log::channel('payment')->debug('Response: ', $response);

                $extraData = $response;

                break;

            case 'AuxVault':
                Log::channel('payment')->debug('Is AuxVault Time');

                $paymentDO = $paymentDTO->getAuxVaultDO();

                try {
                    $serviceResponse = AuxVaultPaymentService::handleCreditCard($paymentDO, 'model');
                    $response = $paymentDO->successResponse($serviceResponse);
                    if (! empty($this->invoiceID)) {
                        $serviceResponse->setInvoiceId($this->invoiceID);
                    }
                    Log::channel('payment')->debug('The operation was a success');
                    $response['aux_vault_id'] = $this->saveChargeAux($serviceResponse, $paymentDO);

                    return $response;

                } catch (Throwable $th) {
                    //throw $th;
                    Log::debug('Error Response AuxVault 228', [$th]);
                    Log::channel('payment')->error('Error Response AuxVault');
                    $response = $paymentDO->failResponse($th->getMessage(), $th->getCode() ?? 500);
                    Log::channel('payment')->error('Response: ', $response);
                    $extraData = $response;

                    break;
                }

            default:
                Log::channel('payment')->info('Active default payment gateway not found');

                return [
                    'success' => false,
                    'payment_gateway' => 'none',
                    'error_description' => 'Active default payment gateway not found',
                    'invoice_id' => $this->invoiceID,
                ];
        }

        Log::channel('payment')->debug('Before Mail CC');

        if ($this->user->id) {
            $user = User::find($this->user->id);

            try {
                Mail::to($user->company->main_email)->send(new FailedPaymentMail($user, $paymentDO->getMailData($extraData)));
            } catch (Throwable $th) {
                Log::error('Mail error: '.$th->getMessage());
            }
        }

        Log::channel('payment')->debug('AfterMail');
        $this->saveFailedPaymentHistory($extraData);

        return $response;
    }

    public function getPaymentGateways()
    {
        if ($this->paymentData->payment_gateway_id) {
            return PaymentGateways::where('company_id', $this->user->company_id)
                ->where('status', 'A')
                ->where('id', $this->paymentData->payment_gateway_id)
                ->first();
        }

        return PaymentGateways::where('company_id', $this->user->company_id)
            ->where('default', 1)
            ->where('status', 'A')
            ->first();

    }

    public function saveChargeAuthorize(array $values, PaymentAuthorizeDO $authorizeData)
    {
        $authorize = Authorize::create($authorizeData->saveChargeAuthorize($values));
        foreach ($authorizeData->amount->fees as $fee) {
            TransactionFees::create([
                'transaction_id' => $authorize->transaction_id,
                'authorize_id' => $authorize->id,
                'type' => $fee['type'],
                'amount' => $fee['amount'],
                'total' => $fee['total'],
                'name' => $fee['name'],
                'payment_gateways_fee_id' => $fee['id'],
                'company_id' => $this->user->company_id,
            ]);
        }

        return $authorize->id;
    }

    public function saveChargeAux(TransactionResponseData $data, AuxVaultData $auxVaultData)
    {
        $auxVault = AuxVault::create($auxVaultData->saveChargeAux($data));
        Log::debug(
            'SaveChargeAux: ',
            [
                'array' => $auxVaultData->amount->fees,
                'total_fee' => $auxVaultData->amount->totalFees,
            ]
        );
        foreach ($auxVaultData->amount->fees as $fee) {
            TransactionFees::create([
                'transaction_id' => $auxVault->transaction_id,
                'aux_vault_id' => $auxVault->id,
                'type' => $fee['type'],
                'amount' => $fee['amount'],
                'total' => $fee['total'],
                'name' => $fee['name'],
                'payment_gateways_fee_id' => $fee['id'],
                'company_id' => $this->user->company_id,
            ]);
        }

        return $auxVault->id;
    }

    // guarda los pagos fallidos

    public function saveFailedPaymentHistory(array $data)
    {
        $date = now()->format('Y-m-d');
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

        Log::channel('payment')->info('Failed payment history saved');
        Log::channel('payment')->info($failed);
    }

    public function chargeAch(PaymentDTO $paymentDTO): array
    {

        $paymentGateway = $this->getPaymentGateways();

        if (is_null($paymentGateway)) {
            Log::channel('payment')->info('Default payment gateway not found for ACH');

            return ['success' => false, 'message' => 'Error: Payment gateway not found for ACH.'];
        }

        Log::channel('payment')->debug($paymentGateway);

        switch ($paymentGateway->name) {
            case 'Authorize':
                Log::channel('payment')->debug('Is Authorize type');

                $paymentDO = $paymentDTO->getAuthorizeDO();

                $response = AuthorizePaymentService::handleCreditAch($paymentDO);
                $response['invoice_id'] = $this->invoiceID;

                if ($response['success']) {
                    Log::channel('payment')->debug('The operation was a success');
                    $response['authorize_id'] = $this->saveChargeAuthorize($response, $paymentDO);

                    return $response;
                }

                Log::channel('payment')->debug('Response Authorize');
                Log::channel('payment')->debug('response: ', $response);

                break;

            case 'AuxVault':
                Log::channel('payment')->debug('Is AuxVault Time');
                $paymentDO = $paymentDTO->getAuxVaultDO();

                try {
                    $serviceResponse = AuxVaultPaymentService::handleAch($paymentDO, 'model');
                    $response = $paymentDO->successResponse($serviceResponse);
                    if (! empty($this->invoiceID)) {
                        $serviceResponse->setInvoiceId($this->invoiceID);
                    }
                    Log::channel('payment')->debug('The operation was a success');
                    $response['aux_vault_id'] = $this->saveChargeAux($serviceResponse, $paymentDO);
                    //return $response;

                } catch (Throwable $th) {
                    //throw $th;
                    Log::debug('Error Response AuxVault 228', [$th]);
                    Log::channel('payment')->error('Error Response AuxVault');
                    $response = $paymentDO->failResponse($th->getMessage(), $th->getCode() ?? 500);
                    Log::channel('payment')->error('Response: ', $response);

                    break;
                }

                break;
            default:
                Log::channel('payment')->info('Active default payment gateway not found');

                return [
                    'success' => false,
                    'payment_gateway' => 'none',
                    'error_description' => 'Active default payment gateway not found for ACH',
                    'invoice_id' => $this->invoiceID,
                ];
        }

        Log::channel('payment')->debug('Before Mail ACH');

        if ($this->user->id) {
            $user = User::find($this->user->id);

            try {
                Mail::to($user->company->main_email)->send(new FailedPaymentMail($user, $paymentDO->getMailData($response)));
            } catch (Throwable $th) {
                Log::error('Mail error: '.$th->getMessage());
            }
        }

        Log::channel('payment')->debug('AfterMail ACH');
        $this->saveFailedPaymentHistory($response);

        return $response;
    }

    /**
     * Store The recharge of credit to the user account
     */
    public function saveAccountPayment($response, PaymentDO $paymentDO): Payment
    {
        Log::channel('payment')->debug('*** Payment for Recharge ***');
        $payment = $paymentDO->generateSuccessPayment($response);

        try {
            $payment->sendSuccessPaymentMail();
        } catch (Throwable $th) {
            //throw $th;
            Log::channel('payment')->debug('There was a error while trying to send success');
            Log::channel('payment')->debug($th->getMessage());
        }

        if ($this->isRecharge) {
            BalanceCustomer::create($paymentDO->getAddBalanceCustomer($payment->id));
        }

        Log::channel('payment')->debug('Save Success Payment');

        return $payment;

    }

    public function rechargeBalance(PaymentAmountData $amount): array
    {
        return $this->handle($amount);
    }

    public function getPrefix()
    {
        return CompanySetting::where('option', 'payment_prefix')
            ->where('company_id', $this->user->company_id)->first()->value ?? 'PAY';
    }
}
