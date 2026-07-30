<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RoomController extends Controller
{
    /** Daftar kamar dengan filter & pagination */
    public function index(Request $request): View
    {
        $rooms = Room::query()
            ->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->type,   fn ($q, $v) => $q->where('type', $v))
            ->orderBy('floor')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        return view('admin.rooms.create');
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $data                = $request->validated();
        $data['facilities']  = $request->input('facilities', []);
        $data['images']      = $this->uploadImages($request);

        $room = Room::create($data);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', "Kamar {$room->name} berhasil ditambahkan.");
    }

    public function show(Room $room): View
    {
        $room->load(['bookings' => fn ($q) => $q->latest()->limit(5)->with('user')]);

        return view('admin.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $data               = $request->validated();
        $data['facilities'] = $request->input('facilities', []);

        // Foto baru ditambahkan ke daftar yang sudah ada (tidak mengganti)
        $existing    = $room->images ?? [];
        $uploaded    = $this->uploadImages($request);
        $data['images'] = array_merge($existing, $uploaded) ?: null;

        $room->update($data);

        return redirect()
            ->route('admin.rooms.show', $room)
            ->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        foreach ($room->images ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $name = $room->name;
        $room->delete();

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', "Kamar {$name} berhasil dihapus.");
    }

    /**
     * Hapus satu foto dari kamar.
     * Route: DELETE /admin/rooms/{room}/images  (body: image=rooms/xxx.jpg)
     */
    public function destroyImage(Request $request, Room $room): RedirectResponse
    {
        $target = $request->input('image');

        $images = collect($room->images ?? [])
            ->reject(fn ($img) => $img === $target)
            ->values()
            ->all();

        Storage::disk('public')->delete($target);
        $room->update(['images' => $images ?: null]);

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    // ── Private ────────────────────────────────────────────────────────────

    /** Upload semua file pada field images[], return array path relatif */
    private function uploadImages(Request $request): array
    {
        $paths = [];
        foreach ($request->file('images', []) as $file) {
            // Path: storage/app/public/rooms/xxx.jpg
            // URL:  /storage/rooms/xxx.jpg  (setelah php artisan storage:link)
            $paths[] = $file->store('rooms', 'public');
        }
        return $paths;
    }
}
