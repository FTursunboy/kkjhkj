<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class GamePageController extends Controller
{
    public function show(string $slug)
    {
        $game = DB::table('games')->where('slug', $slug)->orWhere('id', $slug)->first();

        if (!$game) {
            abort(404, 'Игра не найдена');
        }

        $packages = DB::table('game_packages')
            ->where('game_id', $game->id)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($p) {
                return [
                    'name'    => $p->name,
                    'amount'  => $p->amount,
                    'price'   => (int)$p->price,
                    'bonus'   => $p->bonus,
                    'popular' => (bool)$p->popular,
                    'image'   => $p->image,
                ];
            })
            ->values()
            ->toArray();

        // Получаем отзывы из базы данных
        $reviews = DB::table('reviews')
            ->where('game_id', $game->id)
            ->orderBy('review_date', 'desc')
            ->get()
            ->map(function ($r) {
                return [
                    'author'      => $r->author,
                    'rating'      => (int)$r->rating,
                    'date'        => $r->review_date,
                    'text'        => $r->text,
                    'packageName' => $r->package_name,
                    'tags'        => $r->tags ? json_decode($r->tags, true) : null,
                ];
            })
            ->values()
            ->toArray();

        $meta = json_decode($game->meta ?? '{}', true) ?: [];

        $gamePayload = [
            'id'          => $game->id,
            'slug'        => $game->slug,
            'name'        => $game->name,
            'aliases'     => json_decode($game->aliases ?? '[]', true) ?: [],
            'currency'    => $game->currency,
            'genre'       => $game->genre,
            'platform'    => $game->platform,
            'publisher'   => $game->publisher,
            'description' => $game->description,
            'minPrice'    => $game->min_price,
            'image'       => $game->image,
            'icon'        => $game->icon_svg,
            'packages'    => $packages,
            'reviews'     => $reviews,
            'faq'         => $meta['faq'] ?? [],
            'requirements'=> $meta['requirements'] ?? [],
            'tabs'        => $meta['tabs'] ?? [],
        ];


        return view('game', [
            'initialGame' => $gamePayload,
        ]);
    }
}


