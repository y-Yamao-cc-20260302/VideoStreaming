<?php

namespace App\Http\Requests\Api\Cast;

use Illuminate\Foundation\Http\FormRequest;

class SearchCastRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
