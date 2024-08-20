<?php

namespace Crater\Http\Controllers\V1\Report;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\InvoiceItem;
use Crater\Models\Item;
use Crater\Models\itemCategories;
use Crater\Models\ItemGroup;
use Crater\Models\LogsDev;
use Crater\Models\State;
use Crater\Models\Unit;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use PDF;

class ItemSalesReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, $hash)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['hash' => $hash]);
        $log = LogsDev::initLog($request, "", "D", "ItemSalesReportController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company = Company::where('unique_hash', $hash)->first();

        $locale = CompanySetting::getSetting('language', $company->id);

        App::setLocale($locale);

        $items_name = null;
        $customers_name = null;
        $users_name = null;
        $units_name = null;
        $categories_name = null;
        $groups_name = null;

        $items_id = Item::applyFiltersReport($request->only([
                                'units_id',
                                'categories_id',
                                'groups_id',
                                'item',
                           ]))
                           ->pluck('id')->toarray();

        $list_paid_status = null;
        $paid_status_name = null;

        if ($request->get('paid_status')) {
            $list_paid_status = explode(',', $request->get('paid_status'));
            $cont = count($list_paid_status) - 1;
            foreach ($list_paid_status as $i => $name) {
                $paid_status_name = $paid_status_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        if($request->get('customers_id')) {
            $customers_id = explode(',', $request->get('customers_id'));
            $names = User::whereIN("id", $customers_id)->pluck("name")->toarray();
            $cont = count($names) - 1;
            foreach ($names as $i => $name) {
                $customers_name = $customers_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        if($request->get('users_id')) {
            $users_id = explode(',', $request->get('users_id'));
            $names = User::whereIN("id", $users_id)->pluck("name")->toarray();
            $cont = count($names) - 1;
            foreach ($names as $i => $name) {
                $users_name = $users_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        if($request->get('units_id')) {
            $units_id = explode(',', $request->get('units_id'));
            $names = Unit::whereIN("id", $units_id)->pluck("name")->toarray();
            $cont = count($names) - 1;
            foreach ($names as $i => $name) {
                $units_name = $units_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        if($request->get('categories_id')) {
            $categories_id = explode(',', $request->get('categories_id'));
            $names = itemCategories::whereIN("id", $categories_id)->pluck("name")->toarray();
            $cont = count($names) - 1;
            foreach ($names as $i => $name) {
                $categories_name = $categories_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        if($request->get('groups_id')) {
            $groups_id = explode(',', $request->get('groups_id'));
            $names = ItemGroup::whereIN("id", $groups_id)->pluck("name")->toarray();
            $cont = count($names) - 1;
            foreach ($names as $i => $name) {
                $groups_name = $groups_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        if ($request->get('item')) {
            $list = explode(',', $request->get('item'));
            $names = Item::whereIN("id", $list)->pluck("name")->toarray();
            $cont = count($names) - 1;
            foreach ($names as $i => $name) {
                $items_name = $items_name." ".$name.($cont != $i ? ' | ' : '');
            }
        }

        $items = InvoiceItem::select("*")
            ->whereCompany($company->id)
            ->applyInvoiceFilters($request->only(['from_date', 'to_date', 'paid_status', 'customers_id', 'users_id']))
            ->whereIN("item_id", $items_id)
            ->itemAttributes()
            ->get();

        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item->total_amount;
        }

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
            'items' => $items,
            'colorSettings' => $colorSettings,
            'totalAmount' => $totalAmount,
            'company' => $company,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'paid_status_name' => $paid_status_name,
            'customers_name' => $customers_name,
            'users_name' => $users_name,
            'units_name' => $units_name,
            'categories_name' => $categories_name,
            'groups_name' => $groups_name,
            'items_name' => $items_name,
        ]);
        $pdf = PDF::loadView('app.pdf.reports.sales-items');

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => true, "message" => "ItemSalesReport Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "ItemSalesReport   Payments");
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
