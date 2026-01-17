<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="{{ asset('/') }}">
    <title>Пополнение баланса | Lynx</title>
    <meta name="description" content="Пополняйте баланс для покупки игровой валюты быстро и безопасно.">
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <style>
        /* Убираем чёрный квадрат вокруг radio кнопок */
        input[type="radio"] {
            outline: none !important;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: transparent;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        
        input[type="radio"]:checked {
            border-color: #FFFFFF !important;
            background-color: #FFFFFF !important;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.5) !important;
        }
        
        input[type="radio"]::before {
            box-shadow: inset 1em 1em #FFFFFF !important;
        }
        
        input[type="radio"]:checked::before {
            box-shadow: inset 1em 1em #FFFFFF !important;
        }
        
        input[type="radio"]:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        
        input[type="radio"]:focus-visible {
            outline: none !important;
        }
        
        /* Убираем кнопки увеличения/уменьшения у input type="number" */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        input[type="number"] {
            -moz-appearance: textfield;
        }
        
        /* Убираем внешнюю рамку при фокусе */
        input[type="number"]:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        
        input[type="number"]:focus-visible {
            outline: none !important;
        }
        
        /* Убираем движение текста для payment-option */
        .payment-option {
            transform: none !important;
        }
        
        .payment-option:hover {
            transform: none !important;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .payment-option:focus {
            outline: none !important;
        }
        
        .payment-option:focus-within {
            outline: none !important;
        }

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
                <a href="/top-up" class="flex items-center justify-center text-text-primary hover:opacity-70 transition-custom border border-gray-600/60 rounded-lg px-5 py-2 text-xs md:text-base md:px-4 md:py-2">
                    <span class="user-balance whitespace-nowrap">{{ number_format($user->balance, 0, '.', ' ') }} ₽</span>
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
        <div class="container mx-auto max-w-2xl">
            <button onclick="history.back()" class="mb-6 flex items-center gap-2 text-text-secondary hover:text-text-primary transition-custom inline-flex outline-none border-none focus:outline-none group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Назад
            </button>
            

            <div id="insufficientFundsError" class="hidden bg-red-900/30 border border-red-500/50 text-red-300 rounded-2xl p-5 mb-6 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h2 class="font-semibold">Недостаточно средств</h2>
                        <p class="text-sm opacity-80">Пополните баланс для приобретения услуги</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-[#1a1a1a] via-[#151515] to-[#111] rounded-3xl p-6 md:p-8 border border-white/[0.05] shadow-2xl">
                <!-- Amount Input -->
                <div class="mb-6">
                    <label class="block text-sm text-text-secondary mb-3 font-medium">Сумма пополнения</label>
                    <div class="relative">
                        <input 
                            type="number" 
                            id="topupAmount"
                            placeholder="0" 
                            class="w-full bg-black/40 border-2 border-white/10 rounded-2xl px-5 py-4 text-2xl font-bold text-text-primary placeholder-white/20 focus:border-white/50 transition-all"
                        >
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-2xl font-bold text-white/30">₽</span>
                    </div>
                </div>

                <!-- Quick Amount Buttons -->
                <div class="grid grid-cols-4 gap-2 mb-8">
                    <button onclick="setAmount(100)" class="quick-amount-btn py-3 px-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/50 text-sm font-semibold transition-all hover:scale-105">
                        100 ₽
                    </button>
                    <button onclick="setAmount(500)" class="quick-amount-btn py-3 px-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/50 text-sm font-semibold transition-all hover:scale-105">
                        500 ₽
                    </button>
                    <button onclick="setAmount(1000)" class="quick-amount-btn py-3 px-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/50 text-sm font-semibold transition-all hover:scale-105">
                        1 000 ₽
                    </button>
                    <button onclick="setAmount(5000)" class="quick-amount-btn py-3 px-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/50 text-sm font-semibold transition-all hover:scale-105">
                        5 000 ₽
                    </button>
                </div>
                
                <!-- Payment Methods -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Способ оплаты
                    </h2>
                    <div class="space-y-3" id="paymentMethods">
                        <label class="payment-option flex items-center gap-4 rounded-2xl p-4 cursor-pointer transition-all bg-black/30 border-2 border-white/5 hover:border-white/40 group">
                            <input type="radio" name="payment" value="sbp" class="w-5 h-5" checked>
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-pink-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <span class="font-semibold block">СБП</span>
                                <span class="text-xs text-text-secondary">Быстрый перевод по номеру</span>
                            </div>
                            <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-50 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </label>
                        <label class="payment-option flex items-center gap-4 rounded-2xl p-4 cursor-pointer transition-all bg-black/30 border-2 border-white/5 hover:border-white/40 group">
                            <input type="radio" name="payment" value="crypto" class="w-5 h-5">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-yellow-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.638 14.904c-1.602 6.43-8.113 10.34-14.542 8.736C2.67 22.05-1.244 15.525.362 9.105 1.962 2.67 8.475-1.243 14.9.358c6.43 1.605 10.342 8.115 8.738 14.546z"/>
                                    <path fill="#1a1a1a" d="M15.075 10.413c.207-1.378-.844-2.12-2.28-2.615l.466-1.868-1.137-.283-.453 1.818c-.299-.074-.606-.144-.911-.213l.456-1.83-1.136-.283-.466 1.867c-.247-.056-.49-.111-.724-.17l.001-.005-1.567-.392-.302 1.215s.844.194.826.206c.46.115.544.42.53.662l-.531 2.13c.032.008.073.02.118.038l-.12-.03-.744 2.986c-.057.14-.2.35-.523.27.012.017-.827-.207-.827-.207l-.565 1.302 1.48.37c.275.069.544.14.809.208l-.47 1.89 1.135.283.466-1.87c.31.085.612.163.906.237l-.464 1.86 1.137.283.47-1.886c1.936.367 3.392.219 4.005-1.533.494-1.411-.024-2.225-.997-2.756.71-.164 1.243-.632 1.386-1.598zm-2.48 3.477c-.351 1.411-2.726.649-3.496.457l.624-2.5c.77.192 3.24.573 2.872 2.043zm.352-3.495c-.32 1.284-2.297.631-2.938.472l.566-2.267c.64.16 2.704.457 2.372 1.795z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <span class="font-semibold block">Криптовалюта</span>
                                <span class="text-xs text-text-secondary">Bitcoin, USDT, ETH</span>
                            </div>
                            <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-50 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </label>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button id="topupBtn" type="button" class="w-full py-4 rounded-2xl font-bold text-lg text-black transition-all hover:shadow-lg hover:shadow-accent-green/25 active:scale-[0.98]" style="background: linear-gradient(90deg, rgba(0, 255, 136, 1) 0%, rgba(52, 211, 153, 1) 100%)">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Пополнить баланс
                    </span>
                </button>

                <!-- Security Note -->
                <div class="mt-6 flex items-center justify-center gap-2 text-text-secondary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Безопасная оплата с шифрованием данных</span>
                </div>
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
        <a href="/top-up" class="mobile-nav-item active flex flex-col items-center justify-center px-4 py-1 rounded-lg text-text-secondary hover:text-text-primary transition-colors">
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
    <script>
        // Quick amount selection
        function setAmount(amount) {
            document.getElementById('topupAmount').value = amount;
            // Highlight active button
            document.querySelectorAll('.quick-amount-btn').forEach(btn => {
                btn.classList.remove('border-white/60', 'bg-white/10');
                btn.classList.add('border-white/10', 'bg-white/5');
            });
            event.target.classList.remove('border-white/10', 'bg-white/5');
            event.target.classList.add('border-white/60', 'bg-white/10');
        }

        // Payment method selection visual feedback
        document.querySelectorAll('.payment-option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('border-white/50', 'bg-white/5');
                    opt.classList.add('border-white/5');
                });
                if (radio.checked) {
                    radio.closest('.payment-option').classList.remove('border-white/5');
                    radio.closest('.payment-option').classList.add('border-white/50', 'bg-white/5');
                }
            });
        });

        // Initialize first payment option as selected
        document.addEventListener('DOMContentLoaded', () => {
            const firstOption = document.querySelector('.payment-option');
            if (firstOption) {
                firstOption.classList.remove('border-white/5');
                firstOption.classList.add('border-white/50', 'bg-white/5');
            }

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('error') === 'insufficient_funds') {
                const errorDiv = document.getElementById('insufficientFundsError');
                errorDiv.classList.remove('hidden');
            }

            // Обработчик кнопки пополнения
            const topupBtn = document.getElementById('topupBtn');
            const topupAmountInput = document.getElementById('topupAmount');
            
            if (topupBtn && topupAmountInput) {
                topupBtn.addEventListener('click', async () => {
                    const amount = parseFloat(topupAmountInput.value);
                    const selectedPayment = document.querySelector('input[name="payment"]:checked');
                    const method = selectedPayment ? selectedPayment.value : 'sbp';

                    // Валидация
                    if (!amount || amount < 100) {
                        showNotification('Минимальная сумма пополнения — 100₽', 'error');
                        return;
                    }

                    if (amount > 100000) {
                        showNotification('Максимальная сумма пополнения — 100,000₽', 'error');
                        return;
                    }

                    // Отключаем кнопку
                    topupBtn.disabled = true;
                    topupBtn.querySelector('span').innerHTML = `
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h1M3 12H2m15.325-7.757l-.707-.707M6.343 17.657l-.707-.707M16.95 16.95l.707.707M7.05 7.05l.707-.707"/>
                        </svg>
                        Обработка...
                    `;

                    try {
                        const response = await fetch('{{ route('top-up.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ amount, method })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Обновляем баланс на странице
                            document.querySelectorAll('.user-balance').forEach(el => {
                                el.textContent = `${data.new_balance.toLocaleString('ru-RU')} ₽`;
                            });

                            // Обновляем баланс в window.__USER__
                            if (window.__USER__) {
                                window.__USER__.balance = data.new_balance;
                            }

                            showNotification(`Баланс успешно пополнен на ${amount.toLocaleString('ru-RU')}₽!`, 'success');
                            
                            // Очищаем поле ввода
                            topupAmountInput.value = '';

                            // Через 2 секунды перенаправляем на профиль
                            setTimeout(() => {
                                window.location.href = '{{ route('profile') }}';
                            }, 2000);
                        } else {
                            showNotification(data.message || 'Ошибка при пополнении баланса', 'error');
                        }
                    } catch (error) {
                        console.error('Ошибка при пополнении баланса:', error);
                        showNotification('Произошла ошибка при пополнении баланса', 'error');
                    } finally {
                        // Восстанавливаем кнопку
                        topupBtn.disabled = false;
                        topupBtn.querySelector('span').innerHTML = `
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            Пополнить баланс
                        `;
                    }
                });
            }

            // Функция уведомлений
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-20 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white font-semibold transition-all transform translate-x-0 ${
                    type === 'success' ? 'bg-green-600' : 'bg-red-600'
                }`;
                notification.textContent = message;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.transform = 'translateX(400px)';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>

