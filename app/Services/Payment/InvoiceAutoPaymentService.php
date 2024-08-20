<?php

namespace Crater\Services\Payment;

use Crater\Exceptions\PaymentMaxTriesReaches;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\PaymentAccount;
use Crater\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoiceAutoPaymentService
{
    private Invoice $invoice;

    private User $user;

    private PaymentService $paymentService;

    public int $maxTries;

    public int $tries;

    public function __construct(Invoice $invoice, User $user, int $tries = 0)
    {
        $this->invoice = $invoice;
        $this->user = $user;
        $paymentAccount = PaymentAccount::with('country', 'state')->where('client_id', $this->user->id)
            ->where('main_account', 1)->where('status', 'A')->whereNull('deleted_at')->first();
        /* @var PaymentAccount $paymentAccount */
        $this->paymentService = new PaymentService($user, $paymentAccount);
        $this->maxTries = $user->customerConfig->auto_debit_attempts ?? 0;
        $this->tries = $tries;
    }

    public function handle()
    {
        if (! $this->user->auto_debit) {
            return false;
        }

        if ($this->user->status_payment != 'prepaid') {
            return false;
        }

        return true;
    }

    /**
     * @throws Throwable
     */
    public function payWithCash(): array
    {
        Log::debug('Inicio del método payWithCash');

        // Verificación de si se ha alcanzado el número máximo de intentos
        if ($this->tries >= $this->maxTries) {
            Log::debug('Se ha alcanzado el número máximo de intentos de pago');

            throw new PaymentMaxTriesReaches('Max tries reached');
        }

        try {
            do {
                // Intento de realizar el pago
                Log::debug('Intentando realizar el pago');
                $val = $this->paymentService->payInvoice($this->invoice);

                // Verificación del resultado del intento de pago
                if (! $val['success']) {
                    // Registro del fallo y el mensaje de error si está disponible
                    Log::debug('El intento de pago ha fallado');
                    Log::debug($val['message'] ?? 'No hay mensaje de error disponible');
                    // Incremento del contador de intentos
                    $this->tries++;
                }
            } while (! $val['success'] && $this->tries < $this->maxTries);
        } catch (Throwable $th) {
            // Registro de cualquier error inesperado durante el proceso de pago
            Log::error('Error inesperado al pagar con efectivo');
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());
            // Incremento del contador de intentos antes de relanzar la excepción
            $this->tries++;

            throw $th;
        }

        // Verificación del éxito del pago
        if ($val['success']) {
            // Actualización del estado de la factura a pagada y guardado de cambios
            Log::debug('El pago se ha realizado con éxito');
            $this->invoice->due_amount = 0;
            $this->invoice->paid_status = Invoice::STATUS_PAID;
            $this->invoice->status = Invoice::STATUS_COMPLETED;
            $this->invoice->save();
            Log::debug('Fin del método payWithCash con éxito');

            return $val;
        }

        // Si se llega a este punto, significa que se han superado los intentos sin éxito
        $this->tries++;
        Log::debug('Se ha superado el número de intentos sin un pago exitoso');
        Log::debug('Fin del método payWithCash con errores');

        throw new PaymentMaxTriesReaches('Max tries reached');
    }

    public function payWithBalance()
    {
        $totals = $this->getTotalsForBalance();
        Log::debug('totals');
        Log::debug($totals);

        $this->invoice->due_amount = $totals['due_amount'];
        $this->invoice->paid_status = $totals['status_paid'];
        $this->invoice->status = $totals['status'];
        $this->invoice->save();
        $this->user->balance = $totals['balance'];
        User::where('id', $this->user->id)->update(['balance' => $totals['balance']]);
        $this->refreshUser();
        //Log::debug("Current Balance: {$service->user->balance}");
        $service = new BalancePaymentService($this->user, $this->invoice);
        $payment = $service->handle($totals);

        try {
            $payment->sendSuccessPaymentMail();
        } catch (Throwable $th) {
            //throw $th;
            Log::debug('There was a error while trying to send success in new invoice payment');
            Log::debug($th->getMessage());
        }
    }

    private function getTotalsForBalance(): array
    {

        //$balance = $this->user->balance * 100;
        $toPay = $this->invoice->due_amount > ($this->user->balance * 100) ? $this->user->balance * 100 : $this->invoice->due_amount;

        return [
            'paid' => $toPay,
            'due_amount' => $this->invoice->due_amount - $toPay,
            'balance' => $this->user->balance - ($toPay / 100),
            'status_paid' => $this->invoice->due_amount > ($this->user->balance * 100) ? Invoice::STATUS_PARTIALLY_PAID : Invoice::STATUS_PAID,
            'status' => $this->invoice->due_amount > ($this->user->balance * 100) ? $this->invoice->status : Invoice::STATUS_COMPLETED,
            'old_balance' => $this->user->balance,
        ];

    }

    public function refreshUser(): void
    {
        $this->user->refresh();
    }

    public function getPrefix()
    {
        return CompanySetting::where('option', 'payment_prefix')
            ->where('company_id', $this->user->company_id)->first()->value ?? 'PAY';
    }
}
