<?php

namespace App\Http\Requests\Drinks;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCashDepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'amount_cash' => ['required', 'numeric', 'min:0'],
            'amount_cheque' => ['required', 'numeric', 'min:0'],
            'amount_other' => ['required', 'numeric', 'min:0'],
            'document_date' => ['required', 'date'],
            'observation' => ['nullable', 'string'],
        ];
    }
}
