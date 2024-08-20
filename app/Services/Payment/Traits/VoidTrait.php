<?php

namespace Crater\Services\Payment\Traits;

trait VoidTrait
{
    public string $name;

    public string $refId;

    /** @return array{'success': false, 'gateway': string, 'transaction_id': string, 'ref_id': string, 'code': string, 'description': string, 'note': string } */
    public function errorResponse(string $code, string $description, string $note, ?string $transactionId = null): array
    {
        return $this->response(false, $code, $description, $note, $transactionId);
    }

    /** @return array{'success': bool, 'gateway': string, 'transaction_id': string, 'ref_id': string, 'code': string, 'description': string, 'note': string } */
    public function response(bool $success, string $code, string $description, string $note, ?string $transactionId = null): array
    {
        return [
            'success' => $success,
            'gateway' => $this->name,
            'transaction_id' => $transactionId,
            'ref_id' => $this->refId,
            'code' => $code,
            'description' => $description,
            'note' => $note,
        ];
    }

    /** @return array{'success': true, 'gateway': string, 'transaction_id': string, 'ref_id': string, 'code': string, 'description': string, 'note': string } */
    public function successResponse(string $code, string $description, string $note, ?string $transactionId = null): array
    {
        return $this->response(true, $code, $description, $note, $transactionId);
    }
}
