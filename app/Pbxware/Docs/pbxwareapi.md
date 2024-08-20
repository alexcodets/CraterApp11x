# Get Did List By Tenant

```php
$pbxApi = new PbxWareApi();
$resDID = $pbxApi->getDIDListByTenant($idPbxPackage, $idTenant);
```

### Success

```php
[
       "36" =>  [
       "number" =>  "4035555876",
       "number2" =>  null,
       "server" =>  "42",
       "trunk" =>  "60",
       "type" =>  "-",
       "ext" =>  "",
       "e164" =>  null,
       "e164_2" =>  null,
       "status" =>  "enabled",
       ],
];
```

### Error (Invalid Tenant Id)

```php
[
    error" => "Invalid Server ID is specified."
];
```

### Success (PbxPackage NotFind/InvalidId or PbxServer null)

```php
[
    "message" => "Paquete Pbx no tiene servidor asociado",
    "status"  => 405,
    "success" => true
];
```

### Success (PbxPackage Did = 0)

```php
[
    "message" => "Debe Habilitar los DID para este paquete Pbx",
    "status"  => 403,
    "success" => true
];
```

---

# Get Extension List By Tenant

```php
$pbxApi = new PbxWareApi();
$resExt = $pbxApi->getExtListByTenant($idPbxPackage, $idTenant);
```

### Success

```php
[
     "3" => [
       "name" => "Peter Griffin7",
       "email" => "none@careonecomm.com",
       "ext" => "2001",
       "protocol" => "sip",
       "location" => "local",
       "ua_id" => "50",
       "ua_name" => "generic_sip",
       "ua_fullname" => "Generic SIP",
       "status" => "disabled",
       "macaddress" => "",
       "linenum" => "",
    ]
];
```

### Error (Invalid Tenant Id)

```php
[
    error" => "Invalid Server ID is specified."
];
```

### Success (PbxPackage NotFind/InvalidId or PbxServer null)

```php
[
    "message" => "Paquete Pbx no tiene servidor asociado",
    "status"  => 405,
    "success" => true
];
```

### Success (PbxPackage extensions = 0/null)

```php
[
    "message" => "Debe Habilitar las extensiones para este paquete Pbx",
    "status"  => 403,
    "success" => true
];
```
