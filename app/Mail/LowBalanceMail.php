<?php

namespace Crater\Mail;

use Carbon\Carbon;
use Crater\Models\Address;
use Crater\Models\CompanySetting;
use Crater\Models\TableEmailPeriod;
use Crater\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class LowBalanceMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->body = '';
        $this->data = '';
        //
        //$this->subject = $subject;
        //$this->message = $message;
        //$this->data = $data;
    }

    public function sendMail()
    {

        //check if mail was send before
        $present_date = Carbon::now()->format('Y-m-d');
        $period = TableEmailPeriod::where("user_id", $this->user->id)->where("option", "customer_email_notification")->where("date", $present_date)->count();

        if ($period != 0) {
            //Log::debug("A period was found?");
            return false;
        }

        $message = CompanySetting::where("company_id", $this->user->company_id)->where("option", "customer_email_notification")->first();

        if ($message->value == null || $message->value == '') {
            //Log::debug("There is not configured message for low balance email");
            return false;
        }

        $newMessage = $message->value ?? '';

        $address = $this->getAddress();

        if ($address == null) {
            //Log::debug("No address was found");
            return false;
        }

        $company = $this->user->company;

        if ($company == null) {
            //Log::debug("No company found");
            return false;
        }

        $this->body = $this->getBody($newMessage, $address, $company);

    }

    public function getAddress()
    {
        $address = Address::whereNULL("user_id")
            ->join("countries", "countries.id", "=", "addresses.country_id")
            ->join("states", "states.id", "=", "addresses.state_id")
            ->select("countries.name as country", "states.name as state", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
            ->first();

        if ($address == null) {
            $address = Address::first()
                ->join("countries", "countries.id", "=", "addresses.country_id")
                ->join("states", "states.id", "=", "addresses.state_id")
                ->select("countries.name as country", "states.name as state", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
                ->first();
        }

        return $address;
    }

    public function getBody($body, $address, $company)
    {

        $password = ! is_null($this->user->password_encrypted) ? Crypt::decryptString($this->user->password_encrypted) : null;

        $body = str_replace("{PRIMARY_CONTACT_NAME}", $this->user->name, $body);
        $body = str_replace("{CONTACT_DISPLAY_NAME}", $this->user->contact_name, $body);
        $body = str_replace("{CONTACT_PHONE}", $this->user->phone, $body);
        $body = str_replace("{CONTACT_EMAIL}", $this->user->email, $body);
        $body = str_replace("{CONTACT_WEBSITE}", $this->user->website, $body);
        $body = str_replace("{CONTACT_ROLE}", $this->user->role, $body);
        $body = str_replace("{CONTACT_BALANCE}", number_format($this->user->balance, 2, '.', ''), $body);
        $body = str_replace("{CONTACT_STATUS_CUSTOMER}", $this->user->status_payment, $body);
        $body = str_replace("{CONTACT_MINIMUN_BALANCE}", $this->user->minimun_balance, $body);
        $body = str_replace("{CONTACT_CUSTOM_CODE}", $this->user->customcode, $body);
        $body = str_replace("{CONTACT_AUTO_REPLENISH_AMOUNT}", $this->user->auto_replenish_amount, $body);
        $body = str_replace("{CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}", $this->user->email, $body);
        $body = str_replace("{COMPANY_NAME}", $company->name, $body);
        $body = str_replace("{COMPANY_COUNTRY}", $address->country, $body);
        $body = str_replace("{COMPANY_STATE}", $address->state, $body);
        $body = str_replace("{COMPANY_CITY}", $address->city, $body);
        $body = str_replace("{CONTACT_PASSWORD}", $password, $body);
        $body = str_replace("{COMPANY_ADDRESS_STREET_1}", $address->address_street_1, $body);
        $body = str_replace("{COMPANY_ADDRESS_STREET_2}", $address->address_street_2, $body);
        $body = str_replace("{COMPANY_PHONE}", $address->phone, $body);
        $body = str_replace("{COMPANY_ZIP_CODE}", $address->zip, $body);

        return $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->markdown('emails.send.EmailLowNotification')->with([
            'my_message' => $this->message,
            'data' => $this->data
        ]);
    }
}
