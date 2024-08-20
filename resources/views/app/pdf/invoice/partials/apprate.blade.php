<table width="100%" class="items-table" cellspacing="0" border="0">
    <tr>
        <td class="item-table-title" colspan="3">@lang('pdf_apprate_label')</td>
    </tr>
    <tr class="item-table-heading-row">
        <th width="50%" class="pl-0 text-left item-table-heading">@lang('pdf_name_label')</th>
        <th class="text-center item-table-heading">@lang('pdf_quantity_label')</th>
        <th class="text-right item-table-heading">@lang('pdf_total')</th>
    </tr>

    @php
    $_apprate =$_apprate->get();

    @endphp
    @foreach ($_apprate as  $value)

    <tr class="item-row">
        <td
            class="pl-0 text-left item-cell"
            style="vertical-align: top;"
        >
        {{  $value->app_name }}
        </td>
        <td
            class="text-center item-cell"
            style="vertical-align: top;"
        >
        {{  $value->quantity }}
        </td>

        <td
            class="text-right item-cell"
            style="vertical-align: top;"
        >
        {{  $value->costo }}
        </td>
    </tr>


@endforeach
</table>

<div class="total-display-container">
    <table width="100%" cellspacing="0px" border="0" class="total-display-table @if(count($_extensions) > 12) page-break @endif">
        <!--<tr>
            <td class="border-0 border-left-top total-table-attribute-label">
                @lang('pdf_count')
            </td>
            <td class="py-2 border-0 border-right-top item-cell total-table-attribute-value">
                {{ count($_extensions) }}
            </td>
        </tr>
        <tr>
            <td class="py-3"></td>
            <td class="py-3"></td>
        </tr>-->
        <tr>
            <td class="border-0 total-border-left total-table-attribute-label">

            </td>
            <td
                class="py-8 border-0 total-border-right item-cell total-table-attribute-value"
                style="color: #5851D8"
            >
                {!! format_money_pdf($invoice->pbx_total_apprate, $_currency) !!}
            </td>
        </tr>
    </table>
</div>
