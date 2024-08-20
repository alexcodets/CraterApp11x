<?php

namespace Crater\Listeners;

use Carbon\Carbon;
use Crater\DataObject\AddressDO;
use Crater\Events\LowBalanceEvent;
use Crater\Mail\EmailLowNotification;
use Crater\Models\Address;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\EmailLog;
use Crater\Models\TableEmailPeriod;
use Crater\Models\User;
use Crater\Traits\SendEmailsTrait;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class LowBalanceListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LowBalanceEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(LowBalanceEvent $event): void
    {
        Log::debug('Inside Listener');

        try {
            $user = User::findOrFail($event->userId);
            list($body, $company, $todayDate, $notificationSetting) = $this->getMessageData($user);
        } catch (Throwable $th) {
            Log::debug($th->getMessage());

            //throw $th;
            return;
        }

        try {
            // Si el usuario es postpaid, cancela la operación
            if ($user->status_payment == "postpaid") {
                Log::debug('Suspendido por ser postpaid');
                return; // Retornamos sin valor
            }

            Mail::to($user->email)->send(new EmailLowNotification("Notification: Low Balance on Account", $body, $company));
            $objs = new TableEmailPeriod();
            $objs->user_id = $user->id;
            $objs->option = "customer_email_notification";
            $objs->date = $todayDate;
            $objs->save();

            // Guardar logs de email
            $mailable_id = $notificationSetting->id;
            $customer_id = $user->id;
            $this->saveEmailLog($user->email, "Notification: Low Balance on Account", $body, $mailable_id, $company->id, $customer_id);

            return; // Este return es opcional y no genera problema
        } catch (Exception $ex) {
            Log::error("Error while sending lowBalance Email to user: {$user->name} with id: {$user->id}");
            Log::error($ex->getMessage());
            Log::debug($ex->getTraceAsString());

            return; // Este return también es correcto si deseas salir del flujo
        }
    }

    /**
     * @throws Exception
     */
    public function getMessageData(User $user): array
    {
        // Revisar si el correo ya se envió hoy
        $todayDate = Carbon::now()->format('Y-m-d');
        $period = TableEmailPeriod::where("user_id", $user->id)->where("option", "customer_email_notification")->where("date", $todayDate)->count();

        if ($period != 0) {
            throw new Exception('A mail has already been sent in the current period');
        }

        $notificationSetting = CompanySetting::where("company_id", $user->company_id)->where("option", "customer_email_notification")->first();

        if ($notificationSetting == null || $notificationSetting->value == null || $notificationSetting->value == '') {
            throw new Exception('Notification setting is empty');
        }

        $address = AddressDO::getAddress();

        if ($address == null) {
            throw new Exception('No address found');
        }

        $company = $user->company;

        if ($company == null) {
            throw new Exception('No company found for the user');
        }

        return [$this->getBody($notificationSetting->value, $address, $company, $user), $company, $todayDate, $notificationSetting];
    }

    public function getBody($body, Address $address, Company $company, User $user)
    {
        $password = ! is_null($user->password_encrypted) ? Crypt::decryptString($user->password_encrypted) : null;
        $colorInvoice = CompanySetting::getSetting('color_invoice', $company->id);
        $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';

        $body = str_replace("{PRIMARY_CONTACT_NAME}", $user->name, $body);
        $body = str_replace("{PRIMARY_COLOR}", $colorInvoice, $body);
        $body = str_replace("{CONTACT_DISPLAY_NAME}", $user->contact_name, $body);
        $body = str_replace("{CONTACT_PHONE}", $user->phone, $body);
        $body = str_replace("{CONTACT_EMAIL}", $user->email, $body);
        $body = str_replace("{CONTACT_WEBSITE}", $user->website, $body);
        $body = str_replace("{CONTACT_ROLE}", $user->role, $body);
        $body = str_replace("{CONTACT_BALANCE}", number_format($user->balance, 2, '.', ''), $body);
        $body = str_replace("{CONTACT_STATUS_CUSTOMER}", $user->status_payment, $body);
        $body = str_replace("{CONTACT_MINIMUN_BALANCE}", $user->minimun_balance, $body);
        $body = str_replace("{CONTACT_CUSTOM_CODE}", $user->customcode, $body);
        $body = str_replace("{CONTACT_AUTO_REPLENISH_AMOUNT}", $user->auto_replenish_amount, $body);
        $body = str_replace("{CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}", $user->email, $body);
        $body = str_replace("{COMPANY_NAME}", $company->name, $body);
        $body = str_replace("{COMPANY_COUNTRY}", $address->country, $body);
        $body = str_replace("{COMPANY_STATE}", $address->state, $body);
        $body = str_replace("{COMPANY_CITY}", $address->city, $body);
        $body = str_replace("{CONTACT_PASSWORD}", $password, $body);
        $body = str_replace("{COMPANY_ADDRESS_STREET_1}", $address->address_street_1, $body);
        $body = str_replace("{COMPANY_ADDRESS_STREET_2}", $address->address_street_2, $body);
        $body = str_replace("{COMPANY_PHONE}", $address->phone, $body);
        $body = str_replace("{COMPANY_ZIP_CODE}", $address->zip, $body);
        $body = str_replace("{CUSTOMER_LOGIN}", route('login'), $body);

        return $body;
    }

    public function saveEmailLog($customerEmail, $subject, $message, $mailable_id, $company_id, $customer_id)
    {
        // Llenar objeto email log y almacenarlo
        $emailLog['from'] = 'service@careonecomm.com';
        $emailLog['to'] = $customerEmail;
        $emailLog['subject'] = $subject;
        $emailLog['body'] = $message;
        $emailLog['mailable_type'] = SendEmailsTrait::class;
        $emailLog['mailable_id'] = $mailable_id;
        $emailLog['company_id'] = $company_id;
        $emailLog['creator_id'] = $customer_id;

        EmailLog::create($emailLog);
    }
}
