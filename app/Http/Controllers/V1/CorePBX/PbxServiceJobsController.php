<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\JobBatch;
use Crater\Models\PbxServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Bus;

class PbxServiceJobsController extends Controller
{
    public function index(PbxServices $pbxService): JsonResponse
    {
        try {
            $jobs = JobBatch::forService($pbxService->id)
                ->selectRaw('sum(total_jobs) as total_jobs')
                ->selectRaw('sum(failed_jobs) as failed_jobs')
                ->selectRaw('sum(pending_jobs) as pending_jobs')->first();

            $jobs->active_jobs = $jobs->pending_jobs > 0;

            return response()->json([
                'success' => true,
                'data' => $jobs
            ]);


        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ]);
        }

    }

    public function destroy(PbxServices $pbxService): JsonResponse
    {
        try {
            $qty = JobBatch::forService($pbxService->id)
                ->selectRaw('sum(pending_jobs) as pending_jobs')->first();

            if ($qty->pending_jobs == 0) {
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'message' => 'There are no jobs to delete'
                ]);
            }

            $jobs = JobBatch::forService($pbxService->id)->pluck('id');
            foreach ($jobs as $id) {
                Bus::findBatch($id)->cancel();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'jobs_cancelled' => $qty->pending_jobs,
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ]);
        }

    }

    public function fullDestroy(PbxServices $pbxService): JsonResponse
    {
        $re = '/:"Crater\\\\Models\\\\PbxServices";s:2:"id";i:'.$pbxService->id.';/m';
        $total = 0;

        try {
            JobBatch::chunk(200, function ($batches) use ($re, &$total) {
                /* @var JobBatch $batch */
                foreach ($batches as $batch) {
                    if (preg_match($re, $batch->options)) {
                        $bus = Bus::findBatch($batch->id);
                        $total += $bus->pendingJobs;
                        $bus->cancel();
                    }
                }
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'jobs_cancelled' => $total,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $th->getMessage(),
                'code' => $th->getCode()
            ]);
        }

    }
}
