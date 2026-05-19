<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => (int) $this->id,
            'rating'     => (int) $this->rating,
            'comment'    => $this->comment,
            'created_at' => $this->created_at?->toIso8601String(),
            'user'       => $this->whenLoaded('user', fn () => [
                'id'         => (int) $this->user->id,
                'nickname'   => $this->user->nickname ?? $this->user->name,
                'avatar_url' => $this->user->avatar_path ? asset('storage/'.$this->user->avatar_path) : null,
            ]),
        ];
    }
}
