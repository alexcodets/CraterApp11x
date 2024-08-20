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
        Schema::create('pbx_extensions', function (Blueprint $table) {
            $table->increments('id');
            // columns fk
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();

            /*
                        $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                        $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            */
            $table->string('ext_name')->nullable();
            $table->string('ext_email')->nullable();
            $table->string('ext_status')->nullable();
            $table->json('ext_details')->nullable();
            $table->string('tenant_name')->nullable();
            $table->string('tenant_code')->nullable();
            $table->json('tenant_details')->nullable();

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
        Schema::dropIfExists('pbx_extensions');
    }
};






/* campos ext by tenant:
"name": "1001",
"email": "none@careonecomm.com",
"ext": "1001",
"protocol": "sip",
"location": "remote",
"ua_id": "50",
"ua_name": "generic_sip",
"ua_fullname": "Generic SIP",
"status": "enabled",
"macaddress": "",
"linenum": ""

*/
