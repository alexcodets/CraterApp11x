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
            width: 100%;
            height: 90px;
            left: 0px;
            top: -50px;
        }

        .header-logo {
            height: 50px;
            margin-top: 20px;
            text-transform: capitalize;
            color: #817AE3;
        }

        .header {
            font-size: 16.5px;
            color: rgba(0, 0, 0, 0.7);
        }

        .content-wrapper {
            display: block;
            margin-top: 0px;
            padding-top: 16px;
            padding-bottom: 20px;
        }

        .company-address-container {
            padding-top: 15px;
            padding-left: 30px;
            float: left;
            width: 30%;
            text-transform: capitalize;
            margin-bottom: 2px;
        }

        .company-address-container h1 {
            font-size: 13px;
            line-height: 22px;
            letter-spacing: 0.05em;
            margin-bottom: 0px;
            margin-top: 10px;
        }

        .company-address {
            margin-top: 2px;
            text-align: left;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            width: 280px;
            word-wrap: break-word;
        }

        .invoice-details-container {
            float: right;
            padding: 10px 30px 0 0;
        }

        .attribute-label {
            font-size: 10px;
            line-height: 18px;
            padding-right: 40px;
            text-align: left;
            color: #55547A;
        }

        .attribute-value {
            font-size: 10px;
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
            font-size: 10px;
            line-height: 15px;
            color: #595959;
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
            font-size: 10px;
            line-height: 18px;
            padding: 0px;
            margin-top: 27px;
            margin-bottom: 0px;
        }

        .billing-address-name {
            max-width: 160px;
            font-size: 13px;
            line-height: 22px;
            padding: 0px;
            margin: 0px;
        }

        .billing-address {
            font-size: 10px;
            line-height: 15px;
            color: #595959;
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
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            padding: 45px 30px 0px 40px;
            margin: 0px;
            word-wrap: break-word;
        }

        /* -- Items Table -- */

        .items-table {
            margin-top: 10px;
            padding: 0px 30px 10px 30px;
            page-break-before: avoid;
            page-break-after: auto;
        }

        .items-table hr {
            height: 0.1px;
        }

        .item-table-title {
            font-weight:bold;
            color: #ffffff;
            text-align:left;
            background-color:#5851D8;
            padding-left: 10;
        }

        .item-table-heading {
            font-size: 10.5;
            text-align: center;
            color: rgba(0, 0, 0, 0.85);
            padding: 5px;
            color: #55547A;
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
            line-height: 12px;
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
            margin-top: 10px;
        }

        .total-table-attribute-label {
            font-size: 11px;
            color: #55547A;
            text-align: left;
            padding-left: 10px;
        }

        .total-table-attribute-value {
            font-weight: bold;
            text-align: right;
            font-size: 11px;
            color: #040405;
            padding-right: 10px;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .total-border-left {
            border: 1px solid #E8E8E8 !important;
            border-right: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        .total-border-right {
            border: 1px solid #E8E8E8 !important;
            border-left: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        .border-left-top {
            border: 1px solid #E8E8E8 !important;
            border-right: 0px !important;
            border-bottom: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        .border-left-bottom {
            border: 1px solid #E8E8E8 !important;
            border-right: 0px !important;
            border-top: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        .border-right-top {
            border: 1px solid #E8E8E8 !important;
            border-left: 0px !important;
            border-bottom: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        .border-right-bottom {
            border: 1px solid #E8E8E8 !important;
            border-left: 0px !important;
            border-top: 0px !important;
            padding-top: 0px;
            padding: 8px !important;
        }

        /* -- Notes -- */

        .notes {
            font-size: 10px;
            color: #595959;
            margin-top: 15px;
            margin-left: 30px;
            width: 442px;
            text-align: left;
            page-break-inside: avoid;
        }

        .notes-label {
            font-size: 13px;
            line-height: 22px;
            letter-spacing: 0.05em;
            color: #040405;
            width: 108px;
            height: 19.87px;
            padding-bottom: 10px;
        }

        /* -- Helpers -- */

        .text-primary {
            color: #5851DB;
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
    </style>
</head>

<body>
    <div class="header-container">
        <table width="100%">
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
        <hr class="header-bottom-divider" style="border: 0.620315px solid #E8E8E8;" />
    </div>
    <div class="content-wrapper">
        <div style="padding-top: 30px">
            <div class="company-address-container company-address">
                {!! $company_address !!}
            </div>
            <div class="invoice-details-container">
                <table>
                    <tr>
                        <td class="attribute-label">@lang('pdf_invoice_number')</td>
                        <td class="attribute-value"> &nbsp;{{$invoice->invoice_number}}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">@lang('pdf_invoice_date')</td>
                        <td class="attribute-value"> &nbsp;{{$invoice->formattedInvoiceDate}}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">@lang('pdf_invoice_due_date')</td>
                        <td class="attribute-value"> &nbsp;{{$invoice->formattedDueDate}}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="billing-address-container billing-address">
            @if($billing_address)
                
                {!! $billing_address !!}
            @endif
        </div>

        <div class="shipping-address-container shipping-address" @if($billing_address !== '</br>') style="float:left;" @else style="display:block; float:left; padding-left: 0px;" @endif>
        @if($invoice->user->add_shipping_addres == 1 ||$invoice->user->add_shipping_addres == true )
            @if($shipping_address)
                @lang('pdf_ship_to')
                {!! $shipping_address !!}
            @endif
        @endif
        </div>

        @php
            $service = null
        @endphp

        @if(!is_null($invoice->pbx_service_id))
            @php
                $service = $invoice->pbxService
            @endphp

            <div class="pbx-service-container pbx-service">
                <div style="font-size: 13px; letter-spacing: 0.05em; padding-bottom: 5px">
                    <strong>@lang('pdf_pbx_service_label')</strong>
                </div>
                @lang('pdf_customer_number')
                {{ ': ' . $invoice->user->customcode }}<br>
                @lang('pdf_service_number')
                {{ ': ' . $service->pbx_services_number }}<br>
                @lang('pdf_billing_period')
                {{ ': (' . $invoice->formattedPrevDate . ' - ' . $invoice->formattedRenewalDate . ')' }}<br>
            </div>
        @endif



        @if(!is_null($invoice->pbxservice_date_prev) && !is_null($invoice->pbxservice_date_renewal)  && is_null($invoice->pbx_service_id))


<div class="pbx-service-container pbx-service">
    <div style="font-size: 13px; letter-spacing: 0.05em; padding-bottom: 5px">
        <strong>SERVICE</strong>
    </div>

                @lang('pdf_billing_period')
                {{ ': (' . $invoice->formattedPrevDate . ' - ' . $invoice->formattedRenewalDate . ')' }}<br>
            </div>
        @endif

        @php
        $_currency = $invoice->user->currency;
        $_items = $invoice->items;
        $_avalaraTaxes = $invoice->avalaraInvoice ? $invoice->avalaraInvoice->avalaraTaxes()->with('item:id,name,description')->get(['id', 'invoice_item_id','item_id', 'amount', 'rate', 'tax', 'name', 'lvl']) : collect();
        @endphp
        <div style="position: relative; clear: both;">
            @if(is_null($invoice->pbx_service_id))
                @include('app.pdf.invoice.partials.table')
            @else
            @php
                $_extensions = $invoice->extensions;
                $_dids = $invoice->dids;
                $_charges = $invoice->additionalCharges;
                $_cdr = $invoice->pbxCdr();
                $_apprate = $invoice->pbxAppRates();
                $_avalaraTaxes = null;
                if( $invoice->avalaraInvoice != null){
                $_avalaraTaxes = $invoice->avalaraInvoice ? $invoice->avalaraInvoice->avalaraTaxes()->with('item:id,name,description')->get(['id', 'invoice_item_id','item_id', 'amount', 'rate', 'tax', 'name', 'lvl']) : collect();
            }
            @endphp



                @if($service && $service->allow_pbxpackages)
                    @include('app.pdf.invoice.partials.pbx_package')
                @endif

                @if(count($_items) && $service->allow_items)
                    @include('app.pdf.invoice.partials.items')
                @endif
                @if(count($_extensions) && $service->allow_extensions)
                    @include('app.pdf.invoice.partials.extensions')
                @endif
                @if(count($_dids) && $service->allow_did)
                    @include('app.pdf.invoice.partials.did')
                @endif
                @if(count($_charges) && $service->allow_aditionalcharges)
                    @include('app.pdf.invoice.partials.charges')
                @endif

                @if(count(array( $_apprate)) && $service->allow_customapp)
                    @include('app.pdf.invoice.partials.apprate')
                @endif
                @if($_cdr->count() > 0 && $service->allow_usagesummary)
                    @include('app.pdf.invoice.partials.cdr')
                @endif
                @if( !empty($service->pbxPackage) && $user->status_payment === "prepaid")
                    @include('app.pdf.invoice.partials.prepaid_charges')
                @endif
                @isset($_avalaraTaxes)
                    @if($_avalaraTaxes->isNotEmpty())
                        @include('app.pdf.invoice.partials.avalara_taxes')
                    @endif
                @endisset

                @include('app.pdf.invoice.partials.footer')
            @endif
        </div>
        <div class="notes">
            @if($notes)
                <div class="notes-label">
                    @lang('pdf_notes')
                </div>
                {!! $notes !!}
            @endif
        </div>
</body>

</html>
