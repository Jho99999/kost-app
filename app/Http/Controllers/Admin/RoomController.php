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
        $data = $request->validated();

        $this->calculateArea($data);

        $data['facilities'] = array_values(
            array_filter($data['facilities'] ?? [])
        );
        
        $data['images'] = $this->uploadImages($request);

       
        $data['cover_image'] = min(
            (int) $request->input('cover_image', 0),
            max(count($data['images']) - 1, 0)
        );
        Room::create($data);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil ditambahkan.');
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
        $data = $request->validated();

        $data['facilities'] = array_values(
            array_filter($data['facilities'] ?? [])
        );

        $existing = $room->images ?? [];
        $uploaded = $this->uploadImages($request);

        $data['images'] = array_merge($existing, $uploaded);
        $this->calculateArea($data);
        $data['cover_image'] = min(
            (int) $request->input('cover_image', $room->cover_image ?? 0),
            max(count($data['images']) - 1, 0)
        );
        
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

    private function calculateArea(array &$data): void
    {
        $data['size_sqm'] = null;

        if (!empty($data['length_m']) &&
            !empty($data['width_m'])) {

            $data['size_sqm'] = round(
                $data['length_m'] * $data['width_m'],
                2
            );
        }
    }
    /**
     * Hapus satu foto dari kamar.
     * Route: DELETE /admin/rooms/{room}/images  (body: image=rooms/xxx.jpg)
     */
    public function destroyImage(Request $request, Room $room): RedirectResponse
    {
        
        $target = $request->input('image');
        if (!in_array($target, $room->images ?? [])) {
            abort(404);
        }
        $images = collect($room->images ?? [])
            ->reject(fn ($img) => $img === $target)
            ->values()
            ->all();

        Storage::disk('public')->delete($target);
        $cover = $room->cover_image ?? 0;

        $deletedIndex = array_search($target, $room->images ?? []);

        if ($deletedIndex !== false) {

            if ($cover == $deletedIndex) {
                $cover = 0;
            } elseif ($cover > $deletedIndex) {
                $cover--;
            }
        }

        $room->update([
            'images' => $images ?: null,
            'cover_image' => $images ? $cover : 0,
        ]);

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    // ── Private ────────────────────────────────────────────────────────────

    /** Upload semua file pada field images[], return array path relatif */
    private function uploadImages(Request $request): array
    {
        $paths = [];

        if (!$request->hasFile('images')) {
            return [];
        }

        foreach ($request->file('images') as $file) {
            if ($file->isValid()) {
                $paths[] = $file->store('rooms', 'public');
            }
        }

        return $paths;
    }
}
