<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /** Semua tagihan milik user, diurutkan berdasarkan jatuh tempo */
    public function index(): View
    {
        $payments = Payment::query()
            ->where('user_id', auth()->id())
            ->with(['booking.room'])
            ->orderByRaw("FIELD(status, 'overdue', 'pending', 'paid')")  // prioritaskan overdue
            ->orderBy('due_date')
            ->paginate(12);

        return view('user.payments.index', compact('payments'));
    }

    /** Detail satu tagihan + form upload bukti */
    public function show(Payment $payment): View
    {
        abort_if($payment->user_id !== auth()->id(), 403, 'Akses ditolak.');

        $payment->load(['booking.room', 'verifiedBy', 'paymentMethod']);
        $paymentMethods = PaymentMethod::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('user.payments.show', compact('payment', 'paymentMethods'));
    }

    /**
     * Upload atau ganti bukti pembayaran.
     * Proof yang sudah ada dihapus dari storage sebelum diganti.
     */
    public function upload(UploadPaymentProofRequest $request, Payment $payment): RedirectResponse
    {
        abort_if($payment->user_id !== auth()->id(), 403, 'Akses ditolak.');
        abort_if($payment->status === 'paid', 422, 'Tagihan ini sudah lunas, tidak perlu upload ulang.');

        // Hapus proof lama jika ada
        if ($payment->proof_image) {
            Storage::disk('public')->delete($payment->proof_image);
        }

        $path = $request->file('proof_image')->store('payments/proofs', 'public');

        $payment->update([
            'proof_image' => $path,
            'notes'       => null, // Reset catatan penolakan sebelumnya
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    /** Simpan metode pembayaran aktif yang dipilih user. */
    public function selectMethod(Request $request, Payment $payment): RedirectResponse
    {
        abort_if($payment->user_id !== auth()->id(), 403, 'Akses ditolak.');
        abort_if($payment->status === 'paid', 422, 'Tagihan sudah lunas.');

        $request->validate([
            'payment_method_id' => [
                'required',
                Rule::exists('payment_settings', 'id')
                    ->where('is_active', true),
            ],
        ]);

        $method = PaymentMethod::findOrFail($request->integer('payment_method_id'));

        $payment->update([
            'payment_method_id' => $method->id,
            'payment_method' => $method->type === 'qris' ? 'qris' : 'transfer',
        ]);

        return back()->with('success', 'Metode pembayaran dipilih. Silakan lanjutkan pembayaran sesuai instruksi.');
    }
}
