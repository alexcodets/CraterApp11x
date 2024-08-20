<?php

use Crater\Models\Invoice;
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
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('edited_at', 0)->nullable();
        });

        Invoice::chunk(150, function ($invoices) {
            foreach ($invoices as $invoice) {
                $invoice->edited_at = $invoice->created_at;
                $invoice->save();
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            //

            $table->dropColumn('edited_at');
        });
    }
};
