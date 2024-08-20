<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Verificar si la tabla 'payment_gateways' existe
        if (Schema::hasTable('payment_gateways')) {
            // Verificar y actualizar el registro de PayPal-Logo.jpg a PayPal-Logo.png
            $paypal = DB::table('payment_gateways')->where('url_img', '/images/PayPal-Logo.jpg')->first();
            if ($paypal) {
                DB::table('payment_gateways')
                    ->where('url_img', '/images/PayPal-Logo.jpg')
                    ->update(['url_img' => '/images/PayPal-Logo.png']);
            }

            // Verificar y actualizar el registro de authorize-net-01.png a authorize-net-02.png
            $authorizeNet = DB::table('payment_gateways')->where('url_img', '/images/authorize-net-01.png')->first();
            if ($authorizeNet) {
                DB::table('payment_gateways')
                    ->where('url_img', '/images/authorize-net-01.png')
                    ->update(['url_img' => '/images/authorize-net-02.png']);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Revertir los cambios solo si la tabla 'payment_gateways' existe
        if (Schema::hasTable('payment_gateways')) {
            // Verificar y revertir el registro de PayPal-Logo.png a PayPal-Logo.jpg
            $paypal = DB::table('payment_gateways')->where('url_img', '/images/PayPal-Logo.png')->first();
            if ($paypal) {
                DB::table('payment_gateways')
                    ->where('url_img', '/images/PayPal-Logo.png')
                    ->update(['url_img' => '/images/PayPal-Logo.jpg']);
            }

            // Verificar y revertir el registro de authorize-net-02.png a authorize-net-01.png
            $authorizeNet = DB::table('payment_gateways')->where('url_img', '/images/authorize-net-02.png')->first();
            if ($authorizeNet) {
                DB::table('payment_gateways')
                    ->where('url_img', '/images/authorize-net-02.png')
                    ->update(['url_img' => '/images/authorize-net-01.png']);
            }
        }
    }
};
