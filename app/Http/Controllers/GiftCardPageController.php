<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class GiftCardPageController extends Controller
{
    public function show(string $slug)
    {
        $card = DB::table('gift_cards')
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$card) {
            abort(404);
        }

        $packages = DB::table('gift_card_packages')
            ->where('gift_card_id', $card->id)
            ->orderBy('sort_order')
            ->get();

        $plain = [];
        $categories = [];
        foreach ($packages as $p) {
            $item = [
                'name'  => $p->name,
                'price' => (int)$p->price,
                'image' => $p->image,
            ];
            if ($p->category) {
                $categories[$p->category][] = $item;
            } else {
                $plain[] = $item;
            }
        }

        $payload = [
            'id'            => $card->id,
            'slug'          => $card->slug,
            'name'          => $card->name,
            'aliases'       => json_decode($card->aliases ?? '[]', true) ?: [],
            'image'         => $card->image,
            'icon'          => $card->icon_svg,
            'packageImage'  => $card->package_image,
            'customUI'      => $card->custom_ui,
            'currencies'    => json_decode($card->currencies ?? '[]', true) ?: [],
            'quickAmounts'  => json_decode($card->quick_amounts ?? '[]', true) ?: [],
            'packages'      => $plain,
            'categories'    => $categories,
        ];

        return view('gift-card', [
            'initialCard' => $payload,
        ]);
    }
}


