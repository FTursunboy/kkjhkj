<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Загружаем все игры
        $games = DB::table('games')
            ->select('id', 'slug', 'name', 'image', 'icon_svg', 'min_price', 'platform')
            ->orderBy('id')
            ->get()
            ->map(function ($game) {
                return [
                    'id'       => $game->id,
                    'slug'     => $game->slug,
                    'name'     => $game->name,
                    'image'    => $game->image,
                    'icon'     => $game->icon_svg,
                    'minPrice' => $game->min_price,
                    'platform' => $game->platform,
                ];
            })
            ->toArray();

        // Загружаем все подарочные карты
        $giftCards = DB::table('gift_cards')
            ->select('id', 'slug', 'name', 'image', 'icon_svg')
            ->orderBy('id')
            ->get()
            ->map(function ($card) {
                return [
                    'id'    => $card->id,
                    'slug'  => $card->slug,
                    'name'  => $card->name,
                    'image' => $card->image,
                    'icon'  => $card->icon_svg,
                ];
            })
            ->toArray();

        return view('index', [
            'games'     => $games,
            'giftCards' => $giftCards,
            'isAuthenticated' => Auth::check(),
        ]);
    }
}
