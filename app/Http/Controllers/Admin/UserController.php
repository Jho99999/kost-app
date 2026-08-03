<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $users = User::query()

            ->where('role', 'user')

            ->when($request->search, function ($q, $search) {

                $q->where(function ($query) use ($search) {

                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");

                });

            })

            ->when($request->booking == 'active', function ($q) {

                $q->has('activeBooking');

            })

            ->when($request->booking == 'inactive', function ($q) {

                $q->doesntHave('activeBooking');

            })

            ->when($request->ktp == 'uploaded', function ($q) {

                $q->whereNotNull('ktp_image');

            })

            ->when($request->ktp == 'empty', function ($q) {

                $q->whereNull('ktp_image');

            })

            ->with('activeBooking.room')

            ->latest()

            ->paginate(10)

            ->withQueryString();

        $summary = [

            'users' => User::where('role', 'user')->count(),

            'active_booking' => User::where('role', 'user')
                ->has('activeBooking')
                ->count(),

            'ktp_missing' => User::where('role', 'user')
                ->whereNull('ktp_image')
                ->count(),

        ];

        return view(
            'admin.users.index',
            compact('users', 'summary')
        );
    }
    public function show(User $user): View
    {
        $user->load([
            'activeBooking.room',
            'bookings.room',
        ]);

        $payments = $user->payments()
            ->latest()
            ->limit(5)
            ->get();

        $complaints = $user->complaints()
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.users.show', compact(
            'user',
            'payments',
            'complaints'
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
