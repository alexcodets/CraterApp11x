<?php

namespace Crater\Traits;

use Crater\Models\PbxPackages;
use Crater\Models\PbxServers;
use GuzzleHttp\Client;

trait PbxApiTrait
{
    /**
     * Display a listing of the DID by tenant / server ID .
     * @param $idPbxPackage
     * @param $idTenant
     */
    public function getDIDListByTenant($idPbxPackage, $idTenant = '')
    {
        $url = '';
        if (strlen($idTenant) > 0) {
            // get pbx package
            $PbxPackage = PbxPackages::find($idPbxPackage);

            if ($PbxPackage->pbx_server_id) {

                if ($PbxPackage->did) {
                    // get pbx server
                    $PbxServer = PbxServers::find($PbxPackage->pbx_server_id);
                    $url = $PbxServer->hostname.'?apikey='.$PbxServer->api_key.'&action=pbxware.did.list&server='.$idTenant;

                } else {

                    $resp = [
                        "message" => "Enable the DID's for this PBX package",
                        "status" => 403,
                        "success" => true];

                    return json_decode(json_encode($resp));
                }

            } else {

                $resp = [
                    "message" => "PBX package does not have server asociate",
                    "status" => 405,
                    "success" => true];

                return json_decode(json_encode($resp));
            }
        }

        $client = new Client();
        $response = $client->request('GET', $url, [
            'verify' => false,
        ]);

        $resp = $response->getBody();

        return json_decode($resp);
    }

    /**
     * Display a listing of the Tenant.
     * @param $idPbxPackage
     */
    public function getTenantList($idPbxPackage = null)
    {
        try {
            //code...
            if (strlen($idPbxPackage) > 0) {
                // get pbx package
                $PbxPackage = PbxPackages::find($idPbxPackage);

                if ($PbxPackage->pbx_server_id) {
                    // get pbx server
                    $PbxServer = PbxServers::find($PbxPackage->pbx_server_id);
                    $url = $PbxServer->hostname.'?apikey='.$PbxServer->api_key.'&action=pbxware.tenant.list';

                } else {
                    /* $obj = (object) [
                    'message' => 'Paquete no tiene servidor asociado',
                    'status' => 405
                    ];

                    return $obj; */
                    $url = $this->host.'?apikey=""&action=pbxware.tenant.list';
                }

            } else {
                $url = $this->host.'?apikey='.$this->apiKey.'&action=pbxware.tenant.list';
            }

            $client = new Client();
            $response = $client->request('GET', $url, [
                'verify' => false,
            ]);

            $resp = $response->getBody();

            return json_decode($resp);
        } catch (\Throwable $th) {
            //throw $th;
            $response = [
                "error" => true,
                "message" => "Error, can't get Tenant list"
            ];

            return json_encode($response);
        }
    }

    /**
     * Display a listing of the extension by tenant / server ID .
     * @param $idPbxPackage
     * @param $idTenant
     */
    public function getExtListByTenant($idPbxPackage, $idTenant = '')
    {
        if (strlen($idTenant) > 0) {
            // get pbx package
            $PbxPackage = PbxPackages::find($idPbxPackage);

            if ($PbxPackage->pbx_server_id) {

                if ($PbxPackage->extensions) {
                    // get pbx server
                    $PbxServer = PbxServers::find($PbxPackage->pbx_server_id);
                    $url = $PbxServer->hostname.'?apikey='.$PbxServer->api_key.'&action=pbxware.ext.list&server='.$idTenant;
                } else {

                    $resp = [
                        "message" => "Enable the DID's for this PBX package",
                        "status" => 403,
                        "success" => true];


                    return json_decode(json_encode($resp));
                }

            } else {

                $resp = [
                    "message" => "PBX package does not have server asociate",
                    "status" => 405,
                    "success" => true];

                return json_decode(json_encode($resp));
            }

        }

        $client = new Client();
        $response = $client->request('GET', $url, [
            'verify' => false,
        ]);

        $resp = $response->getBody();
        // print_r($resp);

        return json_decode($resp);
    }
}
