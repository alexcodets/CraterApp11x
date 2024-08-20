<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="5">@lang('pdf_payment_paid_label')</td>
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

        {!! format_money_pdf($value["amount"] , $_currency) !!}

        </td>
    </tr>

    @if($value["type"] != null )

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

        <span class="item-description"> <b>@lang('pdf_type_label'): </b> {{$value["type"] }}</span><br>
        <span class="item-description"> <b>@lang('pdf_payment_gateway'): </b>{{$value["Paymentgateway"] }}</span><br>
        <span class="item-description"> <b>@lang('pdf_transaction_number'): </b> {{$value["transaction_id"] }}</span>

        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >
        @if($value["type"] == "Credit Card" )
        <span class="item-description"> <b>@lang('pdf_payment_type_credit_card'): </b> {{$value["credit_card"] }}</span><br>
        <span class="item-description"> <b>@lang('pdf_payment_credit_card_number'): </b> {{$value["card_number"] }} </span>
        @endif

        @if($value["type"] == "ACH" )
        <span class="item-description"> <b>@lang('pdf_account_type'): </b> {{$value["type_ach"] }}</span><br>
        <span class="item-description"> <b>@lang('pdf_account_number'): </b> {{$value["account_number"] }} </span>
        @endif
       
        </td>
    </tr>
      
    @endif

@endforeach
</table>

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table">
        <!--<tr>
            <td class="border-0 border-left-top total-table-attribute-label">
                @lang('pdf_count')
            </td>
            <td class="py-2 border-0 border-right-top item-cell total-table-attribute-value">

            </td>
        </tr>
        <tr>
            <td class="py-3"></td>
            <td class="py-3"></td>
        </tr>-->
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                @lang('pdf_payment_applied_total')
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($PaymentsTotal, $_currency) !!}
            </td>
        </tr>
        @if($invoice->paid_status != "UNPAID")
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label"
                style="color: #FF0000 !important;font-size: 15px !important;"
            >
                @lang('pdf_payment_amount_due')
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"     
                style="color: #000000 !important;font-size: 14px !important;"           
            >
                {!! format_money_pdf($invoice->due_amount, $_currency) !!}
            </td>
        </tr>
        @endif
    </table>
</div>
