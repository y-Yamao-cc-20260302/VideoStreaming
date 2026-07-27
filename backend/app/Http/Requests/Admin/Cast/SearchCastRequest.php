<?php
// 検索するときのバリデーションチェック
namespace App\Http\Requests\Admin\Cast;

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
            'keyword' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'integer'],
            'occupation_id' => ['nullable', 'integer', 'exists:occupations,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_publish' => $this->boolean('is_publish'),
        ]);
    }
}
