<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/games-data.json');
        if (!file_exists($path)) {
            $this->command?->warn("games-data.json not found, skip seeding reviews");
            return;
        }

        $payload = json_decode(file_get_contents($path), true);
        if (!is_array($payload) || !isset($payload['games'])) {
            $this->command?->warn("games-data.json has unexpected format");
            return;
        }

        // Очистка таблицы отзывов
        DB::table('reviews')->truncate();

        $reviewsCount = 0;

        foreach ($payload['games'] as $game) {
            $gameId = (string)($game['id'] ?? '');
            if ($gameId === '') {
                continue;
            }

            // Проверяем наличие отзывов у игры
            if (!isset($game['reviews']) || !is_array($game['reviews'])) {
                continue;
            }

            // Добавляем каждый отзыв
            foreach ($game['reviews'] as $review) {
                DB::table('reviews')->insert([
                    'game_id'      => $gameId,
                    'author'       => $review['author'] ?? 'Аноним',
                    'rating'       => (int)($review['rating'] ?? 5),
                    'text'         => $review['text'] ?? '',
                    'review_date'  => $review['date'] ?? now()->format('Y-m-d'),
                    'package_name' => $review['packageName'] ?? null,
                    'tags'         => isset($review['tags']) ? json_encode($review['tags'], JSON_UNESCAPED_UNICODE) : null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $reviewsCount++;
            }
        }

        $this->command?->info("✓ Imported {$reviewsCount} reviews");
    }
}
