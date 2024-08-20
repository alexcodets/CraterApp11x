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

        table {
            border-collapse: collapse;
        }

        hr {
            margin: 0 30px 0 30px;
            color: rgba(0, 0, 0, 0.2);
            border: 0.5px solid #EAF1FB;
        }

        /* -- Header -- */

        .header-container {
            background: #817AE3;
            position: absolute;
            width: 100%;
            height: 141px;
            left: 0px;
            top: -60px;
        }

        .header-section-left {
            padding-top: 45px;
            padding-bottom: 45px;
            padding-left: 30px;
            display: inline-block;
            width: 30%;
        }

        .header-logo {
            position: absolute;
            height: 50px;
            text-transform: capitalize;
            color: #fff;
        }

        .header-section-right {
            display: inline-block;
            width: 35%;
            float: right;
            padding: 20px 30px 20px 0px;
            text-align: right;
            color: white;
        }

        .header {
            font-size: 16.5px;
            color: rgba(0, 0, 0, 0.7);
        }

        /*  -- Estimate Details -- */

        .invoice-details-container {
            text-align: center;
            width: 40%;
        }

        .invoice-details-container h1 {
            margin: 0;
            font-size: 21px;
            line-height: 36px;
            text-align: right;
        }

        .invoice-details-container h4 {
            margin: 0;
            font-size: 9px;
            line-height: 15px;
            text-align: right;
        }

        .invoice-details-container h3 {
            margin-bottom: 1px;
            margin-top: 0;
        }

        /* -- Content Wrapper -- */

        .content-wrapper {
            display: block;
            margin-top: 60px;
            padding-bottom: 20px;
        }

        .address-container {
            display: block;
            padding-top: 20px;
            margin-top: 18px;
        }

        /* -- Company -- */

        .company-address-container {
            padding: 0 0 0 30px;
            display: inline;
            float: left;
            width: 30%;
        }

        .company-address-container h1 {
            font-weight: bold;
            font-size: 13px;
            letter-spacing: 0.05em;
            margin-bottom: 0;
            /* margin-top: 18px; */
        }

        .company-address{
            font-size: 9px;
            line-height: 15px;
            color: #595959;
            margin-top: 0px;
            word-wrap: break-word;
        }

        /* -- Billing -- */

        .billing-address-container {
            display: block;
            /* position: absolute; */
            float: right;
            padding: 0 40px 0 0;
        }

        .billing-address-label {
            font-size: 11px;
            line-height: 18px;
            padding: 0px;
            margin-bottom: 0px;
        }

        .billing-address-name {
            max-width: 250px;
            font-size: 13px;
            line-height: 22px;
            padding: 0px;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .billing-address{
            font-size: 9px;
            line-height: 15px;
            color: #595959;
            padding: 0px;
            margin: 0px;
            width: 170px;
            word-wrap: break-word;
        }

        /* -- Shipping -- */

        .shipping-address-container {
            display: block;
            float: right;
            padding: 0 30px 0 0;
        }

        .shipping-address-label {
            font-size: 10px;
            line-height: 18px;
            padding: 0px;
            margin-bottom: 0px;
        }

        .shipping-address-name {
            max-width: 250px;
            font-size: 13px;
            line-height: 22px;
            padding: 0px;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .shipping-address {
            font-size: 9px;
            line-height: 15px;
            color: #595959;
            padding: 0px 30px 0px 30px;
            width: 170px;
            word-wrap: break-word;
        }

        /* -- Pbx Service -- */

        .pbx-service-container {
            display: block;
            /* position: absolute; */
            float: right;
            padding: 0 30px 0 0;
        }

        .pbx-service {
            font-size: 9px;
            line-height: 15px;
            color: #595959;
            padding: 0 30px 0 0;
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
            font-size: 12;
            text-align: center;
            padding: 5px;
           /* padding-top: 10px;*/
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

        .item-cell-table-hr {
            margin: 0 25px 0 30px;
        }

        .total-display-table {
            box-sizing: border-box;
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
            margin-left: 500px;
            border: 1px solid #EAF1FB;
            border-top: none;
        }

        .total-table-attribute-label {
            font-size: 10px;
            color: #55547A;
            text-align: left;
            padding-left: 10px;
        }

        .total-table-attribute-value {
            font-weight: bold;
            text-align: right;
            font-size: 10px;
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
            font-size: 11px;
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
                @if(false)
                <td width="60%" class="header-section-left">
                    <img class="header-logo" src="{{ $logo }}" alt="Company Logo">
                    @else
                <td width="60%" class="header-section-left" style="padding-top: 0px;">
                    @if($invoice->user->company)
                    <h3 class="header-logo"> {{$invoice->user->company->name}} </h3>
                    @endif
                    @endif
                </td>
                <td width="40%" class="header-section-right invoice-details-container">
                    <h1>@lang('pdf_invoice_label')</h1>
                    <h4>{{$invoice->invoice_number}}</h4>
                    <h4>{{$invoice->formattedInvoiceDate}}</h4>
                </td>
            </tr>
        </table>
    </div>
    <hr>
    <div class="content-wrapper">
        <div class="address-container">
            <div class="company-address-container company-address">
                {!! $company_address !!}
            </div>

            @php
                $service = null
            @endphp

            @if(!is_null($invoice->pbx_service_id))
                @php
                    $service = $invoice->pbxService
                @endphp

                <div class="pbx-service-container pbx-service">
                    <div style="font-size: 10px; line-height: 15px; padding-bottom: 5px">
                        <strong>@lang('pdf_pbx_service_label')</strong>
                    </div>
                    @lang('pdf_customer_number')
                    {{ ': ' . $invoice->user->customcode }}<br>
                    @lang('pdf_service_number')
                    {{ ': ' . $service->pbx_services_number }}<br>
                    @lang('pdf_billing_period')
                    {{ ': (' . $invoice->formattedPrevDate . ' - ' . $invoice->formattedRenewalDate . ')' }}<br>
                    @lang('pdf_invoice_date')
                    {{ ': ' . $invoice->formattedInvoiceDate }}<br>
                    @lang('pdf_due_date')
                    {{ ': ' . $invoice->formattedDueDate }}
                </div>
            @endif



            @if(!is_null($invoice->pbxservice_date_prev) && !is_null($invoice->pbxservice_date_renewal)  && is_null($invoice->pbx_service_id))


<div class="pbx-service-container pbx-service">
    <div style="font-size: 12px; letter-spacing: 0.05em; padding-bottom: 5px">
        <strong>SERVICE</strong>
    </div>

          @lang('pdf_billing_period')
          {{ ': (' . $invoice->formattedPrevDate . ' - ' . $invoice->formattedRenewalDate . ')' }}<br>
      </div>
  @endif

            @if($shipping_address !== '</br>')
                <div class="shipping-address-container shipping-address">
                @if($invoice->user->add_shipping_addres == 1 ||$invoice->user->add_shipping_addres == true )
        @if($shipping_address)
                @lang('pdf_ship_to')
                {!! $shipping_address !!}
            @endif
            @endif
                </div>
            @endif
            <div class="billing-address-container billing-address"  @if($shipping_address !== '</br>') @else style="float:right; margin-right:30px;" @endif>
                @if($billing_address)
                    
                    {!! $billing_address !!}
                @endif
            </div>

            <div style="clear: both;"></div>
        </div>

            @if(is_null($invoice->pbx_service_id))
            @php
        $_currency = $invoice->user->currency;
        $_items = $invoice->items;
        $_avalaraTaxes = $invoice->avalaraInvoice ? $invoice->avalaraInvoice->avalaraTaxes()->with('item:id,name,description')->get(['id', 'invoice_item_id','item_id', 'amount', 'rate', 'tax', 'name', 'lvl']) : collect();
        @endphp
                @include('app.pdf.invoice.partials.table')
            @else
                @php
                    $_currency = $invoice->user->currency;
                    $_items = $invoice->items;
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

                @if(!empty($service) && $service->allow_pbxpackages)
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
                @if(!empty($service->pbxPackage) && $user->status_payment === 'prepaid')
                    @include('app.pdf.invoice.partials.prepaid_charges')
                @endif
                @isset($_avalaraTaxes)
                    @if($_avalaraTaxes->isNotEmpty())
                        @include('app.pdf.invoice.partials.avalara_taxes')
                    @endif
                @endisset

                @include('app.pdf.invoice.partials.footer')
            @endif
            <div class="notes">
                @if($notes)
                    <div class="notes-label">
                        @lang('pdf_notes')
                    </div>
                    {!! $notes !!}
                @endif
            </div>
        </div>
</body>

</html>
