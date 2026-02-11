<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TopUpController extends Controller
{
    /**
     * Показать страницу пополнения баланса
     */
    public function show()
    {
        // Если не авторизован - редирект на регистрацию
        if (!Auth::check()) {
            return redirect()->route('register')->with('message', 'Пожалуйста, авторизуйтесь для пополнения баланса');
        }

        $user = Auth::user();
        return view('top-up', compact('user'));
    }

    /**
     * Обработать пополнение баланса (пока без реальной оплаты)
     */
    public function addBalance(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'method' => 'required|string|in:sbp,crypto',
        ]);

        // Оплата еще не подключена: блокируем пополнение и не меняем баланс.
        Log::warning('Top-up attempt blocked: payment integration is not enabled.', [
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Ошибка',
        ], 503);
    }
}
