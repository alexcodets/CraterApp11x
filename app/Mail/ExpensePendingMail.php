<?php

namespace Crater\Mail;

use Crater\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ExpensePendingMail extends BaseMail
{
    use Queueable;
    use SerializesModels;

    protected string $optionSubject = 'job_expense_pending_mail_subject';

    protected string $optionBody = 'job_expense_pending_mail_body';

    private Expense $expense;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Expense $expense)
    {
        //
        /*$body = $settings['job_expense_pending_mail_body'] ?? '';
        $subject =  $settings['job_expense_pending_mail_subject'] ?? '';*/
        $this->expense = $expense;
        $this->body = $this->getFinalBody($expense->company, $expense->user);
        $this->body = $this->additionalBodyUpdate($expense);

        $this->subject = $this->getFinalSubject($expense->company, $expense->user);
        $this->data = $this->getData($expense->company);

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ExpensePendingMail
    {
        return $this->markdown('emails.send.general')
            ->with([
                'message' => $this->body,
                'data' => $this->data,
            ]);
    }

    public function additionalBodyUpdate(Expense $expense)
    {

        $search = [
            '{EXPENSE_NUMBER}', '{PAYMENT_DATE}', '{DUE_DATE}', '{AMOUNT}',
            '{CATEGORY}',
            '{PAYMENT_NUMBER}', '{PAYMENT_METHOD}',
            '{CUSTOMER_NUMBER}', '{CUSTOMER_NAME}',
            '{ITEM}', '{TRANSACTION_NUMBER}',
            '{}'
        ];

        $payment = $this->expense->payment;
        $method = $payment ? $payment->paymentMethod : null;
        $user = $expense->user;
        $item = $expense->item;

        //Expenses number, payment number, payment method, customer number, customer name, amount, expense, category, due date, item,

        $replace = [
            $expense->expense_number, $expense->payment_date, $expense->expense_date, $expense->amount,
            $expense->category ? $expense->category->name : 'N/A',
            $payment ? $payment->payment_number : 'N/A', $method ? $method->name : 'N/A',
            $user ? $user->customcode : 'N/A', $user ? $user->name : 'N/A', $expense->expense_date,
            $item ? $item->name : 'N/A', $payment ? $payment->transaction_status : 'N/A', $payment ? $payment->payment_number : 'N/A'
        ];

        return str_replace($search, $replace, $this->body);
    }
}
