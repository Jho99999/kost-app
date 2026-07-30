<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                               // misal: "Kamar 101"
            $table->string('type')->default('Standard');                          // Standard / Deluxe / VIP
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);                                      // harga per bulan
            $table->enum('status', ['available', 'occupied', 'maintenance'])
                  ->default('available');
            $table->unsignedTinyInteger('floor')->default(1);
            $table->unsignedTinyInteger('capacity')->default(1);
            $table->unsignedSmallInteger('size_sqm')->nullable();                 // luas m²
            $table->json('facilities')->nullable();                               // ["WiFi","AC","Kamar Mandi Dalam"]
            $table->json('images')->nullable();                                   // ["rooms/101-1.jpg","rooms/101-2.jpg"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
