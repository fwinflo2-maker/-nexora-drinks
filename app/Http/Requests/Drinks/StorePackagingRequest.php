<?php

namespace App\Http\Requests\Drinks;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackagingRequest extends FormRequest
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
            'code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('drinks_packagings', 'code')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'deposit_price' => ['required', 'numeric', 'min:0'],
            'packs_per_unit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'is_returnable' => ['boolean'],
        ];
    }
}
