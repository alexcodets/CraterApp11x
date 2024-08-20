<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('pbx_extensions', function (Blueprint $table) {
            $table->boolean('dhcp')->nullable()->default(false)->after('macaddress');
            $table->string('static_ip', 15)->nullable()->after('dhcp');
            $table->string('time_zone', 50)->nullable()->after('static_ip');
        });
    }
};
