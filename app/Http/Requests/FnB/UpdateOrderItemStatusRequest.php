<?php

declare(strict_types=1);

namespace App\Http\Requests\FnB;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:sent,preparing,ready,served'],
        ];
    }
}
