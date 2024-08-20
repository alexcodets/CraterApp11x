# Instantiating the Service

```php
$api = new PbxWareApi(PbxServers::first());
```

# Get Did

## Code

```php
$api = new PbxWareApi(PbxServers::first());
$api->getDids($tenantCode);
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

### Success (PbxPackage NotFind/InvalidId)

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
