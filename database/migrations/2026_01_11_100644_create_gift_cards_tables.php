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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->string('id')->primary();           // slug/id из JSON
            $table->string('name');
            $table->string('image')->nullable();       // путь к обложке
            $table->text('icon_svg')->nullable();
            $table->string('package_image')->nullable();
            $table->string('custom_ui')->nullable();
            $table->json('aliases')->nullable();
            $table->json('currencies')->nullable();    // массив валют/коэффициентов
            $table->json('quick_amounts')->nullable(); // быстрые суммы
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('gift_card_packages', function (Blueprint $table) {
            $table->id();
            $table->string('gift_card_id');
            $table->string('name');
            $table->unsignedInteger('price')->default(0);
            $table->string('image')->nullable();
            $table->string('category')->nullable();    // например stars/premium для Telegram
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('gift_card_id')->references('id')->on('gift_cards')->cascadeOnDelete();
            $table->index(['gift_card_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_card_packages');
        Schema::dropIfExists('gift_cards');
    }
};
