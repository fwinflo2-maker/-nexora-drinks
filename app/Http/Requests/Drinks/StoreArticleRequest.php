<?php

namespace App\Http\Requests\Drinks;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! is_numeric($this->packaging_id)) {
            $this->merge(['packaging_id' => null]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('drinks_articles', 'code')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'category_id' => [
                'required',
                Rule::exists('drinks_categories', 'id')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'packaging_id' => [
                'nullable',
                Rule::exists('drinks_packagings', 'id')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'retail_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'packs_per_unit' => ['nullable', 'integer', 'min:1'],
            'discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rebate_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'is_consignable' => ['boolean'],
        ];
    }
}
