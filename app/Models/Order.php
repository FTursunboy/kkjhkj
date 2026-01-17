<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'product_type',
        'product_name',
        'package_name',
        'amount',
        'status',
        'details',
        'completed_at',
    ];

    protected $casts = [
        'details' => 'array',
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить статус заказа на русском
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'В обработке',
            'completed' => 'Выполнен',
            'cancelled' => 'Отменен',
            'failed' => 'Ошибка',
            default => 'Неизвестно',
        };
    }

    /**
     * Получить цвет статуса для UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'text-yellow-400',
            'completed' => 'text-green-400',
            'cancelled' => 'text-gray-400',
            'failed' => 'text-red-400',
            default => 'text-gray-400',
        };
    }

    /**
     * Генерация уникального номера заказа
     */
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
