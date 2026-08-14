<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CastDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'picture_path' => $this->picture_path ? asset('storage/' . $this->picture_path) : null,
            'birthday' => $this->birthday,
            'occupation' => new OccupationResource($this->whenLoaded('occupation')),
            'is_publish' => $this->is_publish,
            'is_favored' => $this->when(isset($this->is_favored), fn() => (bool) $this->is_favored),

        ];
    }
}
