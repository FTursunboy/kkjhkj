<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Связь с пользователем
            $table->string('order_number')->unique(); // Номер заказа (например: ORD-123456)
            $table->string('product_type'); // game или gift_card
            $table->string('product_name'); // Название игры/сервиса
            $table->string('package_name'); // Название пакета
            $table->decimal('amount', 10, 2); // Сумма заказа
            $table->string('status')->default('pending'); // pending, completed, cancelled, failed
            $table->json('details')->nullable(); // Доп. информация (ID игры, кол-во валюты и т.д.)
            $table->timestamp('completed_at')->nullable(); // Дата выполнения
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
