<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();                             // BOOK-2026-0001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date')->nullable();
            $table->unsignedSmallInteger('duration_months')->default(1);
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'active',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->text('notes')->nullable();                                    // catatan dari penyewa
            $table->text('rejection_reason')->nullable();                         // alasan penolakan oleh admin
            $table->foreignId('approved_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
