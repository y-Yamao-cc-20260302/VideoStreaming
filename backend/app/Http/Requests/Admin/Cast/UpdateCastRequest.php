<?php

namespace App\Http\Requests\Admin\Cast;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCastRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:casts,name'],
            'gender' => ['nullable', 'integer'],
            'birthday' => ['nullable', 'date'],
            'occupation_id' => ['nullable', 'integer', 'exists:occupations,id'],
            'is_publish' => ['nullable', 'boolean'],
            'picture' => ['nullable', 'image', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_publish' => $this->boolean('is_publish'),
        ]);
    }
}
