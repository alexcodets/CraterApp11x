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
        Schema::create('invoice_extensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('pbx_extension_id');
            $table->unsignedInteger('template_extension_id')->nullable();
            $table->string('pbx_extension_name')->nullable();
            $table->unsignedInteger('pbx_extension_ext')->nullable();
            $table->string('pbx_extension_email')->nullable();
            $table->string('pbx_extension_ua_fullname')->nullable();
            $table->string('template_extension_name');
            $table->decimal('template_extension_rate')->default(0)->nullable();

            /*     $table->foreign('invoice_id')
                     ->references('id')
                     ->on('invoices')
                     ->onDelete('cascade');

                 $table->foreign('pbx_extension_id')
                     ->references('id')
                     ->on('pbx_extensions')
                     ->onDelete('cascade');

                 $table->foreign('template_extension_id')
                     ->references('id')
                     ->on('profile_extensions')
                     ->onDelete('cascade');*/

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_extensions');
    }
};
