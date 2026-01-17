<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Создание нового заказа
     */
    public function store(Request $request)
    {
        // Проверка авторизации
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ], 401);
        }

        // Валидация данных
        $validated = $request->validate([
            'product_type' => 'required|in:game,gift_card',
            'product_id' => 'required|string',
            'product_name' => 'required|string',
            'package_id' => 'required|string',
            'package_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'player_id' => 'nullable|string', // ID игрока для игр
            'details' => 'nullable|array',
        ]);

        $user = Auth::user();

        // Проверка баланса
        if ($user->balance < $validated['amount']) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно средств на балансе'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Создаем заказ
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'product_type' => $validated['product_type'],
                'product_name' => $validated['product_name'],
                'package_name' => $validated['package_name'],
                'amount' => $validated['amount'],
                'status' => 'pending',
                'details' => [
                    'product_id' => $validated['product_id'],
                    'package_id' => $validated['package_id'],
                    'player_id' => $validated['player_id'] ?? null,
                    ...$validated['details'] ?? [],
                ],
            ]);

            // Списываем средства с баланса
            $user->decrement('balance', $validated['amount']);

            // В реальной системе здесь была бы логика отправки заказа в систему обработки
            // Для демо сразу помечаем заказ как выполненный
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Заказ успешно создан!',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'amount' => $order->amount,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании заказа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить список заказов текущего пользователя
     */
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ], 401);
        }

        $orders = Auth::user()->orders()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Получить детали заказа
     */
    public function show($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ], 401);
        }

        $order = Auth::user()->orders()->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Заказ не найден'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }
}
