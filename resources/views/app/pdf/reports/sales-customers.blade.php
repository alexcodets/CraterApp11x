<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales Customer Report</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
        }

        table {
            border-collapse: collapse;
        }

        .table-sale {
            width: 100%;
        }

        th, td {
            text-align: left;
            font-weight: normal;
            font-size: 14px;

        }

        table tr td:last-child {
            text-align: right;
            font-size: 14px;
            font-weight: normal;
        }

        .sub-container {
            padding: 0 20px;
        }

        .report-header {
            width: 100%;
        }

        .heading-text {
            font-weight: bold;
            font-size: 24px;
            color: #5851D8;
            width: 100%;
            text-align: left;
            padding: 0;
            margin: 0;
        }

        .heading-date-range {
            font-weight: normal;
            font-size: 15px;
            color: #A5ACC1;
            width: 100%;
            text-align: right;
            padding: 0;
            margin: 0;
        }

        .sub-heading-text {
            font-weight: bold;
            font-size: 16px;
            line-height: 21px;
            color: #595959;
            padding: 0;
            margin: 30px 0 0;
        }

        .sales-customer-name {
            margin-top: 20px;
            padding-left: 3px;
            font-size: 16px;
            line-height: 21px;
            color: #040405;
        }

        .sales-table-container {
            padding-left: 10px;
        }

        .sales-table {
            width: 100%;
            padding-bottom: 10px;
        }

        .sales-information-text {
            padding: 0;
            margin: 0;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .sales-amount {
            padding: 0;
            margin: 0;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #595959;
        }

        .sales-total-indicator-table {
            border-top: 1px solid #EAF1FB;
            width: 100%;
        }

        .sales-total-cell {
            padding-top: 10px;
        }

        .sales-total-amount {
            padding: 0;
            margin: 0;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            line-height: 21px;
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
            padding: 0;
            margin: 0;
            text-align: left;
            font-weight: bold;
            font-size: 16px;
            line-height: 21px;
            color: #595959;
        }

        .report-footer-value {
            padding: 0;
            margin: 0;
            text-align: right;
            font-weight: bold;
            font-size: 20px;
            line-height: 21px;
            color: #5851D8;
        }

        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
<div class="sub-container">
    <table class="report-header">
        <tr>
            <td>
                <p class="heading-text">{{ $company->name }}</p>
            </td>
            <td>
                <p class="heading-date-range">{{ $from_date }} - {{ $to_date }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="2" >
                <p class="text-left" style="font-size:10px;">{{$info_company}}</p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p class="sub-heading-text text-center">@lang('pdf_customer_sales_report')</p>
            </td>
        </tr>
    </table>

    @if ($listCustomers != null || $users_name != null || $countryname != null|| $statename != null ||                  $paid_status_name != null)
        <div class="styleFilter">
            <h3 class="sub-heading-text">Filters</h3>
            <!-- validar que tengan datos -->
            @if ($paid_status_name != null)
                <p><b> Paid Status:</b> {{ $paid_status_name }}</p>
            @endif

            @if ($listCustomers != null)
                <p><b> Customers:</b> {{ $listCustomers }}</p>
            @endif

            @if ($users_name != null)
                <p><b> Users:</b> {{ $users_name }}</p>
            @endif

            @if ($countryname != null)
                <p><b>Country:</b> {{ $countryname }}</p>
            @endif

            @if ( $statename != null)
                <p><b>State:</b> {{  $statename }}</p>
            @endif

        </div>
    @endif

    @foreach ($customers as $customer)
        <p class="sales-customer-name">{{ $customer->name }}</p>
        <div class="sales-table-container">
            <table class="table-sale">
                @foreach ($customer->invoices as $invoice)
                    <tr>
                        <td colspan="3">{{ formatDate($invoice->invoice_date, $customer->companySettings[0]->value ?? null) }}
                            ({{ $invoice->invoice_number }})</td>
                        <td>{!! format_money_pdf($invoice->total, $customer->currency ?? $defaultCurrency) !!}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        <table class="sales-total-indicator-table">
            <tr>
                <td class="sales-total-cell">
                    <p class="sales-total-amount">
                        {!! format_money_pdf($customer->invoices_sum_total, $customer->currency ?? $defaultCurrency) !!}
                    </p>
                </td>
            </tr>
        </table>
    @endforeach
</div>
<table class="report-footer">
    <tr>
        <td>
            <p class="report-footer-label">@lang('pdf_total_sales_label')</p>
        </td>
        <td>
            <p class="report-footer-value">
                {!! format_money_pdf($totalAmount) !!}
            </p>
        </td>
    </tr>
</table>
</body>
</html>
