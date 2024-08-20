<?php

namespace Crater\Http\Controllers\V1\CorePOS;

use Crater\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosPaymentMethodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
            $companyId = auth()->user()->company()->pluck('id')->first();
            $data = $request->all();

            DB::table('pos_payment_methods')->where('company_id', $companyId)->delete();

            foreach($data['data'] as $payment_method) {
                DB::table('pos_payment_methods')->insert([
                    'payment_method_id' => $payment_method['id'],
                    'company_id' => $companyId
                ]);
            }

            return response()->json([
                'success' => true
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getPosPaymentMethodsCompany()
    {
        $companyId = auth()->user()->company()->pluck('id')->first();

        $paymentMethods = DB::table('pos_payment_methods')
            ->select(
                'pos_payment_methods.id',
                'pos_payment_methods.payment_method_id',
                'pos_payment_methods.company_id',
                'payment_methods.name AS name'
            )
            ->join('payment_methods', 'pos_payment_methods.payment_method_id', '=', 'payment_methods.id')
            ->where('pos_payment_methods.company_id', $companyId)->get();

        return response()->json([
            'success' => true,
            'data' => $paymentMethods
        ]);
    }
}
