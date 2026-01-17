<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DataController extends Controller
{
    /**
    * Возвращает данные игр в формате исходного JSON.
    */
    public function games()
    {
        $games = DB::table('games')
            ->orderBy('name')
            ->get()
            ->map(function ($game) {
                $packages = DB::table('game_packages')
                    ->where('game_id', $game->id)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(function ($pkg) {
                        return [
                            'name'    => $pkg->name,
                            'amount'  => $pkg->amount,
                            'price'   => (int) $pkg->price,
                            'bonus'   => $pkg->bonus,
                            'popular' => (bool) $pkg->popular,
                            'image'   => $pkg->image,
                        ];
                    })
                    ->values();

                $meta = json_decode($game->meta ?? '{}', true) ?: [];

                return [
                    'id'          => is_numeric($game->id) ? (int) $game->id : $game->id,
                    'slug'        => $game->slug ?: Str::slug($game->name),
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
                    // Доп. данные переносим в том же ключе, чтобы фронт мог использовать при необходимости
                    'reviews'     => $meta['reviews'] ?? [],
                    'faq'         => $meta['faq'] ?? [],
                    'requirements'=> $meta['requirements'] ?? [],
                    'tabs'        => $meta['tabs'] ?? [],
                ];
            })
            ->values();

        return response()->json(['games' => $games], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Поиск по играм и gift-картам (name/aliases/currency/genre).
     */
    public function search(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        if ($query === '') {
            return response()->json(['results' => []], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $q = mb_strtolower($query);

        $games = DB::table('games')
            ->select('id', 'slug', 'name', 'aliases', 'image', 'currency', 'genre')
            ->get()
            ->filter(function ($g) use ($q) {
                $aliases = json_decode($g->aliases ?? '[]', true) ?: [];
                return str_contains(mb_strtolower($g->name), $q)
                    || str_contains(mb_strtolower($g->currency ?? ''), $q)
                    || str_contains(mb_strtolower($g->genre ?? ''), $q)
                    || collect($aliases)->contains(function ($alias) use ($q) {
                        return str_contains(mb_strtolower($alias), $q);
                    });
            })
            ->map(function ($g) {
                return [
                    'type' => 'game',
                    'id'   => $g->id,
                    'slug' => $g->slug ?: Str::slug($g->name),
                    'name' => $g->name,
                    'image'=> $g->image,
                ];
            })
            ->values();

        $cards = DB::table('gift_cards')
            ->select('id', 'slug', 'name', 'aliases', 'image')
            ->get()
            ->filter(function ($c) use ($q) {
                $aliases = json_decode($c->aliases ?? '[]', true) ?: [];
                return str_contains(mb_strtolower($c->name), $q)
                    || collect($aliases)->contains(function ($alias) use ($q) {
                        return str_contains(mb_strtolower($alias), $q);
                    });
            })
            ->map(function ($c) {
                return [
                    'type' => 'gift-card',
                    'id'   => $c->id,
                    'slug' => $c->slug ?: Str::slug($c->name),
                    'name' => $c->name,
                    'image'=> $c->image,
                ];
            })
            ->values();

        $results = $games->merge($cards)->take(10)->values();

        return response()->json(['results' => $results], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
    * Возвращает данные подарочных карт в формате исходного JSON.
    */
    public function giftCards()
    {
        $cards = DB::table('gift_cards')
            ->orderBy('name')
            ->get()
            ->map(function ($card) {
                $packages = DB::table('gift_card_packages')
                    ->where('gift_card_id', $card->id)
                    ->orderBy('sort_order')
                    ->get();

                // Разбираем пакеты на обычные и категоризированные, чтобы сохранить структуру JSON
                $plainPackages = [];
                $categories = [];
                foreach ($packages as $pkg) {
                    $item = [
                        'name'  => $pkg->name,
                        'price' => (int) $pkg->price,
                        'image' => $pkg->image,
                    ];
                    if ($pkg->category) {
                        $categories[$pkg->category][] = $item;
                    } else {
                        $plainPackages[] = $item;
                    }
                }

                return [
                    'id'            => $card->id,
                    'slug'          => $card->slug ?: Str::slug($card->name),
                    'name'          => $card->name,
                    'aliases'       => json_decode($card->aliases ?? '[]', true) ?: [],
                    'image'         => $card->image,
                    'icon'          => $card->icon_svg,
                    'packageImage'  => $card->package_image,
                    'customUI'      => $card->custom_ui,
                    'currencies'    => json_decode($card->currencies ?? '[]', true) ?: [],
                    'quickAmounts'  => json_decode($card->quick_amounts ?? '[]', true) ?: [],
                    'packages'      => $plainPackages,
                    'categories'    => $categories ?: new \stdClass(),
                ];
            })
            ->values();

        return response()->json(['gift_cards' => $cards], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
