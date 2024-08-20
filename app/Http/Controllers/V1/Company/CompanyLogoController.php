<?php

namespace Crater\Http\Controllers\V1\Company;

use Crater\Http\Controllers\Controller;
use Crater\Models\Company;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyLogoController extends Controller
{
    public function show(Company $company): JsonResponse
    {
        $logo = $company->logo_base64;

        return response()->json(
            ['data' => $logo],
            $logo ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );

    }
}
