<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoomController extends Controller
{
    /** Daftar kamar yang tersedia / bisa dilihat penghuni */
    public function index(Request $request): View
    {
        $maxPrice = $request->filled('max_price')
            ? filter_var($request->max_price, FILTER_VALIDATE_FLOAT)
            : null;

        $rooms = Room::query()
            ->where('status', '!=', 'maintenance')       // sembunyikan kamar perbaikan
            ->when($request->type, fn ($q, $v) => $q->where('type', $v))
            ->when($maxPrice !== false && $maxPrice !== null, fn ($q) => $q->where('price', '<=', $maxPrice))
            ->when(
                $request->sort === 'price_asc',
                fn ($q) => $q->orderBy('price'),
                fn ($q) => $request->sort === 'price_desc'
                    ? $q->orderByDesc('price')
                    : $q->orderBy('floor')->orderBy('name')
            )
            ->paginate(9)
            ->withQueryString();

        return view('user.rooms.index', compact('rooms'));
    }

    /** Detail satu kamar */
    public function show(Room $room): View
    {
        // Kamar sedang perbaikan tidak bisa dilihat penghuni
        abort_if($room->status === 'maintenance', 404, 'Kamar tidak tersedia.');

        $activeBooking = null;

        if (Auth::check()) {
            $user = Auth::user();

            if ($user instanceof User) {
                $activeBooking = $user->bookings()
                    ->whereIn('status', ['pending', 'approved'])
                    ->first();
            }
        }

        return view('user.rooms.show', compact(
            'room',
            'activeBooking'
        ));
    }
}
