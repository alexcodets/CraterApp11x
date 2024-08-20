<?php

namespace Crater\Traits;

use Carbon\Carbon;
use Crater\Models\CustomerPackage;
use Crater\Models\CustomerPackageDiscount;
use Crater\Models\CustomerPackageItem;
use Crater\Models\CustomerPackageTax;
use Illuminate\Support\Facades\DB;

trait ServicesRecalculateTrait
{
    // Traerse el total de extensiones - sumatoria de price - pbx services extension - tambien traerse el count o numero de registros
    // Traerse el total de did  - sumatoria de price - pbx services did -- tambien traerse el count o numero de registros
    // Traerse el total de items - sumatoria del campo amount - pbx_services _item el amounot debe dividirse entre 100 para la suma.
    // Traerse el total de custom app rates -  sumatoria de  la multiplicacion del campo quantity X price
    public static function calculatePriceService(CustomerPackage $service, $bandera)
    {
        \Log::debug("calculatePriceService");
        \Log::debug($service->id);
        \Log::debug($service->code);

        $serviceId = $service->id;
        $discount = CustomerPackageDiscount::where('customer_package_id', $serviceId)
            ->whereNull('deleted_at')
            ->first();
        $items = CustomerPackageItem::where('customer_package_id', $serviceId)
            ->whereNull('deleted_at')
            ->get();
        $taxes = CustomerPackageTax::where('customer_package_id', $serviceId)->whereNull('deleted_at')->get();

        if ($bandera) {
            self::calculateItemsperiod($items);
        }

        $items = CustomerPackageItem::where('customer_package_id', $serviceId)
            ->whereNull('deleted_at')
            ->get();

        $ids = DB::table('customer_package_items')
            ->where('customer_package_id', $serviceId)
            ->whereNull('deleted_at')
            ->pluck('id');

        $taxesitem = CustomerPackageTax::whereIn('customer_package_item_id', $ids)
            ->get();

        \Log::debug("discount");
        \Log::debug(json_encode($discount));
        \Log::debug("items");
        \Log::debug($items);
        \Log::debug("taxes");
        \Log::debug($taxes);
        \Log::debug("taxes item ");
        \Log::debug($taxesitem);

        $preSubTotal = $items->sum('total');

        \Log::debug("presubtotal");
        \Log::debug($preSubTotal);

        $discountamount = 0;
        $taxamount = 0;
        if ($service->allow_discount && $discount != null) {
            $discountamount = self::calculateDiscount($service, $discount, $preSubTotal);
        } else {

            $service->discount = 0;
            $service->discount_val = 0;
            $service->save();
        }

        \Log::debug("discount");
        \Log::debug($discountamount);

        $subtotal = $preSubTotal - $discountamount;

        if ($subtotal < 0) {
            $subtotal = 0;
        }

        \Log::debug("subtotal");
        \Log::debug($subtotal);

        $taxamount = self::calculateTaxes($service, $subtotal, $taxes, $taxesitem);

        \Log::debug("tax amount");
        \Log::debug($taxamount);

        $service->sub_total = $preSubTotal;
        $service->tax = $taxamount;
        $service->total = $subtotal + $taxamount;
        $service->save();

    }

    public static function calculateItemsperiod($items)
    {
        \Log::debug('Iniciando el método calculateItemsperiod.');

        foreach ($items as $item) {
            \Log::debug('-------Revisando el item con ID: '.$item->name.'-------');

            if ($item->end_period_act == 1 || $item->end_period_act === true) {
                \Log::debug('El item con ID '.$item->name.' tiene end_period_act activo.');

                $item->end_period_number--;
                \Log::debug('Decrementando end_period_number. Nuevo valor: '.$item->end_period_number);

                // Guardar el registro actualizado
                $item->save();
                \Log::debug('Item con ID '.$item->name.' actualizado y guardado.');

                if ($item->end_period_number == 0) {
                    \Log::debug('El end_period_number del item con ID '.$item->name.' ha llegado a cero.');

                    // Eliminar registros relacionados en CustomerPackageTax
                    CustomerPackageTax::where('customer_package_item_id', $item->id)
                        ->update(['deleted_at' => Carbon::now()]);
                    \Log::debug('Registros relacionados en CustomerPackageTax eliminados para el item con ID '.$item->name);

                    // Eliminar el registro actual
                    $item->delete(); // Esto establecerá 'deleted_at' a la fecha actual automáticamente
                    \Log::debug('Item con ID '.$item->name.' eliminado.');
                }
            } else {
                \Log::debug('El item con ID '.$item->name.' no tiene end_period_act activo o es falso.');
            }
        }

        \Log::debug('Método calculateItemsperiod completado.');
    }

    public static function calculateDiscount($service, $discount, $preSubTotal)
    {
        \Log::debug('Inicio del método calculateDiscount');
        \Log::debug('Datos del descuento: '.json_encode($discount));

        $today = Carbon::now();
        \Log::debug('Fecha actual: '.$today);

        if ($discount->term_type == "D") {
            $startDate = Carbon::parse($discount->start_date);
            $endDate = Carbon::parse($discount->end_date);
            \Log::debug('Verificando fechas para descuento de tipo D: Inicio '.$startDate.' - Fin '.$endDate);

            if (! $today->between($startDate, $endDate)) {
                \Log::debug('La fecha actual no está entre las fechas de inicio y fin del descuento.');
                $service->allow_discount = 0;
                $service->discount = 0;
                $service->discount_val = 0;
                $service->save();
                \Log::debug('Descuento no permitido. Valores reseteados y guardados.');

                return 0;
            }
        } else {
            $activationDate = Carbon::parse($service->activation_date);
            $expiryDate = $activationDate->copy();
            \Log::debug('Verificando fechas para descuento con término diferente de D: Fecha de activación '.$activationDate);

            switch ($discount->term) {
                case 'days':
                    $expiryDate->addDays($discount->time_unit_number);

                    break;
                case 'weeks':
                    $expiryDate->addWeeks($discount->time_unit_number);

                    break;
                case 'months':
                    $expiryDate->addMonths($discount->time_unit_number);

                    break;
                case 'years':
                    $expiryDate->addYears($discount->time_unit_number);

                    break;
            }
            \Log::debug('Fecha de expiración calculada: '.$expiryDate);

            if ($today->greaterThan($expiryDate)) {
                \Log::debug('La fecha actual es mayor que la fecha de expiración del descuento.');
                $service->allow_discount = 0;
                $service->discount = 0;
                $service->discount_val = 0;
                $service->save();
                \Log::debug('Descuento no permitido. Valores reseteados y guardados.');

                return 0;
            }
        }

        if ($discount->discount_type == "fixed") {
            \Log::debug('Aplicando descuento fijo.');
            $service->discount = $discount->discount;
            $service->discount_val = $discount->discount_val;
            $service->save();
            \Log::debug('Descuento fijo aplicado y guardado.');

            return $discount->discount_val;
        } else {
            \Log::debug('Aplicando descuento porcentual.');
            $discount->discount_val = $preSubTotal * ($discount->discount / 100);
            $discount->save();
            \Log::debug('Descuento porcentual calculado y guardado: '.$discount->discount_val);

            $service->discount = $discount->discount;
            $service->discount_val = $discount->discount_val;
            $service->save();
            \Log::debug('Descuento porcentual aplicado al servicio y guardado.');

            return $discount->discount_val;
        }

        \Log::debug('Fin del método calculateDiscount');
    }

    public function calculateTaxes($service, $subtotal, $taxes, $taxesitem)
    {

        if ($service->tax_by == "G") {
            \Log::debug("impuesto g");

            // Verificar si la colección está vacía
            if ($taxes->isEmpty()) {
                return 0;
            }

            $totalAmount = 0;

            foreach ($taxes as $tax) {
                // Calcular el nuevo amount basado en el percent y el subtotal
                $calculatedAmount = ($tax->percent / 100) * $subtotal;
                \Log::debug($tax->name);
                \Log::debug($calculatedAmount);

                // Redondear el valor calculado a un entero antes de guardarlo
                $tax->amount = round($calculatedAmount);
                \Log::debug($tax->amount);

                // Guardar el registro
                $tax->save();

                // Sumar al total
                $totalAmount += $tax->amount;
            }

            // Retornar el resultado de la sumatoria
            return $totalAmount;

        } else {
            \Log::debug("impuesto i");

            // Verificar si el array está vacío o es null
            if ($taxesitem->isEmpty()) {
                return 0;
            }

            // Sumar los valores del campo 'amount'
            $totalAmount = $taxesitem->sum('amount');

            // Retornar el resultado de la sumatoria
            return $totalAmount;

        }

        return 0;

    }
}
