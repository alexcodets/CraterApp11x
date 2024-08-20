<?php

namespace Crater\Http\Controllers\V1\Report;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Address;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Country;
use Crater\Models\Currency;
use Crater\Models\LogsDev;
use Crater\Models\State;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Log;
use PDF;

class CustomerSalesReportController extends Controller
{
    public function __invoke(Request $request, string $hash)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomerSalesReportController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company = Company::where('unique_hash', $hash)->first();
        $locale = CompanySetting::getSetting('language', $company->id);
        App::setLocale($locale);

        $start = Carbon::createFromFormat('Y-m-d', $request->from_date);
        $end = Carbon::createFromFormat('Y-m-d', $request->to_date);
        $userIds = User::customer()->pluck("id")->toarray();

        ///busca ppor cliente
        $listCustomers = null;
        if ($request->get('customer')) {
            $listCustomers = "";
            $list = explode(',', $request->get('customer'));
            $listName = User::whereIN("id", $list)->pluck("name")->toarray();
            $userIds = User::whereIN("id", $list)->customer()->pluck("id")->toarray();
            $userIds = array_unique($userIds);
            $cont = count($listName) - 1;
            foreach ($listName as $i => $lname) {
                $listCustomers = $listCustomers." ".$lname.($cont != $i ? ' | ' : '');
            }
        }

        $users_name = null;
        if($request->get('users_id')) {
            $users_id = explode(',', $request->get('users_id'));
            $names = User::whereIN("id", $users_id)->pluck("name")->toarray();
            $cont = count($names) - 1;
            foreach ($names as $i => $name) {
                $users_name = $users_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        $existValueAll = null;
        $existPaidStatus = false;
        $list_paid_status = null;
        $paid_status_name = null;

        if ($request->get('paid_status')) {
            $existPaidStatus = true;
            $list_paid_status = explode(',', $request->get('paid_status'));
            $cont = count($list_paid_status) - 1;
            foreach ($list_paid_status as $i => $name) {
                if($name == "ALL") {
                    $existValueAll = true;
                }
                $paid_status_name = $paid_status_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        //busca por pais
        $countryName = null;
        if ($request->get('country')) {
            $countryob = Country::where("id", $request->get('country'))->first();
            if ($countryob != null) {
                $countryName = $countryob->name;
            }
            $userIds = Address::where("country_id", $request->get('country'))->whereIN("user_id", $userIds)->pluck("user_id")->toarray();
            $userIds = array_unique($userIds);
        }

        //busca por stado
        $stateName = null;
        if ($request->get('state')) {
            $countryob = State::where("id", $request->get('state'))->first();
            if ($countryob != null) {
                $stateName = $countryob->name;
            }
            $userIds = Address::where("state_id", $request->get('state'))->whereIN("user_id", $userIds)->pluck("user_id")->toarray();
            $userIds = array_unique($userIds);
        }

        $customers = User::query()
            ->customer()
            ->whereIN("users.id", $userIds)
            ->whereCompany($company->id)
            ->applyInvoiceFilters($request->only(['from_date', 'to_date', 'users_id']))
            ->with([
                'companySettings' => function ($query) {
                    $query->where('option', 'carbon_date_format');
                    $query->select('id', 'company_id', 'value');
                }
            ])
            ->with([
                'invoices' => function ($query) use ($start, $end, $list_paid_status, $existPaidStatus, $existValueAll) {
                    $query->select('id', 'total', 'invoice_date', 'user_id', 'invoice_number', 'user_id', "status", "paid_status");
                    $query->where("status", "!=", "SAVE_DRAFT");
                    $query->whereBetween(
                        'invoice_date',
                        [$start->format('Y-m-d'), $end->format('Y-m-d')]
                    );
                    if($existPaidStatus) {
                        $existValueAll == true
                            ? $query->where('paid_status', "!=", null)
                            : $query->whereIn('paid_status', $list_paid_status);
                    }
                }
            ])
            ->select(['id', 'name', 'company_id'])
            ->withSum(
                [
                'invoices' => function ($query) use ($start, $end, $list_paid_status, $existPaidStatus, $existValueAll) {
                    $query->where("status", "!=", "SAVE_DRAFT");
                    $query->whereBetween(
                        'invoice_date',
                        [$start->format('Y-m-d'), $end->format('Y-m-d')]
                    );
                    if($existPaidStatus) {
                        $existValueAll == true
                            ? $query->where('paid_status', "!=", null)
                            : $query->whereIn('paid_status', $list_paid_status);
                    }
                }
            ],
                'total'
            )
            ->get();

        $totalAmount = $customers->sum('invoices_sum_total');

        $dateFormat = CompanySetting::getSetting('carbon_date_format', $company->id);
        $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->format($dateFormat);
        $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->format($dateFormat);

        $colors = [
            'primary_text_color',
            'heading_text_color',
            'section_heading_text_color',
            'border_color',
            'body_text_color',
            'footer_text_color',
            'footer_total_color',
            'footer_bg_color',
            'date_text_color',
        ];

        $colorSettings = CompanySetting::whereIn('option', $colors)
            ->whereCompany($company->id)
            ->get();

        $infoCompany = $this->getTextInformationCompanyReports($company);

        view()->share([
            'info_company' => $infoCompany,
            'customers' => $customers,
            'totalAmount' => $totalAmount,
            'colorSettings' => $colorSettings,
            'company' => $company,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'listCustomers' => $listCustomers,
            'users_name' => $users_name,
            'countryname' => $countryName,
            'statename' => $stateName,
            'defaultCurrency' => Currency::findOrFail(CompanySetting::getSetting('currency', 1)),
            'paid_status_name' => $paid_status_name
        ]);

        $pdf = PDF::loadView('app.pdf.reports.sales-customers');
        //$pdf = PDF::loadView('app.pdf.reports.sales-customers_respaldo');


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => true, "message" => "CustomerSales Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "CustomerSales Payments");
        /////////////////////////////////////////

        if ($request->has('download')) {
            return $pdf->download();
        }

        return $pdf->stream();
    }

    public function getTextInformationCompanyReports($company)
    {

        $text = CompanySetting::where('option', 'company_report_info')->where('company_id', $company->id)->get();

        $user = Auth::user();

        $user->load([
            'addresses',
            'addresses.country',
            'company',
            'company.address',
            'company.address.country',
        ]);

        $keys = [
            '{COMPANY_NAME}',
            '{COMPANY_NUMBER}',
            '{COMPANY_COUNTRY}',
            '{COMPANY_STATE}',
            '{COMPANY_CITY}',
            '{COMPANY_ADDRESS_STREET_1}',
            '{COMPANY_ADDRESS_STREET_2}',
            '{COMPANY_PHONE}',
            '{COMPANY_ZIP_CODE}',
            '{STATE_CODE}'
        ];

        $state = State::where('id', $user->company->address->state_id)->first();
        Log::debug($state);
        $values = [
            $company->name,
            $company->company_identifier,
            $user->company->address->country->name,
            $state ? $state->name : 'N/A' ,
            $user->company->address->city,
            $user->company->address->address_street_1,
            $user->company->address->address_street_2,
            $user->company->address->phone,
            $user->company->address->zip_code,
            $state ? $state->code : 'N/A',
        ];

        if(! $text->isEmpty()) {
            return $this->removeAttributesHtml(str_replace($keys, $values, $text->first()->value));
        }

        return '';

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
