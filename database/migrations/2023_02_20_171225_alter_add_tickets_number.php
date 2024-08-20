<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerTicket;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        //

        $companies = Company::all();

        if ($companies->isEmpty()) {
            return;
        }

        foreach ($companies as $company) {

            $prefixobj = CompanySetting::where("option", "TTW_prefix")->where("company_id", $company->id)->first();
            $prefix = "TTW";

            if ($prefixobj == null) {
                $prefixobj = new CompanySetting();
                $prefixobj->option = "TTW_prefix";
                $prefixobj->value = "TTW";
                $prefixobj->company_id = $company->id;
                $prefixobj->save();

            }

            $tickets = CustomerTicket::whereNULL("ticket_number")->where("company_id", $company->id)->get();

            foreach ($tickets as $ticket) {
                $lastOrder = CustomerTicket::where('ticket_number', 'LIKE', $prefix.'-%')

                    ->orderBy('ticket_number', 'desc')
                    ->first();

                if (! $lastOrder) {
                    // We get here if there is no order at all
                    // If there is no number set it to 0, which will be 1 at the end.
                    $number = 0;
                } else {
                    $number = explode("-", $lastOrder->ticket_number);
                    $number = $number[1];
                }

                $ticket->ticket_number = $prefix."-".sprintf('%06d', intval($number) + 1);
                $ticket->save();

            }

        }
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
