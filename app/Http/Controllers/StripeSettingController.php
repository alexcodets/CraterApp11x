<?php

namespace Crater\Http\Controllers;

use Crater\Http\Requests\StripeSettingRequest;
use Crater\Http\Resources\StripeSettingResource;
use Crater\Models\StripeSetting;
use Illuminate\Http\Request;

class StripeSettingController
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 15);

        $stripeSettings = StripeSetting::paginate($limit);

        return StripeSettingResource::collection($stripeSettings);
    }

    public function store(StripeSettingRequest $request)
    {
        $data = array_merge($request->validated(), ['creator_id' => auth()->id()]);

        $stripeSetting = StripeSetting::create($data);

        return new StripeSettingResource($stripeSetting);
    }

    public function show(StripeSetting $stripeSetting)
    {
        return new StripeSettingResource($stripeSetting);
    }

    public function update(StripeSettingRequest $request, StripeSetting $stripeSetting)
    {
        $data = array_merge($request->validated(), ['creator_id' => auth()->id()]);

        $stripeSetting->update($data);

        return new StripeSettingResource($stripeSetting);
    }

    public function destroy(StripeSetting $stripeSetting)
    {
        $stripeSetting->delete();

        return response()->json();
    }

    public function getDefaultSetting()
    {
        $stripeSetting = StripeSetting::where('status', 'A')->first();

        return new StripeSettingResource($stripeSetting);
    }

    public function requestIDs()
    {
        $stripeSetting = StripeSetting::where('status', 'A')->first();

        if ($stripeSetting) {
            $stripe = new \Stripe\StripeClient($stripeSetting->secret_key);
            $customer = $stripe->customers->create();
            $request_id = $customer->getLastResponse()->headers["Request-Id"];

            return response()->json(['request_id' => $request_id]);
        } else {
            return response()->json(['error' => 'No active stripe setting'], 400);
        }
    }
}
