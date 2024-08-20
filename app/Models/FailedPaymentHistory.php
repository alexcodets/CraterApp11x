<?php

namespace Crater\Models;

use Carbon\Carbon;
use Crater\DataObject\AddressDO;
use Crater\Jobs\SendEmailJob;
use Crater\Traits\SendEmailsTrait;
use Exception;
// traits
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Log;
use Request;

class FailedPaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'failed_payment_history';

    protected $fillable = ['payment_gateway', 'transaction_number', 'date', 'amount', 'payment_number', 'customer_id', 'invoice_id', 'description', 'error_description', 'type'];

    //protected $appends = ['invoice_number'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /*
    public function getInvoiceNumberAttribute()
    {
        if($this->invoice)
        {
            return $this->invoice->invoice_number;
        }
    }
    */
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('customer')) {
            $query->whereCustomer($filters->get('customer'));
        }
        if ($filters->get('customerId')) {
            $query->whereCustomerId($filters->get('customerId'));
        }

        if ($filters->get('payment_gateway')) {
            $query->WherePaymentGateway($filters->get('payment_gateway'));
        }

        if ($filters->get('payment_number')) {
            $query->wherePaymentNumber($filters->get('payment_number'));
        }

        if ($filters->get('invoice_number')) {
            $query->whereInvoiceNumber($filters->get('invoice_number'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->logsBetween($start, $end);
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'name';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->where(function ($query) use ($term) {
                $query->where('payment_number', 'LIKE', '%'.$term.'%')
                    ->orWhere('created_at', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeWhereCustomer($query, $customer)
    {
        $query->where('users.name', 'LIKE', '%'.$customer.'%')
            ->orWhere('users.customcode', 'LIKE', '%'.$customer.'%');
    }

    public function scopeWhereCustomerId($query, $customerId)
    {
        $query->where('customer_id',  $customerId);
    }

    public function scopeWherePaymentNumber($query, $name)
    {
        return $query->where('payment_number', 'LIKE', '%'.$name.'%');
    }

    public function scopeWhereInvoiceNumber($query, $invoice_number)
    {
        $query->whereHas('invoice', function ($query) use ($invoice_number) {
            $query->where('invoice_number', 'LIKE', '%'.$invoice_number.'%');
        });
    }

    public function scopeWherePaymentGateway($query, $name)
    {
        return $query->where('payment_gateway', 'LIKE',  $name);
    }

    public function scopeWhereDate($query, $name)
    {
        return $query->where('created_at', 'LIKE', '%'.$name.'%');
    }

    public function scopeLogsBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'date',
            [$start->format('Y-m-d'), $end->format('Y-m-d ')]
        );
    }

    public static function createFailedPaymentHistory($request)
    {
        $data = $request->validated();
        $failed_payment_history = FailedPaymentHistory::create($data);

        return $failed_payment_history;
    }

    public function paymentFailed($failed_payment)
    {
        Log::info("Esto es en log");
        Log::info("-------********************-----------");
        Log::info([ $failed_payment['customer_id'], "failed_payment_history"]);
        if ($failed_payment != null) {

            $customer = User::where("id", $failed_payment['customer_id'])->first();
            if ($customer != null) {
                if ($failed_payment['type_trasaction'] == 'ACH') {
                    $message = CompanySetting::where("company_id", $customer->company_id)->where("option", "payment_ach_declined")->first();
                    $newTitle = CompanySetting::where("company_id", $customer->company_id)->where("option", "payment_ach_declined_subject")->first();
                    $mode = "ACH";
                    $title = "ACH payment declined";

                    if($newTitle != null) {
                        $title = $newTitle->value;
                    }
                } else {
                    $message = CompanySetting::where("company_id", $customer->company_id)->where("option", "payment_credit_card_rejected")->first();
                    $newTitle = CompanySetting::where("company_id", $customer->company_id)->where("option", "payment_credit_card_rejected_subject")->first();
                    $mode = "Credit Card";
                    $title = "Payment with credit card rejected";

                    if($newTitle != null) {
                        $title = $newTitle->value;
                    }
                }

                if ($message != null) {

                    //Log::debug("entro aqui en message");
                    $superadmin = User::where("role", "super admin")->first();
                    $address = AddressDO::getAddress();
                    $company = Company::where("id", $customer->company_id)->first();

                    $newMessage = $message->value;
                    $array = [];

                    // COMPANY FIELD
                    $array["COMPANY_NAME"] = $company->name;
                    $array["COMPANY_COUNTRY"] = $address->country;
                    $array["COMPANY_STATE"] = $address->state;
                    $array["STATE_CODE"] = $address->state_code;
                    $array["COMPANY_CITY"] = $address->city;
                    $array["COMPANY_ADDRESS_STREET_1"] = $address->address_street_1;
                    $array["COMPANY_ADDRESS_STREET_2"] = $address->address_street_2;
                    $array["COMPANY_PHONE"] = $address->phone;
                    $array["COMPANY_ZIP_CODE"] = $address->zip;

                    // CUSTOMER FIELD
                    $array["PRIMARY_CONTACT_NAME"] = $customer->name;
                    $array["CONTACT_DISPLAY_NAME"] = $customer->contact_name;
                    $array["CONTACT_EMAIL"] = $customer->email;
                    $array["CONTACT_PHONE"] = $customer->phone;
                    $array["CONTACT_WEBSITE"] = $customer->website;

                    // PAYMENT FIELD
                    $array["PAYMENT_DATE"] = $failed_payment['date'];
                    $array["PAYMENT_NUMBER"] = $failed_payment['payment_number'];
                    $array["PAYMENT_MODE"] = $mode;
                    $array["PAYMENT_AMOUNT"] = ($failed_payment['amount'] / 100);

                    $array["CARD_NUMBER"] = "";
                    $array["CREDIT_CARD"] = "";
                    $array["EXPIRATION_DATE"] = "";
                    $array["CUSTOMER_LOGIN"] = Request::root().'/login';
                    $array["PAYMENT_LINK"] = "";
                    $array["TRANSACTION"] = "No Present";

                    // isset or null

                    if(isset($failed_payment['credit_card_number']) == true) {
                        if($failed_payment['credit_card_number'] != null) {
                            $array["CARD_NUMBER"] = $failed_payment['credit_card_number'];
                        }
                    }
                    //Log::debug('177');
                    if(isset($failed_payment['credit_card_type']) == true) {
                        if($failed_payment['credit_card_type'] != null) {
                            $array["CREDIT_CARD"] = $failed_payment['credit_card_type'];
                        }
                    }
                    //Log::debug('184');
                    if(isset($failed_payment['credit_card_expiration_date']) == true) {
                        if($failed_payment['credit_card_expiration_date'] != null) {
                            $array["EXPIRATION_DATE"] = $failed_payment['credit_card_expiration_date'];
                        }
                    }

                    //    EMAIL BODY
                    $newMessage = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $newMessage);
                    $newMessage = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $newMessage);
                    $newMessage = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $newMessage);
                    $newMessage = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $newMessage);
                    $newMessage = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $newMessage);
                    $newMessage = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $newMessage);
                    $newMessage = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $newMessage);
                    $newMessage = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $newMessage);
                    $newMessage = str_replace("{STATE_CODE}", $array["STATE_CODE"], $newMessage);
                    $newMessage = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $newMessage);
                    $newMessage = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $newMessage);
                    $newMessage = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $newMessage);
                    $newMessage = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $newMessage);
                    $newMessage = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $newMessage);
                    $newMessage = str_replace("{PAYMENT_DATE}", $array["PAYMENT_DATE"], $newMessage);
                    $newMessage = str_replace("{PAYMENT_NUMBER}", $array["PAYMENT_NUMBER"], $newMessage);
                    $newMessage = str_replace("{PAYMENT_MODE}", $array["PAYMENT_MODE"], $newMessage);
                    $newMessage = str_replace("{PAYMENT_AMOUNT}", $array["PAYMENT_AMOUNT"], $newMessage);

                    $newMessage = str_replace("{CARD_NUMBER}", $array["CARD_NUMBER"], $newMessage);
                    $newMessage = str_replace("{CREDIT_CARD}", $array["CREDIT_CARD"], $newMessage);
                    $newMessage = str_replace("{EXPIRATION_DATE}", $array["EXPIRATION_DATE"], $newMessage);
                    $newMessage = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $newMessage);
                    $newMessage = str_replace("{PAYMENT_LINK}", $array["PAYMENT_LINK"], $newMessage);
                    $newMessage = str_replace("{TRANSACTION}", $array["TRANSACTION"], $newMessage);

                    // EMAIL SUBJECT
                    $title = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $title);
                    $title = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $title);
                    $title = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $title);
                    $title = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $title);
                    $title = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $title);
                    $title = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $title);
                    $title = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $title);
                    $title = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $title);
                    $title = str_replace("{STATE_CODE}", $array["STATE_CODE"], $title);
                    $title = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $title);
                    $title = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $title);
                    $title = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $title);
                    $title = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $title);
                    $title = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $title);
                    $title = str_replace("{PAYMENT_DATE}", $array["PAYMENT_DATE"], $title);
                    $title = str_replace("{PAYMENT_NUMBER}", $array["PAYMENT_NUMBER"], $title);
                    $title = str_replace("{PAYMENT_MODE}", $array["PAYMENT_MODE"], $title);
                    $title = str_replace("{PAYMENT_AMOUNT}", $array["PAYMENT_AMOUNT"], $title);

                    $title = str_replace("{CARD_NUMBER}", $array["CARD_NUMBER"], $title);
                    $title = str_replace("{CREDIT_CARD}", $array["CREDIT_CARD"], $title);
                    //$title = str_replace("{EXPIRATION_DATE}", $array["EXPIRATION_DATE"], $title);
                    $title = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $title);
                    $title = str_replace("{PAYMENT_LINK}", $array["PAYMENT_LINK"], $title);
                    $title = str_replace("{TRANSACTION}", $array["TRANSACTION"], $title);


                    try {
                        $email = $array["CONTACT_EMAIL"];
                        $title = $this->removeAttributesHtml($title);
                        SendEmailJob::dispatch($title, $newMessage, $email);
                        // save emails logs
                        $mailable_id = $message->id;
                        $customer_id = \Auth::user()->id;
                        $emailTrait = new SendEmailsTrait();
                        $emailTrait->saveEmailLog($customer->email, $title, $newMessage, $mailable_id, $company->id, $customer->id);

                    } catch (Exception $ex) {
                        //Log::debug($ex);
                    }


                }
            }
        }

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
}
