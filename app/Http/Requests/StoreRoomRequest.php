<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'type'         => ['required', 'in:Standard,Deluxe,VIP'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'price'        => ['required', 'numeric', 'min:1', 'max:99999999'],
            'status'       => ['required', 'in:available,occupied,maintenance'],
            'floor'        => ['required', 'integer', 'min:1', 'max:99'],
            'capacity'     => ['required', 'integer', 'min:1', 'max:20'],
            'size_sqm'     => ['nullable', 'integer', 'min:1', 'max:500'],
            'facilities'   => ['nullable', 'array'],
            'facilities.*' => ['string', 'max:100'],
            'images'       => ['nullable', 'array', 'max:6'],
            'images.*'     => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama kamar wajib diisi.',
            'type.required'     => 'Tipe kamar wajib dipilih.',
            'type.in'           => 'Tipe kamar tidak valid.',
            'price.required'    => 'Harga sewa wajib diisi.',
            'price.numeric'     => 'Harga harus berupa angka.',
            'status.required'   => 'Status kamar wajib dipilih.',
            'floor.required'    => 'Nomor lantai wajib diisi.',
            'capacity.required' => 'Kapasitas wajib diisi.',
            'images.max'        => 'Maksimal 6 foto per kamar.',
            'images.*.image'    => 'File harus berupa gambar.',
            'images.*.mimes'    => 'Format: jpg, jpeg, png, atau webp.',
            'images.*.max'      => 'Ukuran tiap gambar maksimal 2MB.',
        ];
    }
}
