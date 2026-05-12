<?php

namespace App\Http\Requests\Drinks;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLossRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'document_date' => ['required', 'date'],
            'observation' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.article_id' => ['required', Rule::exists('drinks_articles', 'id')],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.reason' => ['nullable', 'string'],
        ];
    }
}
