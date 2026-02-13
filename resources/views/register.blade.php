<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация | Platinow</title>
    <meta name="theme-color" content="#1A1A1A">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-bg': '#0E0E0E',
                        'surface': '#1A1A1A',
                        'text-primary': '#FFFFFF',
                        'text-secondary': '#BFBFBF',
                        'accent-green': '#00ff88',
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <style>
        .auth-card {
            background: linear-gradient(145deg, rgba(26, 26, 26, 0.95) 0%, rgba(15, 15, 15, 0.98) 100%);
            backdrop-filter: blur(20px);
        }
        .glow-green {
            box-shadow: 0 0 60px rgba(0, 255, 136, 0.1);
        }
        .btn-google {
            background: linear-gradient(135deg, #4285F4 0%, #34A853 50%, #FBBC05 75%, #EA4335 100%);
            background-size: 300% 300%;
            animation: gradientShift 5s ease infinite;
        }
        .btn-telegram {
            background: linear-gradient(135deg, #2AABEE 0%, #229ED9 100%);
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            animation: slideIn 0.3s ease-out;
        }
        .alert-success {
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
            color: #00ff88;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="font-inter flex items-center justify-center min-h-screen bg-primary-bg overflow-hidden">

    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-accent-green/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-blue-500/5 rounded-full blur-[100px]"></div>
    </div>

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 h-[60px] z-50 bg-transparent">
        <div class="container mx-auto px-4 h-full flex items-center justify-between max-w-7xl">
            <a href="/" class="text-2xl font-bold gradient-text">Platinow</a>
            <a href="/" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/10 text-text-secondary hover:text-text-primary transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>
    </header>

    <!-- Auth Container -->
    <div class="relative z-10 w-full max-w-md px-4">
        <div class="auth-card rounded-3xl p-8 md:p-10 border border-white/[0.05] glow-green">



            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Logo Icon -->
            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-accent-green/20 to-emerald-500/10 flex items-center justify-center border border-accent-green/20 float-animation">
                    <svg class="w-10 h-10 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-3">Добро пожаловать</h1>
                <p class="text-text-secondary text-sm">
                    Войдите, чтобы получить доступ к покупкам и управлению аккаунтом
                </p>
            </div>

            <!-- Auth Buttons -->
            <div class="space-y-4">
                <!-- Telegram Widget -->
                <div class="w-full flex justify-center py-2">
                    <script async src="https://telegram.org/js/telegram-widget.js?22"
                        data-telegram-login="{{ config('services.telegram.bot_username') }}"
                        data-size="large"
                        data-radius="12"
                        data-auth-url="{{ config('services.telegram.redirect') }}"
                        data-request-access="write">
                    </script>
                </div>

                <a href="{{ route('auth.redirect', 'google') }}" class="w-full bg-white hover:bg-gray-100 text-gray-800 font-semibold py-4 px-6 rounded-2xl flex items-center justify-center gap-3 transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                    <svg class="w-6 h-6" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Войти через Google
                </a>
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-4 my-8">
                <div class="flex-1 h-px bg-white/10"></div>
                <span class="text-text-secondary text-xs uppercase tracking-wider">или</span>
                <div class="flex-1 h-px bg-white/10"></div>
            </div>

            <!-- Continue as Guest -->
            <a href="/" class="w-full bg-white/5 hover:bg-white/10 text-white font-medium py-4 px-6 rounded-2xl flex items-center justify-center gap-2 transition-all border border-white/10 hover:border-white/20">
                <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                Продолжить как гость
            </a>

            <!-- Footer Links -->
            <div class="mt-8 text-center">
                <p class="text-text-secondary text-xs">
                    Авторизуясь, вы соглашаетесь с
                    <span class="text-accent-green">условиями использования</span>
                </p>
            </div>
        </div>

    <!-- Bottom Text -->
    <p class="text-center text-text-secondary text-sm mt-6">
        Возникли проблемы? <a href="https://t.me/AND_2545" target="_blank" class="text-accent-green hover:underline focus:outline-none focus:ring-0">Напишите нам</a>
    </p>
</div>

</body>
</html>
