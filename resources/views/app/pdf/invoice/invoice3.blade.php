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
            color: rgba(0, 0, 0, 0.2);
            border: 0.5px solid #EAF1FB;
        }

        /* -- Header -- */

        .header-container {
            margin-top: -30px;
            width: 100%;
            padding: 0px 30px;
        }
        .header-logo {
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
        .company-address {
            margin-top: 12px;
            font-size: 10px;
            line-height: 15px;
            color: #595959;
            word-wrap: break-word;
        }

        /* -- Content Wrapper  */

        .content-wrapper {
            display: block;
            padding-top: 0px;
            padding-bottom: 20px;
        }

        .customer-address-container {
            display: block;
            /*float: left;
            width: 45%;*/
            padding: 10px 0 0 30px;
        }

        /* -- Shipping -- */
        .shipping-address-container {
            float:right;
            display: block;
        }

        .shipping-address-container--left {
            float:left;
            display: block;
            padding-left: 0;
        }

        .shipping-address {
            font-size: 9px;
            line-height: 15px;
            color: #595959;
            margin-top: 5px;
            width: 160px;
            word-wrap: break-word;
        }

        /* -- Billing -- */

        .billing-address-container {
            display: block;
            float: left;
        }

        .billing-address {
            font-size: 9px;
            line-height: 15px;
            color: #595959;
            margin-top: 5px;
            width: 160px;
            word-wrap: break-word;
        }

        /* -- Pbx Service -- */

        .pbx-service-container {
            float: right;
            display: block;
            padding-right: 30px;
        }

        .pbx-service {
            font-size: 9px;
            line-height: 15px;
            color: #595959;
            margin-top: 5px;
            width: 160px;
            word-wrap: break-word;
        }

        /*  -- Estimate Details -- */

        .invoice-details-container {
            display: block;
            float: right;
            padding: 10px 30px 0 0;
        }

        .attribute-label {
            font-size: 11px;
            line-height: 18px;
            text-align: left;
            color: #55547A
        }

        .attribute-value {
            font-size: 11px;
            line-height: 18px;
            text-align: right;
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
            font-size: 9px;
            line-height: 18px;
        }

        tr.item-row td {
            font-size: 10px;
            line-height: normal;
        }

        .item-cell {
            font-size: 11;
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

        .item-cell-table-hr {
            margin: 0 30px 0 30px;
        }

        /* -- Total Display Table -- */

        .total-display-container {
            padding: 0 25px;
        }


        .total-display-table {
            box-sizing: border-box;
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
            margin-left: 500px;
            margin-top: 10px;
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
                <td width="50%" class="header-section-left">
                    @if(false)
                        <img class="header-logo" src="{{ $logo }}" alt="Company Logo">
                    @else
                        <h3 class="header-logo"> {{$invoice->user->company->name}} </h3>
                    @endif
                </td>
                <td width="50%" class="company-address-container company-address text-right">
                    {!! $company_address !!}
                </td>
            </tr>
        </table>
    </div>

    <hr class="header-bottom-divider">

    <div class="content-wrapper">
        <div class="main-content">
            <div class="customer-address-container">
                <div class="billing-address-container billing-address">
                    @if($billing_address)
                       
                        {!! $billing_address !!}
                    @endif
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
                @if($billing_address !== '</br>')
                <div class="shipping-address-container shipping-address">
                @else
                <div class="shipping-address-container--left shipping-address">
                @endif
                @if($invoice->user->add_shipping_addres == 1 ||$invoice->user->add_shipping_addres == true )
        @if($shipping_address)
                @lang('pdf_ship_to')
                {!! $shipping_address !!}
            @endif
            @endif
                </div>
                <div style="clear: both;"></div>
            </div>
            <div style="clear: both;"></div>
        </div>

        @php
            $_currency = $invoice->user->currency;
            $_items = $invoice->items;
            $_avalaraTaxes = $invoice->avalaraInvoice ? $invoice->avalaraInvoice->avalaraTaxes()->with('item:id,name,description')->get(['id', 'invoice_item_id','item_id', 'amount', 'rate', 'tax', 'name', 'lvl']) : collect();
        @endphp

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
    </div>
</body>

</html>
