<?php

namespace Crater\Traits;

//models

use Crater\Mail\PbxServicesMail;
use Crater\Mail\ServicesMail;
use Crater\Models\Address;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Contacts;
use Crater\Models\EmailLog;
//
use Crater\Models\PbxTenant;
use Crater\Models\User;
use Illuminate\Support\Facades\Log;
use Mail;
use Request;

trait SendEmailsTrait
{
    protected $connection = null;

    protected $table = null;

    public function getPrimaryColor($company_id = null)
    {
        if (isset($company_id)) {
            $colorInvoice = CompanySetting::getSetting('color_invoice', $company_id);

            return $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';

        }

    }

    //
    public function sendEmailToCustomer($customer_id = 1, $company_id = 1, $type = '', $subject = '', $data = null, $corePbx = true)
    {
        // Validation seetings send emails
        $settingSendMail = CompanySetting::select('value', 'id')->where('option', 'send_email_deactive')->where('company_id', $company_id)->get();

        if ($settingSendMail != null && count($settingSendMail) > 0) {
            if ($settingSendMail[0]->value === 'YES' || $settingSendMail[0]->value === true) {
                return;
            }
        }
        // definir objetos
        $emailObject = new \stdClass();
        $emailLog = [];
        // consultar customer
        $customer = User::findOrFail($customer_id);
        $company = Company::where("id", $company_id)->first();
        // $superadmin = User::where("role", "super admin")->first();

        $add = Address::whereNULL("user_id")
            ->join("countries", "countries.id", "=", "addresses.country_id")
            ->join("states", "states.id", "=", "addresses.state_id")
            ->select("countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
            ->first();

        if ($add == null) {
            $add = Address::first()
                ->join("countries", "countries.id", "=", "addresses.country_id")
                ->join("states", "states.id", "=", "addresses.state_id")
                ->select("countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
                ->first();
        }

        // definir variable para buscar cuerpo del correo correspondiente dependiendo del tipo de correo a enviar
        if ($type == 'create') {
            // consultar cuerpo del correo
            $settings = CompanySetting::select('value', 'id')->where('option', 'pbx_creation_services')->where('company_id', $company_id)->first();
            $title = CompanySetting::select('value', 'id')->where('option', 'pbx_creation_services_subject')->where('company_id', $company_id)->first();

            $newTitle = "Service Activation";
            if ($title != null) {
                $newTitle = $title->value;
            }

            $newMessage = "Service Activation";
            if ($settings != null) {
                $newMessage = $settings->value;
            }
            // sustituir
            // $newMessage = str_replace("{contact.first_name}", $customer->name, $message);
            // $newMessage = str_replace("{package.email_html}", '', $newMessage);

        }
        if ($type == 'suspend' && $data != null) {
            $settings = CompanySetting::select('value', 'id')->where('option', 'pbx_suspension_services')->where('company_id', $company_id)->first();
            $title = CompanySetting::select('value', 'id')->where('option', 'pbx_suspension_services_subject')->where('company_id', $company_id)->first();

            $newTitle = "Service Activation";
            if ($title != null) {
                $newTitle = $title->value;
            }

            $newMessage = "Service Activation";
            if ($settings != null) {
                $newMessage = $settings->value;
            }

            // $newMessage = str_replace("{contact.first_name}", $customer->name, $message);
        }
        if ($type == 'cancel' && $data != null) {
            $settings = CompanySetting::select('value', 'id')->where('option', 'pbx_cancellation_services')->where('company_id', $company_id)->first();
            $title = CompanySetting::select('value', 'id')->where('option', 'pbx_cancellation_services_subject')->where('company_id', $company_id)->first();

            $newTitle = "Service Activation";
            if ($title != null) {
                $newTitle = $title->value;
            }

            $newMessage = "Service Activation";
            if ($settings != null) {
                $newMessage = $settings->value;
            }

            // $newMessage = str_replace("{contact.first_name}", $customer->name, $message);
        }
        if (($type == 'suspend' || $type == 'cancel') && $data != null) {
            if ($corePbx) {
                $newMessage = str_replace("{package.name}", $data['pbx_package']['pbx_package_name'], $newMessage);
                $newMessage = str_replace("{service.name}", $data['pbx_services_number'], $newMessage);

            } else {
                $name = $data['packageCustomer'] ? $data['packageCustomer']['package']['name'] : $data['package']['name'];
                $number = $data['packageCustomer'] ? $data['packageCustomer']['package']['package_number'] : $data['package']['package_number'];

                $newMessage = str_replace("{package.name}", $name, $newMessage);
                $newMessage = str_replace("{service.name}", $number, $newMessage);
            }

        }
        if ($type == 'reactivation') {
            // consultar cuerpo del correo
            $settings = CompanySetting::select('value', 'id')->where('option', 'pbx_reactivation_services')->where('company_id', $company_id)->first();
            $title = CompanySetting::select('value', 'id')->where('option', 'pbx_reactivation_services_subject')->where('company_id', $company_id)->first();
            $newTitle = "Service Activation";
            if ($title != null) {
                $newTitle = $title->value;
            }

            $newMessage = "Service Activation";
            if ($settings != null) {
                $newMessage = $settings->value;
            }
        }

        $array = [];
        $array["PRIMARY_CONTACT_NAME"] = $customer->name;
        $array["PRIMARY_COLOR"] = $this->getPrimaryColor($company_id);
        $array["CONTACT_DISPLAY_NAME"] = $customer->contact_name;
        $array["CONTACT_EMAIL"] = $customer->email;
        $array["CONTACT_USERNAME"] = $customer->customer_username;
        if ($array["CONTACT_USERNAME"] == "" || $array["CONTACT_USERNAME"] == null) {
            $array["CONTACT_USERNAME"] = $array["CONTACT_EMAIL"];
        }
        $array["CONTACT_PHONE"] = $customer->phone;
        $array["CONTACT_WEBSITE"] = $customer->website;
        $array["CONTACT_ROLE"] = $customer->role;
        $array["COMPANY_NAME"] = $company->name;
        $array["CONTACT_BALANCE"] = number_format($customer->balance, 2, '.', '');
        $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"] = $customer->email;
        $array["CONTACT_AUTO_REPLENISH_AMOUNT"] = $company->auto_replenish_amount;
        $array["CONTACT_CUSTOM_CODE"] = $company->customcode;
        $array["CONTACT_STATUS_CUSTOMER"] = $company->status_payment;
        $array["CONTACT_MINIMUN_BALANCE"] = $company->minimun_balance;
        $array["COMPANY_COUNTRY"] = $add->country;
        $array["COMPANY_STATE"] = $add->state;
        $array["STATE_CODE"] = $add->state_code;
        $array["COMPANY_CITY"] = $add->city;
        $array["COMPANY_ADDRESS_STREET_1"] = $add->address_street_1;
        $array["COMPANY_ADDRESS_STREET_2"] = $add->address_street_2;
        $array["COMPANY_PHONE"] = $add->phone;
        $array["COMPANY_ZIP_CODE"] = $add->zip;
        $array["PACKAGES_PRICE"] = $add->pbxpackages_price;
        $array["CUSTOMER_LOGIN"] = Request::root().'/account';

        $arrayStatus = ['S' => 'Suspended', 'C' => 'Canceled', 'A' => 'Active'];
        $array["SERVICES_STATUS"] = 'Active';

        if (isset($data)) {
            //Log::debug($data['term']);
            // consultar la tabla pbx tenant donde pbx tenand id == id si el resultado es diferente de null
            // SERVICE_PBX_TENANT_CODE se le debe asignar el campo code del objeto
            // SERVICE_PBX_TENANT_NAME se le debe asignar el campo name del objeto
            // consultar el tenant con el $data->pbx_tenant_id

            $tenant = PbxTenant::where('id', $data->pbx_tenant_id)->first();

            $tenantName = '';
            $tenantCode = '';

            if ($tenant != null) {
                //Log::debug('196');
                $tenantName = $tenant->name;
                $tenantCode = $tenant->code;
            }

            $array["SERVICES_TERM"] = $data->term || '';
            $array["SERVICES_DATE_BEGIN"] = $data->date_begin || '';
            $array["SERVICES_RENEWAL_DATE"] = $data->renewal_date || '';
            $array["SERVICES_AUTO_SUSPENSION"] = ($data->auto_suspension == 0) ? 'No' : 'Yes';
            $array["SERVICES_ALLOW_VALUE"] = $data->allow_discount_value;
            $array["SERVICES_TIME_PERIOD_VALUE"] = $data->time_period_value;
            // $array["SERVICE_PBX_TENANT_ID"] = $data->pbx_tenant_id;
            $array["SERVICE_PBX_TENANT_CODE"] = $tenantCode;
            $array["SERVICE_PBX_TENANT_NAME"] = $tenantName;
            $array["SERVICE_PBX_SERVICES_NUMBER"] = $data->pbx_services_number;
            $array["SERVICE_ALLOW_DISCOUNT"] = ($data->allow_discount == 0) ? 'No' : 'Yes';
            $array["SERVICE_PBX_PACKAGES_PRICE"] = $data->pbxpackages_price;
            $array["SERVICE_TOTAL"] = $data->total;
        }
        // $array["CONTACT_PASSWORD"] = !is_null($customer->password_encrypted) ? \Crypt::decryptString($customer->password_encrypted) : null;
        $newMessage = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $newMessage);
        $newMessage = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $newMessage);
        $newMessage = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $newMessage);
        $newMessage = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $newMessage);
        $newMessage = str_replace("{CONTACT_USERNAME}", $array["CONTACT_USERNAME"], $newMessage);
        $newMessage = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $newMessage);
        $newMessage = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $newMessage);
        $newMessage = str_replace("{CONTACT_ROLE}", $array["CONTACT_ROLE"], $newMessage);
        $newMessage = str_replace("{CONTACT_BALANCE}", $array["CONTACT_BALANCE"], $newMessage);
        $newMessage = str_replace("{CONTACT_STATUS_CUSTOMER}", $array["CONTACT_STATUS_CUSTOMER"], $newMessage);
        $newMessage = str_replace("{CONTACT_MINIMUN_BALANCE}", $array["CONTACT_MINIMUN_BALANCE"], $newMessage);
        $newMessage = str_replace("{CONTACT_CUSTOM_CODE}", $array["CONTACT_CUSTOM_CODE"], $newMessage);
        $newMessage = str_replace("{CONTACT_AUTO_REPLENISH_AMOUNT}", $array["CONTACT_AUTO_REPLENISH_AMOUNT"], $newMessage);
        $newMessage = str_replace("{CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}", $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"], $newMessage);
        $newMessage = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $newMessage);
        $newMessage = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $newMessage);
        $newMessage = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $newMessage);
        $newMessage = str_replace("{STATE_CODE}", $array["STATE_CODE"], $newMessage);
        $newMessage = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $newMessage);
        // $newMessage = str_replace("{CONTACT_PASSWORD}", $array["CONTACT_PASSWORD"], $newMessage);
        $newMessage = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $newMessage);
        $newMessage = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $newMessage);
        $newMessage = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $newMessage);
        $newMessage = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $newMessage);
        $newMessage = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $newMessage);
        $newMessage = str_replace("{SERVICE_STATUS}", $array["SERVICES_STATUS"], $newMessage);
        $newMessage = str_replace("{SERVICE_TERM}", $array["SERVICES_TERM"], $newMessage);
        $newMessage = str_replace("{SERVICE_AUTO_SUSPENSION}", $array["SERVICES_AUTO_SUSPENSION"], $newMessage);
        $newMessage = str_replace("{SERVICE_ALLOW_VALUE}", $array["SERVICES_ALLOW_VALUE"], $newMessage);
        $newMessage = str_replace("{SERVICE_RENEWAL_DATE}", $array["SERVICES_RENEWAL_DATE"], $newMessage);
        $newMessage = str_replace("{SERVICE_AUTO_SUSPENSION}", $array["SERVICES_AUTO_SUSPENSION"], $newMessage);
        $newMessage = str_replace("{SERVICE_TIME_PERIOD_VALUE}", $array["SERVICES_TIME_PERIOD_VALUE"], $newMessage);
        // $newMessage = str_replace("{SERVICE_PBX_TENANT_ID}", $array["SERVICES_PBX_TENANT_ID"], $newMessage);
        $newMessage = str_replace("{SERVICE_PBX_TENANT_CODE}", $array["SERVICE_PBX_TENANT_CODE"], $newMessage);
        $newMessage = str_replace("{SERVICE_PBX_TENANT_NAME}", $array["SERVICE_PBX_TENANT_NAME"], $newMessage);
        $newMessage = str_replace("{SERVICE_PBX_SERVICES_NUMBER}", $array["SERVICE_PBX_SERVICES_NUMBER"], $newMessage);
        $newMessage = str_replace("{SERVICE_DATE_BEGIN}", $array["SERVICES_DATE_BEGIN"], $newMessage);
        $newMessage = str_replace("{SERVICE_ALLOW_DISCOUNT}", $array["SERVICE_ALLOW_DISCOUNT"], $newMessage);
        $newMessage = str_replace("{SERVICE_PBX_PACKAGES_PRICE}", $array["SERVICE_PBX_PACKAGES_PRICE"], $newMessage);
        $newMessage = str_replace("{SERVICE_TOTAL}", $array["SERVICE_TOTAL"], $newMessage);
        $newMessage = str_replace("{PACKAGES_PRICE}", $array["PACKAGES_PRICE"], $newMessage);

        // SUBJECTS

        // $array["CONTACT_PASSWORD"] = !is_null($customer->password_encrypted) ? \Crypt::decryptString($customer->password_encrypted) : null;
        $newTitle = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $newTitle);
        $newTitle = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $newTitle);
        $newTitle = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $newTitle);
        $newTitle = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $newTitle);
        $newTitle = str_replace("{CONTACT_USERNAME}", $array["CONTACT_USERNAME"], $newTitle);
        $newTitle = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $newTitle);
        $newTitle = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $newTitle);
        $newTitle = str_replace("{CONTACT_ROLE}", $array["CONTACT_ROLE"], $newTitle);
        $newTitle = str_replace("{CONTACT_BALANCE}", $array["CONTACT_BALANCE"], $newTitle);
        $newTitle = str_replace("{CONTACT_STATUS_CUSTOMER}", $array["CONTACT_STATUS_CUSTOMER"], $newTitle);
        $newTitle = str_replace("{CONTACT_MINIMUN_BALANCE}", $array["CONTACT_MINIMUN_BALANCE"], $newTitle);
        $newTitle = str_replace("{CONTACT_CUSTOM_CODE}", $array["CONTACT_CUSTOM_CODE"], $newTitle);
        $newTitle = str_replace("{CONTACT_AUTO_REPLENISH_AMOUNT}", $array["CONTACT_AUTO_REPLENISH_AMOUNT"], $newTitle);
        $newTitle = str_replace("{CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}", $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"], $newTitle);
        $newTitle = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $newTitle);
        $newTitle = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $newTitle);
        $newTitle = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $newTitle);
        $newTitle = str_replace("{STATE_CODE}", $array["STATE_CODE"], $newTitle);
        $newTitle = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $newTitle);
        // $newTitle = str_replace("{CONTACT_PASSWORD}", $array["CONTACT_PASSWORD"], $newTitle);
        $newTitle = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $newTitle);
        $newTitle = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $newTitle);
        $newTitle = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $newTitle);
        $newTitle = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $newTitle);
        $newTitle = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $newTitle);
        $newTitle = str_replace("{SERVICE_STATUS}", $array["SERVICES_STATUS"], $newTitle);
        $newTitle = str_replace("{SERVICE_TERM}", $array["SERVICES_TERM"], $newTitle);
        $newTitle = str_replace("{SERVICE_AUTO_SUSPENSION}", $array["SERVICES_AUTO_SUSPENSION"], $newTitle);
        $newTitle = str_replace("{SERVICE_ALLOW_VALUE}", $array["SERVICES_ALLOW_VALUE"], $newTitle);
        $newTitle = str_replace("{SERVICE_RENEWAL_DATE}", $array["SERVICES_RENEWAL_DATE"], $newTitle);
        $newTitle = str_replace("{SERVICE_AUTO_SUSPENSION}", $array["SERVICES_AUTO_SUSPENSION"], $newTitle);
        $newTitle = str_replace("{SERVICE_TIME_PERIOD_VALUE}", $array["SERVICES_TIME_PERIOD_VALUE"], $newTitle);
        // $newTitle = str_replace("{SERVICE_PBX_TENANT_ID}", $array["SERVICES_PBX_TENANT_ID"], $newTitle);
        $newTitle = str_replace("{SERVICE_PBX_TENANT_CODE}", $array["SERVICE_PBX_TENANT_CODE"], $newTitle);
        $newTitle = str_replace("{SERVICE_PBX_TENANT_NAME}", $array["SERVICE_PBX_TENANT_NAME"], $newTitle);
        $newTitle = str_replace("{SERVICE_PBX_SERVICES_NUMBER}", $array["SERVICE_PBX_SERVICES_NUMBER"], $newTitle);
        $newTitle = str_replace("{SERVICE_DATE_BEGIN}", $array["SERVICES_DATE_BEGIN"], $newTitle);
        $newTitle = str_replace("{SERVICE_ALLOW_DISCOUNT}", $array["SERVICE_ALLOW_DISCOUNT"], $newTitle);
        $newTitle = str_replace("{SERVICE_PBX_PACKAGES_PRICE}", $array["SERVICE_PBX_PACKAGES_PRICE"], $newTitle);
        $newTitle = str_replace("{SERVICE_TOTAL}", $array["SERVICE_TOTAL"], $newTitle);
        $newTitle = str_replace("{PACKAGES_PRICE}", $array["PACKAGES_PRICE"], $newTitle);

        if ($type == '') {
            return;
        }

        // armar objeto
        $emailObject->subject = $this->removeAttributesHtml($newTitle);
        $emailObject->message = $newMessage;
        $emailObject->to = $customer->email;
        // $emailObject->to = 'zseijas.personal@gmail.com';
        $dataCompany['company'] = $company;
        $dataCompany['PRIMARY_COLOR'] = $this->getPrimaryColor($company_id);
        $mailable_id = $settings->id;

        // enviar email
        try {
            Mail::to($emailObject->to)->send(new PbxServicesMail($emailObject->subject, $emailObject->message, $corePbx, $dataCompany));
        } catch (\Throwable $th) {
            //Log::debug($th);
        }

        //bbc copy
        $bbcmail = CompanySetting::select('value', 'id')->where('option', 'pbx_service_bbc_email')->where('company_id', $company_id)->first();

        if ($bbcmail != null) {
            if ($bbcmail->value != "") {
                try {
                    Mail::to($bbcmail->value)->send(new PbxServicesMail($emailObject->subject, $emailObject->message, $corePbx, $dataCompany));
                } catch (\Throwable $th) {
                    Log::error($th);
                }

            }

        }
        // enviar correo a usuarios
        $listUser = User::where('role', 'super admin')->where('email_pbx_services', 1)->where('company_id', $company_id)->get();

        foreach ($listUser as $user) {
            try {
                Mail::to($user->email)->send(new ServicesMail($emailObject->subject, $emailObject->message, $dataCompany));
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
        // save emails logs
        $this->saveEmailLog($customer->email, $newTitle, $newMessage, $mailable_id, $company_id, $customer_id);

        // envio de contactos
        $contacts = Contacts::select('email')->where('customer_id', $customer->id)->where('allow_receive_emails', 1)->where('email_pbx_services', 1)->get();
        foreach ($contacts as $key => $email) {
            if ($email != null && $email != '') {
                // enviar email
                try {
                    Mail::to($email)->send(new PbxServicesMail($emailObject->subject, $emailObject->message, $corePbx, $dataCompany));
                } catch (\Throwable $th) {
                    Log::error($th);
                }
                // save emails logs
                $this->saveEmailLog($email, $newTitle, $newMessage, $mailable_id, $company_id, $customer_id);
            }
        }

        return;
    }

    public function sendEmailServices($customer_id = 1, $company_id = 1, $type = '', $subject = '', $data = null)
    {
        // Validation seetings send emails
        $settingSendMail = CompanySetting::select('value', 'id')->where('option', 'send_email_deactive')->where('company_id', $company_id)->get();
        if ($settingSendMail != null && count($settingSendMail) > 0) {
            if ($settingSendMail[0]->value === 'NO' || $settingSendMail[0]->value === false) {
                return;
            }

        }

        // definir objeto
        $emailObject = new \stdClass();
        // consultar customer
        $customer = User::findOrFail($customer_id);
        $company = Company::where("id", $company_id)->first();
        // $superadmin = User::where("role", "super admin")->first();

        $add = Address::whereNULL("user_id")
            ->join("countries", "countries.id", "=", "addresses.country_id")
            ->join("states", "states.id", "=", "addresses.state_id")
            ->select("countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
            ->first();

        if ($add == null) {
            $add = Address::first()
                ->join("countries", "countries.id", "=", "addresses.country_id")
                ->join("states", "states.id", "=", "addresses.state_id")
                ->select("countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2")
                ->first();
        }

        // definir variable para buscar cuerpo del correo correspondiente dependiendo del tipo de correo a enviar
        if ($type == 'create') {
            // consultar cuerpo del correo
            $settings = CompanySetting::select('value', 'id')->where('option', 'creation_services')->where('company_id', $company_id)->first();
            $title = CompanySetting::select('value', 'id')->where('option', 'creation_services_subject')->where('company_id', $company_id)->first();

            $newTitle = "Service Activation";
            if ($title != null) {
                $newTitle = $title->value;
            }

            $newMessage = "Service Activation";
            if ($settings != null) {
                $newMessage = $settings->value;
            }
            // sustituir
            // $newMessage = str_replace("{contact.first_name}", $customer->name, $message);
            // $newMessage = str_replace("{package.email_html}", '', $newMessage);

        }
        if ($type == 'suspend' && $data != null) {
            $settings = CompanySetting::select('value', 'id')->where('option', 'suspension_services')->where('company_id', $company_id)->first();
            $title = CompanySetting::select('value', 'id')->where('option', 'creation_services_subject')->where('company_id', $company_id)->first();

            $newTitle = "Service Activation";
            if ($title != null) {

                $newTitle = $title->value;
            }

            $newMessage = "Service Activation";
            if ($settings != null) {
                $newMessage = $settings->value;
            }
            // $newMessage = str_replace("{contact.first_name}", $customer->name, $message);
        }
        if ($type == 'cancel' && $data != null) {
            $settings = CompanySetting::select('value', 'id')->where('option', 'cancellation_services')->where('company_id', $company_id)->first();
            $title = CompanySetting::select('value', 'id')->where('option', 'creation_services_subject')->where('company_id', $company_id)->first();

            $newTitle = "Service Activation";
            if ($title != null) {

                $newTitle = $title->value;
            }

            $newMessage = "Service Activation";
            if ($settings != null) {
                $newMessage = $settings->value;
            }
            // $newMessage = str_replace("{contact.first_name}", $customer->name, $message);
        }
        if (($type == 'suspend' || $type == 'cancel') && $data != null) {

            $name = $data['packageCustomer'] ? $data['packageCustomer']['package']['name'] : $data['package']['name'];
            $number = $data['packageCustomer'] ? $data['packageCustomer']['package']['package_number'] : $data['package']['package_number'];

            // $newMessage = str_replace("{package.name}", $name, $newMessage);
            // $newMessage = str_replace("{service.name}", $number, $newMessage);

        }
        if ($type == 'reactivation') {
            // consultar cuerpo del correo
            $settings = CompanySetting::select('value', 'id')->where('option', 'reactivation_services')->where('company_id', $company_id)->first();
            $title = CompanySetting::select('value', 'id')->where('option', 'creation_services_subject')->where('company_id', $company_id)->first();

            $newTitle = "Service Activation";
            if ($title != null) {

                $newTitle = $title->value;
            }

            $newMessage = "Service Activation";
            if ($settings != null) {
                $newMessage = $settings->value;
            }
        }

        $array = [];
        $array["PRIMARY_CONTACT_NAME"] = $customer->name;
        $array["PRIMARY_COLOR"] = $this->getPrimaryColor($company_id);
        $array["CONTACT_DISPLAY_NAME"] = $customer->contact_name;
        $array["CONTACT_EMAIL"] = $customer->email;
        $array["CONTACT_USERNAME"] = $customer->customer_username;
        if ($array["CONTACT_USERNAME"] == "" || $array["CONTACT_USERNAME"] == null) {
            $array["CONTACT_USERNAME"] = $array["CONTACT_EMAIL"];
        }
        $array["CONTACT_PHONE"] = $customer->phone;
        $array["CONTACT_WEBSITE"] = $customer->website;
        $array["CONTACT_ROLE"] = $customer->role;
        $array["COMPANY_NAME"] = $company->name;
        $array["CONTACT_BALANCE"] = number_format($customer->balance, 2, '.', '');
        $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"] = $customer->email;
        $array["CONTACT_AUTO_REPLENISH_AMOUNT"] = $company->auto_replenish_amount;
        $array["CONTACT_CUSTOM_CODE"] = $company->customcode;
        $array["CONTACT_STATUS_CUSTOMER"] = $company->status_payment;
        $array["CONTACT_MINIMUN_BALANCE"] = $company->minimun_balance;
        $array["COMPANY_COUNTRY"] = $add->country;
        $array["COMPANY_STATE"] = $add->state;
        $array["STATE_CODE"] = $add->state_code;
        $array["COMPANY_CITY"] = $add->city;
        $array["COMPANY_ADDRESS_STREET_1"] = $add->address_street_1;
        $array["COMPANY_ADDRESS_STREET_2"] = $add->address_street_2;
        $array["COMPANY_PHONE"] = $add->phone;
        $array["COMPANY_ZIP_CODE"] = $add->zip;
        $array["CUSTOMER_LOGIN"] = Request::root().'/login';

        $arrayStatus = ['S' => 'Suspended', 'C' => 'Canceled', 'A' => 'Active'];
        $array["SERVICES_STATUS"] = 'Active';

        //Log::debug("linea 372 ");
        if (isset($data)) {
            //Log::debug($data);
            $array["SERVICES_TERM"] = $data->term;
            $array["SERVICE_DATE_BEGIN"] = $data->activation_date;
            $array["SERVICES_RENEWAL_DATE"] = $data->renewal_date;
            $array["SERVICES_AUTO_SUSPENSION"] = ($data->auto_suspension == 0) ? 'No' : 'Yes';
            $array["SERVICES_ALLOW_VALUE"] = $data->allow_discount_value;
            $array["SERVICES_TIME_PERIOD_VALUE"] = $data->time_period_value;
            // $array["SERVICES_NUMBER"] = $data->code;
            $array["SERVICE_ALLOW_DISCOUNT"] = ($data->allow_discount == 0) ? 'No' : 'Yes';
            $array["SERVICE_TOTAL"] = $data->total;
        }
        //Log::debug("linea 385");
        //Log::debug($array);
        // Campos Body
        // $array["CONTACT_PASSWORD"] = !is_null($customer->password_encrypted) ? \Crypt::decryptString($customer->password_encrypted) : null;
        $newMessage = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $newMessage);
        $newMessage = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $newMessage);
        $newMessage = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $newMessage);
        $newMessage = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $newMessage);
        $newMessage = str_replace("{CONTACT_USERNAME}", $array["CONTACT_USERNAME"], $newMessage);
        $newMessage = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $newMessage);
        $newMessage = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $newMessage);
        $newMessage = str_replace("{CONTACT_ROLE}", $array["CONTACT_ROLE"], $newMessage);
        $newMessage = str_replace("{CONTACT_BALANCE}", $array["CONTACT_BALANCE"], $newMessage);
        $newMessage = str_replace("{CONTACT_STATUS_CUSTOMER}", $array["CONTACT_STATUS_CUSTOMER"], $newMessage);
        $newMessage = str_replace("{CONTACT_MINIMUN_BALANCE}", $array["CONTACT_MINIMUN_BALANCE"], $newMessage);
        $newMessage = str_replace("{CONTACT_CUSTOM_CODE}", $array["CONTACT_CUSTOM_CODE"], $newMessage);
        $newMessage = str_replace("{CONTACT_AUTO_REPLENISH_AMOUNT}", $array["CONTACT_AUTO_REPLENISH_AMOUNT"], $newMessage);
        $newMessage = str_replace("{CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}", $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"], $newMessage);
        $newMessage = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $newMessage);
        $newMessage = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $newMessage);
        $newMessage = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $newMessage);
        $newMessage = str_replace("{STATE_CODE}", $array["STATE_CODE"], $newMessage);
        $newMessage = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $newMessage);
        $newMessage = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $newMessage);
        $newMessage = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $newMessage);
        $newMessage = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $newMessage);
        $newMessage = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $newMessage);
        $newMessage = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $newMessage);
        $newMessage = str_replace("{SERVICE_STATUS}", $array["SERVICES_STATUS"], $newMessage);
        $newMessage = str_replace("{SERVICE_DATE_BEGIN}", $array["SERVICE_DATE_BEGIN"], $newMessage);
        // $newMessage = str_replace("{SERVICES_NUMBER}", $array["SERVICES_NUMBER"], $newMessage);
        $newMessage = str_replace("{SERVICE_TERM}", $array["SERVICES_TERM"], $newMessage);
        $newMessage = str_replace("{SERVICE_AUTO_SUSPENSION}", $array["SERVICES_AUTO_SUSPENSION"], $newMessage);
        $newMessage = str_replace("{SERVICE_ALLOW_VALUE}", $array["SERVICES_ALLOW_VALUE"], $newMessage);
        $newMessage = str_replace("{SERVICE_RENEWAL_DATE}", $array["SERVICES_RENEWAL_DATE"], $newMessage);
        $newMessage = str_replace("{SERVICE_AUTO_SUSPENSION}", $array["SERVICES_AUTO_SUSPENSION"], $newMessage);
        $newMessage = str_replace("{SERVICE_TIME_PERIOD_VALUE}", $array["SERVICES_TIME_PERIOD_VALUE"], $newMessage);
        $newMessage = str_replace("{SERVICE_ALLOW_DISCOUNT}", $array["SERVICE_ALLOW_DISCOUNT"], $newMessage);
        $newMessage = str_replace("{SERVICE_TOTAL}", $array["SERVICE_TOTAL"], $newMessage);

        //Log::debug("linea 421");
        //Log::debug($newMessage);
        //Log::debug("title str replace");
        // Campos Subject
        $newTitle = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $newTitle);
        $newTitle = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $newTitle);
        $newTitle = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $newTitle);
        $newTitle = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $newTitle);
        $newTitle = str_replace("{CONTACT_USERNAME}", $array["CONTACT_USERNAME"], $newTitle);
        $newTitle = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $newTitle);
        $newTitle = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $newTitle);
        $newTitle = str_replace("{CONTACT_ROLE}", $array["CONTACT_ROLE"], $newTitle);
        $newTitle = str_replace("{CONTACT_BALANCE}", $array["CONTACT_BALANCE"], $newTitle);
        $newTitle = str_replace("{CONTACT_STATUS_CUSTOMER}", $array["CONTACT_STATUS_CUSTOMER"], $newTitle);
        $newTitle = str_replace("{CONTACT_MINIMUN_BALANCE}", $array["CONTACT_MINIMUN_BALANCE"], $newTitle);
        $newTitle = str_replace("{CONTACT_CUSTOM_CODE}", $array["CONTACT_CUSTOM_CODE"], $newTitle);
        $newTitle = str_replace("{CONTACT_AUTO_REPLENISH_AMOUNT}", $array["CONTACT_AUTO_REPLENISH_AMOUNT"], $newTitle);
        $newTitle = str_replace("{CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}", $array["CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION"], $newTitle);
        $newTitle = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $newTitle);
        $newTitle = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $newTitle);
        $newTitle = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $newTitle);
        $newTitle = str_replace("{STATE_CODE}", $array["STATE_CODE"], $newTitle);
        $newTitle = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $newTitle);
        $newTitle = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $newTitle);
        $newTitle = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $newTitle);
        $newTitle = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $newTitle);
        $newTitle = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $newTitle);
        $newTitle = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $newTitle);
        $newTitle = str_replace("{SERVICE_STATUS}", $array["SERVICES_STATUS"], $newTitle);
        // $newTitle = str_replace("{SERVICES_NUMBER}", $array["SERVICES_NUMBER"], $newTitle);
        $newTitle = str_replace("{SERVICE_TERM}", $array["SERVICES_TERM"], $newTitle);
        $newTitle = str_replace("{SERVICE_AUTO_SUSPENSION}", $array["SERVICES_AUTO_SUSPENSION"], $newTitle);
        $newTitle = str_replace("{SERVICE_ALLOW_VALUE}", $array["SERVICES_ALLOW_VALUE"], $newTitle);
        $newTitle = str_replace("{SERVICE_RENEWAL_DATE}", $array["SERVICES_RENEWAL_DATE"], $newTitle);
        $newTitle = str_replace("{SERVICE_AUTO_SUSPENSION}", $array["SERVICES_AUTO_SUSPENSION"], $newTitle);
        $newTitle = str_replace("{SERVICE_TIME_PERIOD_VALUE}", $array["SERVICES_TIME_PERIOD_VALUE"], $newTitle);
        $newTitle = str_replace("{SERVICE_ALLOW_DISCOUNT}", $array["SERVICE_ALLOW_DISCOUNT"], $newTitle);
        $newTitle = str_replace("{SERVICE_TOTAL}", $array["SERVICE_TOTAL"], $newTitle);
        $newTitle = str_replace("{SERVICE_DATE_BEGIN}", $array["SERVICE_DATE_BEGIN"], $newTitle);

        if ($type == '') {
            return;
        }

        // armar objeto
        $emailObject->subject = $this->removeAttributesHtml($newTitle);
        $emailObject->message = $newMessage;
        $emailObject->to = $customer->email;
        //Log::debug($emailObject->subject);
        // $emailObject->to = 'zseijas.personal@gmail.com';
        $dataCompany['company'] = $company;
        $dataCompany['PRIMARY_COLOR'] = $this->getPrimaryColor($company_id);
        $mailable_id = $settings->id;

        // enviar email
        try {
            Mail::to($emailObject->to)->send(new ServicesMail($emailObject->subject, $emailObject->message, $dataCompany));
        } catch (\Throwable $th) {
            Log::error($th);
        }
        ////bbc copy
        $bbcmail = CompanySetting::select('value', 'id')->where('option', 'package_bbc_email')->where('company_id', $company_id)->first();

        if ($bbcmail != null) {
            if ($bbcmail->value != "") {
                try {
                    Mail::to($bbcmail->value)->send(new ServicesMail($emailObject->subject, $emailObject->message, $dataCompany));
                } catch (\Throwable $th) {
                    Log::error($th);
                }
            }

        }

        // envio de contactos
        $contacts = Contacts::select('email')->where('customer_id', $customer->id)->where('allow_receive_emails', 1)->where('email_services', 1)->get();
        foreach ($contacts as $key => $email) {
            if ($email != null && $email != '') {
                // enviar email
                try {
                    Mail::to($email)->send(new ServicesMail($emailObject->subject, $emailObject->message, $dataCompany));
                } catch (\Throwable $th) {
                    Log::error($th);
                }
                // save emails logs
                $this->saveEmailLog($email, $newTitle, $newMessage, $mailable_id, $company_id, $customer_id);
            }
        }

        // enviar correo a usuarios
        $listUser = User::where('role', 'super admin')->where('email_services', 1)->where('company_id', $company_id)->get();

        foreach ($listUser as $user) {
            try {
                Mail::to($user->email)->send(new ServicesMail($emailObject->subject, $emailObject->message, $dataCompany));
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }

        // save emails logs
        $this->saveEmailLog($customer->email, $newTitle, $newMessage, $mailable_id, $company_id, $customer_id);

        return;
    }

    public function removeAttributesHtml($string)
    {
        $temp = str_replace('<p>', '', $string);
        $temp = str_replace('</p>', '', $temp);
        $temp = str_replace('<strong>', '', $temp);
        $temp = str_replace('</strong>', '', $temp);
        $temp = str_replace('<em>', '', $temp);
        $temp = str_replace('</em>', '', $temp);
        $temp = str_replace('<s>', '', $temp);
        $temp = str_replace('</s>', '', $temp);
        $temp = str_replace('<u>', '', $temp);
        $temp = str_replace('</u>', '', $temp);
        $temp = str_replace('<code>', '', $temp);
        $temp = str_replace('</code>', '', $temp);
        $temp = str_replace('<h1>', '', $temp);
        $temp = str_replace('</h1>', '', $temp);
        $temp = str_replace('<h2>', '', $temp);
        $temp = str_replace('</h2>', '', $temp);
        $temp = str_replace('<h3>', '', $temp);
        $temp = str_replace('</h3>', '', $temp);
        $temp = str_replace('<ul>', '', $temp);
        $temp = str_replace('</ul>', '', $temp);
        $temp = str_replace('<li>', '', $temp);
        $temp = str_replace('</li>', '', $temp);
        $temp = str_replace('<ol>', '', $temp);
        $temp = str_replace('</ol>', '', $temp);
        $temp = str_replace('<blockquote>', '', $temp);
        $temp = str_replace('</blockquote>', '', $temp);
        $temp = str_replace('<pre>', '', $temp);
        $temp = str_replace('</pre>', '', $temp);

        return $temp;
    }

    public function saveEmailLog($customerEmail, $subject, $message, $mailable_id, $company_id, $customer_id)
    {
        // llenar objeto email log y almacenarlo
        $emailLog['from'] = 'service@careonecomm.com';
        $emailLog['to'] = $customerEmail;
        $emailLog['subject'] = $subject;
        $emailLog['body'] = $message;
        $emailLog['mailable_type'] = SendEmailsTrait::class; // 'Crater\Traits\SendEmailsTrait';
        $emailLog['mailable_id'] = $mailable_id;
        $emailLog['company_id'] = $company_id;
        $emailLog['creator_id'] = $customer_id;

        // $dataEmail[] = $emailLog;

        EmailLog::create($emailLog);

        return;
    }
}
