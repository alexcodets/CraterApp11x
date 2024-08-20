<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tax Summary Report</title>
    <style type="text/css">
        body {
            font-family: "DejaVu Sans";
        }

        table {
            border-collapse: collapse;
        }

        .sub-container{
            padding: 0px 20px;
        }

        .report-header {
            width: 100%;
            margin-bottom: 60px
        }

        .heading-text {
            font-weight: bold;
            font-size: 24px;
            color: #5851D8;
            width: 100%;
            text-align: left;
            padding: 0px;
            margin: 0px;
        }

        .heading-date-range {
            font-weight: normal;
            font-size: 15px;
            color: #A5ACC1;
            width: 100%;
            text-align: right;
            padding: 0px;
            margin: 0px;
        }

        .sub-heading-text {
            font-weight: bold;
            font-size: 16px;
            color: #595959;
            padding: 0px;
            margin: 0px;
            margin-top: 6px;
        }

        .tax-types-title {
            margin-top: 20px;
            padding-left: 3px;
            font-size: 16px;
            line-height: 21px;
            color: #040405;
        }

        .tax-table-container {
            padding-left: 10px;
        }

        .tax-table {
            width: 100%;
            padding-bottom: 10px;
        }

        .tax-title {
            padding: 0px;
            margin: 0px;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .tax-amount {
            padding: 0px;
            margin: 0px;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #595959;
        }

        .tax-total-table {
            border-top: 1px solid #EAF1FB;
            width: 100%;
        }

        .tax-total-cell {
            padding-right: 18px;
            padding-top: 10px;
        }

        .tax-total {
            padding-top: 10px;
            padding-right: 20px;
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            line-height: 21px;
            text-align: right;
            color: #040405;
        }

        .report-footer {
            width: 100%;
            margin-top: 40px;
            padding: 15px 20px;
            background: #F9FBFF;
            box-sizing: border-box;
        }

        .report-footer-label {
            padding: 0px;
            margin: 0px;
            text-align: left;
            font-weight: bold;
            font-size: 16px;
            line-height: 21px;
            color: #595959;
        }

        .report-footer-value {
            padding: 0px;
            margin: 0px;
            text-align: right;
            font-weight: bold;
            font-size: 20px;
            line-height: 21px;
            color: #5851D8;
        }
    </style>
</head>
<body>
    <div class="sub-container">
        <table class="report-header">
            <tr>
                <td>
                    <p class="heading-text">
                        {{ $company->name }}
                    </p>
                </td>
                <td>
                    <p class="heading-date-range">
                        {{ $from_date }} - {{ $to_date }}
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="2" >
                    <p class="text-left" style="font-size:10px;">{{$info_company}}</p>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    <p class="sub-heading-text">@lang('pdf_tax_report_label') {{ $title }} </p>
                </td>


            </tr>

            <tr>

            <td colspan="8">
                    <p class="sub-heading-text">{{ $reporte }} </p>
                </td>
                </tr>
        </table>


        @if ($type === "general")
        <p class="tax-types-title">@lang('pdf_tax_types_label')</p>
        <div class="tax-table-container">
            <table class="tax-table">
                @foreach ($taxTypes as $tax)
                    <tr>
                        <td>
                            <p class="tax-title">
                                {{ $tax->taxType->name }}
                            </p>
                        </td>
                        <td>
                            <p class="tax-amount">
                                {!! format_money_pdf($tax->total_tax_amount) !!}
                            </p>
                        </td>
                    </tr>
                @endforeach
                
                @if ($includeCdr)
                <table class="tax-total-table">
                    <tr>
                        <td class="tax-total-cell">
                            <p class="tax-total">
                                {!! format_money_pdf($subtotalTaxAmount) !!}
                            </p>
                        </td>
                    </tr>
                </table>
                @endif

            </table>
        </div>
        @endif

        @if ($includeCdr && $type === "general")
        <p class="tax-types-title">@lang('pdf_tax_types_cdr_label')</p>
        <div class="tax-table-container">
            <table class="tax-table">
                @foreach ($taxesCdr as $tax)
                    <tr>
                        <td>
                            <p class="tax-title">
                                {{ $tax->name }}
                            </p>
                        </td>
                        <td>
                            <p class="tax-amount">
                                {!! format_money_pdf_cdr($tax->tax) !!}
                            </p>
                        </td>
                    </tr>
                @endforeach

            </table>
            <table class="tax-total-table">
                <tr>
                    <td class="tax-total-cell">
                        <p class="tax-total">
                            {!! format_money_pdf_cdr($subtotalTaxAmountCdr) !!}
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        @endif



        @if ($type === "customer")
        <p class="tax-types-title">Taxes by Customer</p>

        @foreach ($arraycustomer as $customer)
            <div class="tax-table-container">
                <p class="tax-types-title"> Customer:  {{ $customer["customer"]->name }} - {{ $customer["customer"]->customcode }}</p>
                <br />

                <table class="tax-table">
                @foreach ($customer["taxtype"] as $tax)
                <tr>
                    <td>
                        <p class="tax-title">
                            {{ $tax->taxType->name }}
                        </p>
                    </td>
                    <td>
                        <p class="tax-amount">
                            {!! format_money_pdf($tax->total_tax_amount) !!}
                        </p>
                    </td>
                </tr>
                @endforeach
                </table>

                <table class="tax-total-table">
                    <tr>
                        <td class="tax-total-cell">
                            <p class="tax-total">
                                {!! format_money_pdf($customer["total"]) !!}
                            </p>
                        </td>
                    </tr>
                </table>

                @if ($includeCdr)
                <p class="tax-types-title">@lang('pdf_tax_types_cdr_label')</p>
                <div class="tax-table-container">
                    <table class="tax-table">
                        @foreach ($customer["taxesCdr"] as $tax)
                            <tr>
                                <td>
                                    <p class="tax-title">
                                        {{ $tax->name }}
                                    </p>
                                </td>
                                <td>
                                    <p class="tax-amount">
                                        {!! format_money_pdf_cdr($tax->tax) !!}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
        
                    </table>
                    <table class="tax-total-table">
                        <tr>
                            <td class="tax-total-cell">
                                <p class="tax-total">
                                    {!! format_money_pdf_cdr($customer["subtotalCdr"]) !!}
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                @endif
        </div>


            <br />
        @endforeach


        @endif


        @if ($type === "item")
        <p class="tax-types-title">Taxes by Item</p>

        @foreach ($arrayitems as $item)

        <p class="tax-types-title">Item:  {{ $item["item"]->name }} - {{ $item["item"]->item_number}}</p>
            <br />

            <div class="tax-table-container">
            <table class="tax-table">
            @foreach ($item["taxes"] as $tax)
            <tr>
                        <td>
                            <p class="tax-title">
                                {{ $tax["name"] }}
                            </p>
                        </td>
                        <td>
                            <p class="tax-amount">
                                {!! format_money_pdf($tax["amount"]) !!}
                            </p>
                        </td>
                    </tr>
            @endforeach
            </table>

            <table class="tax-total-table">
        <tr>
            <td class="tax-total-cell">
                <p class="tax-total">
                    {!! format_money_pdf($item["totalitem"]) !!}
                </p>
            </td>
        </tr>
        </table>
        </div>


        <br />
        @endforeach
        @endif
    </div>

    <table class="tax-total-table">
        <tr>
            <td class="tax-total-cell">
                <p class="tax-total">
                   
                </p>
            </td>
        </tr>
    </table>
    <table class="report-footer">
        <tr>
            <td>
                <p class="report-footer-label">@lang('pdf_total_tax_label')</p>
            </td>
            <td>
                <p class="report-footer-value">
                    {!! format_money_pdf($totalTaxAmount) !!}
                </p>
            </td>
        </tr>
    </table>
</body>
</html>