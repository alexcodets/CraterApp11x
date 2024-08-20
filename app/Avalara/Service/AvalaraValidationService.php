<?php

namespace Crater\Avalara\Service;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Models\Item;
use Crater\Models\User;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AvalaraValidationService
{
    /**
     * @throws Exception
     */
    public static function itemValidation(Item $item): void
    {
        if ($item->avalara_bool == null || $item->avalara_payment_type == null) {
            throw new Exception(__('avalara.error.not_avalara_item.message'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($item->avalara_type == null || $item->avalara_type == 0) {
            throw new Exception(__('avalara.error.ts_pair.transaction.required.message'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($item->avalara_service_type === null || $item->avalara_service_type === 'ninguno') {
            throw new Exception(__('avalara.error.ts_pair.service.required.message'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (is_null($item->avalaraServiceType)) {
            throw new Exception('Invalid avalara_service_type', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @throws Exception
     */
    public static function companyValidation(User $user)
    {
        if (is_null($user->company)) {
            throw new Exception(__('avalara.error.models.company.not_found'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (is_null($user->company->avalaraConfiguration)) {
            throw new Exception(__('avalara.error.models.config.not_found'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $user->company;
    }

    /**
     * @throws Exception
     */
    public static function validateAndGetAvalaraTaxService(User $user): AvalaraTaxService
    {
        $company = self::companyValidation($user);
        $baseService = new AvalaraService(new AvalaraApi($company->avalaraConfiguration));
        $service = new AvalaraTaxService($user, $baseService);

        $validation = $service->validateUserData($company->avalaraConfiguration);

        if ($validation['success'] === false) {
            throw new Exception($validation['message'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $service;

    }
}
