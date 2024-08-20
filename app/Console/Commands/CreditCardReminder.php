<?php

namespace Crater\Console\Commands;

use Crater\DataObject\AddressDO;
use Crater\Mail\PaymentReminder;
use Crater\Models\Company;
use Crater\Models\PaymentAccount;
use Crater\Models\ScheduleLog;
use Crater\Models\User;
use Crater\Traits\SendEmailsTrait;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Mail;

class CreditCardReminder extends Command
{
    use SendEmailsTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:payment_accounts:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payment reminder 15th';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {

        //Log::debug("-------Cfredit car----------");
        // Log::debug("< -------Credit car---------- >");
        // validar que solo se puede ejecutar los 15
        if (now()->day != 15) {
            $this->info(__('comandos.creditCardReminder.info.not_15'));

            return self::SUCCESS;
        }

        // Log::debug('Now is 15');

        $companies = Company::whereHas('settingAllowCardExpirationPaymentJob')->whereHas('settingTimeRunCarExpirationPaymentJob')
            ->with(['paymentAccounts', 'paymentAccounts.client'])->get();

        $date = now()->addMonth()->format('Y-m');

        foreach ($companies as $company) {
            foreach ($company->paymentAccounts as $account) {
                // Log::debug('There is account');

                if (is_null($account->expiration_date)) {
                    return self::SUCCESS;
                }

                if ($date !== Crypt::decryptString($account->expiration_date)) {
                    return self::SUCCESS;
                }

                //  Log::debug(__('comandos.creditCardReminder.info.run', ['company' => $company->id, 'account' => $account->id]));
                $this->info(__('comandos.creditCardReminder.info.run', ['company' => $company->id, 'account' => $account->id]));
                self::sendEmail($account, $company);
            }
        }
        //   Log::debug('</-------Credit car---------- >');

        return self::SUCCESS;
    }

    public function sendEmail(PaymentAccount $account, Company $company)
    {
        // Log::debug('  Inside Send Email');
        $data = $company->general_email_setting;
        $customer = $account->client;
        $values = $this->getBodyAndSubject($account, $company, $customer, $data);

        try {
            // Log::debug('Trying Mail');
            Mail::to($customer->email)->send(new PaymentReminder($values['subject'], $values['body'], $data));

            // correo bbc
            $bbcMail = $company->settings()->where('option', 'payment_bbc_email')->first('value')->value ?? null;
            if ($bbcMail != null) {
                //  Log::debug('  BBC is not null');
                Mail::to($bbcMail)->send(new PaymentReminder($values['subject'], $values['body'], $data));
            }

            $admins = User::where('role', '=', 'super admin')
                ->where('email_payments', '=', 1)->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new PaymentReminder($values['subject'], $values['body'], $data));
            }

            $contacts = $customer->contacts()->where('allow_receive_emails', '=', 1)
                ->where('email_payments', '=', 1)->get();

            foreach ($contacts as $contact) {
                Mail::to($contact->email)->send(new PaymentReminder($values['subject'], $values['body'], $data));
            }

            /*EmailLog::create([
                'from' => '',
                'to' => '',
                'subject' => '',
                'body' => '',
                'mailable_type' => '',
                'mailable_id' => PaymentReminder::class,
                'company_id' => '',
                'user_id' => ''
            ]);*/

        } catch (Exception $ex) {
            $account->scheduleLogs()->create([
                'module_name' => 'payments:payment_accounts:reminder',
                'lvl' => ScheduleLog::LVL_ERROR,
                'message' => 'Mail Error',
                'extra_data' => json_encode([
                   'email' => [
                       'body' => $values['body'],
                       'subject' => $values['subject'],
                   ],
                   'user' => [
                       'id' => $customer->id,
                       'name' => $customer->name,
                       'email' => $customer->email,
                   ],
                   'error' => $ex->getMessage(),
                ])
            ]);
            // Log::debug($ex->getMessage());
        }

    }

    public function getBodyAndSubject(PaymentAccount $account, Company $company, User $customer, array $data)
    {
        $values = $company->credit_card_reminder_email_setting;

        if (is_null($values['body'])) {
            $values['body'] = ' ';

            return $values;
        }

        $add = AddressDO::getAddress();

        $search = [
            '{PRIMARY_CONTACT_NAME}', '{PRIMARY_COLOR}', '{CONTACT_DISPLAY_NAME}', '{CONTACT_EMAIL}',
            '{CONTACT_WEBSITE}', '{CARD_NUMBER}', '{CONTACT_PHONE}',
            '{COMPANY_NAME}', '{COMPANY_COUNTRY}', '{COMPANY_STATE}', '{COMPANY_CITY}', '{COMPANY_ADDRESS_STREET_1}',
            '{COMPANY_ADDRESS_STREET_2}', '{COMPANY_PHONE}', '{COMPANY_ZIP_CODE}', '{STATE_CODE}'
        ];
        $replace = [
            $customer->company_name, $data['PRIMARY_COLOR'], $customer->contact_name, $customer->email,
            $customer->website, $account->partially_block_card_number, $customer->phone,
            $company->name, $add->country, $add->state, $add->city, $add->address_street_1,
            $add->address_street_2, $add->phone, $add->zip, $add->state_code
        ];

        $values['body'] = str_replace($search, $replace, $values['body']);

        return $values;

    }
}
