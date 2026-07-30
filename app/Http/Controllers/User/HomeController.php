<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Booking yang masih aktif (pending atau approved)
        $activeBooking = $user->bookings()
            ->whereIn('status', ['pending', 'approved'])
            ->with('room')
            ->latest()
            ->first();

        // Tagihan berikutnya yang belum lunas (pending atau overdue), urut jatuh tempo
        $nextPayment = $activeBooking
            ? Payment::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'overdue'])
                ->orderBy('due_date')
                ->first()
            : null;

        // Jumlah tagihan yang sudah lewat jatuh tempo (untuk banner peringatan)
        $overdueCount = Payment::where('user_id', $user->id)
            ->where('status', 'overdue')
            ->count();

        // Kamar yang tersedia untuk ditampilkan di beranda (maks 6)
        $availableRooms = Room::where('status', 'available')
            ->orderBy('price')
            ->limit(6)
            ->get();

        return view('user.home', compact(
            'activeBooking',
            'nextPayment',
            'overdueCount',
            'availableRooms',
        ));
    }
}
