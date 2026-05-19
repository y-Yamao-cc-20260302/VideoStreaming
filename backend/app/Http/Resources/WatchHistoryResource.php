<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatchHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'video'        => new VideoSummaryResource($this->whenLoaded('video')),
            'progress_sec' => (int) $this->progress_sec,
            'watched_at'   => $this->watched_at?->toIso8601String(),
        ];
    }
}
