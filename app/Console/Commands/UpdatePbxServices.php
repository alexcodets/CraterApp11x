<?php

namespace Crater\Console\Commands;

use Crater\Models\PbxAdditionalCharge;
use Crater\Models\PbxServices;
use Crater\Models\ProfileDidTollFree;
use Crater\Traits\PbxServicesReCalculateTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePbxServices extends Command
{
    use PbxServicesReCalculateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdatePbxServices {serviceId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {

        try {
            \Log::debug('<----------START COMMAND UPDATE SERVICE---------->');

            $active = true;
            if ($active) {

                $all = false;
                $limit = 1;
                $id = $this->argument('serviceId');
                $profileExtensionId = '';
                $profileDidId = "";
                // update field price in the pbx services
                $this->updateFieldaPackagePrice($all, $limit, $id);
                if ($all) {
                    $pbxServices = PbxServices::where('status', '!=', 'C')->orderBy('created_at', 'asc')->get();
                } else {
                    $pbxServices = PbxServices::where('status', '!=', 'C')->where('id', $id)->orderBy('created_at', 'asc')->limit($limit)->get();
                    \Log::debug('Line migration 41');
                }

                foreach ($pbxServices as $pbxService) {
                    try {
                        \Log::debug('Line migration 46');
                        $profileExtension = $pbxService->pbxPackage->profileExtensions;
                        if (! is_null($profileExtension)) {
                            \Log::debug('Line migration 49');
                            $profileExtensionId = $profileExtension->id;
                        }

                        // get all services extensions with value price where is null
                        $extensions = $pbxService->pbxServiceExtensions->whereNull('price');
                        $countExtension = $pbxService->pbxServiceExtensions->count();
                        if (! $extensions->isEmpty()) {
                            \Log::debug('Line migration 51');
                            // get price of the template of the extension
                            $profileExtension = $pbxService->pbxPackage->profileExtensions;
                            $rateExtension = $profileExtension->rate;
                            $profileExtensionId = $profileExtension->id;

                            foreach ($extensions as $extension) {
                                \Log::debug('Line migration 58');
                                $extension->price = $rateExtension;
                                $extension->save();
                            }
                        }

                        \Log::debug('Line migration 63');
                        $profileDid = $pbxService->pbxPackage->profileDid;
                        if (! is_null($profileDid)) {
                            \Log::debug('Line migration 72');
                            $profileDidId = $profileDid->id;
                        }
                        // get all services did with value price where is null
                        $dids = $pbxService->pbxServiceDids->whereNull('price');
                        $countDids = $pbxService->pbxServiceDids->count();
                        if (! $dids->isEmpty()) {
                            \Log::debug('Line migration 68');
                            // get price of the template of the did
                            $profileDid = $pbxService->pbxPackage->profileDid;
                            $rateDid = $profileDid->did_rate;
                            $profileDidId = $profileDid->id;
                            foreach ($dids as $did) {
                                \Log::debug('Line migration 74');
                                if (is_null($did->custom_did_id)) {
                                    \Log::debug('Line migration 76');
                                    // if field custom_did_id is null update field price with the rate of the template

                                    $did->price = $rateDid;
                                    $did->save();
                                    \Log::debug('Line migration 81');
                                } else {
                                    \Log::debug('Line migration 83');
                                    // if field custom_did_id does not is null update field with the rate_per_minute of the table profile_did_toll_fees
                                    $ratePerMinute = ProfileDidTollFree::where('id', $did->custom_did_id)->pluck('rate_per_minute')->first();

                                    $did->price = $ratePerMinute;
                                    $did->save();
                                    \Log::debug('Line migration 89');
                                }
                            }
                        }
                        \Log::debug('Line migration 93');
                        \Log::debug('Line migration 93 '.$profileExtensionId);
                        \Log::debug('Line migration 93 '.$profileDidId);
                        $addChargesExtension = DB::table('aditional_charges')->where('status', 1)->whereNull('deleted_at')->where('profile_extension_id', $profileExtensionId)->get();
                        $addChargesDid = DB::table('aditional_charges')->where('status', 1)->whereNull('deleted_at')->where('profile_did_id', $profileDidId)->get();
                        \Log::debug('Line migration 110 '.$pbxService->id);
                        PbxAdditionalCharge::where('pbx_service_id', $pbxService->id)->delete();
                        \Log::debug('Line migration 112');

                        if (! $addChargesExtension->isEmpty()) {
                            \Log::debug('Line migration 101');

                            foreach ($addChargesExtension as $chargeExtension) {
                                \Log::debug('Line migration 104');
                                \Log::debug('Line migration 119 '.$chargeExtension->id);
                                $total = $chargeExtension->amount * $countExtension;
                                $dataExtension = json_decode(json_encode($chargeExtension), true);
                                $dataExtension['total'] = $total;
                                $dataExtension['quantity'] = $countExtension;
                                $dataExtension['pbx_service_id'] = $pbxService->id;
                                $dataExtension['additional_charge_id'] = $chargeExtension->id;

                                try {
                                    \Log::debug('Line migration 112');

                                    PbxAdditionalCharge::create($dataExtension);
                                    \Log::debug('Line migration 115');
                                } catch (\Throwable $th) {
                                    \Log::debug('PbxAdditionalCharge::create', ['result' => $th, 'data' => $dataDid]);
                                }
                            }
                        }

                        \Log::debug('Line migration 122');
                        if (! $addChargesDid->isEmpty()) {
                            \Log::debug('Line migration 124');
                            foreach ($addChargesDid as $chargeDid) {
                                \Log::debug('Line migration 126');
                                \Log::debug('Line migration 142 '.$chargeDid->id);
                                $total = $chargeDid->amount * $countDids;
                                $dataDid = json_decode(json_encode($chargeDid), true);
                                $dataDid['total'] = $total;
                                $dataDid['quantity'] = $countDids;
                                $dataDid['pbx_service_id'] = $pbxService->id;
                                $dataDid['additional_charge_id'] = $chargeDid->id;

                                try {
                                    \Log::debug('Line migration 134');
                                    PbxAdditionalCharge::create($dataDid);
                                } catch (\Throwable $th) {
                                    \Log::debug('PbxAdditionalCharge::create', ['result' => $th, 'data' => $dataDid]);
                                }
                            }
                        }
                        \Log::debug('Line migration 141');

                        $this->calculatePriceService($pbxService);
                        \Log::debug('Line migration 143');
                    } catch (\Throwable $th) {
                        \Log::debug('updateFieldaPackagePrice', ['error' => $th]);
                    }
                }

            }
        } catch (\Throwable $th) {
            \Log::debug('Migration modify pbx services', ['Error' => $th]);
        }
        \Log::debug('<----------END COMMAND UPDATE SERVICE---------->');

        return 0;
    }

    public function updateFieldaPackagePrice($all, $limit, $id)
    {

        try {
            \Log::debug('Line migration 159');
            if ($all) {
                $pbxServices = PbxServices::where('status', '!=', 'C')->orderBy('created_at', 'asc')->get();
            } else {
                $pbxServices = PbxServices::where('status', '!=', 'C')->where('id', $id)->orderBy('created_at', 'asc')->limit($limit)->get();
                \Log::debug('Line migration 164');
            }

            foreach ($pbxServices as $pbxService) {
                \Log::debug('Line migration 168');
                // $pbxService->pbxpackages_price = $pbxService->pbxpackages_price * 100;
                // $pbxService->save();
                DB::table('pbx_services')->where('id', $pbxService->id)
                    ->update([
                        'pbxpackages_price' => $pbxService->pbxpackages_price * 100
                    ]);
                \Log::debug('Line migration 171');

            }
        } catch (\Throwable $th) {
            \Log::debug('updateFieldaPackagePrice', ['error' => $th]);
        }
    }
}
