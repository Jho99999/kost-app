<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_code',
        'booking_id',
        'user_id',
        'amount',
        'due_date',
        'payment_method',
        'payment_method_id',
        'month_period',
        'status',
        'month_number',
        'paid_at',
        'proof_image',
        'verified_by',
        'notes',
        'verified_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'due_date'     => 'date',
        'paid_at'      => 'date',
        'verified_at'  => 'datetime',
        'month_number' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────

    public function booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Admin yang memverifikasi bukti pembayaran */
    public function verifiedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /** Pembayaran yang sudah ada bukti tapi belum diverifikasi */
    public function scopeAwaitingVerification($query)
    {
        return $query->whereNotNull('proof_image')
                     ->whereIn('status', ['pending', 'overdue']);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public static function generateCode(): string
    {
        do {
            $code = 'PAY-' . strtoupper(Str::random(8));
        } while (static::where('payment_code', $code)->exists());

        return $code;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'Lunas',
            'overdue' => 'Jatuh Tempo',
            default   => 'Belum Bayar',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'badge badge-green',
            'overdue' => 'badge badge-red',
            default   => 'badge badge-yellow',
        };
    }
}
