<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pbx_server_tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->unsignedSmallInteger('tenant_code');
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedBigInteger('pbx_server_id');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamps();
            $table->dateTime('completed_at')->nullable();
            $table->softDeletes();
        });

    }
};
