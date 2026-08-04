<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /* ── Login ───────────────────────────────────── */

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return $this->redirectByRole();
    }

    /* ── Register ────────────────────────────────── */

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Selamat datang, ' . $user->name . '!');
    }

    /* ── Logout ──────────────────────────────────── */

    public function logout(Request $request): RedirectResponse
        {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('home');
        }

        /* ── Lupa Password ──────────────────────────── */

        public function showForgotForm(): View
        {
            return view('auth.forgot-password');
        }

        public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Email tidak ditemukan dalam sistem.',
        ]);

        $user = User::where('email', $request->email)->first();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $url = route('password.reset', $token) .
            '?email=' . urlencode($user->email);

        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [

            'sender' => [
                'email' => env('MAIL_FROM_ADDRESS'),
                'name'  => env('MAIL_FROM_NAME'),
            ],

            'to' => [[
                'email' => $user->email,
                'name' => $user->name,
            ]],

            'subject' => 'Reset Password',

            'htmlContent' => view(
                'emails.reset-password',
                [
                    'user' => $user,
                    'url' => $url,
                ]
            )->render(),

        ]);

        

        return back()->with(
            'success',
            'Link reset password telah dikirim ke email Anda.'
        );
    }

    public function showResetForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'email.exists' => 'Email tidak ditemukan dalam sistem.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.')
            : back()->withErrors(['email' => 'Token reset tidak valid atau sudah kedaluwarsa.']);
    }

    /* ── Private ─────────────────────────────────── */

    private function redirectByRole(): RedirectResponse
    {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }
}
