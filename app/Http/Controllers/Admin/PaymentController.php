<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PaymentVerified;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['user', 'booking.room'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when(
                $request->proof === 'uploaded',
                fn ($q) => $q->whereNotNull('proof_image')
            )
            ->when(
                $request->proof === 'empty',
                fn ($q) => $q->whereNull('proof_image')
            )
            ->when($request->search, fn ($q, $v) =>
                $q->where('payment_code', 'like', "%{$v}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$v}%"))
                  ->orWhereHas('booking.room', fn ($r) => $r->where('name', 'like', "%{$v}%"))
            )
            ->orderByRaw("FIELD(status, 'overdue', 'pending', 'paid')")
            ->orderBy('due_date')
            ->paginate(15)
            ->withQueryString();

        // Ringkasan untuk header
        $summary = [
            'pending'  => Payment::where('status', 'pending')->count(),
            'overdue'  => Payment::where('status', 'overdue')->count(),
            'awaiting' => Payment::whereNotNull('proof_image')->where('status', '!=', 'paid')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'summary'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['user', 'booking.room', 'verifiedBy']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Verifikasi bukti pembayaran: approve → status paid, reject → hapus proof.
     * Route: PUT /admin/payments/{payment}/verify
     */
    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        if (! $payment->proof_image) {
            return redirect()->back()->with('error', 'Belum ada bukti pembayaran yang diunggah.');
        }
        if ($payment->status === 'paid') {
            return redirect()->back()->with('error', 'Tagihan ini sudah diverifikasi sebelumnya.');
        }

        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'notes'  => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->action === 'approve') {
            $this->approve($payment);
            $flash = "Pembayaran {$payment->payment_code} berhasil diverifikasi sebagai lunas.";
        } else {
            $this->reject($payment, $request->input('notes'));
            $flash = "Bukti pembayaran {$payment->payment_code} ditolak. Penyewa diminta mengunggah ulang.";
        }

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', $flash);
    }

    // ── Private ─────────────────────────────────────────────────────────

    private function approve(Payment $payment): void
    {
        $payment->update([
            'status'      => 'paid',
            'paid_at'     => now()->toDateString(),
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'notes'       => null,
        ]);

        try {
            Mail::to($payment->user->email)
                ->send(new PaymentVerified($payment->load('booking.room')));
        } catch (\Throwable) {}
    }

    private function reject(Payment $payment, ?string $notes): void
    {
        // Hapus bukti dari storage — user harus upload ulang
        Storage::disk('public')->delete($payment->proof_image);

        $payment->update([
            'proof_image' => null,
            'notes'       => $notes ?? 'Bukti pembayaran tidak valid. Silakan unggah ulang.',
        ]);
    }
}
