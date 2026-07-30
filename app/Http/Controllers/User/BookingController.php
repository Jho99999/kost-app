<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Mail\BookingSubmitted;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    /** Form pengajuan pemesanan */
    public function create(Room $room): View
    {
        abort_if($room->status !== 'available', 404, 'Kamar tidak tersedia untuk dipesan.');

        $user = auth()->user();

        // Booking aktif milik user (jika ada, form ditampilkan tapi submit diblokir via validasi)
        $activeBooking = $user->bookings()
            ->whereIn('status', ['pending', 'approved'])
            ->with('room')
            ->first();

        $ktpMissing = is_null($user->ktp_image);

        return view('user.bookings.create', compact('room', 'activeBooking', 'ktpMissing'));
    }

    /** Simpan pengajuan pemesanan */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $room = Room::findOrFail($request->room_id);

        $booking = Booking::create([
            'booking_code'    => Booking::generateCode(),
            'user_id'         => auth()->id(),
            'room_id'         => $room->id,
            'check_in_date'   => $request->check_in_date,
            'duration_months' => $request->duration_months,
            'monthly_price'   => $room->price,
            'total_price'     => $room->price * $request->duration_months,
            'status'          => 'pending',
        ]);

        try {
            $admin = User::where('role', 'admin')->first();

            if ($admin) {
                Mail::to($admin->email)->send(
                    new BookingSubmitted($booking->load('user', 'room'))
                );
            }
        } catch (\Throwable $e) {
            // Email gagal, proses booking tetap lanjut
        }

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Pemesanan berhasil diajukan! Admin akan memproses dalam 1×24 jam.');
    }

    /** Daftar booking milik user yang sedang login */
    public function index(): View
    {
        $bookings = auth()->user()->bookings()
            ->with('room')
            ->latest()
            ->paginate(10);

        return view('user.bookings.index', compact('bookings'));
    }

    /** Detail satu booking (hanya boleh dilihat pemiliknya) */
    public function show(Booking $booking): View
    {
        abort_if($booking->user_id !== auth()->id(), 403, 'Anda tidak berhak melihat pemesanan ini.');

        $booking->load([
            'room',
            'payments' => fn ($q) => $q->orderBy('due_date'),
        ]);

        return view('user.bookings.show', compact('booking'));
    }
}
