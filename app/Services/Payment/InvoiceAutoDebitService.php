<?php

namespace Crater\Services\Payment;

use Crater\Events\LowBalanceEvent;
use Crater\Exceptions\PaymentMaxTriesReaches;
use Crater\Models\Invoice;
use Crater\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InvoiceAutoDebitService
{
    public const CASH = 0;
    public const BALANCE = 1;

    public int $maxTries;

    public int $tries;

    private User $user;

    /**
     * @var false
     */
    private bool $email;

    private PaymentService $service;

    public function __construct(User $user, PaymentService $service)
    {
        $this->user = $user;
        $this->email = false;
        $this->maxTries = $user->customerConfig->auto_debit_attempts ?? 0;
        $this->tries = 0;
        $this->service = $service;
    }

    /**
     * Intenta pagar todas las facturas proporcionadas con el saldo del usuario.
     *
     * @param Collection $invoices Colección de facturas a pagar.
     */
    public function payInvoices($invoices)
    {
        // Inicializa el servicio de recarga de saldo del usuario.
        Log::debug('Inicializando UserRechargeBalanceService.');
        $rechargeService = new UserRechargeBalanceService($this->user, $this->service);

        try {
            // Maneja el servicio de recarga para asegurar que el usuario tiene saldo.
            Log::debug('Manejando recarga de saldo para el usuario: '.$this->user->id);
            $this->handle($rechargeService);
            // Refresca la información del usuario para obtener el saldo actualizado.
            $this->user->refresh();

            Log::debug('El usuario tiene saldo, intentando pagar facturas.');

            // Itera sobre cada factura para intentar pagarla.
            foreach ($invoices as $invoice) {
                // Inicializa el servicio de pago automático de facturas.
                Log::debug("Inicializando InvoiceAutoPaymentService para la factura: {$invoice->invoice_number}");
                $payService = new InvoiceAutoPaymentService($invoice, $this->user);

                // Intenta pagar la factura.
                Log::debug("Intentando pagar la factura: {$invoice->invoice_number}");
                $response = $this->payInvoice($invoice, $payService);

                // Si la respuesta es falsa, detiene el proceso.
                if (! $response) {
                    Log::debug("Pago fallido para la factura: {$invoice->invoice_number}, deteniendo el proceso.");

                    break;
                }
            }

            // Maneja nuevamente el servicio de recarga después de intentar pagar las facturas.
            Log::debug('Manejando recarga de saldo post-pago de facturas.');
            $this->handle($rechargeService);
        } catch (Throwable $th) {
            // Registra cualquier error que ocurra durante el proceso.
            Log::error('Error durante el proceso de carga de saldo.');
            Log::error('Mensaje de error: '.$th->getMessage());
            // Puedes descomentar la siguiente línea si deseas lanzar la excepción.
            // throw $th;
        }
    }

    private function handle(UserRechargeBalanceService $rechargeService): bool
    {
        if ($this->user->auto_debit != '1') {
            Log::debug('Is not AutoDebit, trying to send email');

            return $this->sendLowBalanceEmail();
        }

        if ($this->user->status_payment == 'prepaid' && $this->maxTries <= $this->tries) {
            return false;
        }

        Log::debug('Going to recharge');
        Log::debug($this->tries);

        return $rechargeService->handle($this->user->auto_replenish_amount * 100);
    }

    public function sendLowBalanceEmail(): bool
    {
        if ($this->user->auto_debit) {
            Log::debug('The user Have auto debit so no message about low balance will be send');

            return false;
        }

        if ($this->user->email_low_balance_notification < $this->user->balance) {
            Log::debug("Notification: {$this->user->email_low_balance_notification} - Balance {$this->user->balance} ");
            Log::debug('The user still have enough balance');

            #has enough balance
            return false;
        }

        if ($this->email) {
            Log::debug('The low balance email was already sent for the session');

            #the email has already been sent
            return false;
        }


        if($this->user->status_payment == "prepaid") {
            LowBalanceEvent::dispatch($this->user->id);
        } else {
            Log::debug('suspendido por ser postpaid autodebit ');

        }

        $this->email = true;

        return true;
    }

    private function payInvoice(Invoice $invoice, InvoiceAutoPaymentService $payService): bool
    {
        // Inicio del bucle para intentar pagar la factura
        do {
            try {
                // Inicio de la transacción de la base de datos
                DB::beginTransaction();
                Log::debug("Inicio de la transacción para la factura con ID: {$invoice->invoice_number}");

                // Registro de información relevante para el proceso
                Log::debug("Balance del usuario: {$this->user->balance}");
                Log::debug("Procesando la factura con ID: {$invoice->invoice_number}");
                Log::debug("Monto adeudado de la factura: {$invoice->due_amount}");

                // Verificación de si se puede continuar con el proceso
                if ($this->canNotContinue()) {
                    Log::debug('No se puede continuar con el proceso');
                    DB::rollback();

                    return false;
                }

                // Decisión sobre el método de pago
                switch ($this->cashOrBalance()) {
                    case self::CASH:
                        Log::debug('Pago con efectivo (tarjeta de crédito o ACH)');
                        $response = $payService->payWithCash();
                        $this->tries = $payService->tries;
                        if (! $response['success']) {
                            Log::debug('El pago con efectivo ha fallado');
                            DB::rollback();

                            return false;
                        }

                        break;
                    case self::BALANCE:
                        Log::debug('Pago con saldo');
                        $payService->payWithBalance();

                        break;
                    default:
                        Log::debug('Método de pago no definido');
                        DB::rollback();

                        return false;
                }

                // Confirmación de la transacción
                DB::commit();
                Log::debug("Transacción confirmada para la factura con ID: {$invoice->id}");

            } catch (PaymentMaxTriesReaches $exception) {
                // Manejo de excepción para máximo número de intentos alcanzados
                Log::debug('Se ha alcanzado el número máximo de intentos');
                $this->tries = $this->maxTries;
                DB::rollback();

                return false;

            } catch (Throwable $th) {
                // Manejo de cualquier otra excepción
                Log::debug('Error en el proceso, se está revirtiendo la transacción');
                DB::rollback();
                Log::error('Error capturado: '.$th->getMessage());

                return false;
            }

            // Actualización del estado de la factura
            $invoice->refresh();
            Log::debug("Estado de la factura actualizado, monto adeudado ahora: {$invoice->due_amount}");

        } while ($invoice->due_amount > 0);   // Continuar mientras la factura tenga monto adeudado

        // Finalización exitosa del proceso
        return true;
    }

    public function canNotContinue(): bool
    {
        return ! $this->canContinue();
    }

    /**
     * Determina si el proceso de pago puede continuar.
     *
     * @return bool Retorna verdadero si el proceso puede continuar, falso en caso contrario.
     */
    public function canContinue(): bool
    {
        // Verifica si el saldo del usuario es mayor o igual a 1.
        Log::debug("Verificando saldo del usuario: {$this->user->balance}");
        if ($this->user->balance >= 1 && ($this->user->customerConfig->auto_apply_credits ?? null)) {
            Log::debug('El saldo es suficiente para continuar.');

            return true;
        }

        // Verifica si el método de pago del usuario es 'prepaid' y si no se ha alcanzado el máximo de intentos.
        Log::debug("Verificando estado de pago y número de intentos: {$this->user->status_payment}, Max intentos: {$this->maxTries} , intentos: {$this->tries}");
        if ($this->maxTries > $this->tries) {
            Log::debug('El estado de pago aún hay intentos disponibles.');

            return true;
        }

        // Si ninguna de las condiciones anteriores se cumple, el proceso no puede continuar.
        Log::debug('No se cumplen las condiciones para continuar, el proceso se detendrá aquí.');

        return false;
    }

    private function cashOrBalance(): int
    {
        \Log::debug('...cashOrBalance...');

        if ($this->user->customerConfig && $this->user->customerConfig->auto_apply_credits && $this->user->balance >= 1) {
            return self::BALANCE;
        }

        return self::CASH;

    }
}
