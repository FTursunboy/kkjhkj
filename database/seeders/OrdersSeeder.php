<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Получаем первого пользователя (или создаем тестового)
        $user = User::first();

        if (!$user) {
            $this->command?->warn("Нет пользователей для создания заказов");
            return;
        }

        // Создаем примеры заказов
        $orders = [
            [
                'product_type' => 'game',
                'product_name' => 'PUBG Mobile',
                'package_name' => '60 UC',
                'amount' => 75.00,
                'status' => 'completed',
                'details' => [
                    'product_id' => '5',
                    'package_id' => 'uc-60',
                    'player_id' => '123456789',
                ],
                'completed_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
            ],
            [
                'product_type' => 'game',
                'product_name' => 'Brawl Stars',
                'package_name' => '170 Gems',
                'amount' => 169.00,
                'status' => 'completed',
                'details' => [
                    'product_id' => '1',
                    'package_id' => 'gems-170',
                ],
                'completed_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
            ],
            [
                'product_type' => 'gift_card',
                'product_name' => 'Telegram',
                'package_name' => 'Telegram Stars 100',
                'amount' => 100.00,
                'status' => 'pending',
                'details' => [
                    'product_id' => 'telegram',
                    'package_id' => 'stars-100',
                ],
                'completed_at' => null,
                'created_at' => now()->subHours(2),
            ],
            [
                'product_type' => 'game',
                'product_name' => 'Clash of Clans',
                'package_name' => '500 Gems',
                'amount' => 489.00,
                'status' => 'completed',
                'details' => [
                    'product_id' => '2',
                    'package_id' => 'gems-500',
                ],
                'completed_at' => now()->subWeek(),
                'created_at' => now()->subWeek(),
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                ...$orderData,
            ]);
        }

        $this->command?->info("Создано " . count($orders) . " тестовых заказов для пользователя {$user->name}");
    }
}
