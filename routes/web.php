<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\GamePageController;
use App\Http\Controllers\GiftCardPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PurchaseSuccessController;
use App\Http\Controllers\TopUpController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);


Route::get('/game/{slug}', [GamePageController::class, 'show']);

// Gift-card pages
Route::get('/gift-card', function (Request $request) {
    if ($request->has('id')) {
        $id = (string)$request->query('id');
        $card = DB::table('gift_cards')->where('id', $id)->orWhere('slug', $id)->first();
        if ($card) {
            return redirect('/gift-card/' . ($card->slug ?? $id), 301);
        }
    }
    abort(404);
});
Route::get('/gift-card/{slug}', [GiftCardPageController::class, 'show']);

Route::view('/cart', 'cart');
Route::get('/top-up', [TopUpController::class, 'show'])->name('top-up');
Route::post('/api/top-up', [TopUpController::class, 'addBalance'])->name('top-up.add');
Route::view('/help', 'help');
Route::view('/contacts', 'contacts');
Route::get('/purchase-success', [PurchaseSuccessController::class, 'show'])->name('purchase-success');

// Auth routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider'])->name('auth.redirect');
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('auth.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Order routes
Route::post('/api/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/api/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/api/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

// Review routes
Route::post('/api/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/api/reviews', [ReviewController::class, 'index'])->name('reviews.index');

// Данные для фронтенда в старом формате JSON, но из базы
Route::get('/games-data.json', [DataController::class, 'games']);
Route::get('/gift-cards-data.json', [DataController::class, 'giftCards']);
Route::get('/api/search', [DataController::class, 'search']);

Route::get('/robots.txt', function () {
    $content = "User-agent: *\nDisallow:\nSitemap: " . url('/sitemap.xml') . "\n";
    return response($content, 200)->header('Content-Type', 'text/plain');
});

Route::get('/sitemap.xml', function () {
    $urls = [
        url('/'),
        url('/games'),
        url('/cart'),
        url('/top-up'),
        url('/profile'),
        url('/help'),
        url('/contacts'),
    ];

    $games = \DB::table('games')->select('slug')->get();
    foreach ($games as $g) {
        $urls[] = url('/game/' . $g->slug);
    }
    $cards = \DB::table('gift_cards')->select('slug')->get();
    foreach ($cards as $c) {
        $urls[] = url('/gift-card/' . $c->slug);
    }

    $xml = view('sitemap', ['urls' => $urls]);
    return response($xml, 200)->header('Content-Type', 'application/xml');
});
