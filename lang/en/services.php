<?php

//'services.invoices.import.errors.title.invoice_number.invalid'
return [
    'invoices' => [
        'import' => [
            'errors' => [
                'file' => [
                    'invalid' => 'The file is not valid.',
                    'empty' => 'The file is empty',
                    'max_lines' => 'The current file has :lines lines, but the max line count permitted is :max_lines.',
                    'min_lines' => 'The current file has :lines lines, but the min line count permitted is :min_lines.',
                    'generic' => 'There was a error with the file or the file is empty.',
                ],
                'invoices' => [
                    'max' => 'The current file has :invoices invoices, but the max invoice count permitted is :max_invoices',
                ],
                'users' => [
                    'no_found' => 'The user :user, does not exist.',
                ],
                'tax' => [
                    'no_found' => 'The tax with name :tax, does not exist.',
                ],
                'pbxService' => 'The pbxService with number :service, does not exist.',
                'service' => 'The service with code :service, does not exist.',
                'date' => [
                    'invoice' => ':date is a invalid date for the invoice.',
                    'due_date' => ':date is a invalid date for the due date.',
                    'service_date' => ':date is a invalid date for the service date.',
                    'service_start' => ':date is a invalid date for the service start.',
                ],
                'header' => 'The Header for the file is incorrect. Go back to your CSV and fix it before trying again.',
                'title' => [
                    'date' => [
                        'due_date' => 'The due date in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'service_date' => 'The service date in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'invoice' => 'The invoice date in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'service_start' => 'The service start date in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'service_end' => 'The service end date in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                    ],
                    'users' => [
                        'no_found' => 'The following user are not in the system. Go back to your CSV and fix it before trying again.',
                        'multiple_diff' => 'Multiple customer found for the following invoices. Go back to your CSV and fix it before trying again.',
                        'multiple_same' => 'The user in the following invoices are declare multiples times, you only has to declare it in the first line of each invoice. Go back to your CSV and fix it before trying again.',
                        'missing' => 'The user in the following invoices is missing. Go back to your CSV and fix it before trying again.',
                    ],
                    'invoices' => [
                        'duplicate' => 'The invoice numbers in the following invoices already exists. Go back to your CSV and fix it before trying again.',
                        'exist' => 'The invoice numbers in the following invoices already exists. Go back to your CSV and fix it before trying again.',
                        'invalid' => 'The invoice number in the following invoices has not a valid format. Go back to your CSV and fix it before trying again.',
                    ],
                    'status' => [
                        'no_valid' => 'The status in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'no_found' => 'The following status are not in the system. Go back to your CSV and fix it before trying again.',
                        'missing' => 'The status in the following invoices is missing. Go back to your CSV and fix it before trying again.',
                        'draft_no_paid' => 'The status in the following invoices is draft, but the payment status is different from paid. Go back to your CSV and fix it before trying again.',
                        'complete_no_paid' => 'The status in the following invoices is complete, but the payment status is different from paid. Go back to your CSV and fix it before trying again.',
                        'complete_no_zero' => 'The status in the following invoices is complete, but the due amount is not zero. Go back to your CSV and fix it before trying again.',
                        'unpaid_total_diff' => 'For a invoice with paid status: unpaid, the total due amount cannot be different from the total amount. Go back to your CSV and fix it before trying again.',
                        'partial_total_diff' => 'For a invoice with paid status: partial, the total due amount cannot be the same than the total amount. Go back to your CSV and fix it before trying again.',

                    ],
                    'status_payment' => 'The status payment in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                    'tax' => [
                        'per' => 'The tax per item in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'amount' => 'The tax amount in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'type' => 'The tax type in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'no_found' => 'The following taxes are not in the system. Go back to your CSV and fix it before trying again.',
                    ],
                    'discount' => [
                        'invoice' => [
                            'positive' => 'The discount amount in the following invoices is not valid, it must be numeric. Go back to your CSV and fix it before trying again.',
                            'numeric' => 'The discount amount in the following invoices is not valid, it cannot be negative. Go back to your CSV and fix it before trying again.',
                            'val' => 'The discount val in the following invoices is not valid, it cannot be negative. Go back to your CSV and fix it before trying again.',
                            'total' => 'The discount val in the following invoice does not match the total amount. Go back to your CSV and fix it before trying again.',

                        ],
                        'line' => [
                            'numeric' => 'The line discount amount in the following invoices is not valid, it must be numeric. Go back to your CSV and fix it before trying again.',
                            'positive' => 'The line discount amount in the following invoices is not valid, it cannot be negative. Go back to your CSV and fix it before trying again.',
                            'val' => 'The line discount val in the following invoices is not valid, it cannot be negative. Go back to your CSV and fix it before trying again.',
                        ],
                        'per' => 'The discount per item in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'amount' => 'The discount amount in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                        'type' => 'The discount type in the following invoices is not valid (values: fixed, percentage). Go back to your CSV and fix it before trying again.',
                    ],
                    'total' => [
                        'int' => 'The total the following invoices is not valid, must be numeric. Go back to your CSV and fix it before trying again.',
                        'positive' => 'The total in the following invoices is not valid, it cannot be negative. Go back to your CSV and fix it before trying again.',
                    ],
                    'quantity' => [
                        'no_int' => 'The quantity in the following invoices is not valid (is not a integer). Go back to your CSV and fix it before trying again.',
                    ],
                    'due_amount' => [
                        'biggest' => 'The due amount in the following invoices is not valid because is mayor than the total. Go back to your CSV and fix it before trying again.',
                        'positive' => 'The total in the following invoices is not valid, it cannot be negative. Go back to your CSV and fix it before trying again.',
                    ],
                    'item' => [
                        'type' => 'The item type in the following invoices is not valid (values: Item, PbxService, Service). Go back to your CSV and fix it before trying again.',
                    ],
                    'pbxService' => 'The PbxService in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                    'service' => 'The PbxService in the following invoices is not valid. Go back to your CSV and fix it before trying again.',
                ],
            ],
        ],
    ],
];
