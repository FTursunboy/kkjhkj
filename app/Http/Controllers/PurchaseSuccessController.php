<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PurchaseSuccessController extends Controller
{
    public function show()
    {
        $lastOrder = null;

        // Если пользователь авторизован, получаем его последний заказ
        if (Auth::check()) {
            $lastOrder = Order::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return view('purchase-success', [
            'lastOrder' => $lastOrder
        ]);
    }
}
