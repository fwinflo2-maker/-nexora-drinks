<?php

namespace App\Http\Requests\Drinks;

use App\Enums\Drinks\PaymentMode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
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
            'client_id' => [
                'required',
                Rule::exists('drinks_clients', 'id')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'document_date' => ['required', 'date'],
            'mode' => ['required', Rule::enum(PaymentMode::class)],
            'sale_id' => ['nullable', Rule::exists('drinks_sales', 'id')],
            'description' => ['nullable', 'string'],
        ];
    }
}
