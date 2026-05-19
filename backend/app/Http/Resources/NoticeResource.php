<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class NoticeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isDetail = $request->routeIs('*.show') || str_contains((string) $request->path(), '/notices/');

        return [
            'id'           => (int) $this->id,
            'title'        => $this->title,
            'body'         => $this->when($isDetail, $this->body),
            'body_excerpt' => $this->when(! $isDetail, Str::limit((string) $this->body, 120)),
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
