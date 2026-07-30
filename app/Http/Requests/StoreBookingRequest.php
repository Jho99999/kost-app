<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'room_id'         => ['required', 'exists:rooms,id'],
            'check_in_date'   => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required'         => 'Kamar wajib dipilih.',
            'room_id.exists'           => 'Kamar tidak ditemukan.',
            'check_in_date.required'   => 'Tanggal masuk wajib diisi.',
            'check_in_date.after_or_equal' => 'Tanggal masuk tidak boleh sebelum hari ini.',
            'duration_months.required' => 'Durasi sewa wajib dipilih.',
            'duration_months.min'      => 'Durasi minimal 1 bulan.',
            'duration_months.max'      => 'Durasi maksimal 12 bulan.',
        ];
    }

    /** Validasi bisnis yang tidak bisa dihandle rules standar */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $user = auth()->user();

            // Cek KTP
            if (! $user->ktp_image) {
                $v->errors()->add('general',
                    'Anda belum melengkapi data KTP. Silakan <a href="' . route('profile.edit') . '" class="underline">upload KTP di profil</a> terlebih dahulu sebelum melakukan pemesanan.');
            }

            $room = Room::find($this->room_id);
            if ($room && $room->status !== 'available') {
                $v->errors()->add('room_id', 'Kamar sudah tidak tersedia untuk dipesan.');
            }

            $hasActive = $user->bookings()
                ->whereIn('status', ['pending', 'approved'])
                ->exists();
            if ($hasActive) {
                $v->errors()->add('general',
                    'Anda masih memiliki pemesanan aktif. Selesaikan atau tunggu prosesnya sebelum mengajukan pemesanan baru.');
            }
        });
    }
}
