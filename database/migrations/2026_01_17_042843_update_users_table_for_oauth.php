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
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('email'); // google, telegram
            $table->string('provider_id')->nullable()->after('provider'); // OAuth provider ID
            $table->string('avatar')->nullable()->after('provider_id'); // URL аватарки
            $table->decimal('balance', 10, 2)->default(0)->after('avatar'); // Баланс пользователя
            $table->string('password')->nullable()->change(); // Пароль необязателен для OAuth
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id', 'avatar', 'balance']);
        });
    }
};
