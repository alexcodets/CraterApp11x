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
        Schema::create('rol_permisions', function (Blueprint $table) {
            $table->id();
            $table->integer('rol_id')->unsigned();
            $table->enum('module', ['customers', 'providers', 'estimates', 'invoices', 'payments', 'items', 'expenses', 'packages', 'corepbx', 'tickets', 'users', 'reports', 'settings', 'account_settings', 'company_info', 'preferences', 'customizations', 'notifications', 'tax_Groups', 'tax_types', 'tax_types', 'payment_modes', 'custom_fields', 'notes', 'expense_categories', 'mail_configuration', 'file_disk', 'backup', 'logs', 'Modules', 'PBXware', 'Avalara', 'BillPay', 'roles', 'payment_gateways', 'Authorize', 'Paypal']);
            $table->boolean('access')->default(true);
            $table->boolean('create')->default(true);
            $table->boolean('read')->default(true);
            $table->boolean('update')->default(true);
            $table->boolean('delete')->default(true);
            $table->integer('company_id')->unsigned()->nullable();
            $table->unsignedInteger('creator_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::create('user_permisions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->enum('module', ['customers', 'providers', 'estimates', 'invoices', 'payments', 'items', 'expenses', 'packages', 'corepbx', 'tickets', 'users', 'reports', 'settings', 'account_settings', 'company_info', 'preferences', 'customizations', 'notifications', 'tax_Groups', 'tax_types', 'tax_types', 'payment_modes', 'custom_fields', 'notes', 'expense_categories', 'mail_configuration', 'file_disk', 'backup', 'logs', 'Modules', 'PBXware', 'Avalara', 'BillPay', 'roles', 'payment_gateways', 'Authorize', 'Paypal']);
            $table->boolean('access')->default(true);
            $table->boolean('create')->default(true);
            $table->boolean('read')->default(true);
            $table->boolean('update')->default(true);
            $table->boolean('delete')->default(true);
            $table->integer('company_id')->unsigned()->nullable();
            $table->unsignedInteger('creator_id')->unsigned()->nullable();
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
        Schema::dropIfExists('userpermisions');
    }
};
