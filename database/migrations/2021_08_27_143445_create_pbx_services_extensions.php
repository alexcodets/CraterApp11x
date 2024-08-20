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
        Schema::create('pbx_services_extensions', function (Blueprint $table) {
            $table->increments('id');
            // columns fk
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('pbx_service_id')->nullable();
            $table->unsignedInteger('pbx_extension_id')->nullable();
            $table->unsignedInteger('pbx_profile_extension_id')->nullable();
            /*
                        $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                        $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
                        $table->foreign('pbx_service_id')->references('id')->on('pbx_services')->onDelete('cascade');
                        $table->foreign('pbx_extension_id')->references('id')->on('pbx_extensions')->onDelete('cascade');
                        $table->foreign('pbx_profile_extension_id')->references('id')->on('profile_extensions')->onDelete('cascade');
            */
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
        Schema::dropIfExists('pbx_services_extensions');
    }
};
