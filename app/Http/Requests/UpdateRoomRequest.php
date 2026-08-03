<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateRoomRequest extends StoreRoomRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['room_number'] = [
            'required',
            'string',
            'max:20',
            Rule::unique('rooms', 'room_number')
                ->ignore($this->route('room')->id),
        ];

        $rules['cover_image'] = [
            'nullable',
            'integer',
            'min:0',
        ];

        return $rules;
    }
}