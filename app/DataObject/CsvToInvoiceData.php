<?php

namespace Crater\DataObject;

use Crater\Helpers\General;

class CsvToInvoiceData
{
    public const STATUSES = ['DRAFT', 'DUE', 'SENT', 'VIEWED', 'OVERDUE', 'COMPLETED', 'SAVE_DRAFT',];
    public const PAID_STATUSES = ['PAID', 'PARTIALLY_PAID', 'UNPAID'];
    public const INVOICE_NUMBER_REGEX = '/^[a-zA-Z]{1,9}-\d{5}$/ms';

    public string $invoiceNumber;

    public string $customerCode;

    public string $status;

    public bool $taxPerItem;

    public bool $discountPerItem;

    public int $subTotal;

    public int $total;

    public int $id;

    public function __construct(
        string $invoiceNumber,
        string $customerCode,
        string $status,
        bool $taxPerItem,
        bool $discountPerItem,
        int $subTotal,
        int $total
    ) {
        $this->invoiceNumber = $invoiceNumber;
        $this->customerCode = $customerCode;
        $this->status = $status;
        $this->taxPerItem = $taxPerItem;
        $this->discountPerItem = $discountPerItem;
        $this->subTotal = $subTotal;
        $this->total = $total;
    }

    public static function fromArray(array $line): self
    {
        return new self($line[0], $line[1], $line[6], $line[8] === 'YES', $line[9] === 'YES', $line[10], $line[15]);
    }

    public static function validateArray(array $line, string $dateFormat, ?array $errors = []): array
    {

        if (preg_match(self::INVOICE_NUMBER_REGEX, $line[0], $matches, PREG_OFFSET_CAPTURE, 0) == 0) {
            $errors['invoice']['invalid'][] = $line[0];
        }

        if (! in_array($line[8], ['YES', 'NO'])) {
            $errors['per']['tax'][] = $line[0];
        }

        if (! in_array($line[9], ['YES', 'NO'])) {
            $errors['per']['discount'][] = $line[0];
        }

        if (! General::validateDate($line[3], $dateFormat)) {
            $errors['date']['invoice'][] = $line[0];
        }
        if (! General::validateDate($line[4], $dateFormat)) {
            $errors['date']['due_date'][] = $line[0];
        }
        if (! in_array($line[6], self::STATUSES)) {
            $errors['status'][] = $line[0];
        }
        if (! in_array($line[7], self::PAID_STATUSES)) {
            $errors['paid_status'][] = $line[0];
        }
        if ($line[10] > $line[15]) {
            $errors['total']['minor_than_due'][] = $line[0];
        }
        if ($line[17]) {
            if (! General::validateDate($line[19], $dateFormat)) {
                $errors['date']['service_start'][] = $line[0];
            }
            if (! General::validateDate($line[20], $dateFormat)) {
                $errors['date']['service_end'][] = $line[0];
            }
        }

        if (! in_array($line[11], ['fixed', 'percentage', null, ''])) {
            $errors['discount']['type'][] = $line[0];
        }

        if ($line[9] == 'NO') {
            if (! is_numeric($line[13])) {
                $errors['discounts_invoice']['number'][] = $line[0];
            }

            if (is_numeric($line[13]) && $line[13] < 0) {
                $errors['discounts_invoice']['positive'][] = $line[0];
            }

            if (! is_numeric($line[12]) || $line[12] < 0) {
                $errors['discounts_invoice']['val'][] = $line[0];
            }

            if (is_numeric($line[12]) && $line[12] > 0 && ($line[12] / 100 * $line[15]) != $line[13]) {
                $errors['discounts_invoice']['total'][] = $line[0];
            }
        }

        return $errors;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
