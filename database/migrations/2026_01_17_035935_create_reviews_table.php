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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('game_id'); // ID игры (может быть число или строка)
            $table->string('author'); // Имя автора отзыва
            $table->integer('rating'); // Оценка от 1 до 5
            $table->text('text'); // Текст отзыва
            $table->date('review_date'); // Дата отзыва
            $table->string('package_name')->nullable(); // Название купленного пакета
            $table->json('tags')->nullable(); // Теги (макеты отзывов)
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index('game_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
