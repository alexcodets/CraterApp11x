<?php

namespace Crater\Http\Controllers;

use Crater\Http\Requests\CustomSearchReportRequest;
use Crater\Models\LogsDev;
use Crater\Pbxware\Service\CustomSearchCdrService;
use Symfony\Component\HttpFoundation\Response;

class CustomSearchReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param CustomSearchReportRequest $request
     * @param CustomSearchCdrService $service
     * @return array|bool[]|int[]
     */
    public function __invoke(CustomSearchReportRequest $request, CustomSearchCdrService $service)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomSearchController", "reportCDR");

        try {
            $response = array_merge($service->run($request), ['success' => true, 'code' => Response::HTTP_OK]);

        } catch (\Throwable $th) {
            $response = ['success' => false, 'data' => '', 'message' => $th->getMessage(), 'code' => $th->getCode()];
        }

        LogsDev::finishLog($log, $response, $time, $response['success'] ? 'D' : 'E', "Custom search Report pbxExtensions");

        return $response;

    }
}
