<?php

namespace App\Http\Requests\Drinks;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
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
            'expense_type_id' => [
                'required',
                Rule::exists('drinks_expense_types', 'id')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'amount' => ['required', 'numeric', 'min:0'],
            'document_date' => ['required', 'date'],
            'label' => ['required', 'string', 'max:255'],
            'observation' => ['nullable', 'string'],
        ];
    }
}
