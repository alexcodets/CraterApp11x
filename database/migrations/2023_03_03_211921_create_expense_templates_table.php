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
        Schema::create('expense_templates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('payment_method_id')->unsigned()->nullable();
            $table->integer('items_id')->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedBigInteger('amount');
            $table->integer('providers_id')->nullable();
            $table->integer('expense_category_id')->unsigned();
            //  $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->boolean('notification')->default(false);
            $table->integer('days_after_payment_date')->nullable();
            $table->enum('initial_status', ['Active', 'Pending'])->default('Active');
            $table->enum('term', ['daily','weekly','monthly','bimonthly','quarterly','biannual','yearly']);
            $table->date('last_date');
            $table->date('expense_date');
            $table->string('description')->nullable();
            $table->string('subject')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->string('template_expense_number')->nullable();



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_templates');
    }
};
