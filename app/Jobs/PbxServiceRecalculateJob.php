<?php

namespace Crater\Jobs;

use Carbon\Carbon;
use Crater\DataObject\ServiceRecalculateData;
use Crater\Models\PbxServices;
use Crater\Models\ProfileDidTollFree;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Log;

class PbxServiceRecalculateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    private array $extraIds;

    private ServiceRecalculateData $data;

    public function __construct(array $extraIds = [])
    {
        $this->extraIds = $extraIds;
    }

    public function handle(): void
    {
        if ($this->batch() ? $this->batch()->cancelled() : null) {
            return;
        }
        $array = array_merge(Cache::get('tenant_synchronize', []), $this->extraIds);
        if ($array == []) {
            Log::debug('Nothing to recalculate');

            return;
        }
        foreach ($array as $item) {
            $pbxService = PbxServices::find($item);

            if (is_null($pbxService)) {
                Log::error('Pbx Service not found', ['service_id' => $item]);

                continue;
            }

            $this->calculatePriceService($pbxService, false);
        }

    }

    public function calculatePriceService(PbxServices $service, bool $band)
    {
        $this->data = new ServiceRecalculateData($service);
        $this->setData($service);

        if ($band) {
            self::calculateItemsPeriod($service->id);
        }

        Log::debug('totals: ', [
            'Extensions' => $this->data->extensionsTotal,
            'Did' => $this->data->didTotal,
            'Items' => $this->data->itemsTotal,
            'App-Rate' => $this->data->appRateTotal,
            'Additional-charges' => $this->data->additionalChargeTotal,
            'Package-price' => $service->pbxpackages_price,
        ]);


        $this->data->discountTotal = $this->getDiscountTotal($service, $this->data->getSubTotal());
        Log::debug($this->data->getSubTotalForTax());

        $this->data->taxService = $this->calculateTaxes($service, $this->data->getSubTotalForTax());

        Log::debug('Tax and discount: ', [
            'tax_items' => $this->data->taxItems,
            'tax_service' => $this->data->taxService,
            'tax_total' => $this->data->getTaxTotal(),
            'discount_total' => $this->data->discountTotal,
            'total' => $this->data->getTotalService(),
            'subtotal' => $this->data->getSubTotal(),
        ]);

        DB::table('pbx_services')
            ->where('id', $service->id)
            ->update([
                'total' => round($this->data->getTotalService(), 2) * 100,
                'sub_total' => round($this->data->getSubTotal(), 2) * 100,
                'tax' => round($this->data->getTaxTotal(), 2) * 100,
                'discount_val' => round($this->data->discountTotal, 2) * 100,
            ]);

    }

    public function setData(PbxServices $service): void
    {
        $this->calculateExtensions($service);
        $this->calculateDid($service);
        $this->calculateItems($service);
        $this->calculateAppRate($service);
        $this->calculateAdditionalCharges($service);
        $this->data->packagePrice = $service->pbxpackages_price;
    }

    public function calculateExtensions(PbxServices $service)
    {

        $this->data->extensionsCount = DB::table('pbx_services_extensions')
            ->where('pbx_service_id', $service->id)
            ->whereNull('deleted_at')
            ->count();

        $profileExtension = $service->pbxPackage->profileExtensions;
        $total = 0;

        DB::table('pbx_services_extensions')
            ->where('pbx_service_id', $service->id)
            ->whereNull('deleted_at')
            ->chunkById(35, function ($extension) use (&$total, $profileExtension) {
                $extension->each(function ($extension) use (&$total, $profileExtension) {
                    Log::debug("Procesando la extensión con ID: {$extension->id}");
                    if (! is_null($extension->price)) {
                        Log::debug("Precio de la extensión (ID: {$extension->id}): {$extension->price}");
                        $total += $extension->price;
                    } else {
                        Log::debug("Tarifa del perfil de extensión aplicada a la extensión (ID: {$extension->id}): {$profileExtension->rate}");
                        $total += $profileExtension->rate;
                    }
                });
            });

        $this->data->extensionsTotal = $total;

    }

    public function calculateDid(PbxServices $service): void
    {

        $this->data->didCount = DB::table('pbx_services_did')
            ->where('pbx_service_id', $service->id)
            ->whereNull('deleted_at')
            ->count();

        $total = 0;

        $templateDid = $service->pbxPackage->profileDid;

        DB::table('pbx_services_did')
            ->where('pbx_service_id', $service->id)
            ->whereNull('deleted_at')
            ->chunkById(35, function ($did) use (&$total, $templateDid) {
                $did->each(function ($did) use (&$total, $templateDid) {
                    Log::debug("Procesando el DID con ID: {$did->id}");

                    if (! is_null($did->price)) {
                        Log::debug("Precio del DID (ID: {$did->id}): {$did->price}");
                        $total += $did->price;

                        return;
                    }

                    if (! is_null($did->custom_did_id)) {
                        $didTollFree = ProfileDidTollFree::where('id', $did->custom_did_id)->first();
                        Log::debug("Tarifa por minuto del DID gratuito (ID: {$did->custom_did_id}): {$didTollFree->rate_per_minute}");
                        $total += $didTollFree->rate_per_minute;

                        return;
                    }

                    Log::debug("Tarifa del DID del perfil aplicada (ID del perfil: {$templateDid->id}): {$templateDid->did_rate}");
                    $total += $templateDid->did_rate;

                });
            });

        $this->data->didTotal = $total;

    }

    public function calculateItems(PbxServices $service): void
    {
        $this->data->itemsCount = DB::table('pbx_services_items')
            ->where('pbx_services_id', $service->id)
            ->whereNull('deleted_at')
            ->count();

        $total = 0;
        $taxes = 0;

        DB::table('pbx_services_items')
            ->where('pbx_services_id', $service->id)
            ->whereNull('deleted_at')
            ->chunkById(35, function ($item) use (&$total, $service, &$taxes) {
                $item->each(function ($item) use (&$total, $service, &$taxes) {
                    $total += ($item->total / 100);

                    if ($service->apply_tax_type == 'I') {
                        $itemTaxes = DB::table('taxes')->where('pbx_service_item_id', $item->id)->get();
                        foreach ($itemTaxes as $tax) {
                            Log::debug("Aplicando impuesto de item con ID: {$tax->id}, cantidad: {$tax->amount}");
                            $taxes += ($tax->amount / 100);
                        }
                        Log::debug("Total de impuestos acumulados: {$taxes}");
                    }
                });
            });

        $this->data->itemsTotal = $total;
        $this->data->taxItems = $taxes;

    }

    public function calculateAppRate(PbxServices $service): void
    {
        $this->data->appRateCount = DB::table('pbx_services_app_rates')
            ->where('pbx_service_id', $service->id)
            ->whereNull('deleted_at')
            ->count();

        $total = 0;

        //TODO: Se puede optimizar con un simple sum('costo')

        DB::table('pbx_services_app_rates')
            ->where('pbx_service_id', $service->id)
            ->whereNull('deleted_at')
            ->chunkById(35, function ($app) use (&$total) {
                $app->each(function ($app) use (&$total) {
                    Log::debug("Procesando tarifa de la aplicación con ID: {$app->id}, costo: {$app->costo}");
                    $total += $app->costo;
                });
            });

        $this->data->appRateTotal = $total;

    }

    public function calculateAdditionalCharges(PbxServices $service): void
    {
        $this->data->additionalChargeTotal = DB::table('pbx_additional_charges')
            ->where('status', 1)
            ->where('pbx_service_id', $service->id)
            ->sum('total');
    }

    /**
     * Actualiza los registros de pbx_services_items y elimina los correspondientes en taxes.
     *
     */
    public function calculateItemsPeriod(int $serviceId)
    {

        Log::debug('Método updateAndCleanUp iniciado.');

        // Consulta inicial para obtener los registros relevantes
        $serviceItems = DB::table('pbx_services_items')
            ->where('pbx_services_id', $serviceId)
            ->where('end_period_act', 1)
            ->whereNull('deleted_at')
            ->get();

        Log::debug("Consulta realizada para serviceId: {$serviceId}");

        foreach ($serviceItems as $item) {
            Log::debug("Procesando item con ID: {$item->id} y end_period_number: {$item->end_period_number}");

            // Reducir end_period_number y verificar si llega a cero
            $newEndPeriodNumber = $item->end_period_number - 1;

            if ($newEndPeriodNumber <= 0) {
                // Si end_period_number es cero, eliminar el registro
                DB::table('pbx_services_items')
                    ->where('id', $item->id)
                    ->update(['deleted_at' => now()]);
                Log::debug("Registro con ID: {$item->id} marcado para eliminación.");

                // Eliminar registros relacionados en la tabla taxes
                DB::table('taxes')
                    ->where('pbx_service_item_id', $item->id)
                    ->delete();
                Log::debug("Registros relacionados en taxes eliminados para el item ID: {$item->id}");
            } else {
                // Si no, solo actualizar end_period_number
                DB::table('pbx_services_items')
                    ->where('id', $item->id)
                    ->update(['end_period_number' => $newEndPeriodNumber]);
                Log::debug("end_period_number actualizado para el item ID: {$item->id}, nuevo valor: {$newEndPeriodNumber}");
            }
        }

        Log::debug('Método updateAndCleanUp completado.');
    }

    public function getDiscountTotal(PbxServices $service, $subTotal): float
    {
        if (! $service->allow_discount) {
            return 0;
        }
        // D - dates
        // T  - period
        switch ($service->discount_term_type) {
            case 'D':
                Log::debug('Line trait pbx service 139');
                if (! is_null($service->date_to) && ! is_null($service->date_from)) {
                    $date = now()->format('Y-m-d');
                    if ($date > $service->date_from && $date < $service->date_to) {
                        return $this->calculateDiscount($service, $subTotal);
                    }
                }

                break;
            case 'T':
                // Days - Weeks - Months - Years
                Log::debug('Inside tt');
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

                if (now()->format('Y-m-d') < $date) {
                    Log::debug('Line trait pbx service 180');

                    return $this->calculateDiscount($service, $subTotal);
                }
                Log::debug('Outside the range broh');

                break;
            default:
                return 0;
        }

        return 0;

    }

    public function getDiscountD(PbxServices &$service, $preSubTotal)
    {
        if (is_null($service->date_to) or is_null($service->date_from)) {
            return 0;
        }
        $discountValue = 0;
        $date = now()->format('Y-m-d');
        Log::debug("Fecha actual: {$date}, Fecha desde: {$service->date_from}, Fecha hasta: {$service->date_to}");

        if ($date >= $service->date_from && $date <= $service->date_to) {
            Log::debug('La fecha actual está dentro del rango de descuento.');

            switch ($service->allow_discount_type) {
                case 'percentage':
                    $discountValue = $preSubTotal * $service->allow_discount_value / 100;
                    Log::debug("Descuento por porcentaje aplicado: {$discountValue}");

                    break;
                case 'fixed':
                    $discountValue = $service->allow_discount_value;
                    Log::debug("Descuento fijo aplicado: {$discountValue}");

                    break;
            }
        }

        if ($service->date_to < $date) {
            Log::debug('La fecha de descuento ha expirado, desactivando descuentos.');
            // Desactivar descuentos
            $service->allow_discount = 0;
            $service->allow_discount_value = 0;
            $service->save();
        }

        return $discountValue;
    }

    public function getDiscountT(PbxServices &$service, $preSubTotal)
    {
        Log::debug('El tipo de término de descuento es por período (T).');
        $discountValue = 0;
        // Days - Weeks - Months - Years
        $dateTo = '';
        $dateFrom = '';
        $toDay = now()->format('Y-m-d');
        Log::debug("Hoy es: {$toDay}");

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
                Log::debug('Tipo de período de tiempo no reconocido.');

                break;
        }

        Log::debug("Período calculado desde: {$dateFrom} hasta: {$dateTo}");

        if ($toDay >= $dateFrom && $toDay <= $dateTo) {
            Log::debug('La fecha actual está dentro del rango de descuento por período.');

            switch ($service->allow_discount_type) {
                case 'percentage':
                    $discountValue = $preSubTotal * $service->allow_discount_value / 100;
                    Log::debug("Descuento por porcentaje aplicado: {$discountValue}");

                    break;
                case 'fixed':
                    $discountValue = $service->allow_discount_value;
                    Log::debug("Descuento fijo aplicado: {$discountValue}");

                    break;
            }
        }

        if ($dateTo < $toDay) {
            Log::debug('El período de descuento ha expirado, desactivando descuentos.');
            // Desactivar
            $service->allow_discount = 0;
            $service->allow_discount_value = 0;
            $service->save();
        }

        return $discountValue;

    }

    public function calculateDiscount(PbxServices $service, $preSubTotal): float
    {
        Log::debug("Verificando descuentos para el servicio con ID: {$service->id}");
        $discountValue = 0;

        // Validar descuentos, desactivar si cumplen las reglas
        if (! $service->allow_discount) {
            Log::debug('El servicio no permite descuentos.');

            return 0;
        }

        Log::debug('El servicio permite descuentos.');

        // D - dates
        // T - period

        switch ($service->discount_term_type) {
            case 'D':
                $discountValue = $this->getDiscountD($service, $preSubTotal);

                break;
            case 'T':
                $discountValue = $this->getDiscountT($service, $preSubTotal);

                break;
            default:
                // default code
                break;
        }

        Log::debug("Valor total del descuento: {$discountValue}");
        Log::debug('Fin del método calculateDiscount.');

        return $discountValue;
    }

    public function calculateTaxes(PbxServices $service, $subtotal)
    {
        Log::debug('--Inicio del método calculateTaxes.--');

        // Recuperar los tipos de impuestos para el servicio dado
        $taxes = DB::table('pbx_services_tax_types')
            ->where('pbx_services_id', $service->id)
            ->get();
        Log::debug("Impuestos recuperados para el servicio con ID: {$service->id}");

        $totalTaxes = 0;

        if ($taxes->isEmpty()) {
            Log::debug('No se encontraron impuestos para calcular.');

            return 0;
        }

        Log::debug('Calculando impuestos.');

        $taxes->each(function ($tax) use ($subtotal, &$totalTaxes) {
            // Calcular el impuesto temporal basado en el subtotal y el porcentaje del impuesto
            $taxTemp = $subtotal * ($tax->percent / 100);
            Log::debug("Impuesto calculado para el tipo de impuesto con ID: {$tax->id}, porcentaje: {$tax->percent}, subtotal: {$subtotal}, impuesto temporal: {$taxTemp}");

            $totalTaxes += $taxTemp;

            // Actualizar el monto del impuesto en la base de datos
            DB::table('pbx_services_tax_types')->where('id', $tax->id)->update([
                'amount' => (round($taxTemp, 2) * 100),
            ]);
            Log::debug("Monto del impuesto actualizado en la base de datos para el tipo de impuesto con ID: {$tax->id}, nuevo monto: ".($taxTemp * 100));
        });

        Log::debug('Fin del método calculateTaxes.');

        return $totalTaxes;
    }
}
