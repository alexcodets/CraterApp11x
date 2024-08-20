<?php

namespace Crater\Http\Controllers;

use Crater\Http\Requests\VerifyDocumentRequest;
use Crater\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentVerificationController extends Controller
{
    public function verifyDocument(VerifyDocumentRequest $request)
    {
        $frontImage = $this->decodeBase64Image($request->input('ic_front'));
        $backImage = $this->decodeBase64Image($request->input('ic_back'));
        $selfieImage = $this->decodeBase64Image($request->input('selfie'));
        $customerId = $request->input('customer_id');

        $documentResult = $this->sendRapidApiDocument([
            'image' => base64_encode($frontImage),
            'image2' => base64_encode($backImage),
        ]);

        $selfieResult = $this->sendRapidApi(
            'https://face-liveness-detection3.p.rapidapi.com/api/liveness_base64',
            ['image' => base64_encode($selfieImage)],
            'face-liveness-detection3.p.rapidapi.com'
        );

        $isValidDocument = $this->isValidResult($documentResult);
        $isValidSelfie = $this->isValidSelfieResult($selfieResult);

        $customerSelected = User::find($customerId);
        if ($customerSelected) {
            $customerSelected->verified = $isValidDocument && $isValidSelfie;
            $customerSelected->save();
        }

        if (! $isValidDocument || ! $isValidSelfie) {
            return response()->json([
                'errors' => $this->showError($isValidDocument, $isValidSelfie),
                'is_valid_document' => $isValidDocument,
                'is_valid_selfie' => $isValidSelfie,
            ], 400);
        }

        return response()->json([
            'document_result' => $documentResult,
            'selfie_result' => $selfieResult,
            'is_valid_document' => $isValidDocument,
            'is_valid_selfie' => $isValidSelfie,
        ]);
    }

    private function decodeBase64Image($imageData)
    {
        return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
    }

    private function sendRapidApi($url, $payload, $host)
    {
        $client = new Client();

        try {
            $response = $client->request('POST', $url, [
                'json' => $payload,
                'headers' => [
                    'x-rapidapi-key' => env('RAPIDAPI_KEY'),
                    'x-rapidapi-host' => $host,
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('Request Exception: '.$e->getMessage());
            if ($e->hasResponse()) {
                Log::error('Response: '.$e->getResponse()->getBody()->getContents());
            }

            throw $e;
        }
    }

    private function sendRapidApiDocument($payload)
    {

        // Storage::put('temp/front_imagadasdasdasde.png', $payload['image']);
        $client = new Client();

        // Log::debug('Payload for document verification: ', $payload);

        try {
            $response = $client->request('POST', 'https://id-document-recognition2.p.rapidapi.com/api/iddoc_base64', [
                'json' => $payload,
                'headers' => [
                    'x-rapidapi-key' => env('RAPIDAPI_KEY'),
                    'x-rapidapi-host' => 'id-document-recognition2.p.rapidapi.com',
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('Request Exception: '.$e->getMessage());
            if ($e->hasResponse()) {
                Log::error('Response: '.$e->getResponse()->getBody()->getContents());
            }

            throw $e;
        }
    }

    private function isValidResult($result)
    {
        Log::debug(['data' => $result]);

        if (! isset($result['data']) || ! isset($result['data']['ocr'])) {
            return false;
        }

        $data = $result['data'];
        $ocr = $data['ocr'];
        Log::debug(['data' => $data, 'ocr' => $ocr]);

        return isset($data['errorCode']) && $data['errorCode'] === 0 &&
            isset($ocr['name']) && isset($ocr['surname']) &&
            isset($ocr['dateOfExpiry']) && isset($ocr['documentNumber']) &&
            isset($data['image']['portrait']) && isset($data['image']['documentFrontSide']);
    }

    private function isValidSelfieResult($result)
    {
        if (! isset($result['data']) || ! isset($result['data']['result'])) {
            return false;
        }

        return $result['data']['result'] === 'genuine';
    }

    private function showError($isDocumentValid, $isSelfieValid)
    {
        $error = [];

        if (! $isDocumentValid) {
            $error[] = 'Document is not valid';
        }

        if (! $isSelfieValid) {
            $error[] = 'Selfie is not valid';
        }

        return $error;
    }
}
