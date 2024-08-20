<?php

namespace Crater\Helpers;

use Crater\Models\CompanySetting;
use Crater\Models\Expense;
use Crater\Models\Payment;
use Crater\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Crypt;

class General
{
    public static function generateUniqueId(string $prefix = '', bool $moreEntropy = true): string
    {
        return uniqid($prefix, $moreEntropy);
    }

    public static function getPbxNotificationAdmins(): Collection
    {
        return User::where('role', 'super admin')->where('pbx_notification', 1)->whereNotNUll('role2')->get();
    }

    public static function getNextExpenseNumber(Payment $payment): string
    {
        // Get the last created order
        $pop = CompanySetting::where('company_id', $payment->company_id)
            ->where('option', 'expense_prefix')
            ->first();
        $value = $pop->value;
        $expense = Expense::where('expense_number', 'LIKE', $value.'-%')
            ->orderBy('created_at', 'desc')
            ->first();
        if (! $expense) {
            // We get here if there is no order at all
            // If there is no number set it to 0, which will be 1 at the end.
            $number = 0;
        } else {
            $number = explode('-', $expense->expense_number);
            $number = $number[1];
        }
        // If we have ORD000001 in the database then we only want the number
        // So the substr returns this 000001

        // Add the string in front and higher up the number.
        // the %05d part makes sure that there are always 6 numbers in the string.
        // so it adds the missing zero's when needed.

        return sprintf('%06d', (int)$number + 1);
    }

    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    public static function decrypt(?string $field): ?string
    {
        if ($field && strlen($field) > 35) {
            return Crypt::decryptString($field);
        }

        return $field;
    }

    public static function encrypt(?string $field): ?string
    {
        if ($field) {
            return Crypt::encryptString($field);
        }

        return $field;
    }
}
