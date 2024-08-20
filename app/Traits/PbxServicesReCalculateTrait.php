<?php

namespace Crater\Traits;

use Carbon\Carbon;
use Crater\Http\Requests\ProfileDidTollFree;
use Crater\Models\PbxServices;
use Illuminate\Support\Facades\DB;

trait PbxServicesReCalculateTrait
{
    // Traerse el total de extensiones - sumatoria de price - pbx services extension - tambien traerse el count o numero de registros
    // Traerse el total de did  - sumatoria de price - pbx services did -- tambien traerse el count o numero de registros
    // Traerse el total de items - sumatoria del campo amount - pbx_services _item el amounot debe dividirse entre 100 para la suma.
    // Traerse el total de custom app rates -  sumatoria de  la multiplicacion del campo quantity X price
    public static function calculatePriceService(PbxServices $service, $band)
    {
        $serviceId = $service->id;
        $extensions = DB::table('pbx_services_extensions')->where('pbx_service_id', $serviceId)->whereNull('deleted_at')->get();
        $dids = DB::table('pbx_services_did')->where('pbx_service_id', $serviceId)->whereNull('deleted_at')->get();

        $appRates = DB::table('pbx_services_app_rates')->where('pbx_service_id', $serviceId)->whereNull('deleted_at')->get();

        //items

        if ($band) {
            self::calculateItemsperiod($serviceId);
        }
        $items = DB::table('pbx_services_items')->where('pbx_services_id', $serviceId)->whereNull('deleted_at')->get();

        \Log::debug('Line trait pbx service 25');

        $subtotal = 0;
        $dataExtensions = [
            'countExtensions' => 0,
            'total' => 0,
        ];
        $dataDids = [
            'countDids' => 0,
            'total' => 0,
        ];
        $dataItems = [
            'countItems' => 0,
            'total' => 0,
            'totalTaxes' => 0,
        ];
        $dataAppRates = [
            'countItems' => 0,
            'total' => 0,
        ];
        // calculate extensions
        if (! $extensions->isEmpty()) {
            \Log::debug('----Inicio del cálculo de extensiones----');

            $dataExtensions['countExtensions'] = $extensions->count();
            $profileExtension = $service->pbxPackage->profileExtensions;
            $total = 0;

            foreach ($extensions as $extension) {
                \Log::debug("Procesando la extensión con ID: {$extension->id}");

                if (! is_null($extension->price)) {
                    \Log::debug("Precio de la extensión (ID: {$extension->id}): {$extension->price}");
                    $total += $extension->price;
                } else {
                    \Log::debug("Tarifa del perfil de extensión aplicada a la extensión (ID: {$extension->id}): {$profileExtension->rate}");
                    $total += $profileExtension->rate;
                }
            }

            \Log::debug("Total calculado de extensiones: {$total}");
            $dataExtensions['total'] = $total;

            \Log::debug('Fin del cálculo de extensiones');
        }

        // calculate dids
        if (! $dids->isEmpty()) {
            \Log::debug('---Inicio del cálculo de DIDs---');

            $dataDids['countDids'] = $dids->count();
            $total = 0;

            foreach ($dids as $did) {
                \Log::debug("Procesando el DID con ID: {$did->id}");

                if (! is_null($did->price)) {
                    \Log::debug("Precio del DID (ID: {$did->id}): {$did->price}");
                    $total += $did->price;
                } else {
                    if (! is_null($did->custom_did_id)) {
                        $didTollFree = ProfileDidTollFree::where("id", $did->custom_did_id)->first();
                        \Log::debug("Tarifa por minuto del DID gratuito (ID: {$did->custom_did_id}): {$didTollFree->rate_per_minute}");
                        $total += $didTollFree->rate_per_minute;
                    } else {
                        $templateDid = $service->pbxPackage->profileDid;
                        \Log::debug("Tarifa del DID del perfil aplicada (ID del perfil: {$templateDid->id}): {$templateDid->did_rate}");
                        $total += $templateDid->did_rate;
                    }
                }
            }

            \Log::debug("Total calculado de DIDs: {$total}");
            $dataDids['total'] = $total;

            \Log::debug('Fin del cálculo de DIDs');
        }
        // calculate items
        if (! $items->isEmpty()) {
            \Log::debug('---Inicio del cálculo de items---');

            $dataItems['countItems'] = $items->count();
            \Log::debug("Conteo de items: {$dataItems['countItems']}");

            $tempItems = 0;
            foreach ($items as $item) {
                \Log::debug("Procesando item con ID: {$item->id}");
                $tempItems += ($item->total / 100);
                \Log::debug("Subtotal acumulado de items: {$tempItems}");

                $itemTaxes = DB::table('taxes')->where('pbx_service_item_id', $item->id)->get();
                foreach ($itemTaxes as $tax) {
                    \Log::debug("Aplicando impuesto de item con ID: {$tax->id}, cantidad: {$tax->amount}");
                    $dataItems['totalTaxes'] = ($tax->amount / 100) + $dataItems['totalTaxes'];
                }
                \Log::debug("Total de impuestos acumulados: {$dataItems['totalTaxes']}");
            }
            $dataItems['total'] = $tempItems;
            \Log::debug("Total calculado de items: {$dataItems['total']}");
        }
        // calculate app rates
        if (! $appRates->isEmpty()) {
            \Log::debug('---Inicio del cálculo de tarifas de la aplicación--');

            $dataAppRates['countAppRates'] = $appRates->count();
            \Log::debug("Conteo de tarifas de la aplicación: {$dataAppRates['countAppRates']}");

            $total = 0;
            foreach ($appRates as $rate) {
                \Log::debug("Procesando tarifa de la aplicación con ID: {$rate->id}, costo: {$rate->costo}");
                $total += $rate->costo;
            }

            $dataAppRates['total'] = $total;
            \Log::debug("Total calculado de tarifas de la aplicación: {$dataAppRates['total']}");
        }

        $additionalCharges = DB::table('pbx_additional_charges')->where('status', 1)->where('pbx_service_id', $serviceId)->get();

        \Log::debug('Recuperando cargos adicionales');
        $totalCharges = 0;
        if (! $additionalCharges->isEmpty()) {
            \Log::debug('---Inicio del cálculo de cargos adicionales--');

            foreach ($additionalCharges as $charge) {
                \Log::debug("Procesando cargo adicional con ID: {$charge->id}, total: {$charge->total}");
                $totalCharges += $charge->total;
            }

            \Log::debug("Total calculado de cargos adicionales: {$totalCharges}");
        }
        \Log::debug('Line trait pbx service 126');
        \Log::debug('Line trait pbx service 126 '.($service->pbxpackages_price / 100));
        \Log::debug('---Iniciando cálculo de subtotal.--');

        /*if($service->apply_tax_type == "G"){
        $subtotal += $dataItems['total'];
        }else{
        $dataitemstotal =$dataItems['total'];
        }*/
        // Inicializar subtotal a 0
        $subtotal = 0;

        \Log::debug("Monto de cargos adicionales: {$totalCharges}");
        $subtotal += $totalCharges;

        \Log::debug("Monto total de DIDs: {$dataDids['total']}");
        $subtotal += $dataDids['total'];

        \Log::debug("Monto total de extensiones: {$dataExtensions['total']}");
        $subtotal += $dataExtensions['total'];

        \Log::debug("Monto total de items: {$dataItems['total']}");
        // Verificar el tipo de aplicación de impuestos antes de sumar los montos de items
        $subtotal += $dataItems['total'];

        \Log::debug("Monto total de impuestos sobre items: {$dataItems['totalTaxes']}");
        // $subtotal += $dataItems['totalTaxes'];

        \Log::debug("Monto total de tarifas de la aplicación: {$dataAppRates['total']}");
        $subtotal += $dataAppRates['total'];

        \Log::debug("Precio del paquete PBX dividido entre 100: ".($service->pbxpackages_price / 100));
        $subtotal += ($service->pbxpackages_price / 100);

        \Log::debug("Subtotal calculado: {$subtotal}");

        $subtotalDiscount = 0;

        if ($service->allow_discount) {
            \Log::debug('Line trait pbx service 135');
            // D - dates
            // T  - period
            if ($service->discount_term_type == 'D') {
                \Log::debug('Line trait pbx service 139');
                if (! is_null($service->date_to) && ! is_null($service->date_from)) {
                    \Log::debug('Line trait pbx service 141');
                    $date = Carbon::now()->format('Y-m-d');
                    if ($date > $service->date_from && $date < $service->date_to) {
                        \Log::debug('Line trait pbx service 144');
                        $subtotalDiscount = self::calculateDiscount($service, $subtotal);
                    }
                }
            }

            if ($service->discount_term_type == 'T') {
                \Log::debug('Line trait pbx service 151');
                // Days - Weeks - Months - Years
                $date = '';
                switch ($service->time_period_value) {
                    case 'Days':
                        $result = Carbon::create($service->date_begin)->format('Y-m-d');
                        $date = Carbon::parse($result)->addDays($service->time_period);

                        break;

                    case 'Weeks':
                        $result = Carbon::create($service->date_begin)->format('Y-m-d');
                        $date = Carbon::parse($result)->addWeeks($service->time_period);

                        break;

                    case 'Months':
                        $result = Carbon::create($service->date_begin)->format('Y-m-d');
                        $date = Carbon::parse($result)->addMonths($service->time_period);

                        break;

                    case 'Years':
                        $result = Carbon::create($service->date_begin)->format('Y-m-d');
                        $date = Carbon::parse($result)->addYears($service->time_period);

                        break;

                    default:
                        break;
                }

                if (Carbon::now()->format('Y-m-d') < $date) {
                    \Log::debug('Line trait pbx service 180');
                    $subtotalDiscount = self::calculateDiscount($service, $subtotal);
                }
            }

        }

        \Log::debug('Iniciando cálculo del total del servicio.');

        // Inicializar totalService a 0
        $totalService = 0;

        \Log::debug("Subtotal antes de descuentos: {$subtotal}");
        \Log::debug("Descuento aplicado al subtotal: {$subtotalDiscount}");

        // Calcular subtotal después de aplicar descuentos
        $subtotalAfterDiscount = $subtotal - $subtotalDiscount;
        if ($subtotalAfterDiscount < 0) {
            $subtotalAfterDiscount = 0;
        }

        \Log::debug("Subtotal después de aplicar descuentos: {$subtotalAfterDiscount}");

        $totalTaxes = self::calculateTaxes($service, $subtotalAfterDiscount);
        \Log::debug('Line 268  '.$totalTaxes);

        // Sumar los impuestos al subtotal después de descuentos
        $totalService = $subtotalAfterDiscount + $totalTaxes;

        if ($service->apply_tax_type == "I") {
            \Log::debug("apply_tax_type es 'I'. Sumando totalTaxes y total de dataItems al totalService.");
            // Si apply_tax_type ES "I", sumar $dataItems['totalTaxes'] y $dataItems['total'] directamente al totalService
            $totalService += $dataItems['totalTaxes'];
            \Log::debug("TotalService actualizado: {$totalService}");

            \Log::debug("Sumando total de dataItems al subtotal.");
            $subtotal += $dataItems['total'];
            \Log::debug("Subtotal actualizado: {$subtotal}");

            \Log::debug("Sumando totalTaxes de dataItems a totalTaxes.");
            $totalTaxes += $dataItems['totalTaxes'];
            \Log::debug("TotalTaxes actualizado: {$totalTaxes}");
        }
        \Log::debug("Total de impuestos a añadir: {$totalTaxes}");

        \Log::debug("Total del servicio calculado: {$totalService}");

        DB::table('pbx_services')
            ->where('id', $service->id)
            ->update([
                'total' => round($totalService, 2) * 100,
                'sub_total' => round($subtotal, 2) * 100,
                'tax' => round($totalTaxes, 2) * 100,
                'discount_val' => round($subtotalDiscount, 2) * 100,
            ]);

        $dataTotals = [
            "status" => "success",
            "message" => "Service caculated correctly",
            "total_service" => round($totalService, 2) * 100,
            "subtotal_discount" => $subtotalDiscount,
            "discount_val" => $subtotalDiscount,
            "subtotal" => $subtotal,
            "taxes" => round($totalTaxes, 2),
            "pbx packages price" => ($service->pbxpackage_price / 100),
            "total charges" => $totalCharges,
            "extensions" => $dataExtensions,
            "items" => $dataItems,
            "did" => $dataDids,
            "appRates" => $dataAppRates,
        ];
        \Log::debug('Pbx Services Calculate Trait', ['result' => $dataTotals]);

        return response()->json($dataTotals);
    }

    public static function calculateDiscount($service, $preSubTotal)
    {
        \Log::debug('Inicio del método calculateDiscount.');

        $discountValue = 0;
        \Log::debug("Verificando descuentos para el servicio con ID: {$service->id}");

        // Validar descuentos, desactivar si cumplen las reglas
        if ($service->allow_discount) {
            \Log::debug('El servicio permite descuentos.');

            // D - dates
            // T - period
            if ($service->discount_term_type == 'D') {
                \Log::debug('El tipo de término de descuento es por fechas (D).');

                if (! is_null($service->date_to) && ! is_null($service->date_from)) {
                    $date = Carbon::now()->format('Y-m-d');
                    \Log::debug("Fecha actual: {$date}, Fecha desde: {$service->date_from}, Fecha hasta: {$service->date_to}");

                    if ($date >= $service->date_from && $date <= $service->date_to) {
                        \Log::debug('La fecha actual está dentro del rango de descuento.');

                        switch ($service->allow_discount_type) {
                            case 'percentage':
                                $discountValue = $preSubTotal * $service->allow_discount_value / 100;
                                \Log::debug("Descuento por porcentaje aplicado: {$discountValue}");

                                break;
                            case 'fixed':
                                $discountValue = $service->allow_discount_value;
                                \Log::debug("Descuento fijo aplicado: {$discountValue}");

                                break;
                        }
                    }

                    if ($service->date_to < $date) {
                        \Log::debug('La fecha de descuento ha expirado, desactivando descuentos.');
                        // Desactivar descuentos
                        $service->allow_discount = 0;
                        $service->allow_discount_value = 0;
                        $service->save();
                    }
                }
            }

            if ($service->discount_term_type == 'T') {
                \Log::debug('El tipo de término de descuento es por período (T).');

                // Days - Weeks - Months - Years
                $dateTo = '';
                $dateFrom = '';
                $toDay = Carbon::now()->format('Y-m-d');
                \Log::debug("Hoy es: {$toDay}");

                switch ($service->time_period_value) {
                    case 'Days':
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addDays($service->time_period);

                        break;

                    case 'Weeks':
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addWeeks($service->time_period);

                        break;

                    case 'Months':
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addMonths($service->time_period);

                        break;

                    case 'Years':
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addYears($service->time_period);

                        break;

                    default:
                        \Log::debug('Tipo de período de tiempo no reconocido.');

                        break;
                }

                \Log::debug("Período calculado desde: {$dateFrom} hasta: {$dateTo}");

                if ($toDay >= $dateFrom && $toDay <= $dateTo) {
                    \Log::debug('La fecha actual está dentro del rango de descuento por período.');

                    switch ($service->allow_discount_type) {
                        case 'percentage':
                            $discountValue = $preSubTotal * $service->allow_discount_value / 100;
                            \Log::debug("Descuento por porcentaje aplicado: {$discountValue}");

                            break;
                        case 'fixed':
                            $discountValue = $service->allow_discount_value;
                            \Log::debug("Descuento fijo aplicado: {$discountValue}");

                            break;
                    }
                }

                if ($dateTo < $toDay) {
                    \Log::debug('El período de descuento ha expirado, desactivando descuentos.');
                    // Desactivar
                    $service->allow_discount = 0;
                    $service->allow_discount_value = 0;
                    $service->save();
                }
            }
        } else {
            \Log::debug('El servicio no permite descuentos.');
        }

        \Log::debug("Valor total del descuento: {$discountValue}");
        \Log::debug('Fin del método calculateDiscount.');

        return $discountValue;
    }

    public static function calculateTaxes(PbxServices $service, $subtotal)
    {
        \Log::debug('--Inicio del método calculateTaxes.--');

        // Recuperar los tipos de impuestos para el servicio dado
        $taxes = DB::table('pbx_services_tax_types')->where('pbx_services_id', $service->id)->get();
        \Log::debug("Impuestos recuperados para el servicio con ID: {$service->id}");

        $totalTaxes = 0;

        // Verificar si hay impuestos a calcular
        if (! $taxes->isEmpty()) {
            \Log::debug('Calculando impuestos.');

            foreach ($taxes as $tax) {
                // Calcular el impuesto temporal basado en el subtotal y el porcentaje del impuesto
                $taxTemp = $subtotal * ($tax->percent / 100);
                \Log::debug("Impuesto calculado para el tipo de impuesto con ID: {$tax->id}, porcentaje: {$tax->percent}, subtotal: {$subtotal}, impuesto temporal: {$taxTemp}");

                // Sumar al total de impuestos
                $totalTaxes += $taxTemp;

                // Actualizar el monto del impuesto en la base de datos
                DB::table('pbx_services_tax_types')->where('id', $tax->id)->update([
                    'amount' => (round($taxTemp, 2) * 100),
                ]);
                \Log::debug("Monto del impuesto actualizado en la base de datos para el tipo de impuesto con ID: {$tax->id}, nuevo monto: ".($taxTemp * 100));
            }

            \Log::debug("Total de impuestos calculados: {$totalTaxes}");
        } else {
            \Log::debug('No se encontraron impuestos para calcular.');
        }

        \Log::debug('Fin del método calculateTaxes.');

        return $totalTaxes;
    }

    /**
     * Actualiza los registros de pbx_services_items y elimina los correspondientes en taxes.
     *
     * @param  int  $serviceId
     * @return void
     */
    public static function calculateItemsperiod($serviceId)
    {

        \Log::debug('Método updateAndCleanUp iniciado.');

        // Consulta inicial para obtener los registros relevantes
        $serviceItems = DB::table('pbx_services_items')
            ->where('pbx_services_id', $serviceId)
            ->where('end_period_act', 1)
            ->whereNull('deleted_at')
            ->get();

        \Log::debug("Consulta realizada para serviceId: {$serviceId}");

        foreach ($serviceItems as $item) {
            \Log::debug("Procesando item con ID: {$item->id} y end_period_number: {$item->end_period_number}");

            // Reducir end_period_number y verificar si llega a cero
            $newEndPeriodNumber = $item->end_period_number - 1;

            if ($newEndPeriodNumber <= 0) {
                // Si end_period_number es cero, eliminar el registro
                DB::table('pbx_services_items')
                    ->where('id', $item->id)
                    ->update(['deleted_at' => now()]);
                \Log::debug("Registro con ID: {$item->id} marcado para eliminación.");

                // Eliminar registros relacionados en la tabla taxes
                DB::table('taxes')
                    ->where('pbx_service_item_id', $item->id)
                    ->delete();
                \Log::debug("Registros relacionados en taxes eliminados para el item ID: {$item->id}");
            } else {
                // Si no, solo actualizar end_period_number
                DB::table('pbx_services_items')
                    ->where('id', $item->id)
                    ->update(['end_period_number' => $newEndPeriodNumber]);
                \Log::debug("end_period_number actualizado para el item ID: {$item->id}, nuevo valor: {$newEndPeriodNumber}");
            }
        }

        \Log::debug('Método updateAndCleanUp completado.');
    }
}
