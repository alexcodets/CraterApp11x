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
        Schema::create('invoice_dids', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('pbx_did_id');
            $table->unsignedInteger('template_did_id')->nullable();
            $table->string('pbx_did_number')->nullable();
            $table->string('pbx_did_server')->nullable();
            $table->string('pbx_did_trunk')->nullable();
            $table->string('pbx_did_type')->nullable();
            $table->string('template_did_name');
            $table->decimal('template_did_rate')->default(0)->nullable();

            /* $table->foreign('invoice_id')
                 ->references('id')
                 ->on('invoices')
                 ->onDelete('cascade');

             $table->foreign('pbx_did_id')
                 ->references('id')
                 ->on('pbx_did')
                 ->onDelete('cascade');

             $table->foreign('template_did_id')
                 ->references('id')
                 ->on('profile_did')
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
        Schema::dropIfExists('invoice_dids');
    }
};
