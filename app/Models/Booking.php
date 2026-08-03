<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'room_id',
        'check_in_date',
        'duration_months',
        'monthly_price',
        'total_price',
        'status',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'check_in_date'   => 'date',
        'duration_months' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Admin yang memproses (approve/reject) booking ini */
    public function approvedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public static function generateCode(): string
    {
        do {
            $code = 'BKG-' . strtoupper(Str::random(8));
        } while (static::where('booking_code', $code)->exists());

        return $code;
    }

    /** Tanggal estimasi akhir kontrak sewa */
    public function getCheckOutDateAttribute($value): \Carbon\Carbon
    {
        return $value
            ? \Carbon\Carbon::parse($value)
            : $this->check_in_date->copy()->addMonths($this->duration_months)->subDay();
    }

    /** Total biaya sewa seluruh durasi */
    public function getTotalAmountAttribute(): float|int
    {
        return $this->room->price * $this->duration_months;
    }

    /** Label dan badge class status */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'expired'  => 'Kadaluarsa',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'badge badge-green',
            'pending'  => 'badge badge-yellow',
            'rejected' => 'badge badge-red',
            'expired'  => 'badge badge-gray',
            default    => 'badge badge-gray',
        };
    }
}
