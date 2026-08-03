<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [

            'name'           => ['required','string','max:100'],
            'room_number'    => ['required','string','max:20','unique:rooms,room_number'],

            'type'           => ['required','in:Standard,Deluxe,VIP'],

            'description'    => ['nullable','string','max:2000'],

            'price'          => ['required','numeric','min:1'],
            'deposit'        => ['nullable','numeric','min:0'],

            'status'         => ['required','in:available,occupied,maintenance'],

            'floor'          => ['required','integer','min:1'],
            'capacity'       => ['required','integer','min:1'],

            'length_m'       => ['nullable','numeric','min:1'],
            'width_m'        => ['nullable','numeric','min:1'],
            'size_sqm'       => ['nullable','numeric','min:1'],

            'bathroom_type'  => ['required','in:inside,outside,shared'],

            'furnished'      => ['required','in:empty,semi,full'],

            'electricity_type' => ['required','in:included,token,meter'],

            'water_type'       => ['required','in:included,meter,well'],

            'facilities'     => ['nullable','array'],
            'facilities.*'   => ['string','max:100'],

            'images'         => ['nullable','array','max:6'],
            'images.*'       => ['image','mimes:jpg,jpeg,png,webp','max:2048'],
            'cover_image' => [
                'nullable',
                'integer',
                'min:0',
                'max:5',
            ],
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
            
            'room_number.required' => 'Nomor kamar wajib diisi.',
            'room_number.unique'   => 'Nomor kamar sudah digunakan.',

            'deposit.numeric'      => 'Deposit harus berupa angka.',

            'length_m.numeric'     => 'Panjang kamar harus berupa angka.',
            'width_m.numeric'      => 'Lebar kamar harus berupa angka.',
            'bathroom_type.in'     => 'Jenis kamar mandi tidak valid.',
            'furnished.in'         => 'Pilihan furnitur tidak valid.',

            'electricity_type.in'  => 'Jenis listrik tidak valid.',

            'water_type.in'        => 'Jenis air tidak valid.',
        ];
    }
}
