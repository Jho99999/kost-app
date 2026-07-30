<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique();                             // PAY-2026-0001
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->string('month_period');                                       // "Juni 2026"
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])
                  ->default('pending');
            $table->string('proof_image')->nullable();                          // path bukti transfer
            $table->enum('payment_method', ['transfer', 'cash', 'qris'])
                  ->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('verified_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
