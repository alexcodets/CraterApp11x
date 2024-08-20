<?php

namespace Crater\Mail;

use Crater\DataObject\AddressDO;
use Crater\Models\Company;
use Crater\Models\User;
use Illuminate\Mail\Mailable;

abstract class BaseMail extends Mailable
{
    protected string $defaultSubject = '';

    protected string $defaultBody = '';

    protected string $optionSubject = '';

    protected string $optionBody = '';

    protected array $data = [];

    protected string $body;

    public function getFinalBody(Company $company, User $user): string
    {
        $body = $company->settings()->where('option', '=', $this->optionBody)->first()->value ?? $this->defaultBody;

        return $this->replace($company, $user, $body);
    }

    public function replace(Company $company, User $user, string $text)
    {

        $add = AddressDO::getAddress();
        $search = [
            '{PRIMARY_CONTACT_NAME}', '{CONTACT_DISPLAY_NAME}', '{CONTACT_PHONE}', '{CONTACT_EMAIL}', '{CONTACT_WEBSITE}',
            '{CONTACT_ROLE}', '{CONTACT_BALANCE}', '{CONTACT_STATUS_CUSTOMER}',
            '{CONTACT_MINIMUN_BALANCE}', '{CONTACT_CUSTOM_CODE}', '{CUSTOMER_LOGIN}',
            '{COMPANY_NAME}', '{COMPANY_COUNTRY}', '{COMPANY_STATE}', '{COMPANY_CITY}', '{COMPANY_ADDRESS_STREET_1}',
            '{COMPANY_ADDRESS_STREET_2}', '{COMPANY_PHONE}', '{COMPANY_ZIP_CODE}', '{STATE_CODE}', '{NAME}',

        ];
        $replace = [
            $user->name, $user->contact_name, $user->phone, $user->email, $user->website,
            $user->role, number_format($user->balance, 2, '.', ''), $user->status_payment,
            $user->minimun_balance, $user->customcode, route('login'),
            $company->name, $add->country, $add->state, $add->city, $add->address_street_1,
            $add->address_street_2, $add->phone, $add->zip, $add->state_code, $company->name,

        ];

        return str_replace($search, $replace, $text);

    }

    public function getFinalSubject(Company $company, User $user): string
    {
        $subject = strip_tags($company->settings()->where('option', '=', $this->optionSubject)->first()->value ?? $this->defaultSubject);

        return $this->replace($company, $user, $subject);

    }

    public function getData(Company $company)
    {
        return $company->general_email_setting;
    }
}
