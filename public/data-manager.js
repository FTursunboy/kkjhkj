// ============================================
// DATA MANAGER - Управление данными игр и корзиной
// ============================================

let gamesData = null;

// ============================================
// ЗАГРУЗКА ДАННЫХ
// ============================================

async function loadGamesData() {
    if (gamesData) return gamesData;

    try {
        const response = await fetch('games-data.json');
        const data = await response.json();
        gamesData = data.games;
        return gamesData;
    } catch (error) {
        console.error('Ошибка загрузки данных игр:', error);
        return [];
    }
}

// ============================================
// ПОИСК И ФИЛЬТРАЦИЯ ИГР
// ============================================

function getGameById(id) {
    if (!gamesData) return null;
    // Приводим оба ID к строке для корректного сравнения
    return gamesData.find(game => String(game.id) === String(id));
}

function getAllGames() {
    return gamesData || [];
}

// ============================================
// ПОЛЬЗОВАТЕЛЬ / РЕГИСТРАЦИЯ
// ============================================

// Generate unique user ID
function generateUserId() {
    return Math.floor(100000 + Math.random() * 900000); // 6-digit number
}

// Get or create user ID
function getUserId() {
    const user = getCurrentUser();
    if (user && user.id) {
        return user.id;
    }
    // Generate new ID if doesn't exist
    const newId = generateUserId();
    return newId;
}

// Get all registered usernames
function getAllUsernames() {
    try {
        const raw = localStorage.getItem('gamecoins_all_usernames');
        return raw ? JSON.parse(raw) : {};
    } catch (e) {
        return {};
    }
}

// Save username to registry
function registerUsername(userId, username) {
    try {
        const usernames = getAllUsernames();
        usernames[userId] = username.toLowerCase();
        localStorage.setItem('gamecoins_all_usernames', JSON.stringify(usernames));
    } catch (e) {
        console.warn('Не удалось сохранить имя пользователя', e);
    }
}

// Check if username is taken by another user
function isUsernameTaken(username, currentUserId) {
    const usernames = getAllUsernames();
    const lowerUsername = username.toLowerCase();

    for (const [userId, name] of Object.entries(usernames)) {
        if (name === lowerUsername && String(userId) !== String(currentUserId)) {
            return true;
        }
    }
    return false;
}

function getCurrentUser() {
    try {
        const raw = localStorage.getItem('gamecoins_user');
        return raw ? JSON.parse(raw) : null;
    } catch (e) {
        console.warn('Не удалось прочитать пользователя из localStorage', e);
        return null;
    }
}

function setCurrentUser(user) {
    try {
        localStorage.setItem('gamecoins_user', JSON.stringify(user));
    } catch (e) {
        console.warn('Не удалось сохранить пользователя в localStorage', e);
    }
}

/**
 * Авторизация через OAuth провайдера (Telegram, Google)
 * @param {string} provider - 'telegram' или 'google'
 * @param {object} authData - данные от провайдера
 * @param {string} authData.id - ID пользователя у провайдера
 * @param {string} authData.name - Имя пользователя
 * @param {string} authData.email - Email (опционально)
 * @param {string} authData.photoUrl - URL фото профиля
 */
function loginWithProvider(provider, authData) {
    const currentUser = getCurrentUser();
    const userId = (currentUser && currentUser.id) ? currentUser.id : generateUserId();

    const user = {
        id: userId,
        name: authData.name || 'Пользователь',
        email: authData.email || '',
        registered: true,
        provider: provider, // 'telegram' или 'google'
        providerId: authData.id,
        avatarUrl: authData.photoUrl || null, // Фото из Telegram/Google
        telegramId: provider === 'telegram' ? authData.id : null
    };

    setCurrentUser(user);
    if (user.name) {
        registerUsername(userId, user.name);
    }

    return user;
}

// Пример использования:
// Telegram: loginWithProvider('telegram', { id: '123456', name: 'Иван', photoUrl: 'https://t.me/i/userpic/...' })
// Google: loginWithProvider('google', { id: 'abc123', name: 'Иван', email: 'ivan@gmail.com', photoUrl: 'https://lh3.googleusercontent.com/...' })

function isUserRegistered() {
    const user = getCurrentUser();
    return !!(user && user.registered);
}

function markUserRegistered(name, email, skipUsernameCheck = false) {
    const currentUser = getCurrentUser();
    const userId = (currentUser && currentUser.id) ? currentUser.id : generateUserId();
    const trimmedName = name && name.trim() ? name.trim() : 'Пользователь';

    // Check if username is taken (unless skipping check)
    if (!skipUsernameCheck && isUsernameTaken(trimmedName, userId)) {
        return { error: 'username_taken' };
    }

    const user = {
        id: userId,
        name: trimmedName,
        email: email && email.trim() ? email.trim() : '',
        registered: true
    };

    setCurrentUser(user);
    registerUsername(userId, trimmedName);

    return user;
}

// ============================================
// ЗАКАЗЫ
// ============================================

// Get next order ID (sequential starting from 999)
function getNextOrderId() {
    try {
        const lastId = localStorage.getItem('gamecoins_last_order_id');
        const nextId = lastId ? parseInt(lastId) + 1 : 999;
        localStorage.setItem('gamecoins_last_order_id', nextId);
        return nextId;
    } catch (e) {
        return 999;
    }
}

// Get all orders
function getUserOrders() {
    try {
        const raw = localStorage.getItem('gamecoins_orders');
        return raw ? JSON.parse(raw) : [];
    } catch (e) {
        return [];
    }
}

// Save order
function saveOrder(order) {
    try {
        const orders = getUserOrders();
        const newOrder = {
            id: getNextOrderId(),
            ...order,
            date: new Date().toISOString(),
            status: 'completed'
        };
        orders.unshift(newOrder);
        localStorage.setItem('gamecoins_orders', JSON.stringify(orders));
        return newOrder;
    } catch (e) {
        console.warn('Не удалось сохранить заказ', e);
        return null;
    }
}

// ============================================
// ПОКУПКИ (для доступа к отзывам)
// ============================================

function trackPurchase(gameId) {
    const key = 'gamecoins_purchased_games';
    const idStr = String(gameId);
    let list = [];

    try {
        const raw = localStorage.getItem(key);
        if (raw) {
            list = JSON.parse(raw);
        }
    } catch (e) {
        console.warn('Не удалось прочитать список покупок', e);
    }

    if (!Array.isArray(list)) {
        list = [];
    }

    if (!list.includes(idStr)) {
        list.push(idStr);
    }

    try {
        localStorage.setItem(key, JSON.stringify(list));
    } catch (e) {
        console.warn('Не удалось сохранить список покупок', e);
    }
}

function hasPurchasedGame(gameId) {
    const key = 'gamecoins_purchased_games';
    const idStr = String(gameId);
    try {
        const raw = localStorage.getItem(key);
        if (!raw) return false;
        const list = JSON.parse(raw);
        return Array.isArray(list) && list.includes(idStr);
    } catch (e) {
        console.warn('Не удалось проверить покупку игры', e);
        return false;
    }
}

// ============================================
// ОТЗЫВЫ ПОЛЬЗОВАТЕЛЕЙ (localStorage)
// ============================================

function saveUserReview(gameId, reviewData) {
    const key = 'gamecoins_user_reviews';
    const idStr = String(gameId);
    let all = {};

    try {
        const raw = localStorage.getItem(key);
        if (raw) {
            all = JSON.parse(raw);
        }
    } catch (e) {
        console.warn('Не удалось прочитать отзывы пользователей', e);
    }

    if (!all || typeof all !== 'object') {
        all = {};
    }

    if (!Array.isArray(all[idStr])) {
        all[idStr] = [];
    }

    // Добавляем новый отзыв в начало списка
    all[idStr].unshift(reviewData);

    try {
        localStorage.setItem(key, JSON.stringify(all));
    } catch (e) {
        console.warn('Не удалось сохранить отзыв пользователя', e);
    }
}

function getUserReviewsMap() {
    const key = 'gamecoins_user_reviews';
    try {
        const raw = localStorage.getItem(key);
        if (!raw) return {};
        const parsed = JSON.parse(raw);
        return parsed && typeof parsed === 'object' ? parsed : {};
    } catch (e) {
        console.warn('Не удалось загрузить отзывы пользователей', e);
        return {};
    }
}

// Собрать все отзывы со всех игр и отсортировать по дате (свежие сверху)
function getAllReviewsSorted() {
    if (!gamesData) return [];

    const allReviews = [];

    // 1) Статические отзывы из games-data.json
    gamesData.forEach(game => {
        if (Array.isArray(game.reviews)) {
            game.reviews.forEach(review => {
                allReviews.push({
                    ...review,
                    gameName: game.name,
                    source: 'static'
                });
            });
        }
    });

    // 2) Отзывы пользователей из localStorage
    const userReviewsMap = getUserReviewsMap();
    Object.keys(userReviewsMap).forEach(gameId => {
        const game = getGameById(gameId);
        const gameName = game ? game.name : '';
        const gameIcon = game ? game.icon : ''; // Get icon
        const list = userReviewsMap[gameId];

        if (Array.isArray(list)) {
            list.forEach(review => {
                // Если в отзыве записано "Игра" (заглушка), но мы нашли игру по ID -> берем нормальное имя
                let finalGameName = review.gameName;
                if ((!finalGameName || finalGameName === 'Игра') && gameName) {
                    finalGameName = gameName;
                }

                allReviews.push({
                    ...review,
                    gameName: finalGameName,
                    gameId: gameId,
                    gameIcon: gameIcon, // Add icon to review object
                    source: 'user'
                });
            });
        }
    });

    // Сортируем по дате: новые сверху
    allReviews.sort((a, b) => {
        const dateA = new Date(a.date);
        const dateB = new Date(b.date);
        return dateB - dateA;
    });

    return allReviews;
}

function searchGames(query) {
    if (!gamesData) return [];
    if (!query || query.trim() === '') return gamesData;

    const searchTerm = query.toLowerCase().trim();
    return gamesData.filter(game =>
        game.name.toLowerCase().includes(searchTerm) ||
        game.currency.toLowerCase().includes(searchTerm) ||
        game.genre.toLowerCase().includes(searchTerm)
    );
}

function filterByGenre(genre) {
    if (!gamesData) return [];
    if (!genre || genre === 'Все жанры') return gamesData;

    return gamesData.filter(game => game.genre === genre);
}

function getTopGames(count = 8) {
    if (!gamesData) return [];
    return gamesData.slice(0, count);
}

function getUniqueGenres() {
    if (!gamesData) return [];
    const genres = [...new Set(gamesData.map(game => game.genre))];
    return ['Все жанры', ...genres.sort()];
}

// ============================================
// УПРАВЛЕНИЕ БАЛАНСОМ
// ============================================

function getUserBalance() {
    const stored = localStorage.getItem('gamecoins_balance');

    // Для новых пользователей (нет записи в localStorage) баланс = 0
    if (stored === null) {
        return 0;
    }

    const value = parseInt(stored, 10);
    return isNaN(value) ? 0 : value;
}

function setUserBalance(newBalance) {
    localStorage.setItem('gamecoins_balance', newBalance);
    updateBalanceDisplay();
}

function deductFromBalance(amount) {
    let currentBalance = getUserBalance();
    if (currentBalance >= amount) {
        setUserBalance(currentBalance - amount);
        return true;
    }
    return false;
}

// ============================================
// ОБНОВЛЕНИЕ UI
// ============================================

function updateBalanceDisplay() {
    const balanceElements = document.querySelectorAll('.user-balance');
    const user = getCurrentUser();

    balanceElements.forEach(el => {
        // Находим кнопку пополнения (родительская ссылка)
        const topUpLink = el.closest('a[href="top-up.html"]');

        if (user) {
            el.textContent = `${getUserBalance()} ₽`;
            if (topUpLink) topUpLink.style.display = ''; // Показываем
        } else {
            // Скрываем весь блок баланса для незарегистрированных
            if (topUpLink) topUpLink.style.display = 'none';
            else el.style.display = 'none';
        }
    });
}

// ============================================
// ГЕНЕРАЦИЯ HTML
// ============================================

/**
 * Helper function to generate the image HTML for a package.
 * Used in both package cards and review cards.
 */
function getPackageImageHtml(gameId, packageName, gameIcon, packageIndex) {
    const name = typeof packageName === 'string' ? packageName.trim().toLowerCase() : '';

    // Попытка найти индекс пакета, если он не передан (например, для отзывов)
    if (typeof packageIndex === 'undefined' && gamesData) {
        const game = getGameById(gameId);
        if (game && Array.isArray(game.packages)) {
            const foundIndex = game.packages.findIndex(p => p.name === packageName);
            if (foundIndex !== -1) {
                packageIndex = foundIndex;
            }
        }
    }

    // Проверяем, задано ли изображение явно в данных пакета
    // Сначала пытаемся найти по индексу, потом по имени
    if (gamesData) {
        const game = getGameById(gameId);
        if (game && Array.isArray(game.packages)) {
            let pkg = null;
            // Пытаемся найти по индексу
            if (typeof packageIndex !== 'undefined' && game.packages[packageIndex]) {
                pkg = game.packages[packageIndex];
            }
            // Если не нашли по индексу, ищем по имени
            if (!pkg && packageName) {
                pkg = game.packages.find(p => p.name && p.name.toLowerCase() === packageName.toLowerCase());
            }
            if (pkg && pkg.image) {
                return `<img src="${pkg.image}" alt="${pkg.name}" class="w-full h-full object-contain" />`;
            }
        }
    }

    const idx1 = (typeof packageIndex === 'number') ? packageIndex + 1 : 1;

    // Логика для Brawl Stars (id = "brawl-stars") - по нумерации
    if (String(gameId) === 'brawl-stars') {
        return `<img src="images/brawl${idx1}.png" alt="Gems" class="w-full h-full object-contain" />`;
    }

    // Логика для Clash of Clans (id = 9) и Clash Royale (id = 17) - по нумерации
    if (String(gameId) === '9' || String(gameId) === '17') {
        // Для Clash Royale пакеты 14000, 16500, 28000 (индексы 6, 7, 8) используют картинку от 9000 (clash6.png)
        if (String(gameId) === '17' && packageIndex >= 6) {
             return `<img src="images/clash6.png" alt="Gems" class="w-full h-full object-contain" />`;
        }

        // Для Clash of Clans
        if (String(gameId) === '9') {
             // Пакет 1850 (idx 3) использует фото от 1200 (idx 2) -> clash3.png
             if (packageIndex === 3) {
                 return `<img src="images/clash3.png" alt="Gems" class="w-full h-full object-contain" />`;
             }

             // Спец. требование: пакеты 2500 (idx 4) и 6500 (idx 5) должны использовать фото от 1850 (бывшее) -> clash4.png
             if (packageIndex === 4 || packageIndex === 5) {
                 return `<img src="images/clash4.png" alt="Gems" class="w-full h-full object-contain" />`;
             }

             if (idx1 > 6) {
                 return `<img src="images/clash6.png" alt="Gems" class="w-full h-full object-contain" />`;
             }
        }

        let imgIdx = idx1;
        if (imgIdx > 6) imgIdx = 6;
        return `<img src="images/clash${imgIdx}.png" alt="Gems" class="w-full h-full object-contain" />`;
    }

    // Логика для Arena Breakout (id = 18) - по нумерации
    if (String(gameId) === '18') {
        let imgIdx = idx1;
        // Для пакета с индексом 3 (1580 Credits) используем картинку от пакета с индексом 2 (630 Credits)
        if (packageIndex === 3) {
            imgIdx = 3;
        }
        // Для пакетов с индексом 4, 5, 6 используем картинку 4
        if (packageIndex >= 4 && packageIndex <= 6) {
            imgIdx = 4;
        }
        // У нас есть картинки от 1 до 5
        if (imgIdx > 5) imgIdx = 5;
        return `<img src="images/Arena Breakout${imgIdx}.png" alt="Credits" class="w-full h-full object-contain" />`;
    }

    // Логика для Delta Force (id = "delta-force") - по нумерации
    if (String(gameId) === 'delta-force') {
        let imgIdx = idx1;

        // Для пакета с индексом 2 (420 Credits) используем deltaforce2.png (как у пакета 300)
        if (packageIndex === 2) {
            imgIdx = 2;
        }
        // Для пакета с индексом 3 (680 Credits) используем deltaforce3.png
        if (packageIndex === 3) {
            imgIdx = 3;
        }
        // Для пакетов с индексом 4 (1280 Credits) и 5 (1680 Credits) используем deltaforce4.png
        if (packageIndex === 4 || packageIndex === 5) {
            imgIdx = 4;
        }
        // Для пакетов с индексом 6 (3280 Credits) и 7 (6480 Credits) используем deltaforce5.png
        if (packageIndex === 6 || packageIndex === 7) {
            imgIdx = 5;
        }

        if (imgIdx > 6) imgIdx = 6;
        return `<img src="images/deltaforce${imgIdx}.png" alt="Credits" class="w-full h-full object-contain" />`;
    }

    // Дополнительная логика для CP‑пакетов (Call of Duty Mobile, id = 13).
    const isCPGame = String(gameId) === '13';
    const is88CP   = isCPGame && name.startsWith('88 cp');
    const is420CP  = isCPGame && name.startsWith('420 cp');
    const is880CP  = isCPGame && name.startsWith('880 cp');
    const is2400CP = isCPGame && name.startsWith('2400 cp');
    const is5000CP = isCPGame && name.startsWith('5000 cp');
    const is10800CP = isCPGame && name.startsWith('10800 cp');
    const is13200CP = isCPGame && name.startsWith('13200 cp');
    const is15800CP = isCPGame && name.startsWith('15800 cp');
    const isAnyOtherCP = isCPGame && !(
        is88CP ||
        is420CP ||
        is880CP ||
        is2400CP ||
        is5000CP ||
        is10800CP ||
        is13200CP ||
        is15800CP
    ) && name.includes(' cp');

    // Логика для Robux пакетов (Roblox, id = 7).
    const isRobloxGame = String(gameId) === '7';
    const isRobuxPackage = isRobloxGame && name.includes('robux');

    // Логика для Clash of Clans (id = 9) и Clash Royale (id = 17)
    const isClashGame = String(gameId) === '9' || String(gameId) === '17';
    // Определяем пакеты по количеству гемов
    const isClash80 = isClashGame && (name.includes('80') || name.includes('88')); // Иногда бывают странные номиналы
    const isClash500 = isClashGame && name.includes('500');
    const isClash1200 = isClashGame && name.includes('1200');
    const isClash2500 = isClashGame && name.includes('2500');
    const isClash6500 = isClashGame && name.includes('6500');
    const isClash9000 = isClashGame && name.includes('9000');
    const isClash14000 = isClashGame && name.includes('14000');
    const isClash16500 = isClashGame && name.includes('16500');
    const isClash28000 = isClashGame && name.includes('28000');

    // Логика для Brawl Stars (id = "brawl-stars")
    const isBrawlStars = String(gameId) === 'brawl-stars';
    const isBrawl30 = isBrawlStars && name.includes('30');
    const isBrawl80 = isBrawlStars && name.includes('80');
    const isBrawl170 = isBrawlStars && name.includes('170');
    const isBrawl360 = isBrawlStars && name.includes('360');
    const isBrawl950 = isBrawlStars && name.includes('950');
    const isBrawl2000 = isBrawlStars && name.includes('2000');
    const isBrawl2950 = isBrawlStars && name.includes('2950');
    const isBrawl4000 = isBrawlStars && name.includes('4000');
    const isBrawl6000 = isBrawlStars && name.includes('6000');

    // Логика для Arena Breakout Infinite (id = 19)
    const isArenaBreakoutInfinite = String(gameId) === '19';
    // Используем startsWith для точного совпадения начала строки, чтобы 100 не путалось с 1000/10000
    const isABI100 = isArenaBreakoutInfinite && name.startsWith('100 ');
    const isABI500 = isArenaBreakoutInfinite && name.startsWith('500 ');
    const isABI1000 = isArenaBreakoutInfinite && name.startsWith('1000 ');
    const isABI2500 = isArenaBreakoutInfinite && name.startsWith('2500 ');
    const isABI5000 = isArenaBreakoutInfinite && name.startsWith('5000 ');
    const isABI7925 = isArenaBreakoutInfinite && name.startsWith('7925');
    const isABI10000 = isArenaBreakoutInfinite && name.startsWith('10000');
    const isABI16100 = isArenaBreakoutInfinite && name.startsWith('16100');
    const isABI21600 = isArenaBreakoutInfinite && name.startsWith('21600');
    const isABI32400 = isArenaBreakoutInfinite && name.startsWith('32400');
    const isABI43200 = isArenaBreakoutInfinite && name.startsWith('43200');

    // Логика для UC (PUBG Mobile и др.)
    const is60UC = name.startsWith('60 uc');
    const is300UC = name.startsWith('300 uc');
    const is325UC = name.startsWith('325 uc');
    const is600UC = name.startsWith('600 uc');
    const is660UC = name.startsWith('660 uc');
    const is985UC = name.startsWith('985 uc');
    const is1320UC = name.startsWith('1320 uc');
    const is1800UC = name.startsWith('1800 uc');
    const is2460UC = name.startsWith('2460 uc');
    const is2785UC = name.startsWith('2785 uc');
    const is3850UC = name.startsWith('3850 uc');
    const is5650UC = name.startsWith('5650 uc');
    // Любой другой пакет, где в названии есть "uc", считается UC‑пакетом
    // и получит изображение 6.png, если не попал под условия выше.
    const isAnyOtherUC = !is60UC && !is300UC && !is325UC && !is600UC && !is660UC && !is985UC && !is1320UC
        && !is1800UC && !is2460UC && !is2785UC && !is3850UC && !is5650UC
        && name.includes(' uc');

    // SVG‑путь: берём gameIcon, если он есть, иначе используем запасной дефолт.
    const iconPath = (typeof gameIcon === 'string' && gameIcon.trim().length > 0)
        ? gameIcon
        : "M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z";

    // HTML иконки: либо одно из PNG, либо SVG.
    return isRobuxPackage
        ? `<img src="images/robux.png" alt="Иконка пакета Robux" class="w-full h-full object-contain" />`
        // Arena Breakout Infinite
        : isABI100
        ? `<img src="images/Arena Breakout Infinite1.png" alt="Bonds" class="w-full h-full object-contain" />`
        : isABI500
        ? `<img src="images/Arena Breakout Infinite2.png" alt="Bonds" class="w-full h-full object-contain" />`
        : isABI1000
        ? `<img src="images/Arena Breakout Infinite3.png" alt="Bonds" class="w-full h-full object-contain" />`
        : isABI2500
        ? `<img src="images/Arena Breakout Infinite4.png" alt="Bonds" class="w-full h-full object-contain" />`
        : isABI5000
        ? `<img src="images/Arena Breakout Infinite5.png" alt="Bonds" class="w-full h-full object-contain" />`
        : (isABI7925 || isABI10000 || isABI16100 || isABI21600 || isABI32400 || isABI43200)
        ? `<img src="images/Arena Breakout Infinite6.png" alt="Bonds" class="w-full h-full object-contain" />`
        : isArenaBreakoutInfinite
        ? `<img src="images/Arena Breakout Infinite1.png" alt="Bonds" class="w-full h-full object-contain" />`
        : is88CP
        ? `<img src="images/сp1.png" alt="Иконка пакета 88 CP" class="w-full h-full object-contain" />`
        : is420CP
        ? `<img src="images/cp2.png" alt="Иконка пакета 420 CP" class="w-full h-full object-contain" />`
        : is880CP
        ? `<img src="images/cp3.png" alt="Иконка пакета 880 CP" class="w-full h-full object-contain" />`
        : is2400CP
        ? `<img src="images/cp4.png" alt="Иконка пакета 2400 CP" class="w-full h-full object-contain" />`
        : is5000CP
        ? `<img src="images/cp5.png" alt="Иконка пакета 5000 CP" class="w-full h-full object-contain" />`
        : is10800CP
        ? `<img src="images/cp6.png" alt="Иконка пакета 10800 CP" class="w-full h-full object-contain" />`
        : is13200CP
        ? `<img src="images/cp7.png" alt="Иконка пакета 13200 CP" class="w-full h-full object-contain" />`
        : (is15800CP || isAnyOtherCP)
        ? `<img src="images/cp8.png" alt="Иконка пакета CP" class="w-full h-full object-contain" />`
        : is60UC
        ? `<img src="images/11.png" alt="Иконка пакета 60 UC" class="w-full h-full object-contain" />`
        : (is325UC || is300UC || is600UC)
        ? `<img src="images/2.png" alt="Иконка пакета UC" class="w-full h-full object-contain" />`
        : (is660UC || is985UC || is1320UC)
        ? `<img src="images/3.png" alt="Иконка пакета UC" class="w-full h-full object-contain" />`
        : (is1800UC || is2460UC || is2785UC)
        ? `<img src="images/4.png" alt="Иконка пакета UC" class="w-full h-full object-contain" />`
        : (is3850UC || is5650UC)
        ? `<img src="images/5.png" alt="Иконка пакета UC" class="w-full h-full object-contain" />`
        : isAnyOtherUC
        ? `<img src="images/6.png" alt="Иконка пакета UC" class="w-full h-full object-contain" />`
        : `<svg class="w-6 h-6 md:w-7 md:h-7 text-text-secondary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${iconPath}"/>
           </svg>`;
}

function generateGameCard(game, linkPrefix = 'game.html') {
    const isGenshin = game.id === 1;

    let cardContent;
    if (game.image) {
        cardContent = `<div class="card-hover bg-surface rounded-lg cursor-pointer relative bg-cover bg-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                           <img src="${game.image}" alt="${game.name}" class="w-full h-full object-cover">
                       </div>`;
    } else if (isGenshin) {
        cardContent = `<div class="card-hover bg-surface rounded-lg cursor-pointer relative bg-cover bg-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                           <div class="w-full h-full bg-cover bg-center" style="background-image: url('images/genshin-impact.jpg.avif');"></div>
                       </div>`;
    } else {
        cardContent = `<div class="card-hover bg-surface rounded-lg p-6 cursor-pointer flex items-center justify-center w-20 h-20 md:w-32 md:h-32 overflow-hidden aspect-square">
                           <svg class="w-10 h-10 md:w-16 md:h-16 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${game.icon}"/>
                           </svg>
                       </div>`;
    }

    return `
        <a href="${linkPrefix}?id=${game.id}" class="block w-20 md:w-32 text-center group">
            ${cardContent}
            <h3 class="text-[10px] md:text-base font-semibold mt-2 text-text-primary transition-colors">${game.name}</h3>
        </a>
    `;
}

function generateGameGridCard(game) {
    const isGenshin = game.id === 1;

    let cardContent;
    if (game.image) {
        cardContent = `<div class="card-hover bg-surface rounded-lg relative bg-cover bg-center aspect-square overflow-hidden">
                           <img src="${game.image}" alt="${game.name}" class="w-full h-full object-cover">
                       </div>`;
    } else if (isGenshin) {
        cardContent = `<div class="card-hover bg-surface rounded-lg relative bg-cover bg-center aspect-square overflow-hidden">
                           <img src="images/genshin-impact.jpg.avif" alt="${game.name}" class="w-full h-full object-cover">
                       </div>`;
    } else {
        cardContent = `<div class="card-hover bg-surface rounded-lg aspect-square flex items-center justify-center overflow-hidden">
                           <svg class="w-16 h-16 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${game.icon}"/>
                           </svg>
                       </div>`;
    }

    return `
        <a href="game.html?id=${game.id}" class="block group">
            ${cardContent}
            <h3 class="text-lg font-semibold mt-2 text-text-primary transition-colors">${game.name}</h3>
        </a>
    `;
}

function generatePackageCard(pkg, packageIndex, gameId, gameIcon) {
    const iconHtml = getPackageImageHtml(gameId, pkg.name, gameIcon, packageIndex);

    return `
        <!-- Карточка пакета: возвращаем базовый фиксированный размер по высоте (h-20),
             как было изначально в макете. -->
        <div class="package-card bg-[#1A1A1A] hover:bg-[#252525] rounded-xl p-4 cursor-pointer border-2 border-transparent transition-all duration-200 flex items-center justify-between gap-4 relative overflow-hidden group h-20"
             data-game-id="${gameId}"
             data-package-index="${packageIndex}">

            <!-- Left side: Icon + Name -->
            <div class="flex items-center gap-3 md:gap-4">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg flex items-center justify-center shrink-0">
                    ${iconHtml}
                </div>
                <div class="font-bold text-white text-[13px] md:text-sm leading-tight">
                    ${pkg.name}
                    ${pkg.bonus ? `<div class="text-[10px] md:text-[10px] text-gray-400 font-normal mt-0.5">${pkg.bonus}</div>` : ''}
                </div>
            </div>

            <!-- Right side: Price -->
            <div class="text-white font-bold text-[13px] md:text-sm whitespace-nowrap">
                ${pkg.price} ₽
            </div>
        </div>
    `;
}

function generateReviewCard(review) {
    const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
    const date = new Date(review.date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    const initials = review.author
        ? review.author.split(' ').map(p => p[0]).join('').slice(0, 2).toUpperCase()
        : 'UX';

    // Генерация информации о купленном пакете (если есть)
    let packageInfoHtml = '';
    if (review.packageName && review.gameId) {
        const iconHtml = getPackageImageHtml(review.gameId, review.packageName, review.gameIcon);
        packageInfoHtml = `
            <div class="flex items-center gap-3 mt-2 bg-white/5 rounded-lg p-2 pr-3 w-fit border border-white/5">
                <div class="w-8 h-8 flex items-center justify-center shrink-0 rounded bg-white/5 p-1">
                    ${iconHtml}
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-text-secondary leading-none mb-0.5">Куплено:</span>
                    <span class="text-xs font-bold text-text-primary leading-tight">${review.packageName}</span>
                </div>
            </div>
        `;
    }

    return `
        <div class="bg-[#1A1A1A] rounded-2xl p-4 md:p-5 mb-4 border border-white/5 flex flex-col gap-3">
            <!-- Верхняя строка: аватар + имя + игра + звезды -->
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-[11px] font-semibold text-text-primary">
                        ${initials}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-text-primary">${review.author}</p>
                        ${review.gameName ? `<p class="text-[11px] text-text-secondary mt-0.5">${review.gameName}</p>` : ''}
                    </div>
                </div>
                <span class="text-sm md:text-base font-semibold text-[#5FFF8F] whitespace-nowrap">
                    ${stars}
                </span>
            </div>

            <!-- Инфо о пакете -->
            ${packageInfoHtml}

            <!-- Теги-лейблы (макеты отзывов) -->
            ${Array.isArray(review.tags) && review.tags.length > 0 ? `
                <div class="flex flex-wrap gap-1.5 mt-1">
                    ${review.tags.map(tag => `
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-white/5 border border-white/10 text-[11px] md:text-xs text-text-primary font-medium">
                            ${tag}
                        </span>
                    `).join('')}
                </div>
            ` : ''}

            <!-- Текст отзыва -->
            <p class="text-sm text-text-secondary leading-relaxed mt-1">${review.text}</p>

            <!-- Дата -->
            <p class="text-[11px] text-text-secondary mt-1">${date}</p>
        </div>
    `;
}

function generateFAQItem(faq, index) {
    return `
        <div class="border-b border-surface pb-4">
            <button onclick="toggleFAQ(this)" class="w-full flex items-center justify-between text-left py-3 faq-button">
                <span class="text-lg font-semibold">${faq.question}</span>
                <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="hidden mt-2 text-text-secondary faq-answer">
                ${faq.answer}
            </div>
        </div>
    `;
}

function generateRequirementsTable(requirements) {
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
            <div class="bg-surface rounded-lg p-6">
                <h4 class="text-xl font-semibold mb-4">Минимальные</h4>
                <ul class="space-y-2 text-text-secondary">
                    <li><strong>ОС:</strong> ${requirements.minimum.os}</li>
                    <li><strong>Процессор:</strong> ${requirements.minimum.processor}</li>
                    <li><strong>Память:</strong> ${requirements.minimum.memory}</li>
                    <li><strong>Видеокарта:</strong> ${requirements.minimum.graphics}</li>
                    <li><strong>Место:</strong> ${requirements.minimum.storage}</li>
                </ul>
            </div>
            <div class="bg-surface rounded-lg p-6">
                <h4 class="text-xl font-semibold mb-4">Рекомендуемые</h4>
                <ul class="space-y-2 text-text-secondary">
                    <li><strong>ОС:</strong> ${requirements.recommended.os}</li>
                    <li><strong>Процессор:</strong> ${requirements.recommended.processor}</li>
                    <li><strong>Память:</strong> ${requirements.recommended.memory}</li>
                    <li><strong>Видеокарта:</strong> ${requirements.recommended.graphics}</li>
                    <li><strong>Место:</strong> ${requirements.recommended.storage}</li>
                </ul>
            </div>
        </div>
    `;
}

// ============================================
// УВЕДОМЛЕНИЯ
// ============================================

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-black text-white' : 'bg-red-600 text-white'
    }`;
    notification.textContent = message;
    notification.style.opacity = '0';
    notification.style.transform = 'translateY(-20px)';
    notification.style.border = type === 'success' ? '2px solid #00ff88' : '2px solid #ff0000';
    notification.style.boxShadow = type === 'success' ? '0 0 20px rgba(0, 255, 136, 0.5)' : '0 0 20px rgba(255, 0, 0, 0.5)';

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 10);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function updateProfileLinks() {
    const user = getCurrentUser();
    // Обновляем ссылки профиля в хедере и других местах
    const links = document.querySelectorAll('a[href="profile.html"], a[href="register.html"]');

    links.forEach(link => {
        // Не меняем ссылки, если мы уже на странице регистрации (чтобы не делать циклическую ссылку на саму себя, хотя href="#" тоже ок)
        if (window.location.pathname.includes('register.html')) return;

        if (user) {
            link.href = 'profile.html';
        } else {
            link.href = 'register.html';
        }
    });
}

// ============================================
// ИНИЦИАЛИЗАЦИЯ
// ============================================

// document.addEventListener('DOMContentLoaded', () => {
//     const storedBalance = localStorage.getItem('gamecoins_balance');
//     if (storedBalance === null || parseInt(storedBalance) < 10000) {
//
//         setUserBalance(0);
//     } else {
//         // Обновить отображение баланса из существующего значения
//         updateBalanceDisplay();
//     }
//
//     updateProfileLinks();
// });

console.log('%cData Manager загружен', 'color: #00ff88; font-size: 14px; font-weight: bold;');
