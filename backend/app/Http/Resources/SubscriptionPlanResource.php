<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => (int) $this->id,
            'code'        => $this->code,
            'name'        => $this->name,
            'price_jpy'   => (int) $this->price_jpy,
            'description' => $this->description,
            'is_active'   => (bool) $this->is_active,
        ];
    }
}
