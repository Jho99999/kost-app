<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('user.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $rules = [
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['required', 'string', 'max:20'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'gender'     => ['nullable', 'in:L,P'],
            'religion'   => ['nullable', 'string', 'max:50'],
            'id_card' => ['nullable', 'string', 'max:30'], // Nomor KTP (angka)
        ];

        // Password: isi optional, tapi kalau diisi wajib valid
        if ($request->filled('password')) {
            $rules['password']              = ['required', 'confirmed', Password::min(8)];
            $rules['current_password']      = ['required', 'current_password'];
        }

        $validated = $request->validate($rules, [
            'current_password.current_password' => 'Password saat ini tidak cocok.',
        ]);

        // Update profil
        $user->name    = $validated['name'];
        $user->phone   = $validated['phone'];
        $user->occupation = $validated['occupation'] ?? $user->occupation;
        $user->gender     = $validated['gender'] ?? $user->gender;
        $user->religion   = $validated['religion'] ?? $user->religion;
        $user->id_card = $validated['id_card'] ?? $user->id_card;

        // Ganti password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /** Upload / ganti foto KTP */
    public function uploadKtp(Request $request): RedirectResponse
    {
        $request->validate([
            'ktp_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // max 2MB
        ]);

        $user = auth()->user();

        // Hapus KTP lama
        if ($user->ktp_image) {
            Storage::disk('public')->delete($user->ktp_image);
        }

        // Simpan yang baru
        $path = $request->file('ktp_image')->store('ktp', 'public');
        $user->update([
            'ktp_image' => $path,
            'ktp_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Foto KTP berhasil diunggah.');
    }

    /** Hapus foto KTP */
    public function deleteKtp(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->ktp_image) {
            Storage::disk('public')->delete($user->ktp_image);
        }

        $user->update([
            'ktp_image' => null,
            'ktp_uploaded_at' => null,
        ]);

        return back()->with('success', 'Foto KTP berhasil dihapus.');
    }
}
