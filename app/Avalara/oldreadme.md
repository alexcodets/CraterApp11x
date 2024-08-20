# Avalara

# Intro

Implementación del sistema De impuestos automatizados Avalara para proyectos Laravel, esta conformado por:

* Una clase Service la cual es el punto central de integración de la api con Laravel.

* Una clase Avalara Api la cual se encarga de la comunicación con el api, asi como de formatear las respuestas que se utilizaran en el sistema.

* Varias clases de datos (Data Managment Object and Data Object) los cuales se encargan de formatear los datos utilizado tanto para el api como para el sistema.

**AvalaraService** es el punto central de la integración, es la clase la cual se encarga de la interacción con el api.

# Configuration

**AvalaraApiDMO** Es la clase encargada de trabajar con la data de configuración del api, Actualmente trabaja con un archivo config, si se desea cambiar para que utilice data que provenga de la base de datos, se deberá cambiar el **__construct** para que utilice el método **dataDB** en lugar del método **dataConfig**. El metodo **dataDb** actualmente no esta hecho ya que dependerá de como se almacene dicha data.

# Uso
Para la utilización del sistema se puede a través de **dependency injection** inicializando en el controlador de la siguiente forma
**functionName(AvalaraService $service)**
```php
functionName(AvalaraService $service){

}

```
o inicializando de forma manual si así se desea.
```php
new AvalaraService(new AvalaraApi())
```
# Usage
## Verificar estado del servidor
```php
functionName(AvalaraService $service){
    return $service->serverStatus();
}

```
## Verificar pCode
```php
functionName(AvalaraService $service){
    return $service->getPCode([
        'ctry' => 'USA',
        'st'   => 'NC',
        'city' => 'Durham',
        'zip'  => 27701,
    ]);
}

```
## Realizar calculo de impuestos
```php
functionName(AvalaraService $service){
    //Clases that hold the data to be used for the service for tax.
    //AvalaraInvoiceDO(invoice_id, invoice_date, commit)
    $invoice = new AvalaraInvoiceDO('2018-09-24 11:00:00', false, 12345);
    
    //For billing location you can use any class that extend AvalaraBillingDO
    //AvalaraDataBillingDO(Country, state, city, zip)
    $billing = new AvalaraDataBillingDO('USA', 'NC', 'Durham', 27701);
    //AvalaraDataBillingDO($billingAddress model)
    $billing = new AvalaraUserBillingDO($user->billingAddress);

    //AvalaraCompanyDO(bscl, svcl, fclt, frch, reg)
    $company = new AvalaraCompanyDO(1, 1, true, true, true);

    $service->prepareTax($invoice, $billing, $company);
    // addLine(charge/line , transaction, service, sale)
    $service->addLine(5, 19, 21, 1); //line VOIP
    $service->addLine(2, 19, 578, 1); //pbx VOIP
    $service->addLine(4, 19, 41, 1); //pbx_extension VOIP
    $service->addCharge(19.99, 19, 8, 1); //install VOIP
    $service->addCharge(29.99, 19, 48, 1); //call VOIP
    return $service->getTaxes();
}

```
# Response format

## Success
```json
{"success": (bool) true,
    "message": (string),
    "status": (int) 200,
    "data": (array)
}
```

## Error
```json
{"success": (bool) false,
    "message": (string),
    "status": (int),
    "errors": (array)
}
```

### Ejemplo de Json response de consulta exitosa al estado del servidor Avalara
```json
{"success":true,"message":"The server is Running just fine","status":200,"data":[]}
```

### Json response getTaxes() for doc 12345
```json
{"success":true,"message":"Taxes calculated","status":200,
    "data":
        {"doc":"12345",
        "items": [
            {"txs":[]},
            {"txs":[]},
            {"txs":[]},
            {"txs":[]},  
        ]
    }
}
```

# Testing

Para correr las pruebas es suficiente con ejecutar el siguiente comando en el terminal dentro de la carpeta del proyecto.

```shell
./vendor/bin/pest --group=avalara
```

Si se sigue utilizando el archivo de configuración para los valores del api sera necesario también colocar dichos valores en el env de testing.
En caso de tener problema con las migraciones es posible que también genere errores al intentar correr las pruebas.
