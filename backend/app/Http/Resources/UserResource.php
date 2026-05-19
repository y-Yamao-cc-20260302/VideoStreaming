<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => (int) $this->id,
            'email'      => $this->email,
            'name'       => $this->name,
            'nickname'   => $this->nickname,
            'avatar_url' => $this->avatar_path ? asset('storage/'.$this->avatar_path) : null,
            'status'     => $this->status,
            'subscription' => $this->whenLoaded('activeSubscription', function () {
                if (! $this->activeSubscription) {
                    return null;
                }
                return [
                    'plan_code'  => $this->activeSubscription->plan?->code,
                    'plan_name'  => $this->activeSubscription->plan?->name,
                    'status'     => $this->activeSubscription->status,
                    'started_at' => $this->activeSubscription->started_at?->toIso8601String(),
                    'ended_at'   => $this->activeSubscription->ended_at?->toIso8601String(),
                ];
            }),
        ];
    }
}
