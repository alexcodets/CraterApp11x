# Get Taxes

---

## Required Models and data

```php
$user       = \Crater\Models\User::find($user_id);
$config     = \Crater\Models\AvalaraConfig::find($config_id);
$invoice    = \Crater\Models\Invoice::find($invoice_id);
//$invoice->company->avalaraConfiguration
$pbxService = \Crater\Models\PbxServices::find($pbx_service_id);
$chronos    = new \Crater\Helpers\Chronometer();
$avaLog     = new \Crater\Models\AvalaraLog();
$commit     = true;

$avaLog->invoice_id = $invoice->id;
$avaLog->user_id    = $user->id;
$avaLog->pbx_service_id = $pbxService->id;

// Filtering make possible to work with mix invoices.
$invoiceItems = $invoice->items()->whereHas('item', function ($query) {
    $query->where('avalara_bool', '!=', null);
})->with('item')->get();
```

Models can also come from relationships.

## Instantiating the Service

```php
$baseService = AvalaraService(new AvalaraApi($config));
$taxService  = new AvalaraTaxService($user, $baseService);
//Validate and Prepare the required Billing and Company data.
$validation  = $taxService->validateUserData($config, $this->invoice, $commit);

if ($validation['success'] === false) {
    //Or whatever logic for failing.
    return false;
}
```

## Filling the request.

```php
foreach ($invoiceItems as $invoiceItem) {
    $taxService->addTaxItemForService($invoiceItem->item, $invoiceItem);
    $invoiceItem->invoice_item_id = $invoiceItem->id;
    $allItems[]                   = $invoiceItem;  //For the log
}
```

## Filling the request without invoiceItem (only Item id).

```php
$item = Item::find($item_id);
$invoiceItem = new stdClass();

$item->item_id          = $item->id;
$invoiceItem->quantity  = 10; //Some logic to calculate
$invoiceItem->price     = 0; //Some logic to calculate
$allItems[]             = $item;

$taxService->addTaxItemForService($item, $invoiceItem);
```

## Exemptions.

```php
$avalaraExemptions = $pbxService->avalaraExemptions()->with('location')->get();
foreach ($avalaraExemptions as $ex) {
    $taxService->addExemption($ex);
}
```

## Getting the Tax (with log).

```php
$avaLog->type    = AvalaraLog::INVOICE_SERVICE; //Will dependen in the actual process.
$avaLog->request = json_encode($taxService->service->request->body);

$chronos->start();
$taxesResponse = $taxService->getTaxes();
$chronos->end();

$avaLog->procesing_time = $chronos->totalExecutionMiliseconds();
//$this->avalaraItems($taxesResponse, $allItems); (*)
$avaLog->response = json_encode($taxesResponse);
$avaLog->status = AvalaraLog::STATUS_SUCCESS;
$avaLog->save();
```

## Notes.

The Invoice fall outside the scope of readme, if necessary another readme will be made. (*)

---
[BACK](../../readme.md)
