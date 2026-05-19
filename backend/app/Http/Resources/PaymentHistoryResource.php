<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => (int) $this->id,
            'plan_code'  => $this->plan?->code,
            'plan_name'  => $this->plan?->name,
            'amount_jpy' => (int) $this->amount_jpy,
            'paid_at'    => $this->paid_at?->toIso8601String(),
        ];
    }
}
