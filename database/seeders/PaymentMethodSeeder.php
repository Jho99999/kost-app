<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'BRI Virtual Account',
                'type' => 'bank',
                'account_number' => '1234567890',
                'account_name' => 'Kost App',
                'notes' => 'Pembayaran via transfer bank BRI.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'BCA Virtual Account',
                'type' => 'bank',
                'account_number' => '0987654321',
                'account_name' => 'Kost App',
                'notes' => 'Pembayaran via transfer bank BCA.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'QRIS',
                'type' => 'qris',
                'notes' => 'Pembayaran scan QRIS.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'DANA',
                'type' => 'ewallet',
                'account_number' => '081234567890',
                'account_name' => 'Kost App',
                'notes' => 'Pembayaran via e-wallet DANA.',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                $method
            );
        }

        $this->command->info('✔ Payment methods seeded.');
    }
}
