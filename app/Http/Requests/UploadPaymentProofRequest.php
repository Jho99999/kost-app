<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120', // 5 MB — bukti transfer bisa berupa scan PDF
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_image.required' => 'File bukti pembayaran wajib diunggah.',
            'proof_image.image'    => 'File harus berupa gambar atau PDF.',
            'proof_image.mimes'    => 'Format: jpg, jpeg, png, webp, atau pdf.',
            'proof_image.max'      => 'Ukuran file maksimal 5 MB.',
        ];
    }
}
