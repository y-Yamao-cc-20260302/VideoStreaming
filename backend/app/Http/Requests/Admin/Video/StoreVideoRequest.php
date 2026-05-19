<?php

namespace App\Http\Requests\Admin\Video;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'category_id'   => ['required', 'integer', 'exists:categories,id'],
            'genre_ids'     => ['nullable', 'array'],
            'genre_ids.*'   => ['integer', 'exists:genres,id'],
            'stream_url'    => ['required', 'string', 'max:500'],
            'duration_sec'  => ['nullable', 'integer', 'min:0'],
            'release_date'  => ['nullable', 'date'],
            'is_published'  => ['nullable', 'boolean'],
            'thumbnail'     => ['nullable', 'image', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
        ]);
    }
}
