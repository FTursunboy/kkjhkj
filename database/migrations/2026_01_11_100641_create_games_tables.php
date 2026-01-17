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
        Schema::create('games', function (Blueprint $table) {
            // Используем строковый ID, чтобы сохранить исходные slug/числовые идентификаторы из JSON
            $table->string('id')->primary();
            $table->string('name');
            $table->string('currency')->nullable();
            $table->string('genre')->nullable();
            $table->string('platform')->nullable();
            $table->string('publisher')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('min_price')->nullable();
            $table->string('image')->nullable();     // относительный путь (public/images/...)
            $table->text('icon_svg')->nullable();    // SVG path из JSON
            $table->json('aliases')->nullable();
            $table->json('meta')->nullable();        // место для FAQ/отзывов и прочего
            $table->timestamps();
        });

        Schema::create('game_packages', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->string('name');
            $table->string('amount')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->string('bonus')->nullable();
            $table->boolean('popular')->default(false);
            $table->string('image')->nullable();     // путь к картинке пакета, если есть
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('games')->cascadeOnDelete();
            $table->index(['game_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_packages');
        Schema::dropIfExists('games');
    }
};
