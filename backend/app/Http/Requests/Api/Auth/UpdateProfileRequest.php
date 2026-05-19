<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'     => ['sometimes', 'string', 'max:100'],
            'nickname' => ['sometimes', 'nullable', 'string', 'max:50'],
            'email'    => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
        ];
    }
}
