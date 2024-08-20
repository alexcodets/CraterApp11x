<?php

namespace Crater\Http\Controllers\V1\PaymentAccount;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeletePaymentAccountRequest;
use Crater\Http\Requests\PaymentAccountRequest;
use Crater\Models\Country;
use Crater\Models\LogsDev;
use Crater\Models\PaymentAccount;
use Crater\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PaymentAccountController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $limit = $request->input('limit', 10);
        $userId = $request->input('user_id', $request->input('customer_id'));

        $payment_accounts = PaymentAccount::applyFilters($request->only([
            'search',
            'first_name',
            'last_name',
            'payment_account_type',
            'address_1',
            'orderByField',
            'orderBy',
            'credit_card',
        ]))
            ->where('status', 'A')
            ->where('payment_accounts.client_id', $userId)
            ->leftJoin('countries', 'countries.id', 'payment_accounts.country_id')
            ->leftJoin('states', 'states.id', 'payment_accounts.state_id')
            ->select('payment_accounts.*', 'countries.name as country_name', 'states.name as state_name')
            ->latest()
            ->paginateData($limit);

        foreach ($payment_accounts as $pa) {
            if ($pa->payment_account_type === 'ACH') {
                $pa->ACH_type = Crypt::decryptString($pa->ACH_type ?? null);
                $pa->account_number = Crypt::decryptString($pa->account_number ?? null);
                $pa->routing_number = Crypt::decryptString($pa->routing_number ?? null);
                $pa->num_check = null;
                $pa->bank_name = Crypt::decryptString($pa->bank_name ?? null);
            } elseif ($pa->payment_account_type === 'CC') {
                $pa->cvv = Crypt::decryptString($pa->cvv ?? null);
                $pa->card_number = Crypt::decryptString($pa->card_number ?? null);
                $pa->expiration_date = Crypt::decryptString($pa->expiration_date ?? null);
            }
        }

        $paymentAccountTotalCount = PaymentAccount::where('payment_accounts.client_id', $userId)
            ->where('status', 'A')
            ->count();

        return response()->json([
            'payment_accounts' => $payment_accounts,
            'paymentAccountTotalCount' => $paymentAccountTotalCount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentAccountRequest $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentAccountController", "store");
        ////////////////

        $payment_accounts = PaymentAccount::createPaymentAccount($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'payment_accounts' => $payment_accounts,
        ], "message" => "Payment Account store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Payment Account store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'payment_accounts' => $payment_accounts,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentAccountController", "show");
        ////////////////

        $payment_accounts = PaymentAccount::where('payment_accounts.id', $id)
            ->leftJoin('countries', 'countries.id', 'payment_accounts.country_id')
            ->leftJoin('states', 'states.id', 'payment_accounts.state_id')
            ->select('payment_accounts.*', 'countries.id as country_id', 'countries.name as country', 'states.id as state_id', 'states.name as state')
            ->first();

        if ($payment_accounts['country_id'] != null) {
            $country = Country::where('id', $payment_accounts['country_id'])->first();
            $payment_accounts['country'] = $country;
        }

        if ($payment_accounts['state_id'] != null) {
            $state = State::where('id', $payment_accounts['state_id'])->first();
            $payment_accounts['state'] = $state;
        }

        if ($payment_accounts['payment_account_type'] === 'ACH') {
            $payment_accounts['ACH_type'] = Crypt::decryptString($payment_accounts->ACH_type);
            $payment_accounts['account_number'] = Crypt::decryptString($payment_accounts->account_number);
            $payment_accounts['routing_number'] = Crypt::decryptString($payment_accounts->routing_number);
            // $payment_accounts['num_check'] = Crypt::decryptString($payment_accounts->num_check);
            $payment_accounts['bank_name'] = Crypt::decryptString($payment_accounts->bank_name);

        } elseif ($payment_accounts['payment_account_type'] === 'CC') {
            $payment_accounts['cvv'] = Crypt::decryptString($payment_accounts->cvv);
            $payment_accounts['card_number'] = Crypt::decryptString($payment_accounts->card_number);
            $payment_accounts['expiration_date'] = Crypt::decryptString($payment_accounts->expiration_date);
            $payment_accounts['credit_card'] = $payment_accounts->credit_card;
        }

        switch ($payment_accounts['status']) {
            case 'A':
                $payment_accounts['status'] = ['value' => 'A', 'text' => 'Active'];

                break;

            case 'I':
                $payment_accounts['status'] = ['value' => 'I', 'text' => 'Inactive'];

                break;

            default:
                break;
        }

        //Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'payment_accounts' => $payment_accounts,
        ], "message" => "Payment Account show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Payment Account show");
        //Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'payment_accounts' => $payment_accounts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentAccount $paymentAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentAccountRequest $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentAccountController", "update");
        ////////////////

        $payment_accounts = PaymentAccount::find($id);

        $data = $request->validated();

        $data['company_id'] = Auth::user()->company_id;

        if ($request->payment_account_type === 'CC') {
            $data['cvv'] = Crypt::encryptString($request->cvv);
            $data['card_number'] = Crypt::encryptString($request->card_number);
            $data['expiration_date'] = Crypt::encryptString($request->expiration_date);
            $data['credit_card'] = $request->credit_cards['value'];
        } elseif ($request->payment_account_type === 'ACH') {
            $data['ACH_type'] = Crypt::encryptString($request->ACH_type);
            $data['account_number'] = Crypt::encryptString($request->account_number);
            $data['routing_number'] = Crypt::encryptString($request->routing_number);
            $data['num_check'] = $request->num_check;
            $data['bank_name'] = Crypt::encryptString($request->bank_name);
        }

        $payment_accounts->update($data);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'payment_accounts' => $payment_accounts,
        ], "message" => "Payment account update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Payment account update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'payment_accounts' => $payment_accounts,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function delete(DeletePaymentAccountRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentAccountController", "destroy");
        //////////////////

        PaymentAccount::destroy($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Payment account deleted successfully',
        ], "message" => "Payment account deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Payment account deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'Payment account deleted successfully',
        ]);
    }

    /**
     * Change the default account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function defaultPayAccount(Request $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentAccountController", "main_account_change");
        ////////////////

        $payment_account = PaymentAccount::find($id);

        $userId = $payment_account->client_id;

        $payment_accounts = PaymentAccount::select('payment_accounts.*')
            ->where('payment_accounts.client_id', $userId)
            ->get();

        foreach ($payment_accounts as $pa) {
            $pa->main_account = false;
            $pa->update();
        }

        if ($payment_account->main_account) {
            $payment_account->main_account = false;
            $payment_account->update();
        } else {
            $payment_account->main_account = true;
            $payment_account->update();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'payment_account' => $payment_account,
        ], "message" => "Default payment account updated"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Default payment account update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'payment_account' => $payment_accounts,
        ]);
    }
}
