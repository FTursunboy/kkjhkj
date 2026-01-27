<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="{{ asset('/') }}">
    <title>Lynx | Виртуальная валюта для игр</title>
    <meta name="description" content="Покупайте внутриигровую валюту мгновенно. Быстро, безопасно, удобно.">
    <meta name="keywords" content="внутриигровая валюта, купить валюту, gamecoins, lynx, uc, gems, premium, gift card">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Lynx | Виртуальная валюта для игр">
    <meta property="og:description" content="Покупайте валюту и гифткарты мгновенно. Быстро, безопасно, удобно.">
    <meta property="og:image" content="{{ asset('images/telegram.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">

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
        #scrollLeftBtn:focus,
        #scrollRightBtn:focus,
        #scrollLeftBtn2:focus,
        #scrollRightBtn2:focus {
            outline: none;
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
                @auth
                <a href="/top-up" class="flex items-center justify-center text-text-primary hover:opacity-70 transition-custom border border-gray-600/60 rounded-lg px-5 py-2 text-xs md:text-base md:px-4 md:py-2">
                    <span class="user-balance whitespace-nowrap">0 ₽</span>
                    <div class="w-5 h-5 ml-2 rounded-full flex items-center justify-center border border-gray-600/60">
                        <svg class="w-3 h-3 text-accent-purple" style="filter: drop-shadow(0 0 2px #00ff88);" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                </a>
                @endauth
                <a href="/profile" class="hidden md:inline-block text-text-primary hover:opacity-70 transition-custom border border-gray-600/60 rounded-lg md:text-base md:px-4 md:py-2">Профиль</a>
            </nav>
        </div>

        <div id="mobileMenu" class="hidden md:hidden bg-surface border-b border-surface">
            <nav class="container mx-auto px-4 py-4 flex flex-col space-y-4">
                @auth
                <a href="/top-up" class="text-text-primary hover:opacity-70 transition-custom">Пополнить</a>
                @endauth
                <a href="/profile" class="text-text-primary hover:opacity-70 transition-custom">Профиль</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-[60px] pb-20 md:pb-0">
        <!-- Popular Games -->
        <section class="pt-28 pb-0 px-4 md:px-8 lg:px-12">
            <div class="container mx-auto max-w-7xl">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 scroll-reveal">
                    Покупка <span class="gradient-text-accent">цифровых товаров</span>
                </h2>

                <div class="relative mt-8">
                    <div class="overflow-hidden bg-surface rounded-3xl pt-6 pb-2">
                        <div class="px-8 mb-4">
                            <h3 class="text-2xl font-semibold mb-2">
                                Игры
                            </h3>
                            <div class="flex items-center space-x-6 border-b border-white/10">
                                <button class="filter-btn active" onclick="filterPopularGames('all')">
                                    <span>Все</span>
                                </button>
                                <button class="filter-btn" onclick="filterPopularGames('mobile')">
                                    <span>Мобильные</span>
                                </button>
                                <button class="filter-btn" onclick="filterPopularGames('pc')">
                                    <span>ПК</span>
                                </button>
                            </div>
                        </div>

                        <div id="gamesContainer" class="overflow-x-auto overflow-y-hidden pb-2 scrollbar-hide snap-x snap-mandatory" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                            <div id="popularGamesGrid" class="grid grid-flow-col grid-rows-2 gap-x-2.5 gap-y-1 w-max pl-10">
                                @foreach($games as $game)
                                    <a href="/game/{{ $game['slug'] }}"
                                       class="game-card block w-20 md:w-32 text-center group snap-start"
                                       data-platform="{{ strtolower($game['platform'] ?? '') }}">
                                        @if($game['image'])
                                            <div class="card-hover bg-surface rounded-lg cursor-pointer relative bg-cover bg-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                                                <img src="{{ $game['image'] }}" alt="{{ $game['name'] }}" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="card-hover bg-surface rounded-lg p-6 cursor-pointer flex items-center justify-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                                                <svg class="w-10 h-10 md:w-16 md:h-16 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $game['icon'] ?? 'M5 12h14' }}"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <h3 class="text-[10px] md:text-base font-semibold mt-2 text-text-primary transition-colors">{{ $game['name'] }}</h3>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="absolute top-9 right-8 hidden md:flex space-x-2 z-20">
                            <button id="scrollLeftBtn" onclick="scrollGames('left')" class="w-12 h-12 flex items-center justify-center bg-black/30 hover:bg-black/60 rounded-full transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button id="scrollRightBtn" onclick="scrollGames('right')" class="w-12 h-12 flex items-center justify-center bg-black/30 hover:bg-black/60 rounded-full transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Second Games Section -->
        <section class="pt-4 pb-20 px-4 md:px-8 lg:px-12">
            <div class="container mx-auto max-w-7xl">
                <div class="relative mt-8">
                    <div class="overflow-hidden bg-surface rounded-3xl pt-6 pb-2">
                        <div class="px-8 mb-4">
                            <h3 class="text-2xl font-semibold mb-4 pt-4">
                                Подписки и сервисы
                            </h3>
                            <div class="border-b border-white/10 pt-4"></div>
                        </div>

                        <div id="gamesContainer2" class="overflow-x-auto overflow-y-hidden pb-2 scrollbar-hide snap-x snap-mandatory" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                            <div id="popularGamesGrid2" class="grid grid-flow-col grid-rows-2 gap-x-2.5 gap-y-1 w-max pl-10">
                                @foreach($giftCards as $card)
                                    <a href="/gift-card/{{ $card['slug'] }}" class="block w-20 md:w-32 text-center group snap-start">
                                        @if($card['image'])
                                            <div class="card-hover bg-surface rounded-lg cursor-pointer relative bg-cover bg-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                                                <img src="{{ $card['image'] }}" alt="{{ $card['name'] }}" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="card-hover bg-surface rounded-lg p-6 cursor-pointer flex items-center justify-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                                                <svg class="w-10 h-10 md:w-16 md:h-16 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] ?? 'M5 12h14' }}"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <h3 class="text-[10px] md:text-base font-semibold mt-2 text-text-primary transition-colors">{{ $card['name'] }}</h3>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="absolute top-9 right-8 hidden md:flex space-x-2 z-20">
                            <button id="scrollLeftBtn2" onclick="scrollGames2('left')" class="w-12 h-12 flex items-center justify-center bg-black/30 hover:bg-black/60 rounded-full transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button id="scrollRightBtn2" onclick="scrollGames2('right')" class="w-12 h-12 flex items-center justify-center bg-black/30 hover:bg-black/60 rounded-full transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- Mobile Nav -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-surface h-16 z-40 flex items-center justify-around mobile-nav-border">
        <!-- Главная -->
        <a href="/" class="mobile-nav-item active flex flex-col items-center justify-center px-4 py-1 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10 0h3a1 1 0 001-1V10M9 21h6" />
            </svg>
            <span class="text-xs font-medium">Главная</span>
        </a>
        <!-- Пополнить -->
        @auth
        <a href="/top-up" class="mobile-nav-item flex flex-col items-center justify-center px-4 py-1 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="text-xs font-medium">Пополнить</span>
        </a>
        @endauth
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
    <script>
        let giftCardsData = [];

        async function loadGiftCardsData() {
            try {
                const response = await fetch('gift-cards-data.json');
                const data = await response.json();
                giftCardsData = data.gift_cards;
            } catch (error) {
                console.error('Ошибка загрузки данных подарочных карт:', error);
            }
        }

        async function loadPopularItems() {
            await Promise.all([loadGamesData(), loadGiftCardsData()]);

            const allGames = getAllGames();
            const container = document.getElementById('popularGamesGrid');

            if (container && allGames.length > 0) {
                container.innerHTML = allGames.map(game => generateGameCard(game, '/game')).join('');
                setTimeout(updateScrollButtons, 100);
            }

            const container2 = document.getElementById('popularGamesGrid2');
            if (container2) {
                container2.innerHTML = giftCardsData.map(service => generateServiceCard(service)).join('');
                setTimeout(updateScrollButtons2, 100);
            }
        }

        function generateServiceCard(service) {
            let cardContent;
            if (service.image) {
                cardContent = `<div class="card-hover bg-surface rounded-lg cursor-pointer relative bg-cover bg-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                                   <img src="${service.image}" alt="${service.name}" class="w-full h-full object-cover">
                               </div>`;
            } else {
                cardContent = `<div class="card-hover bg-surface rounded-lg p-6 cursor-pointer flex items-center justify-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                           <svg class="w-10 h-10 md:w-16 md:h-16 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${service.icon}"/>
                           </svg>
                       </div>`;
            }

            const slug = service.slug || service.id;
            return `
                <a href="/gift-card/${slug}" class="block w-20 md:w-32 text-center group">
                    ${cardContent}
                    <h3 class="text-[10px] md:text-base font-semibold mt-2 text-text-primary transition-colors">${service.name}</h3>
                </a>
            `;
        }

        function filterPopularGames(category) {
            // Находим кнопки только в первом блоке
            const firstSection = document.querySelector('section:first-of-type');
            const buttons = firstSection.querySelectorAll('.filter-btn');
            buttons.forEach(button => button.classList.remove('active'));

            const activeButton = [...buttons].find(button => button.getAttribute('onclick') === `filterPopularGames('${category}')`);
            if (activeButton) {
                activeButton.classList.add('active');
            }

            const gameCards = document.querySelectorAll('#popularGamesGrid .game-card');

            gameCards.forEach(card => {
                const platform = card.getAttribute('data-platform') || '';

                if (category === 'all') {
                    card.style.display = '';
                } else if (category === 'mobile') {
                    card.style.display = platform.includes('mobile') ? '' : 'none';
                } else if (category === 'pc') {
                    // Показываем если платформа содержит "pc"
                    card.style.display = platform.includes('pc') ? '' : 'none';
                }
            });

            setTimeout(updateScrollButtons, 100);
        }

        // Функция для прокрутки категории игр
        function scrollGames(direction) {
            const container = document.getElementById('gamesContainer');
            const scrollAmount = 256; // ширина карточки + gap

            if (direction === 'left') {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            }
        }

        // Управление видимостью кнопок
        function updateScrollButtons() {
            const container = document.getElementById('gamesContainer');
            const leftBtn = document.getElementById('scrollLeftBtn');
            const rightBtn = document.getElementById('scrollRightBtn');

            if (!container || !leftBtn || !rightBtn) return;

            // Проверяем, находимся ли мы в начале
            if (container.scrollLeft <= 0) {
                leftBtn.style.opacity = '0.3';
                leftBtn.style.pointerEvents = 'none';
            } else {
                leftBtn.style.opacity = '1';
                leftBtn.style.pointerEvents = 'auto';
            }

            // Проверяем, находимся ли мы в конце
            if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 10) {
                rightBtn.style.opacity = '0.3';
                rightBtn.style.pointerEvents = 'none';
            } else {
                rightBtn.style.opacity = '1';
                rightBtn.style.pointerEvents = 'auto';
            }
        }

        // Поддержка свайпа на мобильных устройствах
        const gamesContainer = document.getElementById('gamesContainer');

        // Инициализация состояния кнопок
        if (gamesContainer) {
            updateScrollButtons();
            gamesContainer.addEventListener('scroll', updateScrollButtons);
            window.addEventListener('resize', updateScrollButtons);
        }

        // Функция для прокрутки второго блока игр
        function scrollGames2(direction) {
            const container = document.getElementById('gamesContainer2');
            const scrollAmount = 256; // ширина карточки + gap

            if (direction === 'left') {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            }
        }

        // Управление видимостью кнопок для второго блока
        function updateScrollButtons2() {
            const container = document.getElementById('gamesContainer2');
            const leftBtn = document.getElementById('scrollLeftBtn2');
            const rightBtn = document.getElementById('scrollRightBtn2');

            if (!container || !leftBtn || !rightBtn) return;

            // Проверяем, находимся ли мы в начале
            if (container.scrollLeft <= 0) {
                leftBtn.style.opacity = '0.3';
                leftBtn.style.pointerEvents = 'none';
            } else {
                leftBtn.style.opacity = '1';
                leftBtn.style.pointerEvents = 'auto';
            }

            // Проверяем, находимся ли мы в конце
            if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 10) {
                rightBtn.style.opacity = '0.3';
                rightBtn.style.pointerEvents = 'none';
            } else {
                rightBtn.style.opacity = '1';
                rightBtn.style.pointerEvents = 'auto';
            }
        }

        // Поддержка свайпа на мобильных устройствах для второго блока
        const gamesContainer2 = document.getElementById('gamesContainer2');

        // Инициализация состояния кнопок для второго блока
        if (gamesContainer2) {
            updateScrollButtons2();
            gamesContainer2.addEventListener('scroll', updateScrollButtons2);
            window.addEventListener('resize', updateScrollButtons2);
        }
    </script>
</body>
</html>
