<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom notes di bookings — untuk catatan penolakan atau komentar admin.
        // Jika sudah ada dari Phase 1, lewati.
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'notes')) {
                $table->text('notes')->nullable()->after('approved_by');
            }
        });

        // Kolom month_number di payments — urutan bulan tagihan (1 = pertama).
        // Memudahkan tampilan "Tagihan Bulan ke-N" tanpa menghitung dari due_date.
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'month_number')) {
                $table->unsignedTinyInteger('month_number')->default(1)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'notes')) {
                $table->dropColumn('notes');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'month_number')) {
                $table->dropColumn('month_number');
            }
        });
    }
};
