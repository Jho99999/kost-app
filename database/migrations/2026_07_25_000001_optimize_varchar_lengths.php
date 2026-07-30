<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optimasi panjang kolom VARCHAR yang masih default 255.
     *
     * Prinsip: sesuaikan dengan kebutuhan riil + buffer aman.
     * - Nama orang Indonesia rata-rata 3-40 karakter
     * - No HP Indonesia maksimal 15 digit
     * - Path file storage biasanya 30-100 karakter
     * - Kode booking/payment maksimal 20 karakter
     */
    public function up(): void
    {
        // ─── USERS ──────────────────────────────────────────────────
        $this->modCol('users', 'name',          100);  // nama, NOT NULL
        $this->modCol('users', 'phone',          20,  true);  // nullable
        $this->modCol('users', 'occupation',     50,  true);
        $this->modCol('users', 'religion',       25,  true);
        $this->modCol('users', 'id_card',       100,  true);
        $this->modCol('users', 'ktp_image',     100,  true);
        $this->modCol('users', 'avatar',        150,  true);
        // email & password tetap 255 (email bisa panjang, hash algo bervariasi)

        // ─── ROOMS ──────────────────────────────────────────────────
        $this->modCol('rooms', 'name',           50);              // NOT NULL
        $this->modCol('rooms', 'type',           30,  false, "'Standard'"); // NOT NULL DEFAULT 'Standard'

        // ─── BOOKINGS ───────────────────────────────────────────────
        $this->modCol('bookings', 'booking_code',  30);

        // ─── PAYMENTS ───────────────────────────────────────────────
        $this->modCol('payments', 'payment_code',   30);
        $this->modCol('payments', 'month_period',   20);
        $this->modCol('payments', 'proof_image',   150, true);

        // ─── COMPLAINTS ─────────────────────────────────────────────
        $this->modCol('complaints', 'title',      150);
        $this->modCol('complaints', 'image',      150, true);

        // ─── BONUS ──────────────────────────────────────────────────
        $this->modCol('cache_locks', 'owner',     100);

        $this->info('✅ Optimasi kolom selesai.');
    }

    /**
     * Rollback: kembalikan semua ke VARCHAR(255) dengan properti asli.
     */
    public function down(): void
    {
        // Kembalikan dengan properti yang sama (nullable / default)
        $this->modCol('users', 'name',          255);
        $this->modCol('users', 'phone',          255, true);
        $this->modCol('users', 'occupation',     255, true);
        $this->modCol('users', 'religion',       255, true);
        $this->modCol('users', 'id_card',       255, true);
        $this->modCol('users', 'ktp_image',     255, true);
        $this->modCol('users', 'avatar',        255, true);

        $this->modCol('rooms', 'name',           255);
        $this->modCol('rooms', 'type',           255, false, "'Standard'");

        $this->modCol('bookings', 'booking_code',  255);
        $this->modCol('payments', 'payment_code',   255);
        $this->modCol('payments', 'month_period',   255);
        $this->modCol('payments', 'proof_image',   255, true);

        $this->modCol('complaints', 'title',      255);
        $this->modCol('complaints', 'image',      255, true);

        $this->modCol('cache_locks', 'owner',     255);

        $this->info('⏪ Rollback: semua kolom dikembalikan ke VARCHAR(255).');
    }

    /**
     * Helper: ALTER TABLE MODIFY COLUMN.
     *
     * @param  string  $table      Nama tabel
     * @param  string  $column     Nama kolom
     * @param  int     $length     Panjang VARCHAR baru
     * @param  bool    $nullable   Apakah kolom nullable (default: false = NOT NULL)
     * @param  string  $default    Nilai DEFAULT (tanpa keyword DEFAULT, misal "'Standard'")
     */
    private function modCol(
        string $table,
        string $column,
        int    $length,
        bool   $nullable = false,
        string $default  = ''
    ): void {
        if (! Schema::hasColumn($table, $column)) {
            $this->info("  ⚠  Kolom {$table}.{$column} tidak ditemukan, dilewati.");
            return;
        }

        $collation = 'utf8mb4_unicode_ci';
        $nullPart  = $nullable ? 'DEFAULT NULL' : 'NOT NULL';

        // Tambah DEFAULT value jika ada (hanya untuk NOT NULL columns)
        $defaultPart = '';
        if ($default !== '') {
            $defaultPart = " DEFAULT {$default}";
            // Jika ada DEFAULT, null/not null sudah termasuk di default definition
            // Tapi lebih aman tetap explicit
            if ($nullable) {
                $nullPart = "DEFAULT NULL";
                $defaultPart = ''; // DEFAULT NULL sudah cukup
            } else {
                $nullPart = "NOT NULL DEFAULT {$default}";
                $defaultPart = '';
            }
        }

        $sql = sprintf(
            'ALTER TABLE `%s` MODIFY COLUMN `%s` VARCHAR(%d) COLLATE %s %s%s',
            $table,
            $column,
            $length,
            $collation,
            $nullPart,
            $defaultPart
        );

        DB::statement($sql);
        $this->info("  • {$table}.{$column}: 255 → {$length} {$nullPart}");
    }

    /** Output ke console */
    private function info(string $msg): void
    {
        echo "  {$msg}\n";
    }
};
