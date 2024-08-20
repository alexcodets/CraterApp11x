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
        Schema::create('avalara_exemptions', function (Blueprint $table) {
            $table->id();
            $table->boolean('frc')->nullable()->comment('Force: Override level exempt flag on tax type wildcard exemptions');
            $table->unsignedSmallInteger('tpe')->nullable()->comment('Tax Type ID');
            $table->unsignedSmallInteger('cat')->nullable()->comment('Tax Category Id: Tax Category to exempt.');
            $table->unsignedSmallInteger('dom')->nullable()->comment('Exemption Domain');
            $table->unsignedSmallInteger('scp')->nullable()->comment('Exemption Exemption Scope');
            $table->boolean('exnb')->nullable()->comment('Exempt Non-billable: Determines if non-billable taxes are to be considered as candidates for exemption');
            $table->unsignedInteger('pbx_services_id')->nullable();
            $table->unsignedBigInteger('avalara_locations_id')->nullable();

            $table->foreign('pbx_services_id')->references('id')->on('pbx_services')->onDelete('cascade');
            $table->foreign('avalara_locations_id')->references('id')->on('avalara_locations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('avalara_exemptions');
    }
};
