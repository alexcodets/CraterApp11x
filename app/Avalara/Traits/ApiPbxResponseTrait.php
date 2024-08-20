<?php

namespace Crater\Avalara\Traits;

trait ApiPbxResponseTrait
{
    protected function successResponse(string $message = 'OK', array $data = [], int $status = 200): array
    {
        return [
            'success' => true,
            'message' => __($message),
            'status' => $status,
            'data' => $data,
        ];
    }

    protected function errorResponse($data = null, string $message = null, array $errors = [], int $status = null): array
    {
        return [
            'success' => false,
            'message' => __($message) ?? $data['error'],
            'status' => $status ?? ($data['code'] ?? 400),
            'errors' => $errors,
        ];
    }
}
