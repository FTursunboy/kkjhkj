<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Создание отзыва после покупки
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
            'order_id' => 'required|integer|exists:orders,id',
            'game_id' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'nullable|string|max:1000',
            'package_name' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Проверяем, что заказ принадлежит пользователю
        $order = DB::table('orders')
            ->where('id', $validated['order_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Заказ не найден'
            ], 404);
        }

        // Проверяем, не оставил ли пользователь уже отзыв на этот заказ
        $existingReview = DB::table('reviews')
            ->where('game_id', $validated['game_id'])
            ->where('author', $user->name)
            ->where('package_name', $validated['package_name'] ?? $order->package_name)
            ->whereRaw('DATE(review_date) = CURDATE()') // Проверяем только сегодняшние отзывы
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже оставили отзыв на этот товар сегодня'
            ], 400);
        }

        try {
            // Создаем отзыв
            DB::table('reviews')->insert([
                'game_id' => $validated['game_id'],
                'author' => $user->name,
                'rating' => $validated['rating'],
                'text' => $validated['text'] ?? '',
                'review_date' => now(),
                'package_name' => $validated['package_name'] ?? $order->package_name,
                'tags' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Спасибо за ваш отзыв!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Review creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'order_id' => $validated['order_id']
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании отзыва'
            ], 500);
        }
    }

    /**
     * Получить отзывы пользователя
     */
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ], 401);
        }

        $reviews = DB::table('reviews')
            ->where('author', Auth::user()->name)
            ->orderBy('review_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }
}
