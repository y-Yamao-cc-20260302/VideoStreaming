<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentHistoryResource;
use App\Models\PaymentHistory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentHistoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $user = auth('api')->user();
        $payments = PaymentHistory::with('plan')
            ->where('user_id', $user->id)
            ->orderByDesc('paid_at')
            ->paginate(20);

        return PaymentHistoryResource::collection($payments);
    }
}
