<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Subscription\SubscribeRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\PaymentHistory;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function current(): JsonResponse
    {
        $user = auth('api')->user();
        $subscription = Subscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('started_at')
            ->first();

        if (! $subscription) {
            return response()->json(null);
        }

        return response()->json(new SubscriptionResource($subscription));
    }

    public function store(SubscribeRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $plan = SubscriptionPlan::where('code', $request->plan_code)->firstOrFail();

        $subscription = DB::transaction(function () use ($user, $plan) {
            Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'canceled', 'ended_at' => now()]);

            $new = Subscription::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $plan->id,
                'started_at'           => now(),
                'ended_at'             => null,
                'status'               => 'active',
            ]);

            if ($plan->price_jpy > 0) {
                PaymentHistory::create([
                    'user_id'              => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'amount_jpy'           => $plan->price_jpy,
                    'paid_at'              => now(),
                ]);
            }

            return $new->load('plan');
        });

        return response()->json(new SubscriptionResource($subscription), 201);
    }

    public function destroy(): JsonResponse
    {
        $user = auth('api')->user();
        $subscription = Subscription::with('plan')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('started_at')
            ->first();

        if (! $subscription) {
            return response()->json(['message' => 'アクティブなプランがありません'], 404);
        }

        $subscription->update([
            'status'   => 'canceled',
            'ended_at' => now(),
        ]);

        return response()->json(new SubscriptionResource($subscription));
    }
}
