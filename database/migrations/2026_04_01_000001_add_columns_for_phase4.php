<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Catatan penolakan dari admin (proof_image ditolak, user perlu upload ulang)
            if (! Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable()->after('verified_by');
            }

            // Waktu verifikasi admin (lebih presisi dari paid_at yang mungkin diisi manual)
            if (! Schema::hasColumn('payments', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach (['notes', 'verified_at'] as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
