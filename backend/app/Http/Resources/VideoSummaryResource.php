<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (int) $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'thumbnail_url' => $this->thumbnail_path ? asset('storage/'.$this->thumbnail_path) : null,
            'duration_sec'  => (int) $this->duration_sec,
            'release_date'  => $this->release_date?->toDateString(),
            'category'      => new CategoryResource($this->whenLoaded('category')),
            'genres'        => GenreResource::collection($this->whenLoaded('genres')),
        ];
    }
}
