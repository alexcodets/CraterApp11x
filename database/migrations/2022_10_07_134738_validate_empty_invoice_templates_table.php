<?php

use Crater\Models\InvoiceTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $invoice_templates_results = count(InvoiceTemplate::get()->pluck('id')->toArray());

        if ($invoice_templates_results == 0) {
            InvoiceTemplate::create([
                'name' => 'Template 1',
                'view' => 'invoice1',
                'path' => '/assets/img/PDF/Template1.png'
            ]);

            InvoiceTemplate::create([
                'name' => ' Template 2',
                'view' => 'invoice2',
                'path' => '/assets/img/PDF/Template2.png'
            ]);

            InvoiceTemplate::create([
                'name' => 'Template 3',
                'view' => 'invoice3',
                'path' => '/assets/img/PDF/Template3.png'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_templates');
    }
};
