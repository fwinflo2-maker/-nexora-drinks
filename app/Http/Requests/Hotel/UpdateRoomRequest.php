<?php

declare(strict_types=1);

namespace App\Http\Requests\Hotel;

use App\Enums\Hotel\RoomStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'room_type_id' => ['required', 'integer', 'exists:hotel_room_types,id'],
            'number' => [
                'required', 'string', 'max:20',
                Rule::unique('hotel_rooms', 'number')
                    ->where('team_id', $this->user()->currentTeam->id)
                    ->ignore($this->route('room')),
            ],
            'floor' => ['nullable', 'integer'],
            'status' => ['required', Rule::enum(RoomStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
