<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

   
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
            @php echo "border-bottom: 1px solid " .$colorInvoice.";";
            @endphp margin-bottom: 0px;
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
            top: -50px;
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
            @php echo "color: " .$colorInvoice."!important;";
            @endphp padding-right: 10px;
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

        .text-primary {
            @php echo "color: " .$colorInvoice.";";
            @endphp
        }

        .text-center {
            text-align: center
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
    {{-- HEADER INVOICE HOLD --}}
    <div class="header-container">
        <div class="text-center">
            <h1 class="title-invoice" >@lang('pdf_hold_invoice') </h1>
            <h1 class="number-invoice" >{{$invoice['description']}}</h1> 
        </div>
    </div>
    {{-- LOGO OR COMPANY NAME --}}
    <div >
        <table width="100%" style="margin-top: 20px">
            <tr>
                <td class="text-center">
                    @if(true)
                        <img class="header-logo" src="{{ $logo }}" alt="Company Logo">
                    @else
                        @if($invoice->user->company)
                            <h3 class="header-logo"> {{$invoice->user->company->name}} </h3>
                            
                        @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>
{{-- INFORMATION OF COMPANY --}}
    <div class="content-wrapper center-horizontal">
        <table width="100%" style="margin: 0px 5px 0px 5px">
            <tr>
                <td class="text-center">
                    <div class="box-details">
                        <div class="text-details">
                            <span>@lang('pdf_customer_number'):</span>
                            <span>{{$invoice['user']->customcode}}</span>  
                        </div>
                        <div class="text-details">
                            <span>@lang('pdf_hold_invoice_name'):</span>
                            <span>{{$invoice['user']->name}}</span>  
                        </div>
                        <div class="text-details">
                            <span>@lang('pdf_hold_invoice_phone'):</span>
                            <span>{{$invoice['user']->phone}}</span>  
                        </div>
                        <div class="text-details">
                            <span>@lang('pdf_hold_invoice_email'):</span>
                            <span>{{$invoice['user']->email}}</span>  
                        </div>
                        <div class="text-details">
                            <span>@lang('pdf_invoice_date'):</span>
                            <span>{{$invoice['invoice_date']}}</span>  
                        </div>
                        @if($user_creator)
                        <div class="text-details">

                            @if($user_creator->role2)
                            <span>{{$user_creator->role2 }}:</span>
                            @else
                            <span>@lang('pdf_user'):</span>
                            @endif
                            <span>{{$user_creator->name }}</span>
                        </div>
                        @endif
                        <div class="text-details">
                            <span>@lang('cash_register'):</span>
                                @if($cash_register != null)
                                    <span>{{$cash_register->name}}</span>
                                @else
                                    <span>N/A</span>
                                @endif
                        </div>
                    </div>
                </td>

            </tr>
        </table>
    </div>

    {{-- FROM --}}
    <div class="content-wrapper center-horizontal">
        <table width="100%" style="margin: 0px 5px 0px 5px">
            <tr>
                <td class="text-center" >
                    <div class="box-details" style="margin-bottom: 0px !important">
                        <div style="width: 240px !important">
                            <span class="title-details">@lang('pdf_from'):</span>
                        </div>
                        <div class="text-details" >
                            {!! $company_address !!}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    {{-- TO --}}
    <div class="content-wrapper center-horizontal">
        <table width="100%" style="margin: 0px 5px 0px 5px">
            <tr>
                <td class="text-center" >
                    <div class="box-details" style="margin-bottom: 0px !important">
                        <div style="width: 240px !important">
                            <span class="title-details">@lang('pdf_to'):</span>
                        </div>
                        <div class="text-details" >
                            <!-- Bill to -->
                            @if($billing_address)
                                {!! $billing_address !!}
                            @endif
                            <!-- shipping_address -->
                            @if($invoice['user']->add_shipping_addres == 1 ||$invoice['user']->add_shipping_addres == true )
                                @if($shipping_address)
                                    @lang('pdf_ship_to')
                                    {!! $shipping_address !!}
                                @endif
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

  
 

    <hr class="item-cell-table-hr" style="margin-top: 5px; margin-bottom: 5px">

    <div class="content-wrapper">

        @php
            $service = null
        @endphp

        @php
            $_currency = $invoice['user']->currency;
            $_items = $invoice['items'];
            // $_avalaraTaxes = $invoice->avalaraInvoice ? $invoice->avalaraInvoice->avalaraTaxes()->with('item:id,name,description')->get(['id', 'invoice_item_id','item_id', 'amount', 'rate', 'tax', 'name', 'lvl']) : collect();
        @endphp

        <div style="position: relative; clear: both;">
            @include('app.pdf.invoice.partials_hold.table')
        </div>

        
    @if(count($invoice['tables_selected']) != 0)
        <div class="content-wrapper center-horizontal">
            <div style="width:100%" class="text-center">
                <span class="title-details">@lang('pdf_tables_selected'):</span>
            </div>
            <table  style="margin-left: 20px;" width="100%" class="items-table line">

                <tr class="item-table-heading-row">
                    <th width="50%" class="  text-left item-table-heading">@lang('pdf_invoice_table_name')</th>
                    <th width="50%" class="  text-center item-table-heading">@lang('pdf_invoice_quantity_persons')</th>
                </tr>

                @foreach($invoice['tables_selected'] as $table_selected)
                    <tr class="item-row">
                        <td class=" text-left item-cell" style="vertical-align: top;">
                        <span>
                            {{ $table_selected['table']['name']  }}
                            </span>
                        </td>
                        <td class=" text-center item-cell" style="vertical-align: top;">
                            <span>
                                {{ $table_selected['quantity']  }}
                            </span>
                        </td>
                            
                    </tr>
                @endforeach
            </table>
        </div>
    @endif 
    
        @if($invoice['notes'] )
            <div class="notes">
                <div class="notes-label">
                    @lang('pdf_notes')
                </div>
                {!! $invoice['notes'] !!}
            </div>
        @endif

    </div>

   

    <div class="">
        @isset($invoice['contact'])
            @if($invoice['contact'])
                <div class="content-wrapper center-horizontal" style="margin-top: 10px;">
                    <table width="100%" style="margin: 0px 5px 0px 5px">
                        <tr>
                            <!-- contact -->
                            <td class="text-center">    
                                <div class="box-details">
                                    <div class="">
                                        <span class="title-details">@lang('pdf_hold_invoice_contact'):</span>
                                    </div>
                                    <div class="text-details">
                                        <span>@lang('pdf_hold_invoice_name'):</span>
                                        <span>{{$invoice['contact']['name']}}</span>  
                                    </div>
                                    <div class="text-details">
                                        <span>@lang('pdf_hold_invoice_last_name'):</span>
                                        <span>{{$invoice['contact']['last_name']}}</span>  
                                    </div>
                                    <div class="text-details">
                                        <span>@lang('pdf_hold_invoice_phone'):</span>
                                        <span>{{$invoice['contact']['phone']}}</span>  
                                    </div>
                                    <div class="text-details">
                                        <span>@lang('pdf_hold_invoice_second_phone'):</span>
                                        <span>{{$invoice['contact']['second_phone']}}</span>  
                                    </div>
                                    <div class="text-details">
                                        <span>@lang('pdf_hold_invoice_identification'):</span>
                                        <span>{{$invoice['contact']['identification']}}</span>  
                                    </div>
                                    <div class="text-details">
                                        <span>@lang('pdf_hold_invoice_email'):</span>
                                        <span>{{$invoice['contact']['email']}}</span>  
                                    </div>
                                    
                                </div>
                            </td>
                            <td style="vertical-align:top ;">
                            </td>
                            <td style="vertical-align:top ;" >
                            </td>
                        </tr>
                    </table>
                </div>
            @endif
        @endisset
    </div>

    <div class="">
        @if($store)
            <div class="content-wrapper center-horizontal" style="margin-top: 10px;">
                <table width="100%" style="margin: 0px 5px 0px 5px">
                    <tr>
                        <!-- store -->
                        <td class="text-center">    
                            <div class="box-details">
                                <div class="">
                                    <span class="title-details">@lang('pdf_hold_store'):</span>
                                </div>
                                <div class="text-details">
                                    <span>@lang('pdf_hold_store_name'):</span>
                                    <span>{{$store['name']}}</span>  
                                </div>
                                <div class="text-details">
                                    <span>@lang('pdf_hold_store_description'):</span>
                                    <span>{{$store['description']}}</span>  
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align:top ;">
                        </td>
                        <td style="vertical-align:top ;" >
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    </div>


    {{-- 
    <div class="notes2">
        @if($Footer)
        <br>
        <hr>
            {!! $Footer !!}
        @endif
    </div> --}}

</body>

</html>
