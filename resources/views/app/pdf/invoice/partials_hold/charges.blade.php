<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="6">@lang('pdf_charges_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th class="pl-0 text-left item-table-heading">@lang('pdf_name_label')</th>
        <th class="pr-20 text-right item-table-heading">@lang('pdf_amount_label')</th>
        <th class="pr-20 text-right item-table-heading">@lang('pdf_quantity_label')</th>
        <th class="text-right item-table-heading">@lang('pdf_type_label')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_template_label')</th>
        <th class="text-right item-table-heading">@lang('pdf_total')</th>
    </tr>

    @foreach ($_charges as $charge)
        <tr class="item-row">
            <td
                class="pl-0 text-left item-cell"
                style="vertical-align: top;"
            >
                {{ $charge->additional_charge_name }}
            </td>
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {{ $_currency->symbol . number_format((float)$charge->additional_charge_amount, 2, '.', ',') }}
            </td>
            <td
                class="pr-20 text-right item-cell"
                style="vertical-align: top;"
            >
                {{ $charge->qty }}
            </td>
            <td
                class="text-right item-cell"
                style="vertical-align: top;"
            >
                {{ $charge->additional_charge_type }}
            </td>
            <td
                class="pl-10 text-right item-cell"
                style="vertical-align: top;"
            >
                {{ $charge->template_name }}
            </td>
            <td
                class="text-right item-cell"
                style="vertical-align: top;"
            >
                {{ $_currency->symbol . number_format((float)$charge->total, 2, '.', ',') }}
            </td>
        </tr>
    @endforeach
</table>

<!--<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if(count($_charges) > 12) page-break @endif">
        <tr>
            <td class="border-0 border-left-top total-table-attribute-label">
                @lang('pdf_count')
            </td>
            <td class="py-2 border-0 border-right-top item-cell total-table-attribute-value">
                {{ count($_charges) }}
            </td>
        </tr>
        <tr>
            <td class="py-3"></td>
            <td class="py-3"></td>
        </tr>
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">
                @lang('pdf_subtotal')
            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($invoice->pbx_total_aditional_charges, $_currency) !!}
            </td>
        </tr>
    </table>
</div>-->


