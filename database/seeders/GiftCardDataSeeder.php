<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GiftCardDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $path = database_path('data/gift-cards-data.json');
        if (!file_exists($path)) {
            $this->command?->warn("gift-cards-data.json not found, skip seeding gift cards");
            return;
        }

        $payload = json_decode(file_get_contents($path), true);
        if (!is_array($payload) || !isset($payload['gift_cards'])) {
            $this->command?->warn("gift-cards-data.json has unexpected format");
            return;
        }

        DB::table('gift_card_packages')->truncate();
        DB::table('gift_cards')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($payload['gift_cards'] as $cardIndex => $card) {
            $cardId = (string)($card['id'] ?? '');
            if ($cardId === '') {
                continue;
            }

            $slug = \Str::slug($card['name'] ?? $cardId);

            DB::table('gift_cards')->updateOrInsert(
                ['id' => $cardId],
                [
                    'slug'          => $slug,
                    'name'          => $card['name'] ?? '',
                    'image'         => $card['image'] ?? null,
                    'icon_svg'      => $card['icon'] ?? null,
                    'package_image' => $card['packageImage'] ?? null,
                    'custom_ui'     => $card['customUI'] ?? null,
                    'aliases'       => isset($card['aliases']) ? json_encode($card['aliases'], JSON_UNESCAPED_UNICODE) : null,
                    'currencies'    => isset($card['currencies']) ? json_encode($card['currencies'], JSON_UNESCAPED_UNICODE) : null,
                    'quick_amounts' => isset($card['quickAmounts']) ? json_encode($card['quickAmounts'], JSON_UNESCAPED_UNICODE) : null,
                    'meta'          => json_encode($card['meta'] ?? [], JSON_UNESCAPED_UNICODE),
                    'updated_at'    => now(),
                    'created_at'    => now(),
                ]
            );

            // Обычные пакеты
            $packages = $card['packages'] ?? [];
            foreach ($packages as $pkgIndex => $pkg) {
                DB::table('gift_card_packages')->updateOrInsert(
                    [
                        'gift_card_id' => $cardId,
                        'name'         => $pkg['name'] ?? '',
                    ],
                    [
                        'price'       => $pkg['price'] ?? 0,
                        'image'       => $pkg['image'] ?? null,
                        'category'    => $pkg['category'] ?? null,
                        'sort_order'  => $pkgIndex,
                        'updated_at'  => now(),
                        'created_at'  => now(),
                    ]
                );
            }

            // Категории (например Telegram stars/premium)
            if (!empty($card['categories']) && is_array($card['categories'])) {
                foreach ($card['categories'] as $categoryKey => $list) {
                    foreach ($list as $pkgIndex => $pkg) {
                        DB::table('gift_card_packages')->updateOrInsert(
                            [
                                'gift_card_id' => $cardId,
                                'name'         => $pkg['name'] ?? '',
                            ],
                            [
                                'price'       => $pkg['price'] ?? 0,
                                'image'       => $pkg['image'] ?? null,
                                'category'    => $categoryKey,
                                'sort_order'  => $pkgIndex,
                                'updated_at'  => now(),
                                'created_at'  => now(),
                            ]
                        );
                    }
                }
            }
        }
    }
}
