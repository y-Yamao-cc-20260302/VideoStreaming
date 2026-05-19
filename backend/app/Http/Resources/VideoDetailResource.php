<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (int) $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'thumbnail_url' => $this->thumbnail_path ? asset('storage/'.$this->thumbnail_path) : null,
            'stream_url'    => $this->stream_url,
            'duration_sec'  => (int) $this->duration_sec,
            'release_date'  => $this->release_date?->toDateString(),
            'category'      => new CategoryResource($this->whenLoaded('category')),
            'genres'        => GenreResource::collection($this->whenLoaded('genres')),
            'rating_avg'    => $this->when(isset($this->rating_avg), fn () => $this->rating_avg !== null ? round((float) $this->rating_avg, 2) : null),
            'rating_count'  => $this->when(isset($this->rating_count), fn () => (int) $this->rating_count),
            'is_favored'    => $this->when(isset($this->is_favored), fn () => (bool) $this->is_favored),
            'progress_sec'  => $this->when(isset($this->progress_sec), fn () => (int) $this->progress_sec),
        ];
    }
}
