<?php

namespace Crater\Avalara\Traits;

trait ApiResponseTrait
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

    protected function errorResponse(string $message = 'Error', array $errors = [], int $status = 400): array
    {
        return [
            'success' => false,
            'message' => __($message),
            'status' => $status,
            'errors' => $errors,
        ];
    }
}
