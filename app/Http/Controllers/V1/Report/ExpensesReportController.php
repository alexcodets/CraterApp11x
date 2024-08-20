<?php

namespace Crater\Http\Controllers\V1\Report;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Expense;
use Crater\Models\ExpenseCategory;
use Crater\Models\Item;
use Crater\Models\LogsDev;
use Crater\Models\PaymentMethod;
use Crater\Models\Provider;
use Crater\Models\State;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDF;

class ExpensesReportController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "ExpensesReportController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company = Company::where('unique_hash', $hash)->first();

        $locale = CompanySetting::getSetting('language',  $company->id);

        App::setLocale($locale);

        $filters = $request->only([
            'provider',
            'customer',
            'payment_mode',
            'category',
            'status',
            'item',
        ]);
        $filters = collect($filters);

        $filters->put('provider', explode(',', $filters->get('provider')));
        $filters->put('customer', explode(',', $filters->get('customer')));
        $filters->put('payment_mode', explode(',', $filters->get('payment_mode')));
        $filters->put('category', explode(',', $filters->get('category')));
        $filters->put('item', explode(',', $filters->get('item')));

        $expenseCategories = Expense::with('category')
            ->where('company_id', $company->id)
            ->applyFilters($request->only([
                'from_date',
                'to_date',
            ]))
            ->applyFiltersReport($request->only([
                'provider',
                'customer',
                'payment_mode',
                'category',
                'status',
                'item',
            ]))
            ->whereNull('providers_id')
            ->expensesAttributes()
            ->get();

        $totalAmount = $expenseCategories->sum('total_amount');


        $expenseCategoriesProviders = Provider::has('expenses')
            ->applyFilters($request->only([
                'provider',
            ]))
            ->with(['expenses' => function ($query) use ($request) {
                $query->with('category')->select('*')
                ->applyFilters($request->only([
                    'from_date',
                    'to_date',
                ]))->applyFiltersReport($request->only([
                    'customer',
                    'payment_mode',
                    'category',
                    'status',
                    'item',
                ]));
            }])
            ->where('company_id', $company->id)
            ->get();

        $expensesProviders = [];

        foreach($expenseCategoriesProviders as $provider) {
            $data = [
                "first_name" => $provider['first_name'],
                "last_name" => $provider['last_name'],
                "title" => $provider['title'],
                'total_expense' => 0
            ];
            $expensesCollection = collect($provider['expenses']);
            $data['total_expense'] = $expensesCollection->sum('amount');
            $data['expenses'] = $expensesCollection->reduce(function ($carry, $item) {
                $categoryId = $item['expense_category_id'];
                if (! isset($carry[$categoryId])) {
                    $carry[$categoryId] = $item;
                } else {
                    $carry[$categoryId]['amount'] += $item['amount'];
                }

                return $carry;
            }, []);

            array_push($expensesProviders, $data);
        }

        $totalExpensesProviders = collect($expensesProviders)->sum('total_expense');
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
            'date_text_color'
        ];
        $colorSettings = CompanySetting::whereIn('option', $colors)
            ->whereCompany($company->id)
            ->get();

        // consultar la lista de proveedores y extraer solo el nombre
        $providers = Provider::whereIn('id', $filters->get('provider'))->get()->pluck('first_name')->implode(' | ');
        $customerList = User::whereIn('id', $filters->get('customer'))->get()->pluck('name')->implode(' | ');
        $payment_modeList = PaymentMethod::whereIn('id', $filters->get('payment_mode'))->get()->pluck('name')->implode(' | ');
        $categoryList = ExpenseCategory::whereIn('id', $filters->get('category'))->get()->pluck('name')->implode(' | ');

        $statusList = null;
        if ($filters->get('status')) {
            $status_selected = explode(',', $filters->get('status'));
            $statusList = array_map(function ($status) {
                if($status == "Active") {
                    $status = 'Processed';
                }

                return $status;
            }, $status_selected);
            $statusList = implode(', ', $statusList);
        }

        $itemList = Item::whereIn('id', $filters->get('item'))->get()->pluck('name')->implode(' | ');

        $infoCompany = $this->getTextInformationCompanyReports($company);
        //Log::debug($expensesProviders);

        view()->share([
            'totalAllExpenses' => $totalAmount + $totalExpensesProviders,
            'info_company' => $infoCompany,
            'expenseCategoriesProviders' => $expensesProviders,
            'expenseCategories' => $expenseCategories,
            'colorSettings' => $colorSettings,
            'totalExpense' => $totalAmount,
            'company' => $company,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'providers' => $providers,
            'customerList' => $customerList,
            'payment_modeList' => $payment_modeList,
            'categoryList' => $categoryList,
            'statusList' => $statusList,
            'itemList' => $itemList,
        ]);
        $pdf = PDF::loadView('app.pdf.reports.expenses');

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => true, "message" => "ExpensesReport Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "ExpensesReport  Payments");
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
