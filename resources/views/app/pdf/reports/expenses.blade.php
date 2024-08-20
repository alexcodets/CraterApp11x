<!DOCTYPE html>
<html lang="en">

<head>
    <title>Expenses Report</title>
    <style type="text/css">
        body {
            font-family: "DejaVu Sans";
        }

        table {
            border-collapse: collapse;
        }

        .sub-container {
            padding: 0px 20px;
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
            font-weight: normal;
            font-size: 16px;
            color: #595959;
            padding: 0px;
            margin: 0px;
            margin-top: 6px;
        }

        .expenses-title {
            margin-top: 60px;
            padding-left: 3px;
            font-size: 16px;
            line-height: 21px;
            color: #040405;
        }

        .expenses-table-container {
            padding-left: 10px;
        }

        .expenses-table {
            width: 100%;
            padding-bottom: 10px;
        }

        .expense-title {
            padding: 0px;
            margin: 0px;
            font-size: 14px;
            line-height: 21px;
            color: #595959;
        }

        .expense-amount {
            padding: 0px;
            margin: 0px;
            font-size: 14px;
            line-height: 21px;
            text-align: right;
            color: #595959;
        }

        .expense-total-table {
            border-top: 1px solid #EAF1FB;
            width: 100%;
        }

        .expense-total-cell {
            padding-right: 20px;
            padding-top: 10px;
        }

        .expense-total {
            padding-top: 10px;
            padding-right: 30px;
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

        .styleFilter {
            background: #F9FBFF;
            box-sizing: border-box;
            padding: 15px 20px;
            margin-top: 40px;
            width: 100%;

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
                    <p class="heading-text">{{ $company->name }}</p>
                </td>
                <td>
                    <p class="heading-date-range">{{ $from_date }} - {{ $to_date }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p class="text-left" style="font-size:10px;">{{$info_company}}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p class="sub-heading-text">@lang('pdf_expense_report_label')</p>
                </td>
            </tr>
        </table>


        <!-- imprimir en html los valores de la variable providers -->
        <!-- providers
        customerList
        payment_modeList
        categoryList
        statusList
        itemList -->
        @if ($providers != null || $customerList != null || $payment_modeList != null || $categoryList != null || $statusList != null || $itemList != null)
        <div class="styleFilter">
            <h3 class="sub-heading-text">Filters</h3>
            <!-- validar que tengan datos -->
            @if ($providers != null)
            <p>Providers: {{ $providers }}</p>
            @endif
            @if ($customerList != null)
            <p>Customer: {{ $customerList }}</p>
            @endif
            @if ($payment_modeList != null)
            <p>Payment Mode: {{ $payment_modeList }}</p>
            @endif
            @if ($categoryList != null)
            <p>Category: {{ $categoryList }}</p>
            @endif
            @if ($statusList != null)
            <p>Status: {{ $statusList }}</p>
            @endif
            @if ($itemList != null)
            <p>Item: {{ $itemList }}</p>
            @endif

        </div>
        @endif

        <p class="expenses-title">@lang('pdf_expenses_label')</p>
        <div class="expenses-table-container">
            <!-- EXPENSES WITHOUT PROVIDERS -->
            @if($totalExpense != 0)
            <table class="expenses-table">
                @foreach ($expenseCategories as $expenseCategory)
                <tr>
                    <td>
                        <p class="expense-title">
                            {{ $expenseCategory->category->name }}
                        </p>
                    </td>
                    <td>
                        <p class="expense-amount">
                            {!! format_money_pdf($expenseCategory->total_amount) !!}
                        </p>
                    </td>
                </tr>
                @endforeach
            </table>
            <table class="expense-total-table">
                <tr>
                    <td class="">
                        <p class="expense-total">{!! format_money_pdf($totalExpense) !!}</p>
                    </td>
                </tr>
            </table>
            @endif
            <!-- EXPENSES WITH PROVIDERS -->
            <table class="expenses-table">
                @foreach ($expenseCategoriesProviders as $expenseCategory)
                @if($expenseCategory['total_expense'] != 0)
                <tr>
                    <td>
                        <p class="expense-title" style="font-weight: bold;">
                            {{ $expenseCategory['title']  }}
                        </p>
                    </td>
                    <td>

                    </td>

                </tr>
                @foreach($expenseCategory['expenses'] as $expense)
                <tr>
                    <td>
                        <p class="expense-title">
                            {{ $expense['category']['name'] }}
                        </p>
                    </td>
                    <td>
                        <p class="expense-amount">
                            {!! format_money_pdf( $expense['amount']) !!}
                        </p>
                    </td>

                </tr>
                @endforeach
                <tr>
                    <table class="expense-total-table">
                        <tr>
                            <td class="">
                                <p class="expense-total">{!! format_money_pdf($expenseCategory['total_expense']) !!}</p>
                            </td>
                        </tr>
                    </table>

                </tr>
                @endif
                @endforeach
            </table>

        </div>
    </div>


    <table class="report-footer">
        <tr>
            <td>
                <p class="report-footer-label">@lang('pdf_total_expenses_label')</p>
            </td>
            <td>
                <p class="report-footer-value">{!! format_money_pdf($totalAllExpenses) !!}</p>
            </td>
        </tr>
    </table>
</body>

</html>