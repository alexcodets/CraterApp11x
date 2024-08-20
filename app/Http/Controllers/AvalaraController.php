<?php

namespace Crater\Http\Controllers;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\DataObject\AvalaraCompanyDO;
use Crater\Avalara\DataObject\AvalaraDataBillingDO;
use Crater\Avalara\DataObject\AvalaraInvoiceDO;
use Crater\Avalara\Service\AvalaraService;
use Crater\Models\AvalaraConfig;
use Crater\Models\LogsDev;
// models
use Crater\Models\Modules;
use Illuminate\Http\Request;

class AvalaraController extends Controller
{
    public function index(AvalaraService $service)
    {
        return $service->getPCode([
            'ctry' => 'USA',
            'st' => 'NC',
            'city' => 'Durham',
            'zip' => 27701,
        ]);
    }

    public function store(AvalaraService $service)
    {
        $invoice = new AvalaraInvoiceDO('2018-09-24 11:00:00', false, '12345');
        $billing = new AvalaraDataBillingDO('USA', 'NC', 'Durham', 27701);
        $company = new AvalaraCompanyDO(1, 1, true, true, true);

        $service->prepareTax($invoice, $billing, $company);
        $service->addLine(5, 19, 21, 1); //line VOIP
        $service->addLine(2, 19, 578, 1); //pbx VOIP
        $service->addLine(4, 19, 41, 1); //pbx_extension VOIP
        $service->addCharge(19.99, 19, 8, 1); //install VOIP
        $service->addCharge(29.99, 19, 48, 1); //call VOIP

        return $service->getTaxes();
    }

    public function testCalcTaxes()
    {
        $config = config('avalara.sandbox');
        $transaction = config('avalara.transaction_type');
        $services = config('avalara.services');
        $api = new AvalaraApi();
        $doc = 12345;
        $body = [
            #Company Data
            'cmpn' => [
                'bscl' => 1,
                'svcl' => 1,
                'fclt' => true,
                'frch' => true,
                'reg' => true,
            ],
            #Invoice
            'inv' => [
                [
                    'doc' => $doc,
                    'bill' => [
                        'ctry' => 'USA',
                        'st' => 'NC',
                        'city' => 'Durham',
                        'zip' => 27701,
                    ],
                    'cust' => 1,
                    'date' => '2018-09-24T11:00:00',
                    // chg = Charge, tran = Transaction, ser = Service Pair, line = Number of Lines
                    'itms' => [
                        [
                            'line' => 5,
                            'sale' => 1,
                            'tran' => $transaction,
                            'serv' => $services['lines'],
                        ],
                        [
                            'line' => 2,
                            'sale' => 1,
                            'tran' => $transaction,
                            'serv' => $services['pbx'],
                        ],
                        [
                            'line' => 4,
                            'sale' => 1,
                            'tran' => $transaction,
                            'serv' => $services['pbx_extension'],
                        ],
                        [
                            'chg' => 19.99,
                            'sale' => 1,
                            'tran' => $transaction,
                            'serv' => $services['install'],
                        ],
                        [
                            'chg' => 29.99,
                            'sale' => 1,
                            'tran' => $transaction,
                            'serv' => $services['wireless_access_charge'],
                        ],
                    ],
                    'cmmt' => false,
                ],
            ],
        ];

        return $api->getTaxes($body);
    }

    public function existsAvalara(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraController", "existsAvalara");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // consultar avalara en tabla modules
        $avalara = Modules::select('*')->where('name', 'avalara')->where('status', 'A')->first();
        $avalaraConfig = AvalaraConfig::select('*')->where('status', 'A')->get();


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'Avalara' => $avalara,
        ], "message" => "AvalaraController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "AvalaraController existsAvalara");
        /////////////////////////////////////////

        /* //Log::debug('count!!');
        //Log::debug(count($avalaraConfig)); */


        if (isset($avalara->id) && count($avalaraConfig) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
