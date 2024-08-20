# Void Invoice

---

The Process for a full VOID/Refund split into 2 branches, according to the billing period:
- Current Billing Period: The Tax transaction is changed from **Commit** to **Uncommitted**.
- PREVIOUS billing period: A new transaction must be sent, that would be exactly as the original but would 
have 'adj=true' producing a negative tax, reversing them the calculation from the original Invoice.

Models can also come from relationships.
## Current Period.
```php
$invoice    = \Crater\Models\Invoice::withTrashed()->find($invoice_id);
$service    = new \Crater\Avalara\Service\AvalaraService($invoice->company->avalaraConfiguration)
$service->unCommitInvoice($invoice->avalara_document_code);
```
## Previous Period.
```php
//TODO: Process
$invoice    = \Crater\Models\Invoice::withTrashed()->find($invoice_id);
$service    = new \Crater\Avalara\Service\AvalaraService($invoice->company->avalaraConfiguration)
$service->prepareForVoid($invoice->avalaraLog->jsonRequest);
$service->getTaxes();
```

## Notes.
The Invoice fall outside the scope of readme, if necessary another readme will be made. (*)

---
[BACK](../../readme.md)
