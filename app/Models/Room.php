<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'price',
        'status',
        'floor',
        'capacity',
        'size_sqm',
        'facilities',
        'images',
    ];

    protected $casts = [
        'facilities' => 'array',
        'images'     => 'array',
        'price'      => 'decimal:2',
    ];

    /* ── Helpers ─────────────────────────────────── */

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /** Harga dalam format Rupiah, misal "Rp 800.000" */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    /** Gambar pertama, atau placeholder jika kosong */
    public function getPrimaryImageAttribute(): string
    {
        if (! empty($this->images)) {
            return asset('storage/' . $this->images[0]);
        }

        return asset('images/room-placeholder.jpg');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available'   => 'Tersedia',
            'occupied'    => 'Terisi',
            'maintenance' => 'Perbaikan',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available'   => 'badge-green',
            'occupied'    => 'badge-red',
            'maintenance' => 'badge-yellow',
            default       => 'badge-gray',
        };
    }

    /* ── Scopes ──────────────────────────────────── */

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    /* ── Relationships ───────────────────────────── */

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->whereIn('status', ['approved', 'active']);
    }
}
