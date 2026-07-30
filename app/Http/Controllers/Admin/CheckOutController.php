<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CheckOutController extends Controller
{
    /** Daftar penghuni aktif */
    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'room'])
            ->whereIn('status', ['approved', 'active'])
            ->when($request->search, fn ($q, $v) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$v}%"))
                  ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$v}%"))
            )
            ->orderBy(
                DB::raw('DATE_ADD(check_in_date, INTERVAL duration_months MONTH)')
            )
            ->paginate(15);

        return view('admin.checkouts.index', compact('bookings'));
    }

    /** Detail booking + tombol check-out/perpanjang */
    public function show(Booking $booking): View
    {
        abort_if(! in_array($booking->status, ['approved', 'active']), 404);

        $booking->load(['user', 'room', 'payments' => fn ($q) => $q->orderBy('due_date')]);

        return view('admin.checkouts.show', compact('booking'));
    }

    /**
     * Proses check-out:
     * 1. Booking → completed
     * 2. Kamar → available
     * 3. Hapus KTP penyewa
     * 4. Catat check_out_date real
     */
    public function process(Booking $booking): RedirectResponse
    {
        abort_if(! in_array($booking->status, ['approved', 'active']), 422, 'Booking sudah tidak aktif.');

        DB::transaction(function () use ($booking) {
            // 1. Update booking
            $booking->update([
                'status'         => 'completed',
                'check_out_date' => now()->toDateString(),
            ]);

            // 2. Kosongkan kamar
            $booking->room->update(['status' => 'available']);

            // 3. Hapus KTP penyewa
            $user = $booking->user;
            if ($user && $user->ktp_image) {
                Storage::disk('public')->delete($user->ktp_image);
                $user->update([
                    'ktp_image'       => null,
                    'ktp_uploaded_at' => null,
                ]);
            }
        });

        return redirect()
            ->route('admin.checkouts.index')
            ->with('success', "Check-out {$booking->user->name} dari {$booking->room->name} berhasil diproses.");
    }

    /**
     * Perpanjang kontrak:
     * 1. Tambah duration_months
     * 2. Generate tagihan baru untuk bulan tambahan
     * 3. Update check_out_date
     */
    public function extend(Request $request, Booking $booking): RedirectResponse
    {
        abort_if(! in_array($booking->status, ['approved', 'active']), 422);

        $request->validate([
            'additional_months' => ['required', 'integer', 'min:1', 'max:12'],
        ], [
            'additional_months.required' => 'Jumlah bulan perpanjangan wajib diisi.',
            'additional_months.min' => 'Minimal perpanjangan 1 bulan.',
            'additional_months.max' => 'Maksimal perpanjangan 12 bulan.',
        ]);

        $months = (int) $request->additional_months;

        DB::transaction(function () use ($booking, $months) {
            // 1. Cari bulan terakhir dari tagihan yang sudah ada
            $lastMonth = $booking->payments()->max('month_number') ?? 0;

            // 2. Generate tagihan baru untuk bulan tambahan
            for ($i = 1; $i <= $months; $i++) {
                $monthNumber = $lastMonth + $i;
                $dueDate = now()->addMonths($monthNumber - 1)->startOfMonth();

                Payment::create([
                    'payment_code' => Payment::generateCode(),
                    'booking_id'   => $booking->id,
                    'user_id'      => $booking->user_id,
                    'amount'       => $booking->room->price,
                    'due_date'     => $dueDate,
                    'status'       => 'pending',
                    'month_number' => $monthNumber,
                    'month_period' => $dueDate->locale('id')->isoFormat('MMMM YYYY'),
                ]);
            }

            // 3. Update durasi
            $booking->increment('duration_months', $months);

            // 4. Update check_out_date (hapus agar dihitung ulang otomatis dari accessor)
            $booking->update([
                'check_out_date' => null,
            ]);
        });

        return redirect()
            ->route('admin.checkouts.show', $booking)
            ->with('success', "Kontrak {$booking->user->name} berhasil diperpanjang {$months} bulan. {$months} tagihan baru telah digenerate.");
    }

    /** Riwayat penghuni suatu kamar */
    public function roomHistory(Room $room): View
    {
        $bookings = $room->bookings()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.checkouts.history', compact('room', 'bookings'));
    }
}
