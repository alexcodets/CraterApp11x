@php
$package = $invoice->pbxService->pbxPackage;
@endphp
<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="4">@lang('pdf_pbx_package_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th class="pl-0 text-left item-table-heading">@lang('pdf_name_label')</th>
        <th class="text-center item-table-heading">@lang('pdf_number_label')</th>
        <th class="pl-10 text-right item-table-heading">@lang('pdf_price_label')</th>
        <th class="text-right item-table-heading">@lang('pdf_type_service_label')</th>
    </tr>

    <tr class="item-row">
        <td class="pl-0 text-left item-cell" style="vertical-align: top;">
            {{ $package->pbx_package_name }}
        </td>
        <td class="text-center item-cell" style="vertical-align: top;">
            {{ $package->packages_number }}
        </td>
        <td class="text-right item-cell" style="vertical-align: top;">
            {!! format_money_pdf($invoice->pbx_packprice, $_currency) !!}
        </td>
        <td class="text-right item-cell" style="vertical-align: top;">
            {{ ucfirst($package->status_payment) }}
        </td>
    </tr>
</table>
