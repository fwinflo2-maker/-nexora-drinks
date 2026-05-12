<?php

namespace App\Http\Requests\Drinks;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePricingTierRequest extends FormRequest
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
            'article_id' => [
                'required',
                Rule::exists('drinks_articles', 'id')
                    ->where('team_id', $this->user()->currentTeam->id),
            ],
            'label' => ['required', 'string', 'max:100'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
