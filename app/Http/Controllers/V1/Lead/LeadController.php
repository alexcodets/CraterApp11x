<?php

namespace Crater\Http\Controllers\V1\Lead;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Mail\CustomerCreation;
use Crater\Models\Address;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Lead;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Log;
use Mail;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);

        $leads = Lead::applyFilters(
            $request->only([
                'company_name',
                'email',
                'status',
                'type',
                'customer_type',
                'phone',
                'last_contacted_date',
                'followup_date',
            ])
        )
            ->orderBy($request["orderByField"], $request["orderBy"])
            ->latest()
            ->paginateData($limit);

        return response()->json([
            'leads' => $leads,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $data = $request->all();
            $data['company_id'] = auth()->user()->company->id;
            $data['last_contacted_date'] = Carbon::now()->format('Y-m-d');
            Lead::create($data);

            return response()->json([
                'success' => true,
            ]);
        } catch (\throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        return response()->json([
            'lead' => $lead,
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        try {
            Log::debug($lead);
            Log::debug($request);
            $data = $request->all();
            $lead = Lead::where('id', $lead->id)->update($data);

            return response()->json([
                'success' => true,
                'message' => ' Lead created success',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        //
    }

    public function getCustomersLeads(Request $request)
    {

        $limit = $request->limit ?? 10;
        $fieldsLead = [
            'id',
            'customer_type',
            'type',
            'status',
            'company_name',
            'primary_contact_name',
            'email',
            'phone',
            'website',
            'first_name',
            'last_name',
            DB::raw("'lead' as type_from"),
        ];
        $fieldsCustomer = [
            'id',
            'customer_type',
            'status_payment AS type',
            DB::raw("'C' AS status"),
            'name AS company_name',
            'contact_name AS primary_contact_name',
            'email',
            'phone',
            'website',
            'first_name',
            'last_name',
            DB::raw("'customer' AS type_from"),
        ];

        $leads = Lead::select($fieldsLead)
            ->when($request->is_url, function ($query) use ($request) {
                return $query->orWhere('company_name', 'LIKE', '%'.$request->param.'%')
                    ->orWhere('first_name', 'LIKE', '%'.$request->param.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$request->param.'%')
                    ->orWhere('email', 'LIKE', '%'.$request->param.'%')
                    ->orWhere('phone', 'LIKE', '%'.$request->param.'%')
                    ->orWhere('primary_contact_name', 'LIKE', '%'.$request->param.'%')
                    ->orWhere('website', 'LIKE', '%'.$request->param.'%');

            });

        $customers = User::select($fieldsCustomer)
            ->where('role', 'customer')
            ->when($request->is_url, function ($query) use ($request) {
                return $query->where(function (Builder $query) use ($request) {
                    $query->orWhere('company_name', 'LIKE', '%'.$request->param.'%')
                        ->orWhere('first_name', 'LIKE', '%'.$request->param.'%')
                        ->orWhere('last_name', 'LIKE', '%'.$request->param.'%')
                        ->orWhere('email', 'LIKE', '%'.$request->param.'%')
                        ->orWhere('phone', 'LIKE', '%'.$request->param.'%')
                        ->orWhere('website', 'LIKE', '%'.$request->param.'%');
                });
            });
        switch ($request->type) {
            case 'customer':
                $data = $customers
                    ->orderBy($request->orderByField, $request->orderBy)
                    ->paginate($limit);

                break;

            case 'lead':
                $data = $leads
                    ->orderBy($request->orderByField, $request->orderBy)
                    ->paginate($limit);

                break;

            default:
                $data = $leads
                    ->union($customers)
                    ->orderBy($request->orderByField, $request->orderBy)
                    ->paginate($limit);

                break;
        }
        // $data = $leads
        // ->union($customers)
        //     ->orderBy($request->orderByField, $request->orderBy)
        //     ->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Endpoint for get all customer and leads',
            'data' => $data,
        ]);
    }

    public function sendemail(Request $request)
    {

        \Log::debug($request->input());
        $data = $request->input();

        $validator = Validator::make($data, [
            'from' => 'required',
            'to' => 'required',
            'subject' => 'required',
            'body' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => "Data no valid",
                'success' => false,
            ]);
        }

        $leadobject = Lead::where("id", $data["id"])->first();

        if ($leadobject == null) {
            return response()->json([
                'message' => "Data no found",
                'success' => false,
            ]);

        }

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

        $company = Company::where("id", $leadobject->company_id)->first();
        $array = [];
        $array["PRIMARY_COLOR"] = $this->getPrimaryColor($leadobject->company_id);
        $array["COMPANY_COUNTRY"] = $add->country;
        $array["COMPANY_STATE"] = $add->state;
        $array["STATE_CODE"] = $add->state_code;
        $array["COMPANY_NAME"] = $company->name;
        $array["COMPANY_CITY"] = $add->city;
        $array["COMPANY_ADDRESS_STREET_1"] = $add->address_street_1;
        $array["COMPANY_ADDRESS_STREET_2"] = $add->address_street_2;
        $array["COMPANY_PHONE"] = $add->phone;
        $array["COMPANY_ZIP_CODE"] = $add->zip;
        $array["CONTACT_PHONE"] = $leadobject->phone;
        $array["CONTACT_WEBSITE"] = $leadobject->website;
        $array["CONTACT_EMAIL"] = $leadobject->email;

        $array["COMPANY_NUMBER"] = "";
        $array["CUSTOMER_LOGIN"] = "";
        if ($leadobject->customer_type == "B") {
            $array["PRIMARY_CONTACT_NAME"] = $leadobject->company_name;
            $array["CONTACT_DISPLAY_NAME"] = $leadobject->primary_contact_name;
        } else {
            $array["PRIMARY_CONTACT_NAME"] = $leadobject->first_name." ".$leadobject->last_name;
            $array["CONTACT_DISPLAY_NAME"] = $leadobject->last_name;
        }

        $data['subject'] = $this->removeAttributesHtml($data['subject']);
        $data['company'] = $company;
        $data['PRIMARY_COLOR'] = $this->getPrimaryColor($leadobject->company_id);

        \Log::debug($array);
        // EMAIL BODY
        $data['body'] = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $data['body']);
        $data['body'] = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $data['body']);
        $data['body'] = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $data['body']);
        $data['body'] = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $data['body']);
        $data['body'] = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $data['body']);
        $data['body'] = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $data['body']);
        $data['body'] = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $data['body']);
        $data['body'] = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $data['body']);
        $data['body'] = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $data['body']);
        $data['body'] = str_replace("{STATE_CODE}", $array["STATE_CODE"], $data['body']);
        $data['body'] = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $data['body']);
        $data['body'] = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $data['body']);
        $data['body'] = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $data['body']);
        $data['body'] = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $data['body']);
        $data['body'] = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $data['body']);
        $data['body'] = str_replace("{COMPANY_NUMBER}", $array["COMPANY_NUMBER"], $data['body']);
        $data['body'] = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $data['body']);

        // EMAIL SUBJECT
        $data['subject'] = str_replace("{PRIMARY_CONTACT_NAME}", $array["PRIMARY_CONTACT_NAME"], $data['subject']);
        $data['subject'] = str_replace("{PRIMARY_COLOR}", $array["PRIMARY_COLOR"], $data['subject']);
        $data['subject'] = str_replace("{CONTACT_DISPLAY_NAME}", $array["CONTACT_DISPLAY_NAME"], $data['subject']);
        $data['subject'] = str_replace("{CONTACT_PHONE}", $array["CONTACT_PHONE"], $data['subject']);
        $data['subject'] = str_replace("{CONTACT_EMAIL}", $array["CONTACT_EMAIL"], $data['subject']);
        $data['subject'] = str_replace("{CONTACT_WEBSITE}", $array["CONTACT_WEBSITE"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_NAME}", $array["COMPANY_NAME"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_COUNTRY}", $array["COMPANY_COUNTRY"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_STATE}", $array["COMPANY_STATE"], $data['subject']);
        $data['subject'] = str_replace("{STATE_CODE}", $array["STATE_CODE"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_CITY}", $array["COMPANY_CITY"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_ADDRESS_STREET_1}", $array["COMPANY_ADDRESS_STREET_1"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_ADDRESS_STREET_2}", $array["COMPANY_ADDRESS_STREET_2"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_PHONE}", $array["COMPANY_PHONE"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_ZIP_CODE}", $array["COMPANY_ZIP_CODE"], $data['subject']);
        $data['subject'] = str_replace("{COMPANY_NUMBER}", $array["COMPANY_NUMBER"], $data['body']);
        $data['subject'] = str_replace("{CUSTOMER_LOGIN}", $array["CUSTOMER_LOGIN"], $data['body']);

        try {
            $data['subject'] = $this->removeAttributesHtml($data['subject']);
            \Log::debug($data['subject']);
            \Log::debug($data['body']);
            Mail::to($data['to'])->send(new CustomerCreation($data['subject'], $data['body'], $data));
            // save emails logs

            // $customer_id = \Auth::user()->id;
            //Log::debug('llegó-------');  frivero@careonecomm.com
            // $emailTrait = new SendEmailsTrait;
            // $this->saveEmailLog($customer->email, $subject = '', $data['body'], $mailable_id, $company->id, $customer->id);

        } catch (Exception $ex) {
            // jump to this part
            // if an exception occurred
        }

        return response()->json([
            'message' => "Credentials sended",
            'success' => true,
        ]);
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

    public function getPrimaryColor($company_id = null)
    {
        if (isset($company_id)) {
            $colorInvoice = CompanySetting::getSetting('color_invoice', $company_id);

            return $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
        }

    }
}
