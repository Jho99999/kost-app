<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Statistik ringkas ───────────────────────────────
        $stats = [
            'available_rooms'  => Room::where('status', 'available')->count(),
            'total_rooms'      => Room::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'active_bookings'  => Booking::whereIn('status', ['approved', 'active'])->count(),
            'awaiting_verify'  => Payment::whereNotNull('proof_image')
                                         ->whereIn('status', ['pending', 'overdue'])
                                         ->count(),
        ];

        // ── Omset bulan ini ─────────────────────────────────
        $revenueThisMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        // ── Occupancy rate ──────────────────────────────────
        $totalRooms   = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // ── Tagihan overdue bulan ini ───────────────────────
        $overdueCount = Payment::where('status', 'overdue')->count();

        // ── Rata-rata lama sewa ─────────────────────────────
        $avgDuration = Booking::whereIn('status', ['approved', 'active', 'completed'])
            ->avg('duration_months');

        // ── Kontrak akan habis (30 hari ke depan) ───────────
        $expiringBookings = Booking::whereIn('status', ['approved', 'active'])
            ->where(DB::raw('DATE_ADD(check_in_date, INTERVAL duration_months MONTH)'),
                '<=', now()->addDays(30)->toDateString())
            ->where(DB::raw('DATE_ADD(check_in_date, INTERVAL duration_months MONTH)'),
                '>', now()->toDateString())
            ->with(['user', 'room'])
            ->orderBy(DB::raw('DATE_ADD(check_in_date, INTERVAL duration_months MONTH)'))
            ->limit(5)
            ->get();

        // ── Data grafik pemasukan 6 bulan ───────────────────
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = (int) $date->format('m');
            $year  = (int) $date->format('Y');

            $total = Payment::where('status', 'paid')
                ->whereMonth('paid_at', $month)
                ->whereYear('paid_at', $year)
                ->sum('amount');

            $revenueChart[] = [
                'label' => $date->isoFormat('MMM'),
                'total' => (float) $total,
            ];
        }

        // ── Data grafik penghuni baru 6 bulan ──────────────
        $tenantChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = (int) $date->format('m');
            $year  = (int) $date->format('Y');

            $total = Booking::where('status', 'approved')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();

            $tenantChart[] = [
                'label' => $date->isoFormat('MMM'),
                'total' => $total,
            ];
        }

        // 5 booking terbaru
        $recentBookings = Booking::with(['user', 'room'])
            ->latest()
            ->limit(5)
            ->get();

        // 5 tagihan menunggu verifikasi
        $pendingPayments = Payment::with(['user', 'booking.room'])
            ->whereNotNull('proof_image')
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'revenueThisMonth',
            'occupancyRate',
            'overdueCount',
            'avgDuration',
            'expiringBookings',
            'revenueChart',
            'tenantChart',
            'recentBookings',
            'pendingPayments',
        ));
    }
}
