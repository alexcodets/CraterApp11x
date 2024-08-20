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
        Schema::create('pbx_services', function (Blueprint $table) {
            $table->increments('id');
            // columns fk
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('pbx_package_id')->nullable();
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            // $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreign('pbx_package_id')->references('id')->on('pbx_packages')->onDelete('cascade');
            // columns
            $table->string('pbx_tenant_id')->nullable();
            $table->enum('status', ['A', 'I'])->default('A');
            $table->enum('term', ['daily','weekly','monthly','bimonthly','quarterly','biannual','yearly'])->nullable();
            $table->date('date_begin')->nullable();
            $table->boolean('allow_discount');
            $table->string('allow_discount_value')->nullable();
            // $table->string('select_time_period');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->integer('time_period')->nullable();
            $table->enum('time_period_value', ['Days', 'Weeks', 'Months', 'Years'])->nullable();

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
        Schema::dropIfExists('pbx_services');
    }
};
