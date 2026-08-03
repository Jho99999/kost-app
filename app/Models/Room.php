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
        'room_number',

        'type',

        'description',

        'price',
        'deposit',

        'status',

        'floor',

        'capacity',

        'length_m',
        'width_m',
        'size_sqm',

        'bathroom_type',
        'furnished',
        'electricity_type',
        'water_type',

        'facilities',
        'images',

        'cover_image',
    ];

    protected $casts = [
        'facilities' => 'array',
        'images'     => 'array',

        'price'      => 'decimal:2',
        'deposit'    => 'decimal:2',

        'length_m'   => 'decimal:2',
        'width_m'    => 'decimal:2',

        'size_sqm'   => 'decimal:2',

        'furnished'  => 'string',
    ];

    /* ── Helpers ─────────────────────────────────── */

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /** Harga dalam format Rupiah, misal "Rp 800.000" */
    public function getFormattedPriceAttribute(): string
    {
        return $this->rupiah((float) $this->price);
    }

    public function getFormattedDepositAttribute(): string
    {
        return $this->rupiah((float) $this->deposit);
    }

    public function getAreaAttribute(): ?float
    {
        return $this->size_sqm;
    }

    public function getDimensionAttribute(): string
    {
        if (!$this->length_m || !$this->width_m) {
            return '-';
        }

        return "{$this->length_m} × {$this->width_m} m";
    }

    private function rupiah(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
    
    public function getBathroomLabelAttribute(): string
    {
        return match ($this->bathroom_type) {
            'inside' => 'Kamar Mandi Dalam',
            'outside' => 'Kamar Mandi Luar',
            'shared' => 'Kamar Mandi Bersama',
            default => '-',
        };
    }

    public function getFurnishedLabelAttribute(): string
    {
        return match ($this->furnished) {
            'empty' => 'Kosong',
            'semi' => 'Semi Furnished',
            'full' => 'Full Furnished',
            default => '-',
        };
    }

    public function getElectricityLabelAttribute(): string
    {
        return match ($this->electricity_type) {
            'included' => 'Termasuk',
            'token' => 'Token',
            'usage', 'meter' => 'Sesuai Pemakaian',
            default => '-',
        };
    }


    public function getWaterLabelAttribute(): string
    {
        return match ($this->water_type) {
            'included' => 'Termasuk',
            'meter' => 'Meteran',
            'well' => 'Sumur',
            default => '-',
        };
    }

    /** Gambar pertama, atau placeholder jika kosong */
    public function getPrimaryImageAttribute(): string
    {
        if (! empty($this->images)) {
            $index = $this->cover_image ?? 0;

            if (isset($this->images[$index])) {
                return asset('storage/' . $this->images[$index]);
            }

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

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available'   => 'badge badge-green',
            'occupied'    => 'badge badge-blue',
            'maintenance' => 'badge badge-yellow',
            default       => 'badge badge-gray',
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'Standard' => 'badge badge-gray',
            'Deluxe'   => 'badge badge-blue',
            'VIP'      => 'badge badge-purple',
            default    => 'badge badge-gray',
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
    public function getImageCountAttribute(): int
    {
        return count($this->images ?? []);
    }
}
