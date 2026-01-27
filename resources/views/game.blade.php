<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{ asset('/') }}">
    <title>{{ $initialGame['name'] ?? 'Игра' }} | Lynx</title>
    <meta name="description" content="Подробная информация об игре и покупка валюты.">
    <meta name="theme-color" content="#1A1A1A">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- User Data (for JavaScript) -->
    @php
        $userData = auth()->check() ? [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'balance' => auth()->user()->balance,
            'avatar' => auth()->user()->avatar,
        ] : null;
    @endphp
    <script>
        window.__USER__ = @json($userData);
        console.log('✅ User data loaded:', window.__USER__);
    </script>

    
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
            <a href="/" class="text-2xl font-bold gradient-text">Lynx</a>
            
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
                    <span class="user-balance whitespace-nowrap">1500 ₽</span>
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
        <div class="container mx-auto max-w-7xl">
            <a href="/" class="mb-8 flex items-center gap-2 text-text-secondary hover:text-text-primary transition-custom inline-flex outline-none border-none focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Назад
            </a>
            
            <div class="flex justify-start">
                <!-- Game Info (скрыт, чтобы на странице не было верхнего блока с иконкой и описанием) -->
                <div class="hidden">
                    <div id="gameIcon" class="bg-surface rounded-lg p-8 mb-6 flex items-center justify-center">
                        <!-- Иконка игры будет загружена -->
                    </div>
                    <h1 id="gameName" class="text-4xl md:text-5xl font-semibold mb-4">Загрузка...</h1>
                    <p id="gameDescription" class="text-text-secondary text-lg mb-6">
                        Загрузка описания...
                    </p>
                    
                    <!-- Game Details -->
                    <div class="bg-surface rounded-lg p-6 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Жанр:</span>
                            <span id="gameGenre" class="font-semibold">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Платформа:</span>
                            <span id="gamePlatform" class="font-semibold">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Издатель:</span>
                            <span id="gamePublisher" class="font-semibold">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-secondary">Валюта:</span>
                            <span id="gameCurrency" class="font-semibold">-</span>
                        </div>
                    </div>
                </div>
                
                <!-- Packages + форма покупки (расширили общий контейнер, чтобы карточки были шире) -->
                <div class="w-full max-w-5xl">
                    <!-- Categories Tabs -->
                    <div class="flex items-center gap-6 mb-6 border-b border-white/5">
                        <button onclick="selectCategoryTab(this, 'main')" style="outline: none !important; box-shadow: none !important; -webkit-tap-highlight-color: transparent;" class="category-tab pb-3 text-base font-bold text-white relative outline-none focus:outline-none ring-0 focus:ring-0 border-none">
                            <span id="category-main-name">Наборы</span>
                            <div class="active-indicator absolute bottom-0 left-0 right-0 h-[3px] bg-white rounded-t-full shadow-[0_0_12px_rgba(255,255,255,0.3)]"></div>
                        </button>
                        <button onclick="selectCategoryTab(this, 'pass')" style="outline: none !important; box-shadow: none !important; -webkit-tap-highlight-color: transparent;" class="category-tab pb-3 text-base font-bold text-text-secondary hover:text-white transition-colors relative outline-none focus:outline-none ring-0 focus:ring-0 border-none">
                            <span>Другое</span>
                            <div class="active-indicator absolute bottom-0 left-0 right-0 h-[3px] bg-white rounded-t-full shadow-[0_0_12px_rgba(255,255,255,0.3)] hidden"></div>
                        </button>
                    </div>
                    
                    <!-- На десктопе: слева пакеты, справа форма Player ID + кнопка "Купить" -->
                    <div class="md:flex md:items-start md:gap-10">
                        <!-- Левая колонка: все пакеты -->
                        <div class="md:flex-1">
                            <div id="packagesContainer">
                                <!-- Карточки будут загружены через JavaScript -->
                            </div>
                        </div>

                        <!-- Правая колонка: форма Player ID и кнопка.
                             На десктопе блок справа, на мобильной версии он отображается под карточками пакетов. -->
                        <div class="mt-8 md:mt-0 md:w-1/3">
                            <div class="bg-surface rounded-lg p-4 flex flex-col h-full md:sticky md:top-[80px]">
                                <!-- Main Content -->
                                <div class="flex-grow">
                                    <!-- Header -->
                                    <div class="flex items-center gap-3 mb-4">
                                        <div id="payment-details-icon" class="w-14 h-14 bg-white/5 rounded-lg flex items-center justify-center p-1 shrink-0">
                                            <!-- Icon -->
                                        </div>
                                        <div>
                                            <p id="payment-details-game-name" class="text-text-secondary text-xs">PUBG Mobile</p>
                                            <h2 id="payment-details-package-name" class="text-lg font-bold">-</h2>
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="mb-4">
                                        <h3 class="text-sm font-semibold mb-2 text-text-secondary">Способ оплаты</h3>
                                        <div class="grid grid-cols-3 gap-2" id="payment-methods">
                                            <button data-method="balance" class="payment-method-btn bg-white/5 rounded-md py-3 text-center text-sm font-semibold transition-colors h-12">
                                                С баланса
                                            </button>
                                            <button data-method="sbp" class="payment-method-btn bg-white/5 rounded-md py-3 text-center text-sm font-semibold transition-colors h-12">СБП</button>
                                            <button data-method="card" class="payment-method-btn bg-white/5 rounded-md py-3 text-center text-sm font-semibold transition-colors h-12">Крипта</button>
                                        </div>
                                    </div>

                                    <!-- Promocode -->
                                    <div class="relative mb-4">
                                        <input type="text" placeholder="Промокод" class="w-full bg-white/5 rounded-lg py-3 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-white/50">
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 bg-white/20 rounded-full flex items-center justify-center text-sm font-bold text-white">i</div>
                                    </div>

                                    <!-- Price Summary -->
                                    <div class="space-y-2 border-t border-b border-white/10 py-3 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-text-secondary">Цена</span>
                                            <span id="payment-details-price">- ₽</span>
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="flex justify-between font-bold text-lg items-center mb-4">
                                        <span>Итого</span>
                                        <span id="payment-details-total">- ₽</span>
                                    </div>
                                    
                                    <!-- Player ID -->
                                    <div class="mb-4">
                                        <label for="playerId" class="block text-sm font-medium text-text-secondary mb-2">Player ID</label>
                                        <div class="relative">
                                            <input
                                                type="text"
                                                id="playerId"
                                                placeholder="Введите ваш ID"
                                                class="w-full bg-transparent border border-white/20 rounded-md px-3 py-3 pr-9 text-sm focus:border-white focus:outline-none"
                                                minlength="5"
                                                inputmode="numeric"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            <!-- Иконка "i" внутри поля логина, как на промокоде -->
                                            <div
                                                id="login-info-icon"
                                                class="absolute right-[10px] top-1/2 -translate-y-1/2 w-5 h-5 bg-white/20 rounded-full flex items-center justify-center text-sm font-bold text-white"
                                                data-tooltip="Если на аккаунте не привязан Activision, привяжите и введите логин в поле ввода">
                                                i
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div class="mt-auto">
                                    <p class="text-[10px] text-center text-text-secondary mb-2">Нажимая "Купить", вы принимаете <a href="#" class="text-green-400 underline">Правила</a> и <a href="#" class="text-green-400 underline">Политику</a></p>
                                    <button id="purchaseBtn" disabled class="bg-white/20 text-white/50 cursor-not-allowed w-full py-4 rounded-lg font-semibold text-lg transition-colors">
                                        Купить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="mt-16">
                <div class="border-b border-white/10 mb-8">
                    <nav class="flex items-center space-x-6">
                        <button onclick="switchGameTab('reviews')" data-tab="reviews" class="filter-btn game-tab active text-sm font-semibold">
                            <span>Отзывы</span>
                        </button>
                        <button onclick="switchGameTab('faq')" data-tab="faq" class="filter-btn game-tab text-sm font-semibold">
                            <span>FAQ</span>
                        </button>
                        <button onclick="switchGameTab('instructions')" data-tab="instructions" class="filter-btn game-tab text-sm font-semibold">
                            <span>Инструкция</span>
                        </button>
                    </nav>
                </div>
                
                <!-- Reviews Tab -->
                <div id="reviewsTab" class="game-tab-content space-y-6">
                    <div id="reviewsContainer">
                        <!-- Отзывы будут загружены -->
                    </div>
                    <div id="reviewFormSection">
                        <!-- Форма отзыва будет загружена динамически -->
                    </div>
                </div>
                
                <!-- FAQ Tab -->
                <div id="faqTab" class="game-tab-content hidden">
                    <div id="faqContainer">
                        <!-- FAQ будут загружены -->
                    </div>
                </div>
                
                <!-- Instructions Tab -->
                <div id="instructionsTab" class="game-tab-content hidden">
                    <div id="instructionsContainer">
                        <!-- Инструкция будет загружена -->
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black/60 z-50 md:items-center md:justify-center">
        <!-- Modal Content -->
        <div id="paymentModalContent" class="bg-surface w-full h-full md:h-auto md:max-w-md md:rounded-2xl p-4 transform translate-y-full transition-transform duration-300 ease-in-out flex flex-col">
            <!-- Top close button -->
            <div class="flex justify-end mb-2">
                <button type="button"
                        onclick="closePaymentModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 text-white text-sm hover:bg-white/20">
                    ✕
                </button>
            </div>

            <!-- Main Content -->
            <div class="flex-grow overflow-y-auto">
                <!-- Selected Package -->
                <div class="flex items-center gap-4 mb-4">
                    <div id="modal-package-icon" class="w-14 h-14 bg-white/5 rounded-lg flex items-center justify-center p-1 shrink-0">
                        <!-- Icon -->
                    </div>
                    <div>
                        <p id="modal-game-name" class="text-text-secondary text-sm">PUBG Mobile</p>
                        <h3 id="modal-package-name" class="font-bold text-lg">-</h3>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-4">
                    <h3 class="font-semibold mb-2 text-sm">Способ оплаты</h3>
                    <div class="grid grid-cols-3 gap-2" id="modal-payment-methods">
                        <button data-method="balance" class="payment-method-btn bg-white/5 rounded-md py-3 text-center text-sm font-semibold transition-colors h-12">
                            С баланса
                        </button>
                        <button data-method="sbp" class="payment-method-btn bg-white/5 rounded-md py-3 text-center text-sm font-semibold transition-colors h-12">СБП</button>
                        <button data-method="card" class="payment-method-btn bg-white/5 rounded-md py-3 text-center text-sm font-semibold transition-colors h-12">Крипта</button>
                    </div>
                </div>

                <!-- Promocode -->
                <div class="relative mb-4">
                    <input type="text" placeholder="Промокод" class="w-full bg-white/5 rounded-lg py-3 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-white/50">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 bg-white/20 rounded-full flex items-center justify-center text-sm font-bold text-white">i</div>
                </div>

                <!-- Price Summary -->
                <div class="space-y-2 border-t border-b border-white/10 py-3 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-text-secondary">Цена</span>
                        <span id="modal-price-original">- ₽</span>
                    </div>
                </div>

                <!-- Total -->
                <div class="flex justify-between font-bold text-lg items-center mb-4">
                    <span>Итого</span>
                    <span id="modal-price-total">- ₽</span>
                </div>
                
                <!-- Player ID -->
                <div class="mb-4">
                     <label for="modal-uid" class="block text-sm font-medium text-text-secondary mb-2">Player ID</label>
                     <div class="relative">
                         <input
                             type="text"
                             id="modal-uid"
                             placeholder="Введите ваш ID"
                             class="w-full bg-transparent border border-white/20 rounded-md px-3 py-3 pr-9 text-sm focus:border-white focus:outline-none"
                             minlength="5"
                             inputmode="numeric"
                             oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                         <!-- Иконка "i" внутри поля логина в модалке -->
                         <div
                             id="modal-login-info-icon"
                             class="absolute right-[10px] top-1/2 -translate-y-1/2 w-5 h-5 bg-white/20 rounded-full flex items-center justify-center text-sm font-bold text-white"
                             data-tooltip="Если на аккаунте не привязан Activision, привяжите и введите логин в поле ввода">
                             i
                         </div>
                     </div>
                </div>
            </div>

            <!-- Footer with button and disclaimer -->
            <div class="mt-auto">
                <!-- Disclaimer -->
                <p class="text-[11px] text-center text-text-secondary mb-4">Нажимая "Купить", вы принимаете <a href="#" class="text-green-400 underline">Правила</a> и <a href="#" class="text-green-400 underline">Политику</a></p>
                
                <!-- Final Purchase Button -->
                <button id="modalPurchaseBtn" disabled class="bg-white/20 text-white/50 cursor-not-allowed w-full py-4 rounded-lg font-semibold text-lg transition-colors">
                    Купить
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Purchase Bar -->
    <div id="mobilePurchaseBar" class="md:hidden fixed bottom-16 left-0 right-0 bg-surface p-3 z-40 transform translate-y-full transition-transform duration-300" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="container mx-auto px-2">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div id="mobile-bar-icon" class="w-12 h-12 bg-white/5 rounded-lg flex items-center justify-center p-1 shrink-0">
                        <!-- Icon will be dynamically inserted here -->
                    </div>
                    <div class="overflow-hidden">
                        <h3 id="mobile-bar-package-name" class="font-bold text-white truncate">-</h3>
                        <div class="flex items-center gap-2">
                            <p id="mobile-bar-price" class="text-white font-semibold">- ₽</p>
                            <span id="mobile-bar-discount" class="bg-pink-500/20 text-pink-400 text-xs px-1.5 py-0.5 rounded-md font-semibold hidden">-10%</span>
                        </div>
                    </div>
                </div>
                <button onclick="openPaymentModal()" class="bg-accent-green text-primary-bg font-semibold px-5 py-3 rounded-lg text-sm whitespace-nowrap">
                    Оплатить
                </button>
            </div>
        </div>
    </div>
    
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
    
    @include('components.footer')

    <script src="{{ asset('common.js') }}"></script>
    <script src="{{ asset('live-search.js') }}"></script>
    @if(isset($initialGame))
    <script>
        window.__GAME__ = {!! json_encode($initialGame, JSON_UNESCAPED_UNICODE) !!};
    </script>
    @endif
    <script>
        let currentGame = null;
        let selectedPackageIndex = 0;
        
        // Helper functions для работы с пользователем (для совместимости со старым кодом)
        function getCurrentUser() {
            return window.__USER__;
        }
        
        function getUserBalance() {
            return window.__USER__ ? window.__USER__.balance : 0;
        }
        
        function isUserRegistered() {
            return window.__USER__ !== null;
        }
        
        // Функция обновления состояния кнопок покупки
        function updatePurchaseButtonsState(method) {
            const user = window.__USER__;
            const btns = [
                document.getElementById('purchaseBtn'), 
                document.getElementById('modalPurchaseBtn')
            ];
            
            // Получаем текущую цену выбранного пакета
            const selectedPackage = currentGame?.packages[selectedPackageIndex];
            const price = selectedPackage?.price || 0;
            const userBalance = user?.balance || 0;
            
            // Отладка
            console.log('🔍 updatePurchaseButtonsState:', {
                method,
                user: user,
                price,
                userBalance,
                canBuy: userBalance >= price
            });
            
            btns.forEach(btn => {
                if (btn) {
                    if (method === 'balance' && !user) {
                        // Не авторизован - показываем кнопку входа
                        btn.textContent = 'Регистрация / Войти';
                        btn.disabled = false;
                        btn.classList.remove('bg-white/20', 'text-white/50', 'cursor-not-allowed');
                        btn.classList.add('bg-text-primary', 'text-primary-bg', 'cursor-pointer');
                    } else if (method === 'balance' && user) {
                        // Авторизован - проверяем баланс
                        btn.textContent = 'Купить';
                        
                        if (userBalance < price) {
                            // Недостаточно средств - кнопка disabled
                            console.log('❌ Недостаточно средств:', { userBalance, price, btn: btn.id });
                            btn.disabled = true;
                            btn.classList.add('bg-white/20', 'text-white/50', 'cursor-not-allowed');
                            btn.classList.remove('bg-text-primary', 'text-primary-bg', 'cursor-pointer');
                        } else {
                            // Достаточно средств - кнопка активна
                            console.log('✅ Активируем кнопку:', { userBalance, price, btn: btn.id });
                            btn.disabled = false;
                            btn.classList.remove('bg-white/20', 'text-white/50', 'cursor-not-allowed');
                            btn.classList.add('bg-text-primary', 'text-primary-bg', 'cursor-pointer');
                            console.log('✅ Классы после:', btn.className);
                        }
                    } else {
                        // Другие способы оплаты
                        btn.textContent = 'Купить';
                        const inputId = (btn.id === 'purchaseBtn') ? 'playerId' : 'modal-uid';
                        const input = document.getElementById(inputId);
                        if (input) {
                            const event = new Event('input');
                            input.dispatchEvent(event);
                        }
                    }
                }
            });
        }
        
        // Получить ID игры из URL
        function getGameIdFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            // Возвращаем ID как строку. Если ID нет, по умолчанию будет '1'
            return urlParams.get('id') || '1';
        }
        
        // Загрузка игры
        async function loadGame() {
            // Используем данные, переданные с сервера
            if (typeof window.__GAME__ !== 'undefined') {
                currentGame = window.__GAME__;
            } else {
                // Fallback на старый метод для совместимости
                await loadGamesData();
                const gameId = getGameIdFromURL();
                currentGame = getGameById(gameId);
            }
            
            if (!currentGame) {
                document.getElementById('gameName').textContent = 'Игра не найдена';
                return;
            }
            
            // Обновить заголовок страницы
            document.title = `${currentGame.name} | Lynx`;
            
            // Отобразить информацию об игре
            document.getElementById('gameIcon').innerHTML = `
                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${currentGame.icon}"/>
                </svg>
            `;
            
            document.getElementById('gameName').textContent = currentGame.name;
            document.getElementById('gameDescription').textContent = currentGame.description;
            document.getElementById('gameGenre').textContent = currentGame.genre;
            document.getElementById('gamePlatform').textContent = currentGame.platform;
            document.getElementById('gamePublisher').textContent = currentGame.publisher;
            document.getElementById('gameCurrency').textContent = currentGame.currency;
            
            const gameIdStr = String(currentGame.id);

            // Показать вкладку "Другое" для PUBG Mobile (id = 5), Brawl Stars (id = "brawl-stars"), Clash Royale (id = 17) и Arena Breakout (id = 18)
            const allCategoryTabs = document.querySelectorAll('.category-tab');
            const passTabButton = Array.from(allCategoryTabs).find(btn => {
                const span = btn.querySelector('span');
                return span && span.textContent.trim() === 'Другое';
            });
            if (passTabButton) {
                const gameId = String(currentGame.id);
                if (gameId === '5' || gameId === 'brawl-stars' || gameId === '17' || gameId === '18') {
                    passTabButton.style.display = '';
                } else {
                    passTabButton.style.display = 'none';
                }
            }

            // ============================================
            // ОСОБЫЕ НАСТРОЙКИ ДЛЯ КОНКРЕТНЫХ ИГР
            // ============================================

            // 1) Roblox (id = 7) — убираем поле Player ID полностью
            if (gameIdStr === '7') {
                // Скрываем блоки с полем Player ID на десктопе и в модалке
                const desktopInput = document.getElementById('playerId');
                const modalInput = document.getElementById('modal-uid');

                const desktopBlock = desktopInput ? desktopInput.closest('.mb-4') : null;
                if (desktopBlock) {
                    desktopBlock.style.display = 'none';
                }

                const modalBlock = modalInput ? modalInput.closest('.mb-4') : null;
                if (modalBlock) {
                    modalBlock.style.display = 'none';
                }

                // Убираем иконку "i", если она есть
                const desktopInfoIcon = document.getElementById('login-info-icon');
                if (desktopInfoIcon) {
                    desktopInfoIcon.style.display = 'none';
                }
                const modalInfoIcon = document.getElementById('modal-login-info-icon');
                if (modalInfoIcon) {
                    modalInfoIcon.style.display = 'none';
                }

                // Для Roblox покупка не зависит от ввода ID — делаем кнопки активными
                const desktopPurchaseBtn = document.getElementById('purchaseBtn');
                if (desktopPurchaseBtn) {
                    desktopPurchaseBtn.disabled = false;
                    desktopPurchaseBtn.classList.remove('bg-white/20', 'text-white/50');
                    desktopPurchaseBtn.classList.add('bg-text-primary', 'text-primary-bg');
                }

                const modalPurchaseButton = document.getElementById('modalPurchaseBtn');
                if (modalPurchaseButton) {
                    modalPurchaseButton.disabled = false;
                    modalPurchaseButton.classList.remove('bg-white/20', 'text-white/50');
                    modalPurchaseButton.classList.add('bg-text-primary', 'text-primary-bg');
                }

            // 2) Call of Duty Mobile (id = 13) – настраиваем поля ID под игру
            // и оставляем подсказку "i". Для всех остальных игр подсказку скрываем.
            } else if (gameIdStr === '13') {
                const desktopLabel = document.querySelector('label[for="playerId"]');
                if (desktopLabel) {
                    desktopLabel.textContent = '';
                }
                const modalLabel = document.querySelector('label[for="modal-uid"]');
                if (modalLabel) {
                    modalLabel.textContent = '';
                }
                
                // Меняем подсказку в поле ввода на "Логин Activision"
                const desktopInput = document.getElementById('playerId');
                if (desktopInput) {
                    desktopInput.placeholder = 'Логин Activision';
                }
                const modalInput = document.getElementById('modal-uid');
                if (modalInput) {
                    modalInput.placeholder = 'Логин Activision';
                }

                // Добавляем дополнительное поле "Пароль" ПОД основным полем.
                // Делаем это только для Call of Duty, чтобы не ломать остальные игры.
                // Контейнер для логина (вся обёртка блока, чтобы иконка "i" оставалась по центру логина)
                const desktopBlock = desktopInput ? desktopInput.closest('.mb-4') : null;
                if (desktopBlock && !document.getElementById('codPassword')) {
                    const passwordWrapper = document.createElement('div');
                    // Делаем маленький отступ сверху, чтобы глазок был почти прямо под подсказкой "i"
                    // и на мобильной, и на десктопной версии.
                    passwordWrapper.className = 'mt-1';
                    // Добавляем поле пароля и кнопку-глаз для показа / скрытия.
                    passwordWrapper.innerHTML = `
                        <label for="codPassword" class="block text-sm font-medium text-text-secondary mb-2"></label>
                        <div class="relative">
                            <input
                                type="password"
                                id="codPassword"
                                placeholder="Пароль"
                                class="w-full bg-transparent border border-white/20 rounded-md px-3 py-3 pr-9 text-sm focus:border-white focus:outline-none">
                            <!-- Кнопка-глаз с SVG, чтобы на телефоне и десктопе иконка была одинаковой -->
                            <button
                                type="button"
                                id="codPasswordToggle"
                                class="password-eye-btn absolute right-[6px] top-1/2 -translate-y-1/2 text-base text-text-secondary hover:text-text-primary transition-colors p-1 border-none focus:outline-none"
                                aria-label="Показать или скрыть пароль"
                            >
                                <!-- Открытый глаз (по умолчанию) -->
                                <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z"/>
                                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                                </svg>
                                <!-- Зачёркнутый глаз (когда пароль показан) -->
                                <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- Зачёркнутый глаз (eye-off) с корректным path, чтобы иконка не обрезалась -->
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.5a10.523 10.523 0 01-4.293 5.307M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"
                                    />
                                </svg>
                            </button>
                        </div>
                    `;
                    desktopBlock.appendChild(passwordWrapper);
                }

                const modalBlock = modalInput ? modalInput.closest('.mb-4') : null;
                if (modalBlock && !document.getElementById('codPasswordModal')) {
                    const modalPasswordWrapper = document.createElement('div');
                    // То же расстояние в модалке, чтобы глазок был прямо под подсказкой.
                    modalPasswordWrapper.className = 'mt-1';
                    modalPasswordWrapper.innerHTML = `
                        <label for="codPasswordModal" class="block text-sm font-medium text-text-secondary mb-2"></label>
                        <div class="relative">
                            <input
                                type="password"
                                id="codPasswordModal"
                                placeholder="Пароль"
                                class="w-full bg-transparent border border-white/20 rounded-md px-3 py-3 pr-9 text-sm focus:border-white focus:outline-none">
                            <!-- Та же SVG-иконка глаза для модального окна -->
                            <button
                                type="button"
                                id="codPasswordModalToggle"
                                class="password-eye-btn absolute right-[6px] top-1/2 -translate-y-1/2 text-base text-text-secondary hover:text-text-primary transition-colors p-1 border-none focus:outline-none"
                                aria-label="Показать или скрыть пароль"
                            >
                                <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z"/>
                                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                                </svg>
                                <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- Зачёркнутый глаз (eye-off) для модалки с корректным path -->
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.5a10.523 10.523 0 01-4.293 5.307M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"
                                    />
                                </svg>
                            </button>
                        </div>
                    `;
                    modalBlock.appendChild(modalPasswordWrapper);
                }

                // Простая логика показа / скрытия пароля + переключение SVG‑иконок глаза.
                const codPasswordInput = document.getElementById('codPassword');
                const codPasswordToggle = document.getElementById('codPasswordToggle');
                if (codPasswordInput && codPasswordToggle) {
                    const openIcon = codPasswordToggle.querySelector('.eye-open');
                    const closedIcon = codPasswordToggle.querySelector('.eye-closed');
                    codPasswordToggle.addEventListener('click', () => {
                        // isHidden описывает состояние ДО клика.
                        const isHidden = codPasswordInput.type === 'password';
                        // Переключаем тип поля.
                        codPasswordInput.type = isHidden ? 'text' : 'password';
                        if (openIcon && closedIcon) {
                            // Делаем НАОБОРОТ относительно старой логики:
                            // теперь при показанном пароле (text) показываем обычный глаз,
                            // а при скрытом пароле (password) — зачёркнутый.
                            openIcon.classList.toggle('hidden', !isHidden);
                            closedIcon.classList.toggle('hidden', isHidden);
                        }
                    });
                }

                const codPasswordModalInput = document.getElementById('codPasswordModal');
                const codPasswordModalToggle = document.getElementById('codPasswordModalToggle');
                if (codPasswordModalInput && codPasswordModalToggle) {
                    const openIconModal = codPasswordModalToggle.querySelector('.eye-open');
                    const closedIconModal = codPasswordModalToggle.querySelector('.eye-closed');
                    codPasswordModalToggle.addEventListener('click', () => {
                        // Аналогично для модального поля: используем состояние ДО клика
                        // и инвертируем отображение иконок.
                        const isHidden = codPasswordModalInput.type === 'password';
                        codPasswordModalInput.type = isHidden ? 'text' : 'password';
                        if (openIconModal && closedIconModal) {
                            openIconModal.classList.toggle('hidden', !isHidden);
                            closedIconModal.classList.toggle('hidden', isHidden);
                        }
                    });
                }

            } else {
                // Для всех других игр убираем подсказку "i" из полей ID,
                // чтобы она была только у карточки Call of Duty Mobile.
                const desktopInfoIcon = document.getElementById('login-info-icon');
                if (desktopInfoIcon) {
                    desktopInfoIcon.style.display = 'none';
                }
                const modalInfoIcon = document.getElementById('modal-login-info-icon');
                if (modalInfoIcon) {
                    modalInfoIcon.style.display = 'none';
                }
            }
            
            // Обновляем категорию и количество
            const mainCategoryName = document.getElementById('category-main-name');
            const mainCategoryCount = document.getElementById('category-main-count');
            if (mainCategoryName) mainCategoryName.textContent = currentGame.currency || 'Наборы';
            if (mainCategoryCount) mainCategoryCount.textContent = currentGame.packages ? currentGame.packages.length : 0;

            // Отобразить пакеты
            renderPackages('main');
            
            // Отобразить отзывы
            displayReviews();
            
            // Отобразить FAQ
            displayFAQ();
            
            // Отобразить инструкцию
            displayInstructions();
        }
        
        // Логика переключения вкладок категорий
        function selectCategoryTab(button, category) {
            // Обновляем визуальное состояние
            document.querySelectorAll('.category-tab').forEach(btn => {
                btn.classList.remove('text-white');
                btn.classList.add('text-text-secondary');
                
                const indicator = btn.querySelector('.active-indicator');
                if (indicator) indicator.classList.add('hidden');
            });
            
            button.classList.remove('text-text-secondary');
            button.classList.add('text-white');
            
            const indicator = button.querySelector('.active-indicator');
            if (indicator) indicator.classList.remove('hidden');
            
            renderPackages(category);
        }

        // Обновление блока с деталями платежа
        function updatePaymentDetails(pkg, game, selectedCard) {
            if (!pkg || !game || !selectedCard) return;

            // Обновить иконку - ищем в разных возможных контейнерах
            let packageIconContainer = selectedCard.querySelector('.w-10.h-10') || 
                                       selectedCard.querySelector('.w-16.h-16') || 
                                       selectedCard.querySelector('.w-20.h-20') ||
                                       selectedCard.querySelector('img') ||
                                       selectedCard.querySelector('svg');
            
            if (packageIconContainer) {
                // Если это изображение, копируем его
                if (packageIconContainer.tagName === 'IMG') {
                    document.getElementById('payment-details-icon').innerHTML = `<img src="${packageIconContainer.src}" alt="${packageIconContainer.alt}" class="w-full h-full object-cover">`;
                } else {
                    // Если это контейнер с иконкой, копируем его содержимое
                    document.getElementById('payment-details-icon').innerHTML = packageIconContainer.innerHTML || packageIconContainer.outerHTML;
                }
            }

            // Обновить текст
            document.getElementById('payment-details-game-name').textContent = game.name;
            
            // Проверяем, есть ли текст под карточкой (для пропусков и подписок)
            let packageName = pkg.name.split('+')[0].trim();
            const passCardContainer = selectedCard.closest('.pass-card-with-text');
            if (passCardContainer) {
                const passTextElement = passCardContainer.querySelector('.pass-text p');
                if (passTextElement) {
                    const textContent = passTextElement.textContent.trim();
                    // Для Clash Royale в разделе "Битва популярности" показываем "Pass Royale"
                    if (String(game.id) === '17' && textContent === 'Pass Royale') {
                        packageName = 'Pass Royale';
                    } else if (textContent.includes('Премиум')) {
                        // Для подписок берем полный текст из описания карточки
                        packageName = textContent;
                    } else {
                        // Для других случаев (например, пропуски) используем текст из карточки
                        packageName = textContent;
                    }
                }
            }
            
            document.getElementById('payment-details-package-name').textContent = packageName;

            // Обновить цены (пока без скидок, просто прямая цена)
            const price = pkg.price;
            document.getElementById('payment-details-price').textContent = `${price} ₽`;
            document.getElementById('payment-details-total').textContent = `${price} ₽`;
            
            // Обновить состояние кнопки покупки в зависимости от баланса
            const activeMethodBtn = document.querySelector('#payment-methods .payment-method-btn.ring-1');
            const method = activeMethodBtn ? activeMethodBtn.dataset.method : 'sbp';
            updatePurchaseButtonsState(method);
        }

        // Данные для вкладки Пропуск (теперь Другое) - разделы по играм
        const passPackagesDataByGame = {
            // Разделы по умолчанию (для PUBG Mobile и других игр)
            'default': [
                {
                    title: "Наборы",
                    alert: "Доступно к покупке один раз на аккаунт",
                    items: [
                        { name: "Набор первой покупки", price: 102, icon: "set_1" },
                        { name: "Набор материалов для улучшаемого оружия", price: 294, icon: "set_2" },
                        { name: "Набор мифических эмблем", price: 472, icon: "set_3" }
                    ]
                },
                {
                    title: "Подписки",
                    items: [
                        { name: "Премиум 1 месяц", price: 102 },
                        { name: "Премиум 3 месяца", price: 300 },
                        { name: "Премиум 6 месяцев", price: 548 },
                        { name: "Премиум 12 месяцев", price: 1080 },
                        { name: "Премиум+ 1 месяц", price: 926 },
                        { name: "Премиум+ 3 месяца", price: 2648 },
                        { name: "Премиум+ 6 месяцев", price: 5305 },
                        { name: "Премиум+ 12 месяцев", price: 10517 }
                    ]
                },
                {
                    title: "Еженедельные наборы",
                    items: [
                        { name: 'Набор "Еженедельное предложение"', price: 106 },
                        { name: "Еженедельный набор мифических эмблем", price: 297 },
                        { name: 'Набор "Еженедельное предложение 2"', price: 297 }
                    ]
                },
                {
                    title: "Битва популярности",
                    items: [
                        { name: 'Cake 10.000', price: 498, discount: "-15%" },
                        { name: "Airplane 25.000", price: 996, discount: "-15%" },
                        { name: 'Helicopter 125.000', price: 4888, discount: "-15%" },
                        { name: 'Private plane 300.000', price: 9885, discount: "-15%" }
                    ]
                }
            ],
            // Разделы для Brawl Stars - только Пропуски
            'brawl-stars': [
                {
                    title: "Пропуски",
                    items: [
                        { name: "Brawl Pass", price: 169, discount: "" },
                        { name: "Brawl Pass + 10 уровней", price: 299, discount: "" },
                        { name: "Brawl Pass + 25 уровней", price: 499, discount: "" },
                        { name: "Brawl Pass + 50 уровней", price: 899, discount: "" },
                        { name: "Brawl Pass Премиум", price: 1299, discount: "" }
                    ]
                }
            ],
            // Разделы для Clash Royale - только Битва популярности
            '17': [
                {
                    title: "Битва популярности",
                    items: [
                        { name: 'Cake 10.000', price: 498, discount: "-15%" }
                    ]
                }
            ],
            // Разделы для Arena Breakout - Боевые пропуски, Сейфы, Набор
            '18': [
                {
                    title: "Боевые пропуски",
                    items: [
                        { name: "Премиальный БП", price: 299 },
                        { name: "Расширенный БП", price: 599 }
                    ]
                },
                {
                    title: "Сейфы",
                    items: [
                        { name: "Пуленепробиваемый ящик", price: 199 },
                        { name: "Композитный контейнер", price: 299 }
                    ]
                },
                {
                    title: "Набор",
                    items: [
                        { name: "Набор для начинающего", price: 399 }
                    ]
                }
            ]
        };

        // Отображение пакетов (Main или Pass)
        function renderPackages(category) {
            const container = document.getElementById('packagesContainer');
            if (!container) return;

            if (category === 'main') {
                // Очищаем контейнер и устанавливаем правильный класс
                container.innerHTML = '';
                container.className = "grid grid-cols-2 gap-y-4 gap-x-2 md:grid-cols-2 md:gap-y-4 md:gap-x-2";
                
                if (currentGame && currentGame.packages) {
                    container.innerHTML = currentGame.packages.map((pkg, index) => 
                        generatePackageCard(pkg, index, currentGame.id, currentGame.icon)
                    ).join('');
                    
                    attachPackageClickHandlers(currentGame.packages);
                    selectFirstPackage(currentGame.packages[0]);
                }
            } else if (category === 'pass') {
                // Для других категорий очищаем контейнер
                container.innerHTML = '';
                container.className = "space-y-8";
                
                let allPassItems = [];
                let html = `
                <style>
                    .pass-section .package-card {
                        height: auto !important;
                        min-height: 160px;
                        flex-direction: column !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                        padding: 1rem 0.5rem !important;
                        text-align: center;
                    }
                    .pass-section .package-card > div:first-child {
                        flex-direction: column !important;
                        gap: 0.75rem !important;
                        width: 100%;
                        flex-grow: 1;
                        align-items: center !important;
                        justify-content: flex-start !important;
                    }
                    .pass-section .package-card > div:first-child > div:first-child {
                        width: 56px !important;
                        height: 56px !important;
                        margin: 0 auto;
                    }
                    .pass-section .package-card > div:first-child > div:last-child {
                        font-size: 0.85rem !important;
                        line-height: 1.2;
                        white-space: normal !important;
                    }
                    .pass-section .package-card > div:last-child {
                        margin-top: 0.75rem;
                        width: 100%;
                        padding-top: 0.5rem;
                        border-top: 1px solid rgba(255,255,255,0.1);
                        font-size: 1rem !important;
                    }
                    /* Стили для карточек с изображениями (Наборы) */
                    .pass-section .package-card.has-image {
                        padding: 0 !important;
                        aspect-ratio: 1 / 1 !important;
                        width: 100% !important;
                        height: auto !important;
                        min-height: 0 !important;
                        max-height: none !important;
                    }
                    .pass-section .package-card.has-image img {
                        width: 100%;
                        height: 100%;
                        display: block;
                        object-fit: cover;
                    }
                    /* Стили для контейнера карточек пропусков с текстом */
                    .pass-section .pass-card-with-text {
                        display: flex;
                        flex-direction: column;
                    }
                    .pass-section .pass-card-with-text .package-card {
                        aspect-ratio: 1 / 1 !important;
                    }
                    .pass-section .pass-card-with-text .pass-text {
                        margin-top: 0.5rem;
                        text-align: center;
                    }
                </style>
                `;
                
                // Выбираем разделы для текущей игры или используем разделы по умолчанию
                const gameId = currentGame ? String(currentGame.id) : 'default';
                const passPackagesData = passPackagesDataByGame[gameId] || passPackagesDataByGame['default'];
                
                passPackagesData.forEach((section) => {
                    let alertHtml = '';
                    if (section.alert) {
                        alertHtml = `
                            <div class="bg-[#332200] text-[#FF9F0A] px-4 py-2.5 rounded-lg flex items-center gap-3 mb-6 text-sm font-bold">
                                <div class="w-5 h-5 rounded-full bg-[#FF9F0A] text-black flex items-center justify-center text-xs shrink-0 font-black">i</div>
                                <span>${section.alert}</span>
                            </div>
                        `;
                    }

                    html += `<div class="pass-section">
                        <h3 class="text-white font-bold text-lg mb-4 pl-1">${section.title}</h3>
                        ${alertHtml}
                        <div class="grid grid-cols-4 gap-y-4 gap-x-2 md:grid-cols-4 md:gap-y-4 md:gap-x-2">`;
                    
                    section.items.forEach((item, itemIndex) => {
                        const pkg = { 
                            name: item.name, 
                            price: item.price, 
                            amount: item.name,
                            icon: currentGame.icon
                        };
                        
                        // Для Clash Royale в разделе "Битва популярности" обновляем название на "Pass Royale"
                        if (String(currentGame.id) === '17' && section.title === "Битва популярности") {
                            pkg.name = "Pass Royale";
                            pkg.amount = "Pass Royale";
                        }
                        
                        allPassItems.push(pkg);
                        const globalIdx = allPassItems.length - 1;
                        
                        // Для раздела "Наборы" создаем карточки с изображениями
                        if (section.title === "Наборы") {
                            const imageNames = ["nabor11.jpg", "nabor22.jpg", "nabor33.jpg"];
                            const imageName = imageNames[itemIndex] || imageNames[0];
                            
                            // Создаем карточку с изображением и описанием
                            cardHtml = `
                                <div class="pass-card-with-text">
                                    <div class="package-card has-image bg-[#1A1A1A] hover:bg-[#252525] rounded-xl cursor-pointer border-2 border-transparent transition-all duration-200 relative overflow-hidden group aspect-square" 
                                         data-game-id="${currentGame.id}" 
                                         data-package-index="${globalIdx}">
                                        <img src="images/${imageName}" alt="${item.name}" class="w-full h-full object-cover rounded-xl">
                                    </div>
                                    <div class="pass-text">
                                        <p class="text-white text-sm font-medium">${item.name}</p>
                                    </div>
                                </div>
                            `;
                        } else if (section.title === "Подписки") {
                            // Для раздела "Подписки" создаем карточки с изображениями
                            const imageNames = ["priime-month.jpg", "priime--season.jpg", "priime-half.jpg", "prime-year.jpg", "priime+month.jpg", "priime+season.jpg", "priime+half.jpg", "priime+year.jpg"];
                            const imageName = imageNames[itemIndex] || imageNames[0];
                            
                            // Определяем описание на основе индекса
                            const descriptions = [
                                "1 месяц",      // Premium MONTH
                                "3 месяца",    // Premium SEASON
                                "6 месяцев",   // Premium HALF-YEAR
                                "12 месяцев",  // Premium YEAR
                                "1 месяц",     // Premium+ MONTH
                                "3 месяца",    // Premium+ SEASON
                                "6 месяцев",   // Premium+ HALF-YEAR
                                "12 месяцев"   // Premium+ YEAR
                            ];
                            const description = descriptions[itemIndex] || descriptions[0];
                            const subscriptionType = itemIndex < 4 ? "Премиум" : "Премиум+";
                            
                            // Создаем карточку с изображением и описанием
                            cardHtml = `
                                <div class="pass-card-with-text">
                                    <div class="package-card has-image bg-[#1A1A1A] hover:bg-[#252525] rounded-xl cursor-pointer border-2 border-transparent transition-all duration-200 relative overflow-hidden group aspect-square" 
                                         data-game-id="${currentGame.id}" 
                                         data-package-index="${globalIdx}">
                                        <img src="images/${imageName}" alt="${item.name}" class="w-full h-full object-cover rounded-xl">
                                    </div>
                                    <div class="pass-text">
                                        <p class="text-white text-sm font-medium">${subscriptionType} • ${description}</p>
                                    </div>
                                </div>
                            `;
                        } else if (section.title === "Еженедельные наборы") {
                            // Для раздела "Еженедельные наборы" создаем карточки с изображениями
                            const imageNames = ["1nabor1.jpg", "nabor3.jpg", "nabor2.jpg"];
                            const imageName = imageNames[itemIndex] || imageNames[0];
                            
                            // Создаем карточку с изображением и описанием
                            cardHtml = `
                                <div class="pass-card-with-text">
                                    <div class="package-card has-image bg-[#1A1A1A] hover:bg-[#252525] rounded-xl cursor-pointer border-2 border-transparent transition-all duration-200 relative overflow-hidden group aspect-square" 
                                         data-game-id="${currentGame.id}" 
                                         data-package-index="${globalIdx}">
                                        <img src="images/${imageName}" alt="${item.name}" class="w-full h-full object-cover rounded-xl">
                                    </div>
                                    <div class="pass-text">
                                        <p class="text-white text-sm font-medium">${item.name}</p>
                                    </div>
                                </div>
                            `;
                        } else if (section.title === "Битва популярности") {
                            // Для Clash Royale используем изображение passroyale.jpg с текстом снизу
                            if (String(currentGame.id) === '17') {
                                cardHtml = `
                                    <div class="pass-card-with-text">
                                        <div class="package-card has-image bg-[#1A1A1A] hover:bg-[#252525] rounded-xl cursor-pointer border-2 border-transparent transition-all duration-200 relative overflow-hidden group" 
                                             data-game-id="${currentGame.id}" 
                                             data-package-index="${globalIdx}">
                                            <img src="images/passroyale.jpg" alt="${item.name}" class="w-full h-full object-cover rounded-xl">
                                        </div>
                                        <div class="pass-text">
                                            <p class="text-white text-sm font-medium">Pass Royale</p>
                                        </div>
                                    </div>
                                `;
                            } else {
                                // Для других игр создаем карточки с иконкой в формате 1:1 без описания
                                const iconHtml = getPackageImageHtml(currentGame.id, item.name, currentGame.icon, globalIdx);
                                // Отключаем все карточки в этом разделе
                                const isDisabled = true;
                                const disabledClasses = 'opacity-50 pointer-events-none';
                                cardHtml = `
                                    <div class="package-card has-image bg-[#1A1A1A] ${disabledClasses} rounded-xl border-2 border-transparent transition-all duration-200 relative overflow-hidden group aspect-square flex items-center justify-center" 
                                         data-game-id="${currentGame.id}" 
                                         data-package-index="${globalIdx}"
                                         data-disabled="true">
                                        <div class="w-16 h-16 md:w-20 md:h-20 flex items-center justify-center">
                                            ${iconHtml}
                                        </div>
                                    </div>
                                `;
                            }
                        } else if (section.title === "Пропуски" || section.title === "Боевые пропуски") {
                            // Для Arena Breakout используем специальные изображения
                            if (String(currentGame.id) === '18') {
                                const imageNames = ["arenapass.jpg", "arenapass+.jpg"];
                                const imageName = imageNames[itemIndex] || imageNames[0];
                                
                                // Определяем текст для карточек Arena Breakout
                                let textContent = item.name;
                                if (itemIndex === 0) {
                                    textContent = "Премиальный БП";
                                } else if (itemIndex === 1) {
                                    textContent = "Расширенный БП";
                                }
                                
                                // Обновляем название пакета
                                pkg.name = textContent;
                                pkg.amount = textContent;
                                
                                cardHtml = `
                                    <div class="pass-card-with-text">
                                        <div class="package-card has-image bg-[#1A1A1A] hover:bg-[#252525] rounded-xl cursor-pointer border-2 border-transparent transition-all duration-200 relative overflow-hidden group aspect-square" 
                                             data-game-id="${currentGame.id}" 
                                             data-package-index="${globalIdx}">
                                            <img src="images/${imageName}" alt="${item.name}" class="w-full h-full object-cover rounded-xl">
                                        </div>
                                        <div class="pass-text">
                                            <p class="text-white text-sm font-medium">${textContent}</p>
                                        </div>
                                    </div>
                                `;
                            } else {
                                // Для раздела "Пропуски" создаем карточки с изображениями в формате 1:1 (Brawl Stars)
                                const imageNames = ["brrawlpass.jpg", "brawlpassplus.jpg", "brrawlpass.jpg", "brawlpasspluss.jpg", "brawlpproppass.jpg"];
                                const imageName = imageNames[itemIndex] || imageNames[0];
                                
                                // Для разных карточек разный текст
                                let textContent = "Brawl pas";
                                if (itemIndex === 0) {
                                    textContent = "Brawl pas (если доступна скидка на аккаунте)";
                                } else if (itemIndex === 1) {
                                    textContent = "Апгрейд Brawl Pass до Brawl Pass Plus";
                                } else if (itemIndex === 2) {
                                    textContent = "Brawl Pass";
                                } else if (itemIndex === 3) {
                                    textContent = "Brawl Pass Plus";
                                } else if (itemIndex === 4) {
                                    textContent = "Pro Pass";
                                }
                                
                                // Обновляем название пакета на текст, который показываем под карточкой
                                pkg.name = textContent;
                                pkg.amount = textContent;
                                
                                cardHtml = `
                                    <div class="pass-card-with-text">
                                        <div class="package-card has-image bg-[#1A1A1A] hover:bg-[#252525] rounded-xl cursor-pointer border-2 border-transparent transition-all duration-200 relative overflow-hidden group" 
                                             data-game-id="${currentGame.id}" 
                                             data-package-index="${globalIdx}">
                                            <img src="images/${imageName}" alt="${item.name}" class="w-full h-full object-cover rounded-xl">
                                        </div>
                                        <div class="pass-text">
                                            <p class="text-white text-sm font-medium">${textContent}</p>
                                        </div>
                                    </div>
                                `;
                            }
                        } else if (section.title === "Сейфы" && String(currentGame.id) === '18') {
                            // Для раздела "Сейфы" в Arena Breakout используем специальные изображения
                            const imageNames = ["arena2x2.jpg", "arena2x3.jpg"];
                            const imageName = imageNames[itemIndex] || imageNames[0];
                            
                            cardHtml = `
                                <div class="pass-card-with-text">
                                    <div class="package-card has-image bg-[#1A1A1A] hover:bg-[#252525] rounded-xl cursor-pointer border-2 border-transparent transition-all duration-200 relative overflow-hidden group aspect-square" 
                                         data-game-id="${currentGame.id}" 
                                         data-package-index="${globalIdx}">
                                        <img src="images/${imageName}" alt="${item.name}" class="w-full h-full object-cover rounded-xl">
                                    </div>
                                    <div class="pass-text">
                                        <p class="text-white text-sm font-medium">${item.name}</p>
                                    </div>
                                </div>
                            `;
                        } else if (section.title === "Набор" && String(currentGame.id) === '18') {
                            // Для раздела "Набор" в Arena Breakout используем специальное изображение
                            const imageName = "arenanabor.jpg";
                            
                            cardHtml = `
                                <div class="pass-card-with-text">
                                    <div class="package-card has-image bg-[#1A1A1A] hover:bg-[#252525] rounded-xl cursor-pointer border-2 border-transparent transition-all duration-200 relative overflow-hidden group aspect-square" 
                                         data-game-id="${currentGame.id}" 
                                         data-package-index="${globalIdx}">
                                        <img src="images/${imageName}" alt="${item.name}" class="w-full h-full object-cover rounded-xl">
                                    </div>
                                    <div class="pass-text">
                                        <p class="text-white text-sm font-medium">${item.name}</p>
                                    </div>
                                </div>
                            `;
                        } else {
                            // Для остальных разделов используем стандартную функцию
                            cardHtml = generatePackageCard(pkg, globalIdx, currentGame.id, currentGame.icon);
                        }
                        
                        html += cardHtml;
                    });
                    
                    html += `</div></div>`;
                });
                
                container.innerHTML = html;
                attachPackageClickHandlers(allPassItems);
            } else if (category === 'donate') {
                container.innerHTML = '<div class="text-text-secondary p-4 text-center">В этом разделе пока нет товаров</div>';
            }
        }

        function attachPackageClickHandlers(packagesList) {
            document.querySelectorAll('.package-card').forEach((card) => {
                // Пропускаем отключенные карточки
                if (card.getAttribute('data-disabled') === 'true') {
                    return;
                }
                
                card.addEventListener('click', () => {
                    document.querySelectorAll('.package-card').forEach(c => {
                        c.classList.remove('package-selected');
                    });
                    card.classList.add('package-selected');
                    
                    // Используем data-package-index вместо индекса из цикла
                    const packageIndex = parseInt(card.getAttribute('data-package-index'));
                    const selectedPackage = packagesList[packageIndex];
                    if (selectedPackage) {
                        updatePaymentDetails(selectedPackage, currentGame, card);
                        updateMobilePurchaseBar(selectedPackage, card);
                    }
                });
            });
        }

        function selectFirstPackage(pkg) {
             const firstCard = document.querySelector('.package-card');
             if (firstCard && pkg) {
                 firstCard.classList.add('package-selected');
                 updatePaymentDetails(pkg, currentGame, firstCard);
             }
        }
        
        // Проверка, может ли текущий пользователь оставить отзыв по игре
        function canUserLeaveReviewForGame(gameId) {
            if (typeof isUserRegistered !== 'function' || typeof hasPurchasedGame !== 'function') {
                return false;
            }
            return isUserRegistered() && hasPurchasedGame(gameId);
        }

        function renderReviewForm() {
            const section = document.getElementById('reviewFormSection');
            if (!section || !currentGame) return;

            // Если пользователь не зарегистрирован или не покупал игру
            if (!canUserLeaveReviewForGame(currentGame.id)) {
                section.innerHTML = `
                    <div class="bg-surface rounded-lg p-4 text-sm text-text-secondary">
                        Оставлять отзывы могут только зарегистрированные пользователи после покупки услуги.
                        <span class="block mt-1">Заполните профиль и совершите покупку, чтобы поделиться впечатлениями.</span>
                    </div>
                `;
                return;
            }

            const user = (typeof getCurrentUser === 'function') ? getCurrentUser() : null;
            const displayName = user && user.name ? user.name : 'Пользователь';

            const tagOptions = [
                'Высокая скорость',
                'Низкая комиссия',
                'Приятный интерфейс',
                'Простота пополнения',
                'Наличие промокода',
                'Надёжность',
                'Быстрая поддержка'
            ];

            section.innerHTML = `
                <div class="bg-[#151515] rounded-2xl p-4 md:p-5 border border-white/5">
                    <h3 class="text-base md:text-lg font-semibold mb-3">Оставить отзыв</h3>
                    <p class="text-xs text-text-secondary mb-4">Вы вошли как <span class="text-text-primary font-semibold">${displayName}</span>. Оцените сервис и выберите, что вам понравилось.</p>

                    <!-- Рейтинг -->
                    <div class="mb-4">
                        <p class="text-xs text-text-secondary mb-2">Ваша оценка</p>
                        <div id="reviewRatingStars" class="flex items-center gap-1.5">
                            ${[1,2,3,4,5].map(value => `
                                <button type="button" data-review-rating="${value}" class="w-7 h-7 flex items-center justify-center rounded-full bg-white/5 text-xs text-text-secondary hover:bg-white/10 transition-colors">
                                    ★
                                </button>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Макеты/теги -->
                    <div class="mb-4">
                        <p class="text-xs text-text-secondary mb-2">Что вам понравилось?</p>
                        <div id="reviewTagList" class="flex flex-wrap gap-1.5">
                            ${tagOptions.map(tag => `
                                <button type="button" data-review-tag="${tag}" class="px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[11px] md:text-xs text-text-secondary hover:text-text-primary hover:bg-white/10 transition-colors">
                                    ${tag}
                                </button>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Текст отзыва -->
                    <div class="mb-4">
                        <textarea id="reviewText" rows="3" class="w-full bg-transparent border border-white/15 rounded-lg px-3 py-2 text-sm text-text-primary placeholder-text-secondary focus:outline-none focus:border-white/40 resize-none" placeholder="Напишите пару предложений о скорости, удобстве и поддержке..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button id="submitReviewBtn" class="bg-text-primary text-primary-bg px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-colors">
                            Отправить отзыв
                        </button>
                    </div>
                </div>
            `;

            // Логика звёздочек
            const ratingContainer = document.getElementById('reviewRatingStars');
            let currentRating = 5;

            function updateStarsUI() {
                if (!ratingContainer) return;
                ratingContainer.querySelectorAll('button[data-review-rating]').forEach(btn => {
                    const value = Number(btn.getAttribute('data-review-rating'));
                    if (value <= currentRating) {
                        btn.classList.add('bg-[#00ff88]', 'text-black');
                        btn.classList.remove('bg-white/5', 'text-text-secondary');
                    } else {
                        btn.classList.add('bg-white/5', 'text-text-secondary');
                        btn.classList.remove('bg-[#00ff88]', 'text-black');
                    }
                });
            }

            if (ratingContainer) {
                ratingContainer.querySelectorAll('button[data-review-rating]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const value = Number(btn.getAttribute('data-review-rating'));
                        currentRating = value;
                        updateStarsUI();
                    });
                });
                updateStarsUI();
            }

            // Логика тегов
            const tagContainer = document.getElementById('reviewTagList');
            if (tagContainer) {
                tagContainer.querySelectorAll('button[data-review-tag]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        btn.classList.toggle('bg-white/10');
                        btn.classList.toggle('text-text-primary');
                    });
                });
            }

            // Отправка отзыва
            const submitBtn = document.getElementById('submitReviewBtn');
            const textArea = document.getElementById('reviewText');

            if (submitBtn && textArea) {
                submitBtn.addEventListener('click', () => {
                    const text = textArea.value.trim();
                    if (!text) {
                        showNotification('Пожалуйста, напишите небольшой отзыв.', 'error');
                        return;
                    }

                    const selectedTags = [];
                    if (tagContainer) {
                        tagContainer.querySelectorAll('button[data-review-tag]').forEach(btn => {
                            if (btn.classList.contains('text-text-primary')) {
                                selectedTags.push(btn.getAttribute('data-review-tag'));
                            }
                        });
                    }

                    const userData = (typeof getCurrentUser === 'function') ? getCurrentUser() : null;
                    const authorName = userData && userData.name ? userData.name : 'Пользователь';

                    const reviewPayload = {
                        author: authorName,
                        rating: currentRating,
                        date: new Date().toISOString(),
                        text,
                        tags: selectedTags,
                        gameName: currentGame.name
                    };

                    if (typeof saveUserReview === 'function') {
                        saveUserReview(currentGame.id, reviewPayload);
                    }

                    textArea.value = '';
                    showNotification('Спасибо! Ваш отзыв отправлен.', 'success');

                    // Перерисовать список отзывов с новым
                    displayReviews();
                });
            }
        }

        // Отображение отзывов
        function displayReviews() {
            const container = document.getElementById('reviewsContainer');
            
            // Если доступна глобальная функция — показываем ВСЕ отзывы с сайта
            if (typeof getAllReviewsSorted === 'function') {
                const allReviews = getAllReviewsSorted();
                if (allReviews.length > 0) {
                    container.innerHTML = allReviews.map(review =>
                        generateReviewCard(review)
                    ).join('');
                    return;
                }
            }

            // Fallback: если что-то пошло не так — показываем отзывы только текущей игры
            if (currentGame.reviews && currentGame.reviews.length > 0) {
                container.innerHTML = currentGame.reviews.map(review => 
                    generateReviewCard(review)
                ).join('');
            } else {
                container.innerHTML = '<p class="text-text-secondary">Отзывов пока нет</p>';
            }

            // После загрузки отзывов подготавливаем форму
            renderReviewForm();
        }
        
        // Отображение FAQ
        function displayFAQ() {
            const container = document.getElementById('faqContainer');
            if (currentGame.faq && currentGame.faq.length > 0) {
                container.innerHTML = currentGame.faq.map((faq, index) => 
                    generateFAQItem(faq, index)
                ).join('');
            } else {
                container.innerHTML = '<p class="text-text-secondary">FAQ пока нет</p>';
            }
        }
        
        // Отображение инструкции
        function displayInstructions() {
            const container = document.getElementById('instructionsContainer');
            if (!container) return;

            const instructions = currentGame ? currentGame.instructions : null;

            // Если для игры не задано — показываем общую инструкцию
            if (!instructions) {
                container.innerHTML = `
                    <ol class="list-decimal list-inside space-y-2 text-text-secondary">
                        <li>Выберите нужный набор и нажмите «Купить».</li>
                        <li>Введите ваш ID/логин (как указано в форме) и проверьте данные.</li>
                        <li>Выберите способ оплаты и завершите оплату.</li>
                        <li>После оплаты пополнение/доставка обычно происходит автоматически.</li>
                    </ol>
                `;
                return;
            }

            // Строка
            if (typeof instructions === 'string') {
                container.innerHTML = `<p class="text-text-secondary whitespace-pre-line">${instructions}</p>`;
                return;
            }

            // Массив шагов
            if (Array.isArray(instructions)) {
                container.innerHTML = `
                    <ol class="list-decimal list-inside space-y-2 text-text-secondary">
                        ${instructions.map(step => `<li>${step}</li>`).join('')}
                    </ol>
                `;
                return;
            }

            // Fallback
            container.innerHTML = '<p class="text-text-secondary">Инструкция не указана</p>';
        }
        
        // Добавить в корзину
        function handleAddToCart() {
            if (!currentGame) return;
            
            const success = addToCart(currentGame.id, selectedPackageIndex);
            if (success) {
                showNotification('Товар добавлен в корзину!', 'success');
            } else {
                showNotification('Ошибка добавления в корзину', 'error');
            }
        }

        // Обновление мобильной панели покупки
        function updateMobilePurchaseBar(pkg, selectedCard) {
            const bar = document.getElementById('mobilePurchaseBar');
            if (!bar) return;

            // Показываем панель
            bar.style.transform = 'translateY(0)';

            // Обновляем иконку или изображение
            const iconContainer = selectedCard.querySelector('.w-10.h-10');
            const imageElement = selectedCard.querySelector('img');
            
            if (imageElement) {
                // Если есть изображение, используем его
                const imgSrc = imageElement.getAttribute('src');
                const imgAlt = imageElement.getAttribute('alt') || '';
                document.getElementById('mobile-bar-icon').innerHTML = `<img src="${imgSrc}" alt="${imgAlt}" class="w-full h-full object-cover rounded-lg">`;
            } else if (iconContainer) {
                // Если есть иконка, используем её
                document.getElementById('mobile-bar-icon').innerHTML = iconContainer.innerHTML;
            }

            // Обновляем название и цену
            document.getElementById('mobile-bar-package-name').textContent = pkg.name.split('+')[0].trim();
            document.getElementById('mobile-bar-price').textContent = `${pkg.price} ₽`;
            
            // Обработка скидки
            const discountBadge = document.getElementById('mobile-bar-discount');
            if (pkg.discount) {
                discountBadge.textContent = `-${pkg.discount}%`;
                discountBadge.classList.remove('hidden');
            } else {
                discountBadge.classList.add('hidden');
            }
        }

        async function processPurchase() {
            const activeMethodBtn = document.querySelector('#payment-methods .payment-method-btn.ring-1');
            const method = activeMethodBtn ? activeMethodBtn.dataset.method : 'sbp';
            const user = window.__USER__;

            // Если выбран способ оплаты "С баланса" и пользователь не авторизован
            if (method === 'balance' && !user) {
                window.location.href = '/register';
                return;
            }

            const playerIdInput = document.getElementById('playerId');
            const playerId = playerIdInput ? playerIdInput.value.trim() : '';

            // Для всех игр КРОМЕ Roblox требуем минимум 5 цифр ID
            if (!(currentGame && String(currentGame.id) === '7')) {
                if (!playerIdInput || playerId.length < 5) {
                    showNotification('Пожалуйста, введите корректный ID аккаунта', 'error');
                    if (playerIdInput) {
                        playerIdInput.focus();
                    }
                    return;
                }
            }
            
            const selectedPackage = currentGame.packages[selectedPackageIndex];
            const price = selectedPackage.price;

            // Если оплата с баланса
            if (method === 'balance' && user) {
                const balance = user.balance || 0;

                // Проверка достаточности баланса
                if (balance < price) {
                    showNotification('Недостаточно средств на балансе', 'error');
                    setTimeout(() => {
                        window.location.href = '/top-up';
                    }, 1500);
                    return;
                }

                // Показываем loader
                const purchaseBtn = document.getElementById('purchaseBtn');
                const originalText = purchaseBtn ? purchaseBtn.textContent : 'Купить';
                if (purchaseBtn) {
                    purchaseBtn.disabled = true;
                    purchaseBtn.textContent = 'Обработка...';
                }

                try {
                    // Создаем заказ через API
                    const response = await fetch('/api/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            product_type: 'game',
                            product_id: String(currentGame.id),
                            product_name: currentGame.name,
                            package_id: String(selectedPackageIndex),
                            package_name: selectedPackage.name,
                            amount: price,
                            player_id: playerId,
                            details: {
                                currency: selectedPackage.amount,
                                platform: currentGame.platform || 'Unknown'
                            }
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Обновляем баланс локально
                        window.__USER__.balance = window.__USER__.balance - price;
                        
                        // Сохраняем информацию о покупке для страницы успеха
                        try {
                            localStorage.setItem('gamecoins_last_purchase_order', data.order.order_number);
                            localStorage.setItem('gamecoins_last_purchase_game', currentGame.name);
                            localStorage.setItem('gamecoins_last_purchase_package', selectedPackage.name);
                            localStorage.setItem('gamecoins_last_purchase_amount', price);
                        } catch (e) {
                            console.warn('Не удалось сохранить данные покупки', e);
                        }

                        showNotification('Покупка успешна! Заказ ' + data.order.order_number, 'success');
                        
                        // Редирект на страницу успеха через 1.5 секунды
                        setTimeout(() => {
                            window.location.href = '/purchase-success';
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Ошибка при создании заказа');
                    }

                } catch (error) {
                    console.error('Ошибка покупки:', error);
                    showNotification(error.message || 'Произошла ошибка при покупке', 'error');
                    
                    // Возвращаем кнопку в исходное состояние
                    if (purchaseBtn) {
                        purchaseBtn.disabled = false;
                        purchaseBtn.textContent = originalText;
                    }
                }

                return;
            }

            // Для других способов оплаты (СБП, Криптовалюта)
            showNotification('Данный способ оплаты временно недоступен', 'error');
        }

        // ============================================
        // PAYMENT MODAL
        // ============================================
        function openPaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (!modal || !currentGame) return;

            // Получаем выбранную карточку
            const selectedCard = document.querySelector('.package-selected');
            let selectedPackage = currentGame.packages[selectedPackageIndex];
            let packageName = selectedPackage ? selectedPackage.name.split('+')[0].trim() : '';
            
            // Если карточка находится в разделе пропусков или подписок, получаем текст из под карточки
            if (selectedCard) {
                const passCardContainer = selectedCard.closest('.pass-card-with-text');
                if (passCardContainer) {
                    const passTextElement = passCardContainer.querySelector('.pass-text p');
                    if (passTextElement) {
                        packageName = passTextElement.textContent.trim();
                    }
                    // Получаем цену из payment-details-price, который уже обновлен через updatePaymentDetails
                    const priceElement = document.getElementById('payment-details-price');
                    if (priceElement) {
                        const priceText = priceElement.textContent.trim();
                        const price = parseInt(priceText.replace(' ₽', '').replace(/\s/g, ''));
                        // Если цена не получена, пытаемся получить из data-package-index
                        let finalPrice = price;
                        if (isNaN(price) || price === 0) {
                            const packageIndex = parseInt(selectedCard.getAttribute('data-package-index'));
                            if (!isNaN(packageIndex) && packageIndex !== null) {
                                // Пытаемся найти пакет в данных подписок
                                const gameId = String(currentGame.id);
                                const passPackagesData = passPackagesDataByGame[gameId] || passPackagesDataByGame['default'];
                                let allPassItems = [];
                                passPackagesData.forEach((section) => {
                                    section.items.forEach((item) => {
                                        allPassItems.push({ name: item.name, price: item.price, amount: item.name });
                                    });
                                });
                                if (allPassItems[packageIndex]) {
                                    finalPrice = allPassItems[packageIndex].price;
                                }
                            }
                        }
                        selectedPackage = {
                            name: packageName,
                            price: finalPrice || (selectedPackage ? selectedPackage.price : 0),
                            amount: packageName
                        };
                    }
                }
                
                const imageElement = selectedCard.querySelector('img');
                const iconContainer = selectedCard.querySelector('.w-10.h-10') || selectedCard.querySelector('.w-16.h-16') || selectedCard.querySelector('.w-20.h-20');
                
                if (imageElement) {
                    // Если есть изображение, используем его
                    const imgSrc = imageElement.getAttribute('src');
                    const imgAlt = imageElement.getAttribute('alt') || '';
                    document.getElementById('modal-package-icon').innerHTML = `<img src="${imgSrc}" alt="${imgAlt}" class="w-full h-full object-cover rounded-lg">`;
                } else if (iconContainer) {
                    // Если есть иконка, используем её
                    document.getElementById('modal-package-icon').innerHTML = iconContainer.innerHTML;
                }
            }
            document.getElementById('modal-game-name').textContent = currentGame.name;
            document.getElementById('modal-package-name').textContent = packageName;
            
            // In the new design, original and total price are the same.
            const finalPrice = selectedPackage ? selectedPackage.price : 0;
            
            document.getElementById('modal-price-original').textContent = `${finalPrice} ₽`;
            document.getElementById('modal-price-total').textContent = `${finalPrice} ₽`;

            // Make the modal visible, but with content still off-screen
            modal.classList.remove('hidden');
            
            // A tiny delay is needed to allow the browser to render the modal
            // in its initial off-screen state before triggering the transition.
            setTimeout(() => {
                modal.classList.add('is-open');
            }, 10); 

            document.body.style.overflow = 'hidden';
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            if (!modal) return;
            
            // Removing the 'is-open' class will trigger the CSS transition
            // to slide the content back down.
            modal.classList.remove('is-open');

            // We wait for the animation to finish (400ms, as defined in styles.css)
            // before completely hiding the modal with 'display: none'.
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 400); 
        }
        
        // Загрузить игру при загрузке страницы
        document.addEventListener('DOMContentLoaded', async () => {
            await loadGame();

            // Setup desktop sidebar logic
            const desktopUidInput = document.getElementById('playerId');
            const desktopPurchaseBtn = document.getElementById('purchaseBtn');

            if (desktopUidInput && desktopPurchaseBtn) {
                // Убираем блокировку кнопки, чтобы можно было нажать и получить подсказку
                desktopPurchaseBtn.disabled = false;

                desktopUidInput.addEventListener('input', () => {
                    // Проверяем, не находимся ли мы в режиме "Регистрация"
                    const activeMethodBtn = document.querySelector('#payment-methods .payment-method-btn.ring-1');
                    const method = activeMethodBtn ? activeMethodBtn.dataset.method : 'sbp';
                    const user = (typeof getCurrentUser === 'function') ? getCurrentUser() : null;

                    if (method === 'balance' && !user) {
                        // В режиме регистрации кнопка всегда активна (белая)
                        desktopPurchaseBtn.classList.remove('bg-white/20', 'text-white/50');
                        desktopPurchaseBtn.classList.add('bg-text-primary', 'text-primary-bg');
                        return;
                    }

                    if (desktopUidInput.value.trim().length >= 5) {
                        desktopPurchaseBtn.classList.remove('bg-white/20', 'text-white/50');
                        desktopPurchaseBtn.classList.add('bg-text-primary', 'text-primary-bg'); // Active (white)
                    } else {
                        // Кнопка визуально "неактивна", но нажимается
                        desktopPurchaseBtn.classList.add('bg-white/20', 'text-white/50');
                        desktopPurchaseBtn.classList.remove('bg-text-primary', 'text-primary-bg');
                    }
                });

                desktopPurchaseBtn.addEventListener('click', processPurchase);
            }
            
            // Setup modal triggers and logic
            const paymentModal = document.getElementById('paymentModal');
            if (paymentModal) {
                paymentModal.addEventListener('click', (e) => {
                    if (e.target === paymentModal) {
                        closePaymentModal();
                    }
                });
            }
            // Payment method selection in sidebar
            const paymentMethodsContainer = document.getElementById('payment-methods');
            if (paymentMethodsContainer) {
                const paymentButtons = paymentMethodsContainer.querySelectorAll('.payment-method-btn');
                
                // Инициализация: устанавливаем "С баланса" как метод по умолчанию
                const defaultMethod = paymentMethodsContainer.querySelector('[data-method="balance"]') || 
                                     paymentMethodsContainer.querySelector('[data-method="sbp"]');
                if (defaultMethod) {
                    defaultMethod.classList.add('ring-1', 'ring-white');
                    updatePurchaseButtonsState(defaultMethod.dataset.method); // Init state
                    console.log('💳 Default payment method:', defaultMethod.dataset.method);
                }

                paymentButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        console.log('🔘 Clicked payment method:', button.dataset.method);
                        paymentButtons.forEach(btn => btn.classList.remove('ring-1', 'ring-white'));
                        button.classList.add('ring-1', 'ring-white');
                        updatePurchaseButtonsState(button.dataset.method);
                    });
                });
            }

            // Modal Payment method selection
            const modalPaymentMethods = document.getElementById('modal-payment-methods');
            if(modalPaymentMethods) {
                const buttons = modalPaymentMethods.querySelectorAll('.payment-method-btn');
                // Инициализация для модалки: устанавливаем "С баланса" по умолчанию
                const defaultModalMethod = modalPaymentMethods.querySelector('[data-method="balance"]') || 
                                          modalPaymentMethods.querySelector('[data-method="sbp"]');
                if (defaultModalMethod) {
                    defaultModalMethod.classList.add('ring-1', 'ring-white');
                    console.log('💳 Modal default payment method:', defaultModalMethod.dataset.method);
                }

                buttons.forEach(button => {
                    button.addEventListener('click', () => {
                        console.log('🔘 Modal clicked payment method:', button.dataset.method);
                        buttons.forEach(btn => btn.classList.remove('ring-1', 'ring-white'));
                        button.classList.add('ring-1', 'ring-white');
                        updatePurchaseButtonsState(button.dataset.method);
                    });
                });
            }

            // Modal Purchase Button Logic
            const uidInput = document.getElementById('modal-uid');
            const modalPurchaseButton = document.getElementById('modalPurchaseBtn');

            if(uidInput && modalPurchaseButton) {
                // Разрешаем нажатие сразу
                modalPurchaseButton.disabled = false;
                
                uidInput.addEventListener('input', () => {
                    // Проверяем режим "Регистрация" для модалки
                    const activeMethodBtn = document.querySelector('#modal-payment-methods .payment-method-btn.ring-1');
                    const method = activeMethodBtn ? activeMethodBtn.dataset.method : 'sbp';
                    const user = (typeof getCurrentUser === 'function') ? getCurrentUser() : null;

                    if (method === 'balance' && !user) {
                         modalPurchaseButton.classList.remove('bg-white/20', 'text-white/50');
                         modalPurchaseButton.classList.add('bg-text-primary', 'text-primary-bg');
                         return;
                    }

                    // Для Roblox не блокируем кнопку от ID
                    if (currentGame && String(currentGame.id) === '7') {
                        modalPurchaseButton.classList.remove('bg-white/20', 'text-white/50');
                        modalPurchaseButton.classList.add('bg-text-primary', 'text-primary-bg');
                        return;
                    }

                    if (uidInput.value.trim().length >= 5) {
                        modalPurchaseButton.classList.remove('bg-white/20', 'text-white/50');
                        modalPurchaseButton.classList.add('bg-text-primary', 'text-primary-bg');
                    } else {
                        // Визуально неактивная, но кликабельная
                        modalPurchaseButton.classList.add('bg-white/20', 'text-white/50');
                        modalPurchaseButton.classList.remove('bg-text-primary', 'text-primary-bg');
                    }
                });

                modalPurchaseButton.addEventListener('click', () => {
                    const activeMethodBtn = document.querySelector('#modal-payment-methods .payment-method-btn.ring-1');
                    const method = activeMethodBtn ? activeMethodBtn.dataset.method : 'sbp';
                    const user = (typeof getCurrentUser === 'function') ? getCurrentUser() : null;

                    if (method === 'balance' && !user) {
                        window.location.href = 'register.html';
                        return;
                    }

                    // We need a specific purchase handler for the modal
                    // as it uses a different input for the UID.
                    const playerId = uidInput.value.trim();

                    // Для всех игр, кроме Roblox, проверяем длину ID
                    if (!(currentGame && String(currentGame.id) === '7')) {
                        if (playerId.length < 5) {
                            showNotification('Пожалуйста, введите корректный ID аккаунта', 'error');
                            uidInput.focus();
                            return;
                        }
                    }
            
                    const selectedPackage = currentGame.packages[selectedPackageIndex];
                    const price = selectedPackage.price;
                    const balance = getUserBalance();

                    if (balance >= price) {
                        deductFromBalance(price);
                        if (typeof trackPurchase === 'function' && currentGame) {
                            trackPurchase(currentGame.id);
                        }
                        try {
                            localStorage.setItem('gamecoins_last_purchase_game_id', String(currentGame.id));
                            localStorage.setItem('gamecoins_last_purchase_package_name', selectedPackage.name);
                        } catch (e) {
                            console.warn('Не удалось сохранить последний заказ', e);
                        }
                        window.location.href = 'purchase-success.html';
                    } else {
                        window.location.href = 'top-up.html?error=insufficient_funds';
                    }
                });
            }
        });
    </script>
</body>
</html>

