<?php

namespace App\Http\Requests\Admin\Cast;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCastRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 編集対象の出演者のnameを取得
        $name = $this->route('cast');
        return [
            // ignoreは編集時の例外、名前を更新せずに保存してもバリデーションエラーが発生しないようにする
            'name' => ['required', 'string', 'max:255', Rule::unique('casts', 'name')->ignore($name)],
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
