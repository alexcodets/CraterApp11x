<?php

use Carbon\Carbon;
use Crater\Models\InvoiceTemplate;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        InvoiceTemplate::create([
            'name' => 'Template 4',
            'view' => 'template_pos',
            'path' => '/assets/img/PDF/Template3.png',
            'created_at ' => Carbon::now()->format('Y-m-d')
        ]);
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
