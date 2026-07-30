<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /** Daftar semua aduan */
    public function index(Request $request): View
    {
        $complaints = Complaint::with(['user', 'room'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->search, fn ($q, $v) =>
                $q->where('title', 'like', "%{$v}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$v}%"))
                  ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$v}%"))
            )
            ->latest()
            ->paginate(15);

        $summary = [
            'pending'  => Complaint::where('status', 'pending')->count(),
            'diproses' => Complaint::where('status', 'diproses')->count(),
            'selesai'  => Complaint::where('status', 'selesai')->count(),
        ];

        return view('admin.complaints.index', compact('complaints', 'summary'));
    }

    /** Detail aduan */
    public function show(Complaint $complaint): View
    {
        $complaint->load(['user', 'room']);

        return view('admin.complaints.show', compact('complaint'));
    }

    /** Update status & catatan admin */
    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $request->validate([
            'status'      => ['required', 'in:pending,diproses,selesai,ditolak'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $data = [
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->status === 'selesai' || $request->status === 'ditolak') {
            $data['resolved_at'] = now();
        }

        $complaint->update($data);

        $label = match ($request->status) {
            'diproses' => 'ditandai sedang diproses',
            'selesai'  => 'ditandai selesai',
            'ditolak'  => 'ditolak',
            default    => 'diperbarui',
        };

        return redirect()
            ->route('admin.complaints.show', $complaint)
            ->with('success', "Aduan {$complaint->title} {$label}.");
    }
}
