<?php

namespace App\Http\Requests\Drinks;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProcurementRequest extends FormRequest
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
            'supplier_id' => [
                'required',
                Rule::exists('drinks_suppliers', 'id')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'observation' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.article_id' => ['required', Rule::exists('drinks_articles', 'id')],
            'lines.*.quantity' => ['required', 'integer', 'min:1'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
