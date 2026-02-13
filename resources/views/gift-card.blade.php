<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="{{ asset('/') }}">
    <title>{{ $initialCard['name'] ?? 'Подарочные карты' }} | Platinow</title>
    <meta name="description" content="Покупайте подарочные карты для ваших любимых сервисов.">
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
            -ms-overflow-style: none;
            scrollbar-width: none;
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
        
        /* Стили для активной карточки */
        .package-selected {
            border-color: #FFFFFF !important;
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.2) !important;
            background-color: transparent !important;
        }
        
        /* Стили для кнопок оплаты */
        .payment-method-btn.selected {
            border: 1px solid #FFFFFF !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #FFFFFF !important;
            ring: 0 !important;
        }
        
    /* Анимация появления */
    .fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Hide spin buttons for number input */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    
    /* Custom Text Selection Color */
    ::selection {
        background: rgba(255, 255, 255, 0.2);
        color: white;
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
        <div class="container mx-auto max-w-7xl">
            <a href="/" class="mb-8 flex items-center gap-2 text-text-secondary hover:text-text-primary transition-custom inline-flex outline-none border-none focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Назад
            </a>
            
            <div class="flex justify-start">
                <!-- Card Info (Hidden) -->
                <div class="hidden">
                    <h1 id="cardName" class="text-4xl md:text-5xl font-semibold mb-4">Загрузка...</h1>
                </div>
                
                <!-- Packages + Form -->
                <div class="w-full max-w-5xl">
                    <!-- Title for Gift Card -->
                    <h1 id="pageTitle" class="text-3xl md:text-4xl font-bold mb-8"></h1>

                    <div class="md:flex md:items-start md:gap-10">
                        <!-- Left Column: Packages -->
                        <div class="md:flex-1">
                            <!-- Categories Tabs (Hidden by default, shown for Telegram) -->
                            <div id="categoryTabs" class="hidden flex items-center gap-6 mb-6 border-b border-white/5">
                                <button onclick="selectGiftCardCategory('stars')" style="outline: none !important; box-shadow: none !important; -webkit-tap-highlight-color: transparent;" class="gift-category-tab pb-3 text-base font-bold text-white relative outline-none focus:outline-none ring-0 focus:ring-0 border-none" data-category="stars">
                                    <span>Telegram звезды</span>
                                    <div class="active-indicator absolute bottom-0 left-0 right-0 h-[3px] bg-white rounded-t-full shadow-[0_0_12px_rgba(255,255,255,0.3)]"></div>
                                </button>
                                <button onclick="selectGiftCardCategory('premium')" style="outline: none !important; box-shadow: none !important; -webkit-tap-highlight-color: transparent;" class="gift-category-tab pb-3 text-base font-bold text-text-secondary hover:text-white transition-colors relative outline-none focus:outline-none ring-0 focus:ring-0 border-none" data-category="premium">
                                    <span>Подписки</span>
                                    <div class="active-indicator absolute bottom-0 left-0 right-0 h-[3px] bg-white rounded-t-full shadow-[0_0_12px_rgba(255,255,255,0.3)] hidden"></div>
                                </button>
                            </div>
                            
                            <div id="packagesContainer" class="grid grid-cols-2 gap-y-4 gap-x-2 md:grid-cols-2 md:gap-y-4 md:gap-x-2">
                                <!-- Карточки будут загружены через JavaScript -->
                            </div>
                            
                            <!-- Steam Custom UI -->
                            <div id="steamCustomUI" class="hidden">
                                <!-- Amount Input + Currency Selector -->
                                <div class="flex gap-2 mb-4">
                                    <div class="flex-1 bg-[#1A1A1A] border border-white/10 rounded-xl p-3">
                                        <label class="text-text-secondary text-xs mb-0.5 block">Получите</label>
                                        <input type="number" id="steamAmount" value="1000" min="100" class="w-full bg-transparent text-white text-xl font-bold focus:outline-none appearance-none border-none outline-none ring-0" oninput="updateSteamPrice()">
                                    </div>
                                    <div class="relative">
                                        <button id="currencyDropdownBtn" onclick="toggleCurrencyDropdown()" style="-webkit-tap-highlight-color: transparent;" class="bg-[#1A1A1A] border border-white/10 rounded-xl p-3 h-full flex items-center gap-1.5 min-w-[100px] justify-between">
                                            <span id="selectedCurrency" class="text-white font-bold text-sm">RU, ₽</span>
                                            <svg class="w-3.5 h-3.5 text-text-secondary" id="currencyArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <!-- Dropdown -->
                                        <div id="currencyDropdown" class="hidden absolute top-full right-0 mt-1 bg-[#1A1A1A] border border-white/20 rounded-lg overflow-hidden z-50 min-w-[120px] shadow-xl">
                                            <button onclick="selectCurrency('RU', '₽')" class="currency-option w-full px-3 py-2 text-left hover:bg-white/10 flex items-center gap-2 text-white" data-code="RU">
                                                <span class="text-sm">RU, ₽</span>
                                            </button>
                                            <button onclick="selectCurrency('СНГ', '$')" class="currency-option w-full px-3 py-2 text-left hover:bg-white/10 flex items-center gap-2 text-white" data-code="СНГ">
                                                <span class="text-sm">СНГ, $</span>
                                            </button>
                                            <button onclick="selectCurrency('KZ', '₸')" class="currency-option w-full px-3 py-2 text-left hover:bg-white/10 flex items-center gap-2 text-white" data-code="KZ">
                                                <span class="text-sm">KZ, ₸</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Quick Amount Buttons -->
                                <div id="quickAmounts" class="flex flex-wrap gap-1.5">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Sticky Payment Form -->
                        <div class="mt-36 md:mt-0 md:w-1/3">
                            <div class="bg-surface rounded-lg p-4 flex flex-col h-full md:sticky md:top-[80px]">
                                <!-- Main Content -->
                                <div class="flex-grow">
                                    <!-- Header -->
                                    <div class="flex items-center gap-3 mb-4">
                                        <div id="payment-details-icon" class="w-14 h-14 bg-white/5 rounded-lg flex items-center justify-center p-1 shrink-0">
                                            <!-- Icon -->
                                        </div>
                                        <div>
                                            <p id="payment-details-game-name" class="text-text-secondary text-xs">-</p>
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
                                    
                                    <!-- Player ID / Email -->
                                    <div class="mb-4">
                                        <label for="playerId" class="block text-sm font-medium text-text-secondary mb-2">Email / ID</label>
                                        <div class="relative">
                                            <input
                                                type="text"
                                                id="playerId"
                                                placeholder="Введите Email или ID"
                                                class="w-full bg-transparent border border-white/20 rounded-md px-3 py-3 pr-9 text-sm focus:border-white focus:outline-none">
                                            <div class="absolute right-[10px] top-1/2 -translate-y-1/2 w-5 h-5 bg-white/20 rounded-full flex items-center justify-center text-sm font-bold text-white">i</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Password Field (Hidden by default) -->
                                    <div class="mb-4" id="passwordContainer" style="display: none;">
                                        <label for="accountPassword" class="block text-sm font-medium text-text-secondary mb-2">Пароль</label>
                                        <div class="relative">
                                            <input
                                                type="password"
                                                id="accountPassword"
                                                placeholder="Введите пароль"
                                                class="w-full bg-transparent border border-white/20 rounded-md px-3 py-3 pr-9 text-sm focus:border-white focus:outline-none">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div class="mt-auto">
                                    <p class="text-[10px] text-center text-text-secondary mb-2">Нажимая "Купить", вы принимаете <a href="#" class="text-green-400 underline">Правила</a> и <a href="#" class="text-green-400 underline">Политику</a></p>
                                    <button id="purchaseBtn" class="bg-white/20 text-white/50 w-full py-4 rounded-lg font-semibold text-lg transition-colors">
                                        Купить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs (Hidden for Gift Cards as content is usually simpler, but structure kept) -->
            <div class="mt-16 hidden">
                <div class="border-b border-white/10 mb-8">
                    <nav class="flex items-center space-x-6">
                        <button class="filter-btn game-tab active text-sm font-semibold">
                            <span>Описание</span>
                        </button>
                    </nav>
                </div>
                <div class="game-tab-content">
                    <p id="cardDescription" class="text-text-secondary"></p>
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
                        <p id="modal-game-name" class="text-text-secondary text-sm">-</p>
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
                
                <!-- Player ID / Email -->
                <div class="mb-4">
                     <label for="modal-uid" class="block text-sm font-medium text-text-secondary mb-2">Email / ID</label>
                     <div class="relative">
                         <input
                             type="text"
                             id="modal-uid"
                             placeholder="Введите Email или ID"
                             class="w-full bg-transparent border border-white/20 rounded-md px-3 py-3 pr-9 text-sm focus:border-white focus:outline-none">
                         <div class="absolute right-[10px] top-1/2 -translate-y-1/2 w-5 h-5 bg-white/20 rounded-full flex items-center justify-center text-sm font-bold text-white">i</div>
                     </div>
                </div>
                
                <!-- Password Field (Hidden by default) -->
                <div class="mb-4" id="modalPasswordContainer" style="display: none;">
                    <label for="modalPassword" class="block text-sm font-medium text-text-secondary mb-2">Пароль</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="modalPassword"
                            placeholder="Введите пароль"
                            class="w-full bg-transparent border border-white/20 rounded-md px-3 py-3 pr-9 text-sm focus:border-white focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Footer with button and disclaimer -->
            <div class="mt-auto">
                <p class="text-[11px] text-center text-text-secondary mb-4">Нажимая "Купить", вы принимаете <a href="#" class="text-green-400 underline">Правила</a> и <a href="#" class="text-green-400 underline">Политику</a></p>
                
                <button id="modalPurchaseBtn" class="bg-white/20 text-white/50 w-full py-4 rounded-lg font-semibold text-lg transition-colors">
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
    
    <script src="{{ asset('common.js') }}"></script>
    <script src="{{ asset('live-search.js') }}"></script>
    @if(isset($initialCard))
    <script>
        window.__GIFT_CARD__ = {!! json_encode($initialCard, JSON_UNESCAPED_UNICODE) !!};
    </script>
    @endif
    <script>
        let currentCard = null;
        let selectedPackageIndex = 0;
        let selectedPaymentMethod = null;
        let currentCategory = 'stars'; // For Telegram tabs

        // Загрузка данных
        async function loadCard() {
            // Используем данные, переданные с сервера
            if (typeof window.__GIFT_CARD__ !== 'undefined') {
                currentCard = window.__GIFT_CARD__;
            } else {
                // Fallback на старый метод для совместимости
                try {
                    const response = await fetch('gift-cards-data.json');
                    const data = await response.json();
                    const cards = data.gift_cards;
                    
                    const urlParams = new URLSearchParams(window.location.search);
                    const cardId = urlParams.get('id');
                    
                    currentCard = cards.find(c => c.id === cardId);
                } catch (error) {
                    console.error('Error loading card:', error);
                }
            }
                
            if (!currentCard) {
                document.getElementById('cardName').textContent = 'Карта не найдена';
                return;
            }
            
            document.title = `${currentCard.name} | Platinow`;
            document.getElementById('cardName').textContent = currentCard.name;
            document.getElementById('pageTitle').textContent = currentCard.name;
            document.getElementById('payment-details-game-name').textContent = currentCard.name;
            document.getElementById('modal-game-name').textContent = currentCard.name;
            
            if (currentCard.description && document.getElementById('cardDescription')) {
                 document.getElementById('cardDescription').textContent = currentCard.description;
            }

            // Show category tabs for Telegram
            if (currentCard.id === 'telegram-premium') {
                    document.getElementById('categoryTabs').classList.remove('hidden');
                    document.getElementById('categoryTabs').classList.add('flex');
                    
                    // Update input field for Telegram
                    document.getElementById('playerId').placeholder = "@username";
                    const mainLabel = document.querySelector('label[for="playerId"]');
                    if (mainLabel) mainLabel.textContent = "Введите имя пользователя";
                    
                    document.getElementById('modal-uid').placeholder = "@username";
                    const modalLabel = document.querySelector('label[for="modal-uid"]');
                    if (modalLabel) modalLabel.textContent = "Введите имя пользователя";
                }
                
                // Show Steam custom UI
                if (currentCard.id === 'steam') {
                    document.getElementById('packagesContainer').classList.add('hidden');
                    document.getElementById('steamCustomUI').classList.remove('hidden');
                    
                    // Update input fields for Steam
                    document.getElementById('playerId').placeholder = "Введите логин Steam";
                    const mainLabel = document.querySelector('label[for="playerId"]');
                    if (mainLabel) mainLabel.textContent = "Логин Steam";
                    
                    document.getElementById('modal-uid').placeholder = "Введите логин Steam";
                    const modalLabel = document.querySelector('label[for="modal-uid"]');
                    if (modalLabel) modalLabel.textContent = "Логин Steam";
                    
                    initSteamUI();
                    return; // Don't display regular packages
                }
                
                // TikTok: Show Login + Password
                if (currentCard.id === 'tiktok-coins') {
                    togglePasswordField(true);
                    
                    // Update ID field to Login
                    document.getElementById('playerId').placeholder = "Введите логин";
                    const mainLabel = document.querySelector('label[for="playerId"]');
                    if (mainLabel) mainLabel.textContent = "Логин";
                    
                    document.getElementById('modal-uid').placeholder = "Введите логин";
                    const modalLabel = document.querySelector('label[for="modal-uid"]');
                    if (modalLabel) modalLabel.textContent = "Логин";
                }

                displayPackages();
        }

        // Отображение пакетов
        function displayPackages(category = null) {
            const container = document.getElementById('packagesContainer');
            
            let packagesToShow = currentCard.packages;
            
            // Filter packages for Telegram by category
            if (currentCard.id === 'telegram-premium' && currentCard.categories) {
                const cat = category || currentCategory;
                packagesToShow = currentCard.categories[cat] || [];
            }
            
            // Use packageImage if available for this card
            const packageImage = currentCard.packageImage;
            
            container.innerHTML = packagesToShow.map((pkg, index) => 
                generateGiftCardPackage(pkg, index, currentCard.id, currentCard.icon, packageImage)
            ).join('');
            
            attachPackageClickHandlers(packagesToShow);
            // Select first package by default
            const firstPackage = container.querySelector('.package-card');
            if (firstPackage) {
                firstPackage.click();
            }
        }
        
        // Generate package card HTML for gift cards
        function generateGiftCardPackage(pkg, index, cardId, icon, packageImage) {
            let iconHtml;
            const img = pkg.image || packageImage;
            if (img) {
                iconHtml = `<img src="${img}" alt="${pkg.name}" class="w-full h-full object-contain rounded-lg" />`;
            } else if (icon) {
                iconHtml = `<svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="${icon}"/></svg>`;
            } else {
                iconHtml = `<span class="text-white text-sm font-bold">${pkg.name.charAt(0)}</span>`;
            }
            
            return `
                <div class="package-card bg-[#1A1A1A] hover:bg-[#252525] rounded-xl p-4 cursor-pointer border-2 border-transparent transition-all duration-200 flex items-center justify-between gap-4 relative overflow-hidden group h-20" 
                     data-card-id="${cardId}" 
                     data-package-index="${index}">
                    
                    <div class="flex items-center gap-3 md:gap-4">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center shrink-0">
                            ${iconHtml}
                        </div>
                        <div class="font-bold text-white text-[13px] md:text-sm leading-tight">
                            ${pkg.name}
                        </div>
                    </div>

                    <div class="text-white font-bold text-[13px] md:text-sm whitespace-nowrap">
                        ${pkg.price} ₽
                    </div>
                </div>
            `;
        }
        
        // Переключение категорий для Telegram
        function selectGiftCardCategory(category) {
            currentCategory = category;
            
            // Update tab styles
            document.querySelectorAll('.gift-category-tab').forEach(tab => {
                const tabCat = tab.dataset.category;
                const indicator = tab.querySelector('.active-indicator');
                
                if (tabCat === category) {
                    tab.classList.remove('text-text-secondary');
                    tab.classList.add('text-white');
                    if (indicator) indicator.classList.remove('hidden');
                } else {
                    tab.classList.remove('text-white');
                    tab.classList.add('text-text-secondary');
                    if (indicator) indicator.classList.add('hidden');
                }
            });
            
            displayPackages(category);
        }
        
        // ===== STEAM CUSTOM UI =====
        let selectedCurrencyCode = 'RU';
        let selectedCurrencySymbol = '₽';
        let steamAmount = 1000;
        
        function initSteamUI() {
            // Populate quick amounts
            const quickAmountsContainer = document.getElementById('quickAmounts');
            const amounts = currentCard.quickAmounts || [200, 500, 1000, 2000, 5000];
            
            quickAmountsContainer.innerHTML = amounts.map(amount => `
                <button onclick="setQuickAmount(${amount})" style="-webkit-tap-highlight-color: transparent;" class="quick-amount-btn bg-[#1A1A1A] border border-white/10 hover:bg-white/10 text-white font-semibold py-2 px-4 rounded-full text-sm">
                    ${amount} ${selectedCurrencySymbol}
                </button>
            `).join('');
            
            // Set initial price
            updateSteamPrice();
            
            // Update payment details
            updateSteamPaymentDetails();
        }
        
        function toggleCurrencyDropdown() {
            const dropdown = document.getElementById('currencyDropdown');
            const arrow = document.getElementById('currencyArrow');
            dropdown.classList.toggle('hidden');
        }
        
        function selectCurrency(code, symbol) {
            selectedCurrencyCode = code;
            selectedCurrencySymbol = symbol;
            document.getElementById('selectedCurrency').textContent = `${code}, ${symbol}`;
            toggleCurrencyDropdown();
            
            // Update quick amounts with new symbol
            const quickAmountsContainer = document.getElementById('quickAmounts');
            const amounts = currentCard.quickAmounts || [200, 500, 1000, 2000, 5000];
            quickAmountsContainer.innerHTML = amounts.map(amount => `
                <button onclick="setQuickAmount(${amount})" style="-webkit-tap-highlight-color: transparent;" class="quick-amount-btn bg-[#1A1A1A] border border-white/10 hover:bg-white/10 text-white font-semibold py-2 px-4 rounded-full text-sm">
                    ${amount} ${symbol}
                </button>
            `).join('');
            
            updateSteamPrice();
        }
        
        function setQuickAmount(amount) {
            document.getElementById('steamAmount').value = amount;
            steamAmount = amount;
            updateSteamPrice();
        }
        
        function updateSteamPrice() {
            steamAmount = parseFloat(document.getElementById('steamAmount').value) || 0;
            
            // Calculate price in rubles (base currency)
            const currency = currentCard.currencies.find(c => c.code === selectedCurrencyCode);
            const rate = currency ? currency.rate : 1;
            
            // Price calculation: Amount in Foreign / Rate = Amount in Rubles
            // Example: 200 USD / 0.011 = ~18181 RUB
            // Example: 200 KZT / 5.2 = ~38 RUB
            const price = Math.round(steamAmount / rate);
            
            // Update payment details
            document.getElementById('payment-details-price').textContent = `${price} ₽`;
            document.getElementById('payment-details-total').textContent = `${price} ₽`;
            document.getElementById('mobile-bar-price').textContent = `${price} ₽`;
            
            updateSteamPaymentDetails();
        }
        
        function updateSteamPaymentDetails() {
            // Update header
            const iconHtml = `<img src="${currentCard.image}" alt="${currentCard.name}" class="w-full h-full object-cover rounded-lg" />`;
            document.getElementById('payment-details-icon').innerHTML = iconHtml;
            document.getElementById('mobile-bar-icon').innerHTML = iconHtml;
            document.getElementById('modal-package-icon').innerHTML = iconHtml;
            
            const packageName = `${steamAmount} ${selectedCurrencySymbol}`;
            document.getElementById('payment-details-package-name').textContent = packageName;
            document.getElementById('mobile-bar-package-name').textContent = packageName;
            document.getElementById('modal-package-name').textContent = packageName;
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const dropdown = document.getElementById('currencyDropdown');
            const btn = document.getElementById('currencyDropdownBtn');
            if (dropdown && btn && !dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.add('hidden');
                document.getElementById('currencyArrow').classList.remove('rotate-180');
            }
        });
        // ===== END STEAM CUSTOM UI =====
        
        // Обработчики клика по пакетам
        function attachPackageClickHandlers(packages) {
            document.querySelectorAll('.package-card').forEach((card, index) => {
                card.addEventListener('click', (e) => {
                    // Prevent triggering if clicking inside the popup
                    if (e.target.closest('#accountTypePopup')) return;

                    document.querySelectorAll('.package-card').forEach(c => {
                        c.classList.remove('package-selected');
                        c.classList.remove('overflow-visible');
                        c.classList.add('overflow-hidden');
                        
                        // Remove any existing popup
                        const popup = c.querySelector('#accountTypePopup');
                        if (popup) popup.remove();
                    });
                    
                    card.classList.add('package-selected');
                    selectedPackageIndex = index;
                    
                    updatePaymentDetails(packages[index], card);
                    
                    // Show popup for ChatGPT
                    if (currentCard.id === 'chatgpt') {
                        showAccountTypePopup(card);
                    } else {
                        // Ensure ID field is shown for other cards
                        toggleIdField(true);
                    }
                });
            });
        }
        
        function showAccountTypePopup(card) {
            card.classList.remove('overflow-hidden');
            card.classList.add('overflow-visible');
            
            const popup = document.createElement('div');
            popup.id = 'accountTypePopup';
            popup.className = 'absolute top-[calc(100%+12px)] left-0 right-0 bg-[#1A1A1A] border border-white/20 rounded-xl p-1.5 z-30 shadow-2xl flex flex-col gap-1 fade-in';
            popup.style.cursor = 'default';
            
            // Получаем индекс выбранного пакета
            // currentCard.packages[selectedPackageIndex]
            const selectedPackage = currentCard.packages[selectedPackageIndex];
            
            // Показываем только "Готовый аккаунт"
            popup.innerHTML = `
                <div class="relative flex bg-white/5 p-1 rounded-lg">
                    <div class="w-full text-center py-2 text-[10px] md:text-xs font-semibold cursor-pointer rounded-md transition-all text-black bg-white shadow-sm" id="opt-ready" onclick="event.stopPropagation()">
                        Готовый аккаунт
                    </div>
                </div>
                <div class="text-[10px] text-text-secondary text-center px-2 py-1 leading-tight" id="account-type-desc">
                    Вы получите данные от нового аккаунта с подпиской
                </div>
            `;
            // Скрываем поле ID
            toggleIdField(false);
            
            card.appendChild(popup);
        }
        
        window.toggleAccountType = function(type, event) {
            if(event) {
                event.stopPropagation();
            }
            
            const readyBtn = document.getElementById('opt-ready');
            const renewalBtn = document.getElementById('opt-renewal');
            const desc = document.getElementById('account-type-desc');
            
            if (type === 'ready') {
                readyBtn.className = 'w-1/2 text-center py-2 text-[10px] md:text-xs font-semibold cursor-pointer rounded-md transition-all text-black bg-white shadow-sm';
                renewalBtn.className = 'w-1/2 text-center py-2 text-[10px] md:text-xs font-semibold cursor-pointer rounded-md transition-all text-text-secondary hover:text-white';
                desc.textContent = 'Вы получите данные от нового аккаунта с подпиской';
                toggleIdField(false);
                togglePasswordField(false);
            } else {
                readyBtn.className = 'w-1/2 text-center py-2 text-[10px] md:text-xs font-semibold cursor-pointer rounded-md transition-all text-text-secondary hover:text-white';
                renewalBtn.className = 'w-1/2 text-center py-2 text-[10px] md:text-xs font-semibold cursor-pointer rounded-md transition-all text-black bg-white shadow-sm';
                desc.textContent = 'Подписка будет активирована на вашем аккаунте';
                toggleIdField(true);
                
                if (currentCard.id === 'chatgpt') {
                    togglePasswordField(true);
                    // Update main label and placeholder
                    document.getElementById('playerId').placeholder = "Введите Email";
                    const mainLabel = document.querySelector('label[for="playerId"]');
                    if (mainLabel) mainLabel.textContent = "Введите данные для входа в ChatGPT";
                    
                    // Update modal label and placeholder
                    document.getElementById('modal-uid').placeholder = "Введите Email";
                    const modalLabel = document.querySelector('label[for="modal-uid"]');
                    if (modalLabel) modalLabel.textContent = "Введите данные для входа в ChatGPT";
                } else {
                    togglePasswordField(false);
                }
            }
        }
        
        function togglePasswordField(show) {
             const container = document.getElementById('passwordContainer');
             if (container) container.style.display = show ? 'block' : 'none';
             
             const modalContainer = document.getElementById('modalPasswordContainer');
             if (modalContainer) modalContainer.style.display = show ? 'block' : 'none';
             
             updatePurchaseButtonsState();
        }
        
        function toggleIdField(show) {
            const input = document.getElementById('playerId');
            if (!input) return;
            const container = input.closest('.mb-4');
            if (container) {
                container.style.display = show ? 'block' : 'none';
            }
            // Update modal input visibility too
            const modalInput = document.getElementById('modal-uid');
            if (modalInput) {
                const modalContainer = modalInput.closest('.mb-4');
                if (modalContainer) {
                    modalContainer.style.display = show ? 'block' : 'none';
                }
            }
            
            // If hidden, we allow purchase without ID
            updatePurchaseButtonsState();
        }
        
        // Обновление правой панели и мобильного бара
        function updatePaymentDetails(pkg, cardElement) {
            // Icon
            const iconHtml = cardElement.querySelector('.w-10').innerHTML;
            document.getElementById('payment-details-icon').innerHTML = iconHtml;
            document.getElementById('mobile-bar-icon').innerHTML = iconHtml;
            document.getElementById('modal-package-icon').innerHTML = iconHtml;
            
            // Name
            document.getElementById('payment-details-package-name').textContent = pkg.name;
            document.getElementById('mobile-bar-package-name').textContent = pkg.name;
            document.getElementById('modal-package-name').textContent = pkg.name;
            
            // Price
            const priceText = `${pkg.price} ₽`;
            document.getElementById('payment-details-price').textContent = priceText;
            document.getElementById('payment-details-total').textContent = priceText;
            document.getElementById('mobile-bar-price').textContent = priceText;
            document.getElementById('modal-price-original').textContent = priceText;
            document.getElementById('modal-price-total').textContent = priceText;
            
            // Show mobile bar
            document.getElementById('mobilePurchaseBar').classList.remove('translate-y-full');
        }

        // Выбор метода оплаты
        document.querySelectorAll('.payment-method-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove selection from all buttons (both desktop and modal)
                const method = this.dataset.method;
                
                document.querySelectorAll(`.payment-method-btn`).forEach(b => {
                    b.classList.remove('selected', 'bg-accent-green', 'text-primary-bg');
                    b.classList.add('bg-white/5');
                    b.style.borderColor = 'transparent';
                });
                
                // Add selection to clicked button type everywhere
                document.querySelectorAll(`.payment-method-btn[data-method="${method}"]`).forEach(b => {
                    b.classList.add('selected');
                    b.classList.remove('bg-white/5');
                });
                
                selectedPaymentMethod = method;
                
                updatePurchaseButtonsState();
            });
        });

        // Ввод ID / Email
        const playerInputs = [
            document.getElementById('playerId'), 
            document.getElementById('modal-uid'),
            document.getElementById('accountPassword'),
            document.getElementById('modalPassword')
        ];
        playerInputs.forEach(input => {
            if (input) {
                input.addEventListener('input', updatePurchaseButtonsState);
            }
        });

        function updatePurchaseButtonsState() {
            const methodSelected = !!selectedPaymentMethod;
            const idEntered = document.getElementById('playerId').value.trim().length > 0;
            const modalIdEntered = document.getElementById('modal-uid').value.trim().length > 0;
            
            // Password validation
            const passwordVisible = document.getElementById('passwordContainer') && document.getElementById('passwordContainer').style.display !== 'none';
            const passwordEntered = document.getElementById('accountPassword').value.trim().length > 0;
            const modalPasswordEntered = document.getElementById('modalPassword').value.trim().length > 0;
            
            const desktopBtn = document.getElementById('purchaseBtn');
            const modalBtn = document.getElementById('modalPurchaseBtn');
            
            // Desktop button state
            // If ID field is hidden (ready account), we don't need ID
            const idFieldHidden = document.getElementById('playerId') && document.getElementById('playerId').closest('.mb-4').style.display === 'none';
            
            // Validity conditions
            const validId = idEntered || idFieldHidden;
            const validPassword = !passwordVisible || passwordEntered;

            if (methodSelected && validId && validPassword) {
                desktopBtn.disabled = false;
                desktopBtn.classList.remove('bg-white/20', 'text-white/50');
                desktopBtn.classList.add('bg-text-primary', 'text-primary-bg');
            } else {
                desktopBtn.disabled = true;
                desktopBtn.classList.add('bg-white/20', 'text-white/50');
                desktopBtn.classList.remove('bg-text-primary', 'text-primary-bg');
            }
            
            // Modal button state
            const validModalPassword = !passwordVisible || modalPasswordEntered;
            
            if (methodSelected && (modalIdEntered || idFieldHidden) && validModalPassword) {
                modalBtn.disabled = false;
                modalBtn.classList.remove('bg-white/20', 'text-white/50');
                modalBtn.classList.add('bg-text-primary', 'text-primary-bg');
            } else {
                modalBtn.disabled = true;
                modalBtn.classList.add('bg-white/20', 'text-white/50');
                modalBtn.classList.remove('bg-text-primary', 'text-primary-bg');
            }
        }

        // Модальное окно
        function openPaymentModal() {
            const modal = document.getElementById('paymentModal');
            const content = document.getElementById('paymentModalContent');
            modal.classList.remove('hidden');
            // Sync desktop inputs to modal
            if (selectedPaymentMethod) {
                const btn = document.querySelector(`#modal-payment-methods button[data-method="${selectedPaymentMethod}"]`);
                if (btn) btn.click();
            }
            document.getElementById('modal-uid').value = document.getElementById('playerId').value;
            
            setTimeout(() => {
                content.classList.remove('translate-y-full');
            }, 10);
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            const content = document.getElementById('paymentModalContent');
            content.classList.add('translate-y-full');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        
        // Покупка
        function handlePurchase() {
            if (!currentCard || !selectedPaymentMethod) return;
            
            const desktopBtn = document.getElementById('purchaseBtn');
            if (desktopBtn.disabled && !document.getElementById('paymentModal').classList.contains('hidden')) {
                 // Check modal button
                 if (document.getElementById('modalPurchaseBtn').disabled) return;
            } else if (desktopBtn.disabled) {
                return;
            }

            const selectedPackage = currentCard.packages[selectedPackageIndex];
            const price = selectedPackage.price;
            const balance = getUserBalance();

            if (selectedPaymentMethod === 'balance') {
                if (balance >= price) {
                    deductFromBalance(price);
                    window.location.href = 'purchase-success.html';
                } else {
                    window.location.href = 'top-up.html?error=insufficient_funds';
                }
            } else {
                // Mock external payment
                window.location.href = 'purchase-success.html';
            }
        }

        document.getElementById('purchaseBtn').addEventListener('click', handlePurchase);
        document.getElementById('modalPurchaseBtn').addEventListener('click', handlePurchase);
        
        // Sync ID inputs
        document.getElementById('playerId').addEventListener('input', (e) => {
            document.getElementById('modal-uid').value = e.target.value;
            updatePurchaseButtonsState();
        });
        document.getElementById('modal-uid').addEventListener('input', (e) => {
            document.getElementById('playerId').value = e.target.value;
            updatePurchaseButtonsState();
        });

        document.addEventListener('DOMContentLoaded', loadCard);
    </script>
</body>
</html>