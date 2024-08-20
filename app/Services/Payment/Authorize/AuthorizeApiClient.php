<?php

namespace Crater\Services\Payment\Authorize;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Log;
use net\authorize\api\constants\ANetEnvironment;

class AuthorizeApiClient
{
    private string $url;

    private Client $client;

    private bool $verify_peer = true; // attempt trust validation of SSL certificates when establishing secure connections.

    public function __construct()
    {
        $this->url = sprintf("%s/xml/v1/request.api", ANetEnvironment::CUSTOM);
        $this->client = new Client();
    }

    protected function setUrl(string $endPoint)
    {
        $this->url = sprintf("%s/xml/v1/request.api", $endPoint);
    }

    public function setPostUrl(string $endPoint)
    {
        $this->setUrl($endPoint);
    }

    private function getCurlOptions(string $xmlRequest): array
    {
        $options = [
            //CURLOPT_INTERFACE      => 'xxx.xxx.xxx.xxx',
            CURLOPT_POSTFIELDS => $xmlRequest,
            CURLOPT_HEADER => 0,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];
        //$this->logger->info(sprintf(" Url: %s", $post_url));
        // Do not log requests that could contain CC info.
        //$this->logger->info(sprintf("Request to AnetApi: \n%s", $xmlRequest));

        if ($this->verify_peer) {
            $options[CURLOPT_CAINFO] = dirname(__FILE__).'/ssl/cert.pem';
            //$options[CURLOPT_CAINFO] = dirname(dirname(__FILE__)) . '/../../ssl/cert.pem';
            //Log::channel('authorize')->info('SSL Path:');
            //Log::channel('authorize')->info(dirname(__FILE__ ) . '/ssl/cert.pem');

            //Log::channel('authorize')->info(dirname(dirname(__FILE__)) . '/../../ssl/cert.pem');

        } else {
            Log::channel('authorize')->error('Invalid SSL option for the request');

            //$this->logger->error("Invalid SSL option for the request");
            return [];
        }

        if (preg_match('/xml/', $this->url)) {
            $options[CURLOPT_HTTPHEADER] = ["Content-Type: text/json"];
            //Log::channel('authorize')->info("Sending 'XML' Request type");
        }

        return $options;
    }

    public function sendRequest(string $xmlRequest)
    {
        $options = $this->getCurlOptions($xmlRequest);
        $xmlResponse = '';
        Log::channel('authorize')->info("<--------Inside Send Request-------->");
        if ($options == []) {
            return '[]';
        }

        try {

            //Log::channel('authorize')->info("Sending http request via Guzzle");
            //Log::channel('authorize')->info("Url:  {$this->url}");

            $apiResponse = $this->client->post($this->url, [
                'curl' => $options
            ]);

            $xmlResponse = $apiResponse->getBody();
            Log::channel('authorize')->info("Code: {$apiResponse->getStatusCode()}");
            //Log::channel('authorize')->info("Response from AnetApi: $xmlResponse");
            //Log::channel('authorize')->info($xmlResponse);

        } catch (GuzzleException $ex) {
            Log::channel('authorize')->error($ex->getMessage());
            $errorMessage = sprintf(
                "\n%s:Error making http request via curl: Code:'%s', Message:'%s', Trace:'%s', File:'%s':'%s'",
                $this->now(),
                $ex->getCode(),
                $ex->getMessage(),
                $ex->getTraceAsString(),
                $ex->getFile(),
                $ex->getLine()
            );
            Log::channel('authorize')->error($errorMessage);
        } catch (Exception $e) {
            Log::channel('authorize')->error('Error with Guzzle');
            Log::channel('authorize')->error($e->getMessage());
        }


        /*       if ($this->logger && $this->logger->getLogFile()) {
                   if ($curl_error = curl_error($curl_request)) {
                       //$this->logger->error("CURL ERROR: $curl_error");
                   }

               }*/

        /*        Log::channel('authorize')->error('Api Response: ');
        Log::channel('authorize')->error($xmlResponse);
        Log::channel('authorize')->error(json_encode(trim($xmlResponse)));*/
        return str_replace("\xEF\xBB\xBF", '', trim($xmlResponse));

    }

    protected function now(): string
    {
        return now()->format(DATE_RFC2822);
    }
}
