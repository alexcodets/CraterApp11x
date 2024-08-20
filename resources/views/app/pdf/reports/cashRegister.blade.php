<!DOCTYPE html>
<html lang="en">

<head>
    <title>Cash Register Report</title>
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

        .text-center {
            text-align: center
        }

        .hr-details {
            height: 1px;
            border-top: 3px solid #25c1c0ff;
            margin-bottom: 10px;

        }

        .title-details {
            font-style: bold !important;
            font-weight: 400;
            font-size: 10px;
            color: black;
        }

        .box-border {
            padding-bottom: 0px;

            width: 200px;
        }

        .box-details {
            width: 240px !important;
            /* height: 125px; */
            border: 1px solid #FFFFFF;
        }


        hr {
            margin: 0 30px 0 30px;
            color: rgba(0, 0, 0, 0.2);
            border: 0.5px solid #EAF1FB;
        }

        /* -- Header -- */

        .header-bottom-divider {
            color: rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 90px;
            left: 0px;
            width: 100%;
        }

        .header-container {
            position: absolute;
            width: 94%;
            height: 90px;
            left: 10px;
            top: 5px;
        }

        .title-invoice {
            font-style: bold !important;
            font-weight: normal !important;
            font-size: 12pt !important;
            font-family: "DejaVu Sans";
            color: #000000 !important;
            word-spacing: -0.0332em;
            letter-spacing: -0.0118px;
            margin-top: 35px !important;
        }

        .number-invoice {
            font-style: normal !important;
            font-weight: normal !important;
            font-size: 12pt !important;
            font-family: "DejaVu Sans";
            color: #000000 !important;
            word-spacing: -0.0332em;
            letter-spacing: -0.0118px;
            margin-top: -10px;
        }

        .center-horizontal {
            width: 94%;
            margin: 0 0;
        }

        .header-logo {
            max-width: 75px;
            margin-top: 20px;
            text-transform: capitalize;
            color: #817AE3;
        }

        .header {
            font-size: 6px;
            color: rgba(0, 0, 0, 0.7);
        }

        .content-wrapper {
            display: block;
            margin-top: 0px;
            padding-top: 5px;
            padding-bottom: 0px;
        }

        .company-address-container {
            font-style: bold !important;
            padding-top: 15px;
            padding-left: 30px;
            float: left;
            width: 30%;
            text-transform: capitalize;
            margin-bottom: 2px;
        }

        .company-address-container h1 {
            font-style: bold !important;
            font-size: 6px;
            line-height: 22px;
            letter-spacing: 0.05em;
            margin-bottom: 0px;
            margin-top: 10px;
        }

        .company-address {
            font-style: bold !important;
            margin-top: 2px;
            text-align: left;
            font-size: 6px;
            line-height: 15px;
            color: #000000;
            width: 280px;
            word-wrap: break-word;
        }

        .invoice-details-container {
            float: right;
            padding: 10px 30px 0 0;
        }

        .attribute-label {
            font-style: bold !important;
            font-size: 6px;
            line-height: 18px;
            padding-right: 40px;
            text-align: left;
            color: #000000;
        }

        .attribute-value {
            font-size: 6px;
            line-height: 18px;
            text-align: right;
        }

        /* -- Shipping -- */

        .shipping-address-container {
            float: right;
            padding-left: 40px;
            width: 160px;
        }

        .shipping-address {
            font-style: bold !important;
            font-size: 6px;
            line-height: 15px;
            color: #000000;
            padding: 45px 0px 0px 40px;
            margin: 0px;
            width: 160px;
            word-wrap: break-word;
        }

        /* -- Billing -- */

        .billing-address-container {
            padding-top: 50px;
            float: left;
            padding-left: 30px;
        }

        .billing-address-label {
            font-style: bold !important;
            font-size: 6px;
            line-height: 18px;
            padding: 0px;
            margin-top: 27px;
            margin-bottom: 0px;
        }

        .billing-address-name {
            font-style: bold !important;
            max-width: 160px;
            font-size: 6px;
            line-height: 22px;
            padding: 0px;
            margin: 0px;
        }

        .billing-address {
            font-style: bold !important;
            font-size: 6px;
            line-height: 15px;
            color: #000000;
            padding: 45px 0px 0px 30px;
            margin: 0px;
            width: 160px;
            word-wrap: break-word;
        }

        /* -- Pbx Service -- */

        .pbx-service-container {
            float: right;
        }

        .pbx-service {
            font-style: bold !important;
            font-size: 6px;
            line-height: 15px;
            color: #000000;
            padding: 45px 30px 0px 40px;
            margin: 0px;
            word-wrap: break-word;
        }

        /* -- Items Table -- */

        .items-table {
            font-style: bold !important;
            margin-top: 2px;
            padding: 0px 10px 0px 10px;
            page-break-before: avoid;
            page-break-after: auto;
        }

        .items-table hr {
            height: 0.1px;
        }

        .item-table-title {
            font-style: bold !important;
            font-weight: bold;
            color: #000000;
            padding-left: 10;
            font-size: 12px;
        }

        .item-table-heading {
            font-style: bold !important;
            font-size: 6;
            text-align: center;
            padding: 5px;
            color: #000000;
        }

        tr.item-table-heading-row th {
            border-bottom: 0.620315px solid #E8E8E8;
            font-size: 6px;
            line-height: 18px;
        }

        tr.item-row td {
            font-size: 6px;
            line-height: normal;
            font-style: bold !important;
        }

        .item-cell {
            font-size: 6px;
            text-align: center;
            padding: 5px;
            /*padding-top: 10px;*/
            color: #000000;
        }

        .item-description {
            font-style: bold !important;
            color: #000000;
            font-size: 5px;
            line-height: 10px;
        }

        /* -- Total Display Table -- */

        .total-display-container {
            padding: 0 25px;
        }

        .total-display-table {
            border-top: none;
            box-sizing: border-box;
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
            margin-left: 500px;
        }

        .total-table-attribute-label {
            font-style: bold !important;
            font-size: 6px;
            color: #000000;
            text-align: right;
            padding-left: 10px;
        }

        .total-table-attribute-value {
            font-weight: bold;
            text-align: right;
            font-size: 6px;
            padding-right: 10px;
        }

        /* -- Notes -- */

        .notes {
            font-style: bold !important;
            font-size: 8px;
            color: #000000;
            margin-top: 15px;
            margin-left: 10px;
            margin-right: 10px;
            width: 100%;
            text-align: left;
            page-break-inside: avoid;
        }

        .notes2 {
            font-style: bold !important;
            font-size: 8px;
            color: #000000;
            margin-top: 15px;
            margin-left: 10px;
            width: 100%;
            text-align: left;
            page-break-inside: avoid;
        }

        .notes-label {
            font-style: bold !important;
            font-size: 8px;
            line-height: 22px;
            letter-spacing: 0.05em;
            color: #000000;
            width: 108px;
            height: 19.86px;
            padding-bottom: 10px;
        }

        /* -- Helpers -- */

        .text-primary {}

        .text-center {
            text-align: center
        }

        .text-right {
            text-align: right
        }

        table .text-left {
            text-align: left;
        }

        table .text-right {
            text-align: right;
        }

        .border-0 {
            border: none;
        }

        .py-2 {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .py-8 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .py-3 {
            padding: 3px 0;
        }

        .pr-20 {
            padding-right: 20px;
        }

        .pr-10 {
            padding-right: 10px;
        }

        .pl-20 {
            padding-left: 20px;
        }

        .pl-10 {
            padding-left: 10px;
        }

        .pl-0 {
            padding-left: 0;
        }

        .text-details {
            font-style: bold !important;
            font-size: 8px;
            color: #000000;

        }

        .line {
            border: 1px solid white !important;
            border-collapse: collapse !important;
        }

        .test-line {
            border: 1px solid black !important;
            border-collapse: collapse !important;
        }

        .test-color {
            background-color: #595959;
        }
    </style>
</head>

<body>
    <div>
        <table width="100%" style="margin-top: -50px">
            <tr>
                <td class="text-center">
                    @if(true)
                    <img class="header-logo" src="{{ $logo }}" alt="Company Logo">
                    @else
                    @if($company)
                    <h3 class="header-logo"> {{$company->name}} </h3>

                    @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="content-wrapper center-horizontal">
        <table width="100%" style="margin: 0px 5px 0px 5px">
            <tr>
                <td class="text-center">
                    <div class="box-details">
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>
                                {{ $company->name}}
                            </span>
                        </div>
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>
                                {{ $company->company_identifier}}
                            </span>
                        </div>
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>
                                {{ $company->address->address_street_1}}
                            </span>
                        </div>
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>
                                {{ $company->address->city}}
                            </span>
                        </div>
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>
                                {{ $company->address->county}}
                            </span>
                        </div>
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>
                                {{ $company->address->phone}}
                            </span>
                        </div>
                    </div>
                </td>
            </tr>

        </table>
    </div>

    <hr style="margin: 10px 10px 0px 10px">

    <div class="content-wrapper center-horizontal">
        <table width="100%" style="margin: 0px 10px 0px 10px">
            <tr>
                <td class="text-center">
                    <div class="box-details">
                        <div style="margin-bottom: 0px !important">
                            <span class="title-details">{{ $cash_register->name }}</span>
                        </div>
                    </div>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {{ $cash_history != null ? $cash_history->ref : 'N/A'}}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <!-- Second -->
                <td class="text-left">
                    <div class="box-details">
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>@lang('pdf_cash_register_cashier'):</span>
                            <span>
                                {{ $cash_register != null ? $cash_register->user_name : 'N/A'}}
                            </span>
                        </div>

                        <div class="text-details" style="margin-bottom:0px;">
                            <span>@lang('pdf_cash_register_status'):</span>
                            @if($cash_history != null)
                            @if($cash_history->open == 1)
                            <span>@lang('pdf_cash_register_open')</span>
                            @else
                            <span>@lang('pdf_cash_register_close')</span>
                            @endif
                            @else
                            <span>N/A</span>
                            @endif
                        </div>

                        <div class="text-details" style="margin-bottom:0px;">
                            <span>@lang('pdf_cash_register_device'):</span>
                            <span>
                                {{ $cash_register->device }}
                            </span>
                        </div>

                        <div class="text-details" style="margin-bottom:0px;">
                            <span>@lang('pdf_cash_register_open_date'):</span>
                            <span>
                                {{ $cash_history != null ? $cash_history->open_date : 'N/A'}}
                            </span>
                        </div>
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>@lang('pdf_cash_register_close_date'):</span>
                            <span>
                                {{ $cash_history != null ? $cash_history->close_date : 'N/A' }}
                            </span>
                        </div>
                        <div class="text-details" style="margin-bottom:0px;">
                            <span>@lang('pdf_cash_register_currency'):</span>
                            <span>
                                {{ $currency->first() }}
                            </span>
                        </div>
                    </div>
                </td>

            </tr>
        </table>
    </div>

    <hr style="margin: 10px 10px 0px 10px">

    <div class="content-wrapper center-horizontal">
        <table width="90%" style="margin: 0px 10px 0px 10px">
            <tr>
                <td class="text-center" colspan="2">
                    <div class="box-details">
                        <div style="margin-bottom: 0px !important">
                            <span class="title-details">@lang('pdf_cash_register_detail_sales')</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    <span class="text-details">@lang('pdf_cash_register_method_name')</span>
                </td>
                <td class="text-left">
                    <span class="text-details">@lang('pdf_cash_register_amount')</span>
                </td>
            </tr>
            @if($detail_sales != null)
            @foreach($detail_sales as $sale)
            <tr>
                <td class="text-left text-details">
                    <div class="">
                        {{ $sale->method_name }}{{ $sale->only_cash == 1 ? ' *' : ''}}
                    </div>
                </td>
                <td class=" text-left text-details">
                    <div class="">
                        {!! format_money_pdf($sale->total_amount) !!}
                    </div>
                </td>
            </tr>
            @endforeach

            @php
            $totalSales = 0;
            foreach($detail_sales as $sale){
            $totalSales += intval($sale->total_amount);
            }
            @endphp
            <tr>
                <td class="text-center" colspan="2">
                    <div class="box-details">
                        <div style="margin-bottom: 0px !important">
                            <span class="title-details">@lang('pdf_cash_register_total'): {!! format_money_pdf($totalSales) !!}</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-left">
                    <div class="  text-details">
                        @lang('pdf_cash_register_payment_method_cash')

                    </div>
                </td>
            </tr> @endif
        </table>
    </div>

    <hr style="margin: 10px 10px 0px 10px">
    @if($cash_history != null)
    <div class="content-wrapper center-horizontal">
        <table width="100%" style="margin: 0px 10px 0px 10px">
            <tr>
                <td colspan="2" class="text-center">
                    <div style="margin-bottom: 0px !important">
                        <span class="title-details">@lang('pdf_cash_register_payment_general_details')</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">
                    <div style="margin-bottom: 0px !important">
                        <span class="text-details">@lang('pdf_cash_register_cash')</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_cash_income'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {!! format_money_pdf($cash_income) !!}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_cash_withdrawal'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {!! format_money_pdf($cash_withdrawal) !!}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_cash_payment_income'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {!! format_money_pdf($invoices_amount->received) !!}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_cash_payment_withdrawal'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {!! format_money_pdf($invoices_amount->returned) !!}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_cash_received'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {{ $cash_history->cash_received }}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_initial_amount'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {{ $cash_history->initial_amount }}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_final_amount'):</span>
                    </div>
                </td>
                <td>

                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {{ $cash_history->final_amount }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @endif

    @php
    $total_income =($cash_history != null ? $cash_history->initial_amount : 0) + ($cash_income / 100)+ ( $invoices_amount != null ? $invoices_amount->received / 100 : 0);
    $total_expenses = ($cash_withdrawal / 100)+ ( $invoices_amount != null ? $invoices_amount->returned / 100 : 0);
    @endphp

    <hr style="margin: 10px 10px 0px 10px">

    <div class="content-wrapper center-horizontal">
        <table width="100%" style="margin: 0px 10px 0px 10px">
            <!-- <tr>
                <td colspan="2" class="text-center">
                    <div style="margin-bottom: 0px !important">
                        <span class="title-details">@lang('pdf_cash_register_payment_general_details'):</span>
                    </div>
                </td>
            </tr> -->
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_total_income'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {!! format_money_pdf($total_income * 100) !!}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_total_expenses'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {!! format_money_pdf($total_expenses * 100) !!}
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>@lang('pdf_cash_register_total_cash'):</span>
                    </div>
                </td>
                <td>
                    <div class="text-details" style="margin-bottom:0px;">
                        <span>
                            {!! format_money_pdf( ( $total_income - $total_expenses ) * 100) !!}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>