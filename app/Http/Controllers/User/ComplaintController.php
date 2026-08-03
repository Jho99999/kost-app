<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /** Daftar aduan milik user */
    public function index(): View
    {
        $complaints = auth()->user()->complaints()
            ->with('room')
            ->latest()
            ->paginate(10);

        return view('user.complaints.index', compact('complaints'));
    }

    /** Form aduan baru */
    public function create(): View
    {
        $roomIds = auth()->user()->bookings()
            ->whereIn('status', ['approved', 'active'])
            ->pluck('room_id');

        $rooms = Room::whereIn('id', $roomIds)->get();

        return view('user.complaints.create', compact('rooms'));
    }

    /** Simpan aduan baru */
    public function store(Request $request): RedirectResponse
    {
        $owns = auth()->user()->bookings()
            ->whereIn('status', ['approved', 'active'])
            ->where('room_id', $validated['room_id'])
            ->exists();

        abort_unless($owns, 403, 'Anda tidak terdaftar sebagai penghuni kamar ini.');
        $validated = $request->validate([
            'room_id'     => ['required', 'exists:rooms,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'room_id.required'     => 'Kamar wajib dipilih.',
            'title.required'       => 'Judul aduan wajib diisi.',
            'description.required' => 'Deskripsi aduan wajib diisi.',
            'image.max'            => 'Foto maksimal 2MB.',
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('complaints', 'public');
        }

        Complaint::create($validated);

        return redirect()
            ->route('complaints.index')
            ->with('success', 'Aduan berhasil dikirim. Admin akan segera memproses.');
    }

    /** Detail aduan */
    public function show(Complaint $complaint): View
    {
        abort_if($complaint->user_id !== auth()->id(), 403);

        return view('user.complaints.show', compact('complaint'));
    }
}
