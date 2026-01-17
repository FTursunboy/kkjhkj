<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Успешная покупка | Lynx</title>
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
    
    <!-- Order Data (for JavaScript) -->
    <script>
        @php
            $userData = auth()->check() ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ] : null;
        @endphp
        window.__USER__ = @json($userData);
        
        // Данные о последнем заказе (если есть)
        window.__LAST_ORDER__ = {!! json_encode($lastOrder ?? null, JSON_UNESCAPED_UNICODE) !!};
    </script>
    
    <style>
        @keyframes drawCheck {
            0% {
                stroke-dashoffset: 10;
                opacity: 0;
            }
            20% {
                opacity: 1;
            }
            100% {
                stroke-dashoffset: 0;
                opacity: 1;
            }
        }
        
        .check-animation {
            stroke-dasharray: 10;
            stroke-dashoffset: 10;
            animation: drawCheck 0.6s ease-out forwards;
            animation-delay: 0.2s;
        }
        
        .rating-star {
            outline: none !important;
            box-shadow: none !important;
            -webkit-tap-highlight-color: transparent;
        }
        .rating-star:focus, .rating-star:active {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body class="font-inter bg-primary-bg min-h-screen">
    
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 glass-strong h-[60px] z-50">
        <div class="container mx-auto px-4 h-full flex items-center justify-between max-w-7xl">
            <a href="/" class="text-2xl font-bold gradient-text">Lynx</a>
            
            <nav class="flex items-center space-x-2 md:space-x-4">
                <a href="/profile" class="hidden md:inline-block text-text-primary hover:opacity-70 transition-custom border border-gray-600/60 rounded-lg md:text-base md:px-4 md:py-2">Профиль</a>
            </nav>
        </div>
    </header>
    
    <!-- Main -->
    <main class="pt-[80px] pb-20 md:pb-8 px-4">
        <div class="container mx-auto max-w-md text-center">
            <div class="bg-surface rounded-lg p-12 border border-gray-600/60">
                <svg class="w-24 h-24 mx-auto mb-6" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" class="text-gray-600 opacity-60" stroke-width="0.25"></circle>
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="0.5" d="M9 12l2 2 4-4" class="text-accent-green check-animation"></path>
                </svg>
                <h1 class="text-3xl font-semibold mb-4">Покупка прошла успешно!</h1>
                
                @if($lastOrder ?? null)
                    <div class="mb-4 text-text-secondary text-sm">
                        <p>Заказ: <span class="text-accent-green font-mono">{{ $lastOrder->order_number }}</span></p>
                        <p>{{ $lastOrder->product_name }} - {{ $lastOrder->package_name }}</p>
                        <p class="text-xs mt-1">Валюта скоро будет зачислена на ваш аккаунт.</p>
                    </div>
                @else
                    <p class="text-text-secondary mb-6">Спасибо за ваш заказ. Валюта скоро будет зачислена на ваш аккаунт.</p>
                @endif
                
                <!-- Форма отзыва -->
                <div class="mt-8">
                    <p class="text-text-secondary text-sm mb-3">Оцените ваш опыт</p>
                    
                    <div id="ratingContainer" class="flex justify-center gap-2 md:gap-3 text-3xl md:text-4xl transition-all duration-300 mb-4">
                        <span class="rating-star cursor-pointer text-text-secondary hover:text-accent-green transition-colors select-none" data-value="1">★</span>
                        <span class="rating-star cursor-pointer text-text-secondary hover:text-accent-green transition-colors select-none" data-value="2">★</span>
                        <span class="rating-star cursor-pointer text-text-secondary hover:text-accent-green transition-colors select-none" data-value="3">★</span>
                        <span class="rating-star cursor-pointer text-text-secondary hover:text-accent-green transition-colors select-none" data-value="4">★</span>
                        <span class="rating-star cursor-pointer text-text-secondary hover:text-accent-green transition-colors select-none" data-value="5">★</span>
                    </div>
                    
                    <!-- Форма отзыва (скрыта по умолчанию) -->
                    <div id="reviewForm" class="w-full hidden opacity-0 transition-all duration-500 transform translate-y-4">
                        <textarea 
                            id="reviewText"
                            class="w-full bg-primary-bg border border-gray-600/60 rounded-lg p-3 text-text-primary text-sm focus:outline-none focus:border-accent-green transition-colors resize-none mb-3"
                            rows="3"
                            placeholder="Оставьте отзыв — ваше мнение очень важно для нас"
                        ></textarea>
                        <button id="submitReviewBtn" class="w-full bg-white text-black font-bold py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            Отправить отзыв
                        </button>
                    </div>
                    
                    <!-- Кнопка "Пропустить" -->
                    <button id="skipBtn" class="mt-4 text-text-secondary text-sm hover:text-white transition-colors">
                        Пропустить
                    </button>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stars = document.querySelectorAll('.rating-star');
            const ratingContainer = document.getElementById('ratingContainer');
            const reviewForm = document.getElementById('reviewForm');
            const reviewText = document.getElementById('reviewText');
            const submitBtn = document.getElementById('submitReviewBtn');
            const skipBtn = document.getElementById('skipBtn');
            
            let currentRating = 0;
            const lastOrder = window.__LAST_ORDER__;
            const user = window.__USER__;

            // Функция отображения рейтинга
            function setRatingDisplay(rating) {
                stars.forEach(star => {
                    const value = parseInt(star.dataset.value, 10);
                    if (value <= rating) {
                        star.classList.add('text-accent-green');
                        star.classList.remove('text-text-secondary');
                    } else {
                        star.classList.add('text-text-secondary');
                        star.classList.remove('text-accent-green');
                    }
                });
            }

            // Обработка выбора звезд
            stars.forEach(star => {
                const value = parseInt(star.dataset.value, 10);

                star.addEventListener('mouseenter', () => {
                    if (!currentRating) {
                        setRatingDisplay(value);
                    }
                });

                star.addEventListener('click', () => {
                    currentRating = value;
                    setRatingDisplay(currentRating);
                    
                    // Показываем форму отзыва
                    if (reviewForm) {
                        reviewForm.classList.remove('hidden');
                        setTimeout(() => {
                            reviewForm.classList.remove('opacity-0', 'translate-y-4');
                        }, 10);
                    }
                    
                    // Скрываем кнопку "Пропустить"
                    if (skipBtn) {
                        skipBtn.style.display = 'none';
                    }
                });
            });

            if (ratingContainer) {
                ratingContainer.addEventListener('mouseleave', () => {
                    setRatingDisplay(currentRating);
                });
            }
            
            // Обработка отправки отзыва
            if (submitBtn) {
                submitBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    
                    if (!currentRating) {
                        alert('Пожалуйста, выберите оценку');
                        return;
                    }
                    
                    if (!lastOrder) {
                        alert('Не удалось найти информацию о заказе');
                        return;
                    }
                    
                    // Блокируем кнопку
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Отправка...';
                    
                    try {
                        const response = await fetch('/api/reviews', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                order_id: lastOrder.id,
                                game_id: lastOrder.details?.product_id || lastOrder.product_type,
                                rating: currentRating,
                                text: reviewText.value.trim(),
                                package_name: lastOrder.package_name
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Показываем сообщение об успехе
                            reviewForm.innerHTML = `
                                <div class="text-white flex flex-col items-center py-4">
                                    <svg class="w-12 h-12 text-accent-green mb-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="font-semibold text-lg">Спасибо за отзыв!</span>
                                </div>
                            `;
                            
                            // Редирект через 2 секунды
                            setTimeout(() => {
                                window.location.href = '/profile';
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Ошибка при отправке отзыва');
                        }

                    } catch (error) {
                        console.error('Ошибка:', error);
                        alert('Произошла ошибка при отправке отзыва: ' + error.message);
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Отправить отзыв';
                    }
                });
            }
            
            // Обработка кнопки "Пропустить"
            if (skipBtn) {
                skipBtn.addEventListener('click', () => {
                    window.location.href = '/profile';
                });
            }
        });
    </script>
    
</body>
</html>
