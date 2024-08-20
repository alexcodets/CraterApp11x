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
        Schema::create('avalara_taxes', function (Blueprint $table) {
            $table->id();
            $table->boolean('bill')->comment('Billable Indicates if the tax is billable to your customer');
            $table->boolean('cmpl')->comment('Compliance Indicates if the tax is to be reported to the jurisdiction');
            $table->double('tm')->comment('Taxable Measure The basis for calculation of rate-based taxes');
            $table->unsignedSmallInteger('calc')->comment('Calculation Type Indicates how the tax is calculated');
            $table->string('name');
            $table->double('exm')->comment('Exempt Sale Amount');
            $table->unsignedSmallInteger('lns')->comment('Number of lines taxed');
            $table->double('min')->comment('Amount of minutes taxed');
            $table->unsignedInteger('pcd')->comment('Reporting PCode PCode representing reporting tax jurisdiction');
            $table->unsignedInteger('taxpcd')->comment('Taxing PCode PCode representing taxing jurisdiction. Only returned when retext in RequestConfig is set to true')->nullable();
            $table->double('rate')->comment('Applicable tax rate');
            $table->boolean('sur')->comment('Surcharge Indicates if this tax is a surcharge');
            $table->double('tax')->comment(' Tax Amount For rate-based taxes, Tax Amount = Taxable Measure * Rate');
            $table->unsignedSmallInteger('lvl')->comment('Tax Level Indicates the jurisdiction level of the tax');
            #opcional fields
            $table->boolean('usexm')->nullable()->comment('User Exempt Flag indicating if the tax has been exempted by the user via Exemptions (exms)');
            $table->boolean('notax')->nullable()->comment('Is No Tax Transaction Flag indicating that the transaction processed successfully but returned no taxes.');
            $table->unsignedInteger('trans')->nullable()->comment('Transaction Type Transaction type use to calculate tax');
            $table->unsignedInteger('svc')->nullable()->comment('Service Type Service type use to calculate tax');
            $table->double('chg')->nullable()->comment('Charge Charge used to calculate tax.');

            #Relations
            $table->unsignedBigInteger('avalara_type_id');
            $table->foreign('avalara_type_id')->references('id')->on('avalara_taxe_types');

            $table->unsignedBigInteger('avalara_category_id');
            //$table->foreign('avalara_category_id')->references('id')->on('avalara_taxe_categories');

            $table->unsignedInteger('package_id')->nullable();
            $table->unsignedInteger('invoice_item_id')->nullable();
            // $table->foreign('invoice_item_id')->references('id')->on('invoice_items')->onDelete('cascade');

            $table->unsignedInteger('estimate_item_id')->nullable();
            //$table->foreign('estimate_item_id')->references('id')->on('estimate_items')->onDelete('cascade');

            $table->unsignedInteger('package_item_id')->nullable();
            // $table->foreign('package_item_id')->references('id')->on('package_items')->onDelete('cascade');
            $table->integer('item_id')->unsigned()->nullable();
            //$table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            $table->enum('status', ['A', 'I'])->default('A');
            $table->integer('company_id')->unsigned()->nullable();
            //$table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedInteger('creator_id')->nullable();
            //$table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('amount');
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
        Schema::dropIfExists('avalara_taxes');
    }
};
