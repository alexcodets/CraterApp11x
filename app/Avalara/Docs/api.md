# Instantiating the Service

```php
$config     = \Crater\Models\AvalaraConfig::find($config_id);
$service = new \Crater\Avalara\Service\AvalaraService(new \Crater\Avalara\Apis\AvalaraApi($config))
```

# Server Status
## Code
```php
$config     = \Crater\Models\AvalaraConfig::find($config_id);
$service = new \Crater\Avalara\Service\AvalaraService(new \Crater\Avalara\Apis\AvalaraApi($config))
$service->serverStatus();
```
## Response
### Success
```php
[
     "success" => true,
     "message" => "The server is Running just fine",
     "status" => 200,
     "data" => [],
]
```
### Fail (no internet connection)
```php
[
     "success" => false,
     "message" => "cURL error 6: Could not resolve host: communicationsua.avalara.net (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)",
     "status" => 500,
     "errors" => [
       "error" => "cURL error 6: Could not resolve host: communicationsua.avalara.net (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)",
       "code" => 500,
     ],
]
```
# Get Pcode
## Code
```php
$config     = \Crater\Models\AvalaraConfig::find($config_id);
$service = new \Crater\Avalara\Service\AvalaraService(new \Crater\Avalara\Apis\AvalaraApi($config))
#Fips
$service->getPcode([
  "Fips"=> "5303303985",
]);

#npa
$service->getPcode([
  "NpaNxx"=> "206033",
]);

#From address
$service->getPcode([
  "CountryISO"=> "USA",
  "State"=> "WA",
  "County"=> "King",
  "City"=> "Seattle",
  "ZipCode"=> "98104",
  "BestMatch"=> true,
  "LimitResults"=> 10,
]);

# Three at the same time, because we can.
$service->getPcode([
  "CountryISO"=> "USA",
  "State"=> "WA",
  "County"=> "King",
  "City"=> "Seattle",
  "ZipCode"=> "98104",
  "BestMatch"=> true,
  "LimitResults"=> 10,
  "NpaNxx"=> "206033",
  "Fips"=> "5303303985",
]);
```
## Response
### Success (3 at the same time)
```php
[
     "success" => true,
     "message" => "Pcode Found",
     "status" => 200,
     "data" => [
       [
         "CountryIso" => "USA",
         "State" => "WA",
         "County" => "KING",
         "Locality" => "SEATTLE",
         "PCode" => 4133800,
       ],
       [
         "CountryIso" => "USA",
         "State" => "WA",
         "County" => "KING",
         "Locality" => "SEATTLE",
         "PCode" => 4133800,
       ],
       [
         "CountryIso" => "USA",
         "State" => "WA",
         "County" => "KING",
         "Locality" => "SEATTLE",
         "PCode" => 4133800,
       ],
     ],
   ]
```
### Fail (Wrong NpaNxx)
```php
[
     "success" => false,
     "message" => "There was no match PCode",
     "status" => 404,
     "errors" => [
       "LocationData" => null,
       "MatchCount" => 0,
       "InputMatchType" => "Best",
       "MatchTypeApplied" => null,
       "ResultsLimit" => 100,
     ],
   ]
```
# Get TsPair
## Code
```php
$config     = \Crater\Models\AvalaraConfig::find($config_id);
$service = new \Crater\Avalara\Service\AvalaraService(new \Crater\Avalara\Apis\AvalaraApi($config))
$service->getTsPair();
```
## Response
### Success
```php
[
     "success" => true,
     "message" => "Pcode Found",
     "status" => 200,
     "data" => [
       [
         "TransactionType" => 0,
         "ServiceType" => 0,
         "MarketType" => 6,
         "InterfaceType" => 1,
         "InputType" => 15,
         "IsBundle" => false,
         "TransactionDescription" => "No Tax",
         "ServiceDescription" => "No Tax",
         "TSPairDescription" => "No Tax/No Tax",
       ],
.
.
.
]
```
# Get Tax Types
## Code
```php
$config     = \Crater\Models\AvalaraConfig::find($config_id);
$service = new \Crater\Avalara\Service\AvalaraService(new \Crater\Avalara\Apis\AvalaraApi($config))
$service->getTaxTypes();
```
## Response
### Success
```php
[
    "success" => true,
    "message" => "TaxTypes:",
    "status" => 200,
    "data" => [
    [
    "TaxType" => 0,
    "CategoryType" => 0,
    "TaxDescription" => "No Tax",
    "CategoryDescription" => "NO CATEGORY DESCRIPTION",
    ],
.
.
.
]
```
---
[BACK](../readme.md)
