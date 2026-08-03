<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        $methods = PaymentMethod::orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10);

        return view(
            'admin.payment-methods.index',
            compact('methods')
        );
    }

    public function create(): View
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {

            $data['image'] = $request
                ->file('image')
                ->store('payment-methods', 'public');
        }

        PaymentMethod::create($data);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with(
                'success',
                'Metode pembayaran berhasil ditambahkan.'
            );
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view(
            'admin.payment-methods.edit',
            compact('paymentMethod')
        );
    }

    public function update(
        Request $request,
        PaymentMethod $paymentMethod
    ): RedirectResponse {

        $data = $this->validateData($request);

        if ($request->hasFile('image')) {

            if ($paymentMethod->image) {

                Storage::disk('public')
                    ->delete($paymentMethod->image);
            }

            $data['image'] = $request
                ->file('image')
                ->store('payment-methods', 'public');
        }

        $paymentMethod->update($data);

        return redirect()
            ->route('admin.payment-methods.index')
            ->with(
                'success',
                'Metode pembayaran berhasil diperbarui.'
            );
    }

    public function destroy(
        PaymentMethod $paymentMethod
    ): RedirectResponse {

        if ($paymentMethod->image) {

            Storage::disk('public')
                ->delete($paymentMethod->image);
        }

        $paymentMethod->delete();

        return back()->with(
            'success',
            'Metode pembayaran berhasil dihapus.'
        );
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([

            'name' => 'required|string|max:100',

            'type' => 'required|in:bank,qris,ewallet',

            'account_number' => 'nullable|string|max:100',

            'account_name' => 'nullable|string|max:100',

            'image' => 'nullable|image|max:2048',

            'notes' => 'nullable|string',

            'sort_order' => 'nullable|integer|min:0',

            'is_active' => 'nullable|boolean',

        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    public function toggle(PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update([
            'is_active' => ! $paymentMethod->is_active,
        ]);

        return back()->with(
            'success',
            'Status metode pembayaran berhasil diperbarui.'
        );
    }

}