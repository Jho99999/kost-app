<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom alamat (tidak relevan untuk penghuni kost yang mobile)
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }

            // Tambah kolom profil yang lebih relevan
            if (! Schema::hasColumn('users', 'occupation')) {
                $table->string('occupation')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['L', 'P'])->nullable()->after('occupation');
            }

            if (! Schema::hasColumn('users', 'religion')) {
                $table->string('religion')->nullable()->after('gender');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['occupation', 'gender', 'religion']);

            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
        });
    }
};
