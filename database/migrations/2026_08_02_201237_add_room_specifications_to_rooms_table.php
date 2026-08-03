<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {

            // Identitas kamar
            $table->string('room_number', 30)
                  ->nullable()
                  ->after('name');

            // Dimensi
            $table->decimal('length_m', 4, 2)
                  ->nullable()
                  ->after('capacity');

            $table->decimal('width_m', 4, 2)
                  ->nullable()
                  ->after('length_m');

            // Kamar mandi
            $table->enum('bathroom_type', [
                'inside',
                'outside',
                'shared',
            ])
            ->default('inside')
            ->after('width_m');

            // Furnished
            $table->enum('furnished', [
                'empty',
                'semi',
                'full',
            ])
            ->default('empty')
            ->after('bathroom_type');

            // Utilitas
            $table->enum('electricity_type', [
                'included',
                'token',
                'usage',
            ])
            ->default('included')
            ->after('furnished');

            $table->enum('water_type', [
                'included',
                'meter',
                'well',
            ])
            ->default('included')
            ->after('electricity_type');

            // Deposit
            $table->unsignedInteger('deposit')
                  ->default(0)
                  ->after('price');

        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {

            $table->dropColumn([
                'room_number',
                'length_m',
                'width_m',
                'bathroom_type',
                'furnished',
                'electricity_type',
                'water_type',
                'deposit',
            ]);

        });
    }
};