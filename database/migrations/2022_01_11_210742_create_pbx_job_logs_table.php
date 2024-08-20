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
        Schema::create('pbx_job_logs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('job name');
            $table->unsignedTinyInteger('lvl')->comment('0 => debug, 1 => info, 2 => warning, 3 => error');
            $table->text('response')->comment('job response, usually error msg');
            $table->json('data')->comment('extra data concerning the job');
            $table->string('note');
            $table->unsignedInteger('pbx_service_id')->nullable();
            $table->foreign('pbx_service_id')->references('id')->on('pbx_services')->onDelete('cascade');
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
        Schema::dropIfExists('pbx_job_logs');
    }
};
