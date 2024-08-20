<?php

namespace Crater\Models\actions;

use Crater\Models\PbxServices;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CallDetailRegisterAction
{
    public function getFallBackTableNameFromService(PbxServices $service)
    {
        $tableName = "pbx_cdrs_{$service->pbx_tenant_id}_{$service->pbxPackage->pbxServer->id}";

        if (! Schema::hasTable($tableName)) {
            $tableName = 'call_detail_registers';
        }

        return $tableName;
    }

    public function getTableNameFromService(PbxServices $service): string
    {
        return "pbx_cdrs_{$service->pbx_tenant_id}_{$service->pbxPackage->pbxServer->id}";
    }

    public function createTableFromService(PbxServices $service): bool
    {
        $tableName = $this->getTableNameFromService($service);
        if (Schema::hasTable($tableName)) {
            return false;
        }
        $this->createTable($tableName);

        return true;
    }

    public function firstOrCreateTableFromService(PbxServices $service): string
    {
        $tableName = $this->getTableNameFromService($service);
        if (! Schema::hasTable($tableName)) {
            $this->createTable($tableName);
        }

        return $tableName;
    }

    public function createTable(string $tableName)
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('from', 60);
            $table->string('to', 60);
            $table->unsignedInteger('start_date');
            $table->unsignedInteger('duration');
            $table->unsignedInteger('billing_duration')->nullable();
            $table->unsignedInteger('round_duration')->nullable();
            $table->unsignedFloat('cost', 9, 5)->nullable();
            $table->unsignedInteger('exclusive_seconds')->default(0);
            $table->unsignedFloat('exclusive_cost', 9, 5)->nullable()->default(0);
            $table->string('status', 30); //(8 ⇒ "Answered", 4 ⇒ "Not Answered", 2 ⇒ "Busy", 1 ⇒ "Failed"
            $table->string('unique_id', 25);
            $table->tinyInteger('type')->comment('inbound(0) / outbound(1)')->nullable();
            $table->integer('trunk_id')->nullable();
            $table->date('billed_at')->nullable();
            $table->unsignedInteger('pbx_did_id')->nullable();
            $table->unsignedInteger('pbx_extension_id')->nullable();
            $table->unsignedBigInteger('international_rate_id')->nullable();
            $table->foreign('pbx_did_id')->references('id')->on('pbx_did')->onDelete('cascade');
            $table->foreign('pbx_extension_id')->references('id')->on('pbx_extensions')->onDelete('cascade');
            $table->foreign('international_rate_id')->references('id')->on('international_rate')->onDelete('cascade');
            $table->index(['unique_id', 'start_date']);
        });
    }
}
