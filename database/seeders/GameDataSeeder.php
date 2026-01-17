<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Отключаем проверки FK на время очистки
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $path = database_path('data/games-data.json');
        if (!file_exists($path)) {
            $this->command?->warn("games-data.json not found, skip seeding games");
            return;
        }

        $payload = json_decode(file_get_contents($path), true);
        if (!is_array($payload) || !isset($payload['games'])) {
            $this->command?->warn("games-data.json has unexpected format");
            return;
        }

        DB::table('game_packages')->truncate();
        DB::table('games')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($payload['games'] as $gameIndex => $game) {
            $gameId = (string)($game['id'] ?? '');
            if ($gameId === '') {
                continue;
            }

            $slug = \Str::slug($game['name'] ?? $gameId);

            DB::table('games')->updateOrInsert(
                ['id' => $gameId],
                [
                    'slug'        => $slug,
                    'name'        => $game['name'] ?? '',
                    'currency'    => $game['currency'] ?? null,
                    'genre'       => $game['genre'] ?? null,
                    'platform'    => $game['platform'] ?? null,
                    'publisher'   => $game['publisher'] ?? null,
                    'description' => $game['description'] ?? null,
                    'min_price'   => $game['minPrice'] ?? null,
                    'image'       => $game['image'] ?? null,
                    'icon_svg'    => $game['icon'] ?? null,
                    'aliases'     => isset($game['aliases']) ? json_encode($game['aliases'], JSON_UNESCAPED_UNICODE) : null,
                    'meta'        => json_encode([
                        'reviews'      => $game['reviews']      ?? null,
                        'faq'          => $game['faq']          ?? null,
                        'requirements' => $game['requirements'] ?? null,
                        'tabs'         => $game['tabs']         ?? null,
                    ], JSON_UNESCAPED_UNICODE),
                ]
            );

            $packages = $game['packages'] ?? [];
            foreach ($packages as $pkgIndex => $pkg) {
                DB::table('game_packages')->updateOrInsert(
                    [
                        'game_id' => $gameId,
                        'name'    => $pkg['name'] ?? '',
                    ],
                    [
                        'amount'      => $pkg['amount'] ?? null,
                        'price'       => $pkg['price'] ?? 0,
                        'bonus'       => $pkg['bonus'] ?? null,
                        'popular'     => (bool)($pkg['popular'] ?? false),
                        'image'       => $pkg['image'] ?? null,
                        'sort_order'  => $pkgIndex,
                        'updated_at'  => now(),
                        'created_at'  => now(),
                    ]
                );
            }
        }
    }
}
