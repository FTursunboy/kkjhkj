<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Редирект на провайдера OAuth (Google или Telegram)
     */
    public function redirectToProvider($provider)
    {
        if (!in_array($provider, ['google', 'telegram'])) {
            abort(404);
        }

        // Для Telegram показываем страницу с виджетом
        if ($provider === 'telegram') {
            return view('telegram-login', [
                'bot_username' => config('services.telegram.bot_username'),
                'callback_url' => config('services.telegram.redirect')
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Обработка callback от провайдера OAuth
     */
    public function handleProviderCallback($provider, Request $request)
    {
        try {
            // Для Telegram обрабатываем данные напрямую из GET параметров
            if ($provider === 'telegram') {
                return $this->handleTelegramCallback($request);
            }

            // Для других провайдеров используем Socialite
            $socialUser = Socialite::driver($provider)->user();

            // Ищем пользователя по provider и provider_id
            $user = User::where('provider', $provider)
                        ->where('provider_id', $socialUser->getId())
                        ->first();

            if (!$user) {
                // Если пользователя нет, создаем нового
                $user = User::create([
                    'name'        => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email'       => $socialUser->getEmail() ?? $socialUser->getId() . '@' . $provider . '.local',
                    'provider'    => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar'      => $socialUser->getAvatar(),
                    'balance'     => 0,
                ]);
            } else {
                // Обновляем аватарку и имя при каждом входе
                $user->update([
                    'name'   => $socialUser->getName() ?? $socialUser->getNickname() ?? $user->name,
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            // Авторизуем пользователя
            Auth::login($user, true);

            // Редирект на профиль
            return redirect('/profile')->with('success', 'Вы успешно авторизовались!');

        } catch (\Exception $e) {
            return redirect('/register')->with('error', 'Ошибка авторизации: ' . $e->getMessage());
        }
    }

    /**
     * Обработка Telegram Login Widget callback
     */
    private function handleTelegramCallback(Request $request)
    {
        // Логируем входящие данные для отладки
        \Log::info('Telegram callback received', $request->all());

        // Получаем данные от Telegram
        $data = $request->only(['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date', 'hash']);

        // Проверяем подлинность данных от Telegram
        if (!$this->verifyTelegramAuth($data)) {
            \Log::error('Telegram auth verification failed', $data);
            return redirect('/register')->with('error', 'Неверная подпись данных от Telegram');
        }

        \Log::info('Telegram auth verification passed');

        // Формируем данные пользователя
        $telegramId = $data['id'];
        $name = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
        $username = $data['username'] ?? 'user' . $telegramId;
        $avatar = $data['photo_url'] ?? null;

        // Ищем или создаем пользователя
        $user = User::where('provider', 'telegram')
                    ->where('provider_id', $telegramId)
                    ->first();

        if (!$user) {
            $user = User::create([
                'name'        => $name ?: $username,
                'email'       => 'telegram_' . $telegramId . '@telegram.local',
                'provider'    => 'telegram',
                'provider_id' => $telegramId,
                'avatar'      => $avatar,
                'balance'     => 0,
            ]);
        } else {
            $user->update([
                'name'   => $name ?: $username,
                'avatar' => $avatar,
            ]);
        }

        // Авторизуем пользователя
        Auth::login($user, true);

        return redirect('/profile')->with('success', 'Вы успешно авторизовались через Telegram!');
    }

    /**
     * Проверка подлинности данных от Telegram
     */
    private function verifyTelegramAuth(array $data): bool
    {
        $checkHash = $data['hash'] ?? '';
        unset($data['hash']);

        $dataCheckArr = [];
        foreach ($data as $key => $value) {
            $dataCheckArr[] = $key . '=' . $value;
        }
        sort($dataCheckArr);

        $dataCheckString = implode("\n", $dataCheckArr);
        $secretKey = hash('sha256', config('services.telegram.bot_token'), true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return strcmp($hash, $checkHash) === 0;
    }

    /**
     * Выход из системы
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Вы вышли из системы');
    }

    /**
     * Страница регистрации/входа
     */
    public function showRegister()
    {
        // Если пользователь уже авторизован, редирект на профиль
        if (Auth::check()) {
            return redirect('/profile');
        }

        return view('register');
    }

    /**
     * Страница профиля
     */
    public function showProfile()
    {
        if (!Auth::check()) {
            return redirect('/register')->with('message', 'Пожалуйста, авторизуйтесь для доступа к профилю');
        }

        $user = Auth::user();

        // Загружаем последние 10 заказов пользователя
        $orders = $user->orders()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('profile', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    /**
     * Обновление профиля
     */
    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/register');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $validated['name']
        ]);

        return redirect('/profile')->with('success', 'Профиль успешно обновлен!');
    }
}
