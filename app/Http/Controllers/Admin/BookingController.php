<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingApproved;
use App\Mail\BookingRejected;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['user', 'room'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->search, fn ($q, $v) =>
                $q->where('booking_code', 'like', "%{$v}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$v}%"))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'user',
            'room',
            'approvedBy',
            'payments' => fn ($q) => $q->orderBy('due_date'),
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Proses approve atau reject.
     * Route: PUT /admin/bookings/{booking}
     * Body : action = approve|reject, notes = string|null
     */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        abort_if(
            $booking->status !== 'pending',
            403,
            'Pemesanan ini sudah diproses sebelumnya.'
        );

        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->action === 'approve') {
            $this->approve($booking);
            $flash = "Pemesanan {$booking->booking_code} disetujui. "
                   . "{$booking->duration_months} tagihan bulanan telah digenerate.";
        } else {
            $this->reject($booking, $request->input('notes'));
            $flash = "Pemesanan {$booking->booking_code} ditolak.";
        }

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', $flash);
    }

    // ── Private helpers ─────────────────────────────────────────────────

    /**
     * Setujui booking:
     * 1. Update status booking → approved
     * 2. Update status kamar → occupied
     * 3. Bulk generate semua record pembayaran bulanan
     * 4. Kirim email ke penyewa (non-blocking)
     *
     * Langkah 1–3 dibungkus DB::transaction agar atomik.
     */
    private function approve(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            // 1. Approve booking
            $booking->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
            ]);

            // 2. Tandai kamar sebagai terisi
            $booking->room->update(['status' => 'occupied']);

            // 3. Generate tagihan bulanan
            // Contoh: check_in 2024-02-01, durasi 3 bulan →
            //   Bulan 1 due: 2024-02-01
            //   Bulan 2 due: 2024-03-01
            //   Bulan 3 due: 2024-04-01
            $checkIn = Carbon::parse($booking->check_in_date);

            for ($i = 1; $i <= $booking->duration_months; $i++) {
                Payment::create([
                    'payment_code' => Payment::generateCode(),
                    'booking_id'   => $booking->id,
                    'user_id'      => $booking->user_id,
                    'amount'       => $booking->room->price,
                    'due_date'     => $checkIn->copy()->addMonths($i - 1),
                    'status'       => 'pending',
                    'month_number' => $i,
                    'month_period' => $checkIn->copy()->addMonths($i - 1)
                        ->locale('id')
                        ->isoFormat('MMMM YYYY'),
                ]);
            }
        });

        // 4. Email di luar transaction — gagal tidak rollback approval
        try {
            Mail::to($booking->user->email)
                ->send(new BookingApproved($booking->load('room', 'payments')));
        } catch (\Throwable) {}
    }

    /** Tolak booking: update status, simpan catatan, kirim email */
    private function reject(Booking $booking, ?string $notes): void
    {
        $booking->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'notes'       => $notes,
        ]);

        try {
            Mail::to($booking->user->email)
                ->send(new BookingRejected($booking->load('room')));
        } catch (\Throwable) {}
    }
}
