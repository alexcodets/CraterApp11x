<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css">
        /* -- Base -- */
        body {
            font-family: "DejaVu Sans";
        }

        html {
            margin: 0px;
            padding: 0px;
            margin-top: 50px;
        }
        table {
            border-collapse: collapse;
        }

        hr {
           color: rgba(0, 0, 0, 0.2);
           border: 0.5px solid #EAF1FB;
        }

        /* -- Heeader -- */

        .header-container {
            /* position: absolute; */
            width: 100%;
            padding: 0 30px;
            margin-bottom: 50px;
            /* height: 150px;
            left: 0px;
            top: -60px; */
        }

        /* .header-section-left {
            padding-top: 45px;
            padding-bottom: 45px;
            padding-left: 30px;
            display:inline-block;
            width:30%;
        } */

        .header-logo {
            /* position: absolute; */
            height: 50px;
            text-transform: capitalize;
            color: #817AE3;
            padding-top: 0px;
        }
        .company-address-container {
            width: 50%;
            text-transform: capitalize;
            padding-left: 80px;
            margin-bottom: 2px;
        }
        /* .header-section-right {
            display:inline-block;
            position: absolute;
            right:0;
            padding: 15px 30px 15px 0px;
            float: right;
        } */

        .header-section-right {
            text-align: right;
        }

        .header {
            font-size: 20px;
            color: rgba(0, 0, 0, 0.7);
        }

        /* -- Company Address -- */

        .company-details h1 {
            margin:0;

            font-weight: bold;
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            text-align: left;
            max-width: 220px;
        }

        .company-address{
             /* margin-top: 12px; */
            font-size: 12px;
            line-height: 15px;
            color: #595959;
            word-wrap: break-word;
        }

        .content-wrapper {
           display: block;
           height: 250px;
        }

        .main-content {
            display: inline-block;
            padding-top: 20px
        }

        /* -- Customer Address -- */
        .customer-address-container {
            display: block;
            float:left;
            width:40%;
            padding: 0 0 0 30px;
        }

        /* -- Shipping -- */

        .shipping-address-label {
            padding-top: 5px;
            font-size: 12px;
            line-height: 18px;
            margin-bottom: 0px;
        }

        .shipping-address-name {
            padding: 0px;
            font-size: 15px;
            line-height: 22px;
            margin: 0px;
        }

        .shipping-address {
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            margin: 0px;
            width: 160px;
        }

        /* -- Billing -- */

        .billing-address-container {
            display: block;
            float: left;
        }

        .billing-address-container--right {
            float: right;
        }

        .billing-address-label {
            padding-top: 5px;
            font-size: 12px;
            line-height: 18px;
            margin-bottom: 0px;
            color: #55547A;
        }

        .billing-address-name {
            padding: 0px;
            font-size: 15px;
            line-height: 22px;
            margin: 0px;
        }

        .billing-address {
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            margin:0px;
            width: 180px;
            word-wrap: break-word;
        }

        .use_payments_credit{
            color: #595959;
            padding-top: 20px;
            /* background-color: rgba(0, 0, 0, 0.7); */

        }
        /* -- Payment Details -- */

        .payment-details-container {
            display: inline;
            position: absolute;
            float: right;
            width: 40%;
            height: 120px;
            padding: 5px 30px 0 0;
        }

        .attribute-label {
            font-size: 12px;
            line-height: 18px;
            text-align: left;
            color: #55547A
        }

        .attribute-value {
            font-size: 12px;
            line-height: 18px;
            text-align: right;
        }

        /* -- Notes -- */

        .notes {
            font-size: 12px;
            color: #595959;
            margin-top: 15px;
            margin-left: 30px;
            width: 442px;
            text-align: left;
            page-break-inside: avoid;
        }

        .notes-label {
            font-size: 15px;
            line-height: 22px;
            letter-spacing: 0.05em;
            color: #040405;
            width: 108px;
            height: 19.87px;
            padding-bottom: 10px;
        }

        .content-heading {
            margin-top: 10px;
            width: 100%;
            text-align: center;
        }

        p {
            padding: 0 0 0 0;
            margin:  0 0 0 0;
        }

        .content-heading span {
            font-weight: normal;
            font-size: 14px;
            line-height: 25px;
            padding-bottom: 5px;
            border-bottom: 1px solid #B9C1D1;
        }

        /* -- Total Display Box -- */

        .total-display-box {
            width: 365px;
            display: block;
            margin-right: 30px;
            background: #F9FBFF;
            border: 1px solid #EAF1FB;
            box-sizing: border-box;
            float: right;
            padding: 12px 15px 15px 15px;
        }

        .total-display-label {
            display: inline;
            font-weight: bold;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .total-display-box span {
            float: right;
            font-weight: bold;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #5851D8;
        }

         /* -- Total Display Box -- */

         .total-display-box-cash {
            width: 320px;
            display: block;
            margin-right: 30px;
            background: #F9FBFF;
            border: 1px solid #EAF1FB;
            box-sizing: border-box;
            float: right;
            padding: 10px 13px 13px 13px;
        }

        .total-display-label-cash {
            display: inline;
            font-weight: bold;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .total-display-box-cash span {
            float: right;
            font-weight: bold;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #5851D8;
        }


        /* -- Items Table -- */

        .items-table {
            margin-top: 2px;
            padding: 0px 30px 0px 30px;
            page-break-before: avoid;
            page-break-after: auto;
        }

        .items-table hr {
            height: 0.1px;
        }

        .item-table-title {
            font-weight:bold;
            color: #595959;
            text-align:left;


            padding-left: 10;
            font-size: 12px;
        }
        .item-table-heading {
            font-size: 10.5;
            text-align: center;
            padding: 5px;
            color: #4A4A4A;
        }

        tr.item-table-heading-row th {
            border-bottom: 0.620315px solid #E8E8E8;
            font-size: 10px;
            line-height: 18px;
        }

        tr.item-row td {
            font-size: 10px;
            line-height: normal;
        }

        .item-cell {
            font-size: 11px;
            text-align: center;
            padding: 5px;
            /*padding-top: 10px;*/
            color: #040405;
        }

        .item-description {
            color: #595959;
            font-size: 8px;
            line-height: 10px;
        }

        /* -- Total Display Table -- */
    </style>
</head>
<body>
    <div class="header-container">
        <table width="100%">
            <tr>


                @if(true)
                    <td  width="50%" class="header-section-left">
                        <img class="header-logo" src="{{ $logo }}" alt="Company Logo">
                @else
                    @if($payment->user->company)
                    <td class="header-section-left" style="padding-top:0px;">
                        <h1 class="header-logo"> {{$payment->user->company->name}} </h1>
                    @endif
                @endif
                </td>
                <td  width="50%" class="header-section-right company-details company-address">
                    {!! $company_address !!}
                </td>
                <!-- <td class="header-section-left" style="padding-top:0px;">
                        <h1 class="header-logo"> {{$payment->user->company->name}} </h1>
                </td>-->
            </tr>
        </table>
    </div>

    <hr style="border: 0.620315px solid #E8E8E8;">

    <p class="content-heading">
        <span>@lang('pdf_payment_receipt_label')</span>
    </p>

    <div class="content-wrapper">
        <div class="main-content">
            <div class="customer-address-container">
                <table width="100%">
                    <tr>
                        <td class="attribute-label">@lang('pdf_responsible')</td>
                        @if($responsible->first( ) != null)
                            <td class="attribute-value"> &nbsp;{{$responsible->first()->name}}</td>
                         @else
                            <td class="attribute-value"> &nbsp; @lang('pdf_responsible_system')</td>
                        @endif
                    </tr>
                    <tr>
                        <td class="attribute-label">@lang('pdf_payment_customer_name')</td>
                        <td class="attribute-value"> &nbsp;{{$payment->user->contact_name}}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">@lang('pdf_payment_customer_number')</td>
                        <td class="attribute-value">{{ $payment->user->customcode }}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">@lang('pdf_payment_customer_email')</td>
                        <td class="attribute-value"> &nbsp;{{$payment->user->email}}</td>
                    </tr>
                    @if(isset($payment->user->phone))
                        <tr>
                            <td class="attribute-label">@lang('pdf_payment_customer_phone')</td>
                            <td class="attribute-value"> &nbsp;{{$payment->user->phone}}</td>
                        </tr>
                    @endif
                </table>
                <br>
                <div class="billing-address-container billing-address">
                    @if($billing_address)
                        @lang('pdf_received_from')
                        <br>
                        {!! $billing_address !!}
                    @endif
                </div>
                <div class="billing-address-container--right">
                </div>
                <div style="clear: both;"></div>
                @if($payment->invoice_id == null )
                <div class="use_payments_credit">
                    @lang('this_payment_was_made_for_add_credit_to_the_customer')
                </div>
                @endif
                @if($payment->transaction_status == "Unapply" )
                    @if($payment->applied_credit_customer == true )
                        <div class="use_payments_credit">
                            @lang('this_customer_credit_applied')
                        </div>
                    @else
                        <div class="use_payments_credit">
                            @lang('this_no_credit_generated_for_the_customer')
                        </div>
                    @endif
                @endif
            </div>



            <div class="payment-details-container">
                <table width="100%">
                    <tr>
                        <td class="attribute-label">@lang('pdf_payment_date')</td>
                        <td class="attribute-value"> &nbsp;{{$payment->formattedPaymentDate}}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">@lang('pdf_payment_number')</td>
                        <td class="attribute-value"> &nbsp;{{$payment->payment_number}}</td>
                    </tr>
                    @if ($payment->invoice && $payment->invoice->invoice_number)
                        <tr>
                            <td class="attribute-label">@lang('pdf_invoice_label')</td>
                            <td class="attribute-value"> &nbsp;{{$payment->invoice->invoice_number}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="attribute-label">@lang('pdf_payment_status')</td>
                        <td class="attribute-value"> &nbsp;{{ $payment->transaction_status }}</td>
                    </tr>

                    {{-- / Payment Mode --}}

                    <tr>
                        <td class="attribute-label">@lang('pdf_payment_mode')</td>
                        @if ($payment->paymentMethod != null)
                            <td class="attribute-value">
                                &nbsp;{{$payment->paymentMethod->name}}
                            </td>
                        @else
                            <td class="attribute-value">
                                @lang('pdf_payment_customer_credit')
                            </td>
                        @endif
                    </tr>

                    @if ($payment->paymentMethod != null)
                        @if ($payment->paymentMethod->is_multiple == 0)

                            @if (isset($payment->paymentMethod) && $payment->paymentMethod->account_accepted == 'C')
                            <tr>
                                <td class="attribute-label">@lang('pdf_payment_type_credit_card')</td>
                                <td class="attribute-value"> &nbsp;{{$payment->credit_card}}</td>
                            </tr>
                            @endif

                            @if(isset($payment->paymentsPaypal))
                                @if(isset($payment->paymentsPaypal->card_number))
                                <tr>
                                    <td class="attribute-label">@lang('pdf_payment_credit_card_number')</td>
                                    <td class="attribute-value">  &nbsp;{{ $payment->paymentsPaypal->card_number}}</td>
                                </tr>
                                <tr>
                                    <td class="attribute-label">@lang('pdf_payment_gateway')</td>
                                    <td class="attribute-value"> Paypal Pro </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="attribute-label">@lang('pdf_payment_authorize_transacction_id')</td>
                                    <td class="attribute-value"> &nbsp;{{$payment->paymentsPaypal->transaction_id}}</td>
                                </tr>
                            @endif

                            @if(isset($payment->authorize))
                                @if($payment->paymentMethod->account_accepted == 'A')
                                                                @if(isset($authorizeobjet) && $authorizeobjet !== null)



<tr>
<td class="attribute-label">@lang('pdf_payment_account_number')</td>
<td class="attribute-value"> {{ substr($authorizeobjet->DecryptedAccountNumber, -4) }}</td>
</tr>
@else

<tr>
<td class="attribute-label">@lang('pdf_payment_account_number')</td>
<td class="attribute-value"> {{ substr( $payment->authorize->account_number, -4) }}</td>
</tr>

@endif
                                @endif
                                @if($payment->paymentMethod->account_accepted == 'C')
                                    <tr>
                                        <td class="attribute-label">@lang('pdf_payment_credit_card_number')</td>
                                        <td class="attribute-value"> &nbsp;{{ $payment->authorize->card_number}}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="attribute-label">@lang('pdf_payment_gateway')</td>
                                    <td class="attribute-value"> Authorize </td>
                                </tr>
                                <tr>
                                    <td class="attribute-label">@lang('pdf_payment_authorize_transacction_id')</td>
                                    <td class="attribute-value"> &nbsp;{{$payment->authorize->transaction_id}}</td>
                                </tr>
                            @endif

                            @if(isset($payment->auxVault))

                             @if($payment->paymentMethod->account_accepted == 'C')
                                <tr>
                                    <td class="attribute-label">@lang('pdf_payment_credit_card_number')</td>
                                    <td class="attribute-value"> &nbsp;{{ $payment->auxVault->card_number}}</td>
                                </tr>
                                @endif

                                @if($payment->paymentMethod->account_accepted == 'A')
                                    <tr>
                                        <td class="attribute-label">@lang('pdf_payment_account_number')</td>
                                        <td class="attribute-value"> &nbsp;{{ $payment->auxVault->ach_account_number}}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <td class="attribute-label">@lang('pdf_payment_gateway')</td>
                                    <td class="attribute-value"> Aux Pay </td>
                                </tr>
                                <tr>
                                    <td class="attribute-label">@lang('pdf_payment_authorize_transacction_id')</td>
                                    <td class="attribute-value"> &nbsp;{{$payment->auxVault->transaction_id}}</td>
                                </tr>

                            @endif

                        @elseif ($payment->paymentMethod->is_multiple == 1)
                            @foreach($payment->paymentMethods as $payment_method)
                            <tr>
                                <td class="attribute-label" style="font-style: italic;">{{ $payment_method->name }}</td>
                                <td class="attribute-value" style="font-style: italic;">
                                    {!! format_money_pdf($payment_method->amount, $payment->user->currency ?? null) !!}
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    @endif
                    {{-- Payment Mode / --}}
                </table>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <br><br><br>

    @if($received != null || $returned != null)
    <div class="total-display-box-cash">
        <p class="total-display-label">@lang('pdf_payment_cash_received_label')</p>
        <span>${!! format_money_pdf($received, $payment->user->currency ?? null) !!}</span>
    </div>

    <br><br><br>

    <div class="total-display-box-cash">
        <p class="total-display-label">@lang('pdf_payment_cash_returned_label')</p>
        <span>${!! format_money_pdf($returned, $payment->user->currency ?? null) !!}</span>
    </div>

    <br><br><br>
    @endif

    <div class="total-display-box">
        <p class="total-display-label">@lang('pdf_payment_amount_received_label')</p>
        <span>${!! format_money_pdf($payment->amount, $payment->user->currency ?? null) !!}</span>
    </div>

    @if(isset($payment->notes))
    <br><br><br>
    <div class="notes">
        <div class="notes-label">
            @lang('pdf_notes')
        </div>
        {!!$payment->notes !!}
    </div>
    @endif
    <br><br><br>
    <div style="position: relative; clear: both;">

    @isset($PaymentsArray)
                    @if( count($PaymentsArray) > 0  )

                    <div class="pbx-service-container pbx-service" >
                    <table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="5">@lang('pdf_payment_associated_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th class="text-left item-table-heading">@lang('pdf_payment_date')</th>
        <th class="text-center item-table-heading">@lang('pdf_payment_mode')</th>
        <th class="text-right item-table-heading">@lang('pdf_payment_number')</th>
        <th class="text-right item-table-heading">@lang('pdf_payment_transaction_status')</th>
        <th class="text-right item-table-heading">@lang('pdf_amount_label')</th>
    </tr>

    @foreach ($PaymentsArray as  $value)

    <tr class="item-row">
        <td
            class="text-left item-cell"
            style="vertical-align: top;"
        >
        {{  $value["payment_date"] }}
        </td>
        <td
            class="text-center item-cell"
            style="vertical-align: top;"
        >
        {{  $value["payment_method"]  }}
        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >

        {{  $value["payment_number"] }}

        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >

        {{  $value["transaction_status"] }}

        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >

        {!! format_money_pdf($value["amount"]) !!}

        </td>
    </tr>



@endforeach
</table>
</div>
                    @endif
                @endisset
                </div>

                @isset($paymentfees)
                @if( count($paymentfees) > 0  )
                <br><br>
                <div style="position: relative; clear: both;">
                <div class="pbx-service-container pbx-service" >
                <table width="100%" class="items-table" cellspacing="0" border="0">
                <tr>
        <td class="item-table-title" colspan="5">@lang('pdf_payment_fees_label')</td>
    </tr>
                <tr class="item-table-heading-row">
        <th class="text-left item-table-heading">@lang('pdf_payment_fees_name')</th>
        <th class="text-center item-table-heading">@lang('pdf_payment_fees_type')</th>
        <th class="text-right item-table-heading">@lang('pdf_payment_fees_amount')</th>
        <th class="text-right item-table-heading">@lang('pdf_payment_fees_total')</th>
    </tr>

    @foreach ($paymentfees as  $value)

    <tr class="item-row">
        <td
            class="text-left item-cell"
            style="vertical-align: top;"
        >
        {{  $value["name"] }}
        </td>
        <td
            class="text-center item-cell"
            style="vertical-align: top;"
        >
        {{  $value["type"]  }}
        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >



        @if( $value["type"] == "fixed"  )
        {!! format_money_pdf( $value["amount"], $payment->user->currency ?? null) !!}
        @endif

        @if( $value["type"] != "fixed"  )
        %   {{  $value["amount"]/100 }}
        @endif

        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >

        {!! format_money_pdf( $value["total"], $payment->user->currency ?? null) !!}



        </td>

        </tr>

    @endforeach

    <tr class="item-row">
        <td
            class="text-left item-cell"
            style="vertical-align: top;"
        >
      
        </td>
        <td
            class="text-center item-cell"
            style="vertical-align: top;"
        >
       
        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >


<b> @lang('pdf_payment_fees_total_sum')</b>
       

        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >

        {!! format_money_pdf( $totalGeneralfees["totalgeneralfees"], $payment->user->currency ?? null) !!}



        </td>

        </tr>

                </table>
                </div>

                <div class="notes">

                @lang('pdf_payment_fees_total_legend')
    </div>
              
                @endif
                @endisset
</body>
</html>
