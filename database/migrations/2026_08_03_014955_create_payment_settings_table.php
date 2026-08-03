<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {

            $table->id();

            // Nama metode
            // contoh:
            // BRI
            // BCA
            // QRIS
            // DANA
            $table->string('name',100);

            // bank
            // qris
            // ewallet
            $table->enum('type',[
                'bank',
                'qris',
                'ewallet'
            ]);

            // Nomor rekening / nomor akun
            $table->string('account_number')->nullable();

            // Nama pemilik rekening
            $table->string('account_name')->nullable();

            // Khusus QRIS
            $table->string('image')->nullable();

            // Catatan
            $table->text('notes')->nullable();

            // Aktif / Nonaktif
            $table->boolean('is_active')
                ->default(true);

            // Urutan tampil
            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};