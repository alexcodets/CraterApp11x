<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        //

        Schema::table('customer_tickets', function (Blueprint $table) {
            // Verificar si la columna 'send_notification_customer' no existe antes de añadirla
            if (! Schema::hasColumn('customer_tickets', 'send_notification_customer')) {
                $table->tinyInteger('send_notification_customer')->default(0)->comment('booleano que indica si se le envia correo de notificacion al cliente');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
