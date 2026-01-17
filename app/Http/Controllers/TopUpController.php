<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'method' => 'required|string|in:sbp,crypto',
        ]);

        $amount = $request->input('amount');
        $method = $request->input('method');

        DB::beginTransaction();
        try {
            // Пока просто добавляем деньги без реальной оплаты
            $user->balance += $amount;
            $user->save();

            DB::commit();
            Log::info("Balance added for user {$user->id}. Amount: {$amount}, Method: {$method}, New Balance: {$user->balance}");

            return response()->json([
                'success' => true,
                'message' => 'Баланс успешно пополнен!',
                'new_balance' => $user->balance,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error adding balance for user {$user->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ошибка при пополнении баланса'], 500);
        }
    }
}
