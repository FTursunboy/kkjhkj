<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль | Lynx</title>
    <meta name="description" content="Управляйте своим профилем и просматривайте историю покупок.">
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
                <a href="/profile" class="hidden md:inline-block text-text-primary hover:opacity-70 transition-custom border border-gray-600/60 rounded-lg md:text-base md:px-4 md:py-2">Профиль</a>
            </nav>
        </div>
    </header>
    
    <!-- Main -->
    <main class="pt-[80px] pb-24 md:pb-12 px-4">
        <div class="container mx-auto max-w-5xl">
            
            @if(session('success'))
                <div class="bg-accent-green/10 border border-accent-green/30 text-accent-green px-6 py-4 rounded-2xl mb-6">
                    {{ session('success') }}
                </div>
            @endif
            
            <!-- Profile Header -->
            <div class="bg-gradient-to-br from-[#1a1a1a] via-[#151515] to-[#0f0f0f] rounded-3xl p-8 md:p-10 mb-6 relative overflow-hidden border border-white/[0.03]">
                <!-- Background glow effects -->
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-accent-green/5 rounded-full blur-[100px] pointer-events-none"></div>
                <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-purple-500/5 rounded-full blur-[80px] pointer-events-none"></div>
                
                <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                     <!-- Left Side: User -->
                     <div class="flex items-center gap-5 w-full md:w-auto">
                        <!-- Avatar -->
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-2xl bg-gradient-to-br from-white/10 to-white/5 border border-white/10 flex items-center justify-center text-4xl select-none shrink-0 shadow-2xl shadow-black/50 overflow-hidden">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="Аватар" class="w-full h-full object-cover">
                            @else
                                <span class="opacity-60">👤</span>
                            @endif
                        </div>
                        
                        <!-- User Info -->
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold mb-2 text-white">{{ $user->name }}</h1>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/[0.03] rounded-lg text-[11px] font-semibold text-white/40 border border-white/[0.05]">
                                <svg class="w-3 h-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                ID: {{ $user->id }}
                            </span>
                        </div>
                     </div>

                    <!-- Right Side: Balance -->
                    <div class="bg-gradient-to-br from-[#0a0a0a] to-[#111] p-6 rounded-2xl w-full md:w-auto md:min-w-[260px] border border-white/[0.05] shadow-xl">
                        <p class="text-white/30 text-[10px] uppercase tracking-[0.2em] mb-2 font-medium">Баланс</p>
                        <div class="text-4xl font-bold text-white mb-5 tracking-tight">{{ number_format($user->balance, 0, ',', ' ') }} ₽</div>
                        <a href="/top-up" class="flex items-center justify-center gap-2 w-full py-3 text-black rounded-xl text-sm font-bold transition-all duration-200 ease-out hover:shadow-lg hover:shadow-accent-green/20 focus:outline-none focus:ring-0 outline-none" style="background: linear-gradient(90deg, rgba(0, 255, 136, 1) 25%, rgba(52, 211, 153, 1) 56%)">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Пополнить
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-[#141414] rounded-3xl p-6 md:p-8 mb-6 border border-white/[0.03]">
                <h2 class="text-base font-bold mb-6 text-white/80 flex items-center gap-2">
                    <svg class="w-4 h-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Личные данные
                </h2>
                
                <form action="/profile/update" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                        <!-- Username -->
                        <div class="space-y-2">
                             <label class="text-[10px] font-bold text-white/30 uppercase tracking-wider ml-1">Имя пользователя</label>
                             <div class="relative group">
                                <input name="name" type="text" value="{{ $user->name }}" class="w-full bg-[#0a0a0a] border border-white/[0.05] rounded-xl px-4 py-3.5 text-white text-sm font-medium transition-all outline-none focus:border-accent-green/30">
                             </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="space-y-2">
                             <label class="text-[10px] font-bold text-white/30 uppercase tracking-wider ml-1">Email</label>
                             <div class="relative">
                                <input type="email" value="{{ $user->email }}" disabled readonly class="w-full bg-[#0a0a0a] border border-white/[0.05] rounded-xl px-4 py-3.5 text-white/25 cursor-default text-sm font-medium pointer-events-none select-none" tabindex="-1">
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-white/15">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                             </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-white/[0.03]">
                         <!-- Logout Button -->
                         <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="px-4 py-2.5 rounded-xl text-red-400/70 hover:text-red-400 hover:bg-red-500/10 transition-all flex items-center gap-2 group">
                            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="text-sm font-medium">Выйти</span>
                        </a>

                        <button type="submit" class="bg-white text-black px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-white/90 transition-all active:scale-[0.98]">
                            Сохранить
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Orders -->
            <div class="bg-[#141414] rounded-3xl p-6 md:p-8 border border-white/[0.03]">
                <h3 class="text-base font-bold text-white/80 mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Последние заказы
                </h3>
                <div class="overflow-x-auto -mx-6 md:mx-0 px-6 md:px-0">
                    @if($orders->isEmpty())
                        <div class="text-center py-12 text-white/30">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm">У вас пока нет заказов</p>
                        </div>
                    @else
                        <table class="w-full text-left border-collapse min-w-[550px]">
                            <thead>
                                <tr class="text-white/25 text-[10px] uppercase tracking-widest">
                                    <th class="py-3 font-semibold pl-4">ID</th>
                                    <th class="py-3 font-semibold">Товар</th>
                                    <th class="py-3 font-semibold">Дата</th>
                                    <th class="py-3 font-semibold">Статус</th>
                                    <th class="py-3 font-semibold text-right pr-4">Сумма</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @foreach($orders as $order)
                                    <tr class="border-t border-white/[0.03] hover:bg-white/[0.02] transition-colors">
                                        <td class="py-4 pl-4 text-white/60 font-mono text-xs">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="py-4">
                                            <div>
                                                <p class="text-white font-medium">{{ $order->product_name }}</p>
                                                <p class="text-white/40 text-xs mt-0.5">{{ $order->package_name }}</p>
                                            </div>
                                        </td>
                                        <td class="py-4 text-white/60">
                                            {{ $order->created_at->format('d.m.Y') }}
                                            <span class="text-white/30 text-xs block">{{ $order->created_at->format('H:i') }}</span>
                                        </td>
                                        <td class="py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium {{ $order->status_color }} bg-white/[0.03] border border-white/[0.05]">
                                                @if($order->status === 'completed')
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                @elseif($order->status === 'pending')
                                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                @elseif($order->status === 'failed')
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="py-4 text-right pr-4 font-semibold text-white">
                                            {{ number_format($order->amount, 0, ',', ' ') }} ₽
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Logout Form (hidden) -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

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
        @auth
        <a href="/top-up" class="mobile-nav-item flex flex-col items-center justify-center px-4 py-1 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="text-xs font-medium">Пополнить</span>
        </a>
        @endauth
        <!-- Профиль -->
        <a href="/profile" class="mobile-nav-item active flex flex-col items-center justify-center px-4 py-1 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs font-medium">Профиль</span>
        </a>
    </nav>

    <script src="{{ asset('live-search.js') }}"></script>
</body>
</html>
