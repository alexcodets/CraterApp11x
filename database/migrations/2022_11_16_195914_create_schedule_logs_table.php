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
        Schema::create('schedule_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module_name');
            $table->unsignedTinyInteger('lvl')->comment('0 => debug, 1 => info, 2 => warning, 3 => error')->default(0);
            $table->text('message');
            $table->json('extra_data')->comment('extra data concerning the module')->nullable();
            $table->morphs('model');
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
        Schema::dropIfExists('schedule_logs');
    }
};
