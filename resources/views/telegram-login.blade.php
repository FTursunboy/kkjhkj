<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход через Telegram | Lynx</title>
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
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid #2AABEE;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            <a href="/" class="text-2xl font-bold gradient-text">Lynx</a>
            <a href="/register" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 hover:bg-white/10 text-text-secondary hover:text-text-primary transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>
    </header>
    
    <!-- Auth Container -->
    <div class="relative z-10 w-full max-w-md px-4">
        <div class="auth-card rounded-3xl p-8 md:p-10 border border-white/[0.05] glow-green">
            
            <!-- Logo Icon -->
            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-600/10 flex items-center justify-center border border-blue-500/20 float-animation">
                    <svg class="w-10 h-10 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 11.944 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-3">Вход через Telegram</h1>
                <p class="text-text-secondary text-sm">
                    Нажмите кнопку ниже для входа через ваш Telegram аккаунт
                </p>
            </div>

            <!-- Telegram Login Widget Container -->
            <div id="telegram-login-container" class="flex justify-center mb-6">
                <div class="spinner"></div>
            </div>

            <!-- Alternative -->
            <div class="flex items-center gap-4 my-6">
                <div class="flex-1 h-px bg-white/10"></div>
                <span class="text-text-secondary text-xs uppercase tracking-wider">или</span>
                <div class="flex-1 h-px bg-white/10"></div>
            </div>

            <a href="/register" class="w-full bg-white/5 hover:bg-white/10 text-white font-medium py-4 px-6 rounded-2xl flex items-center justify-center gap-2 transition-all border border-white/10 hover:border-white/20">
                <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Вернуться назад
            </a>
        </div>
        
    <!-- Bottom Text -->
    <p class="text-center text-text-secondary text-sm mt-6">
        Возникли проблемы? <a href="https://t.me/AND_2545" target="_blank" class="text-accent-green hover:underline focus:outline-none focus:ring-0">Напишите нам</a>
    </p>
</div>

@include('components.footer')

<!-- Telegram Widget Script -->
    <script async src="https://telegram.org/js/telegram-widget.js?22"
        data-telegram-login="{{ $bot_username }}"
        data-size="large"
        data-radius="12"
        data-auth-url="{{ $callback_url }}"
        data-request-access="write"
        data-onauth="onTelegramAuth(user)">
    </script>

    <script>
        // Убираем спиннер после загрузки виджета
        setTimeout(() => {
            const spinner = document.querySelector('.spinner');
            if (spinner) {
                spinner.remove();
            }
        }, 2000);

        // Callback функция (не обязательна, но можно использовать для отладки)
        function onTelegramAuth(user) {
            console.log('Telegram auth success:', user);
        }
    </script>
</body>
</html>

