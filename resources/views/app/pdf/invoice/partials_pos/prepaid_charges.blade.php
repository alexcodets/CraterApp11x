@if($invoice->prepaid_amount > 0 || $invoice->tax_prepaid_amount > 0)
<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title text-center" colspan="2">@lang('pdf_prepaid_charges_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th class="pl-0 text-left item-table-heading">@lang('pdf_total_prepaid_charges_label')</th>
        <th class="text-left item-table-heading">@lang('pdf_total_prepaid_taxes_label')</th>
    </tr>
    <tr class="item-row">        
        <td
            class="pl-0 text-left item-cell"
            style="vertical-align: top;"
        >
            {{ $_currency->symbol . number_format((float)$invoice->prepaid_amount, 2, '.', ',') }}
        </td>       
        <td
            class="text-left item-cell"
            style="vertical-align: top;"
        >
            {{ $_currency->symbol . number_format((float)$invoice->tax_prepaid_amount, 2, '.', ',') }}
        </td>
    </tr>
</table>
@endif
