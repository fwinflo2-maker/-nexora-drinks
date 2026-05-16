<?php

declare(strict_types=1);

namespace App\Http\Requests\FnB;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:free,occupied,reserved,closed'],
        ];
    }
}
