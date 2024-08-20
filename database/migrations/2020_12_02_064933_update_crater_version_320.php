<?php

use Crater\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public const VERSION = '3.2.0';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Setting::setSetting('version', static::VERSION);
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
