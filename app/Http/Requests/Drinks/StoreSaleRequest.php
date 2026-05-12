<?php

namespace App\Http\Requests\Drinks;

use App\Enums\Drinks\SaleKind;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
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
            'kind' => ['required', Rule::enum(SaleKind::class)],
            'document_date' => ['required', 'date'],
            'client_id' => [
                'required',
                Rule::exists('drinks_clients', 'id')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'observation' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.article_id' => ['required', Rule::exists('drinks_articles', 'id')],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.discount_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'validate' => ['nullable', 'boolean'],
        ];
    }
}
