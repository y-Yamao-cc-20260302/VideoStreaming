<?php

namespace App\Http\Requests\Api\CastFavorite;

use Illuminate\Foundation\Http\FormRequest;

class StoreCastFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cast_id' => ['required', 'integer', 'exists:casts,id'],
        ];
    }
}
