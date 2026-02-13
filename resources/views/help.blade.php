<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="{{ asset('/') }}">
    <title>Помощь | Platinow</title>
    <meta name="description" content="Часто задаваемые вопросы и поддержка Platinow.">
    <meta name="theme-color" content="#1A1A1A">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:url" content="{{ url()->current() }}">

    
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
                        'accent-purple': '#00ff88',
                        'accent-violet': '#00cc66',
                        'accent-pink': '#33ff99',
                        'accent-blue': '#00ff88',
                        'accent-cyan': '#00ffaa',
                        'accent-green': '#00ff88',
                    },
                    backgroundImage: {
                        'gradient-primary': 'linear-gradient(135deg, #00ff88 0%, #00cc66 100%)',
                        'gradient-accent': 'linear-gradient(135deg, #00ff88 0%, #00ffaa 100%)',
                        'gradient-warm': 'linear-gradient(135deg, #00ff88 0%, #33ff99 100%)',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <script src="{{ asset('data-manager.js') }}"></script>
    <style>
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #1A1A1A;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            z-index: 10;
            max-height: 300px;
            overflow-y: auto;
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .search-results::-webkit-scrollbar {
            display: none;
        }
        .search-result-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #FFFFFF;
            text-decoration: none;
            transition: background-color 0.2s;
            gap: 0.75rem;
        }
        .search-result-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        input[type="search"]::-webkit-search-cancel-button {
            -webkit-appearance: none;
            appearance: none;
        }
    </style>
</head>
<body class="font-inter">
    
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 glass-strong h-[60px] z-50">
        <div class="container mx-auto px-4 h-full flex items-center justify-between max-w-7xl">
            <a href="/" class="text-2xl font-bold gradient-text">Platinow</a>
            
            <div class="relative flex-1 md:max-w-xl flex justify-center px-2 md:px-4">
                <div class="relative w-4/5 md:w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="search" placeholder="Поиск" class="header-search-input md:hidden w-full bg-transparent border border-gray-600/60 rounded-lg py-2 pl-9 pr-3 text-base text-text-primary placeholder-text-secondary focus:outline-none transition-custom">
                    <input type="search" placeholder="Поиск игр и приложений" class="header-search-input hidden md:block w-full bg-transparent border border-gray-600/60 rounded-lg py-2 pl-10 pr-4 text-base text-text-primary placeholder-text-secondary focus:outline-none transition-custom">
                </div>
            </div>

            <nav class="flex items-center space-x-2 md:space-x-4">
                <a href="/top-up" class="flex items-center justify-center text-text-primary hover:opacity-70 transition-custom border border-gray-600/60 rounded-lg px-5 py-2 text-xs md:text-base md:px-4 md:py-2">
                    <span class="user-balance whitespace-nowrap">{{ auth()->check() ? number_format(auth()->user()->balance, 0, ',', ' ') . ' ₽' : '0 ₽' }}</span>
                    <div class="w-5 h-5 ml-2 rounded-full flex items-center justify-center border border-gray-600/60">
                        <svg class="w-3 h-3 text-accent-purple" style="filter: drop-shadow(0 0 2px #00ff88);" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                </a>
                <a href="/profile" class="hidden md:inline-block text-text-primary hover:opacity-70 transition-custom border border-gray-600/60 rounded-lg md:text-base md:px-4 md:py-2">Профиль</a>
            </nav>
        </div>
    </header>
    
    <!-- Main -->
    <main class="pt-[80px] pb-20 md:pb-8 px-4">
        <div class="container mx-auto max-w-4xl">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4">
                <span class="gradient-text">Помощь</span>
            </h1>
            <p class="text-text-secondary text-xl mb-12">Ответы на частые вопросы</p>
            
            <div class="space-y-4">
                <div class="bg-surface rounded-xl overflow-hidden glass scroll-reveal">
                    <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex justify-between items-center hover:bg-opacity-80 transition-custom">
                        <h3 class="text-xl font-bold">Как купить внутриигровую валюту?</h3>
                        <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-text-secondary">
                            1. Выберите нужную игру в каталоге<br>
                            2. Выберите пакет валюты<br>
                            3. Добавьте в корзину<br>
                            4. Оформите заказ и оплатите
                        </p>
                    </div>
                </div>
                
                <div class="bg-surface rounded-xl overflow-hidden glass scroll-reveal">
                    <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex justify-between items-center hover:bg-opacity-80 transition-custom">
                        <h3 class="text-xl font-bold">Как долго идёт зачисление?</h3>
                        <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-text-secondary text-base">
                            Зачисление происходит мгновенно после подтверждения оплаты.
                        </p>
                    </div>
                </div>
                
                <div class="bg-surface rounded-xl overflow-hidden glass scroll-reveal">
                    <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex justify-between items-center hover:bg-opacity-80 transition-custom">
                        <h3 class="text-xl font-bold">Безопасно ли покупать здесь?</h3>
                        <svg class="w-6 h-6 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-content hidden px-6 pb-6">
                        <p class="text-text-secondary">
                            Да, абсолютно безопасно. Мы используем защищённые каналы связи (SSL/TLS).
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 glass-gradient rounded-2xl p-10 text-center scroll-reveal-scale">
                <h2 class="text-3xl font-bold mb-4">Не нашли ответ?</h2>
                <p class="text-text-secondary text-lg mb-8">Свяжитесь с нашей службой поддержки</p>
                <a href="/contacts" class="btn-gradient px-8 py-4 rounded-xl font-bold inline-block ripple">
                    <span>Связаться с нами</span>
                </a>
            </div>
        </div>
    </main>
    
    <!-- Mobile Nav -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-surface h-16 z-40 flex items-center justify-around mobile-nav-border">
        <!-- Главная -->
        <a href="/" class="mobile-nav-item flex flex-col items-center justify-center px-4 py-1 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10 0h3a1 1 0 001-1V10M9 21h6" />
            </svg>
            <span class="text-xs font-medium">Главная</span>
        </a>
        <!-- Пополнить -->
        <a href="/top-up" class="mobile-nav-item flex flex-col items-center justify-center px-4 py-1 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="text-xs font-medium">Пополнить</span>
        </a>
        <!-- Профиль -->
        <a href="/profile" class="mobile-nav-item flex flex-col items-center justify-center px-4 py-1 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs font-medium">Профиль</span>
        </a>
    </nav>
    
    <script src="{{ asset('common.js') }}"></script>
    <script src="{{ asset('live-search.js') }}"></script>
</body>
</html>

