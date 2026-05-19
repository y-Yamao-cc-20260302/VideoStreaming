<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => (int) $this->id,
            'plan_code'  => $this->plan?->code,
            'plan_name'  => $this->plan?->name,
            'price_jpy'  => $this->plan ? (int) $this->plan->price_jpy : null,
            'status'     => $this->status,
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at'   => $this->ended_at?->toIso8601String(),
        ];
    }
}
