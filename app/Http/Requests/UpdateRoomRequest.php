<?php

namespace App\Http\Requests;

class UpdateRoomRequest extends StoreRoomRequest
{
    // Mewarisi semua rules dan messages dari StoreRoomRequest.
    // Images bersifat opsional saat update (tidak wajib upload ulang).
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['images']   = ['nullable', 'array', 'max:6'];
        $rules['images.*'] = ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        return $rules;
    }
}
