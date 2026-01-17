// ============================================
// MOBILE MENU
// ============================================

function toggleMobileMenu() {
    const burger = document.getElementById('burger');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (!burger || !mobileMenu) return;
    
    burger.classList.toggle('burger-open');
    mobileMenu.classList.toggle('hidden');
    
    const isExpanded = !mobileMenu.classList.contains('hidden');
    burger.setAttribute('aria-expanded', isExpanded);
}

function closeMobileMenu() {
    const burger = document.getElementById('burger');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (!burger || !mobileMenu) return;
    
    burger.classList.remove('burger-open');
    mobileMenu.classList.add('hidden');
    burger.setAttribute('aria-expanded', 'false');
}

// ============================================
// GAME TAB SWITCHING
// ============================================

function switchGameTab(tabName) {
    document.querySelectorAll('.game-tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.game-tab').forEach(tab => {
        tab.classList.remove('active');
        tab.setAttribute('aria-pressed', 'false');
    });
    
    const selectedContent = document.getElementById(`${tabName}Tab`);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    const selectedTab = document.querySelector(`.game-tab[data-tab="${tabName}"]`);
    if (selectedTab) {
        selectedTab.classList.add('active');
        selectedTab.setAttribute('aria-pressed', 'true');
    }
}

// ============================================
// PROFILE TAB SWITCHING
// ============================================

function switchProfileTab(tabName) {
    document.querySelectorAll('.profile-tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.classList.remove('border-text-primary');
        tab.classList.add('border-transparent');
    });
    
    const selectedContent = document.getElementById(`${tabName}Tab`);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    const selectedTab = document.querySelector(`.profile-tab[data-tab="${tabName}"]`);
    if (selectedTab) {
        selectedTab.classList.add('border-text-primary');
        selectedTab.classList.remove('border-transparent');
    }
}

// ============================================
// FAQ FUNCTIONALITY
// ============================================

function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('svg');
    
    if (!content || !icon) return;
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// ============================================
// AUTH MODAL
// ============================================

function openAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function switchToRegister() {
    document.getElementById('loginForm').classList.add('hidden');
    document.getElementById('registerForm').classList.remove('hidden');
}

function switchToLogin() {
    document.getElementById('registerForm').classList.add('hidden');
    document.getElementById('loginForm').classList.remove('hidden');
}

function handleLogin(event) {
    event.preventDefault();
    alert('Вход выполнен успешно!');
    closeAuthModal();
}

function handleRegister(event) {
    event.preventDefault();
    alert('Регистрация успешна! Теперь вы можете войти.');
    switchToLogin();
}

// ============================================
// SCROLL REVEAL ANIMATIONS
// ============================================

function initScrollReveal() {
    const scrollElements = document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right, .scroll-reveal-scale');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                scrollObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    scrollElements.forEach(element => {
        scrollObserver.observe(element);
    });
}

// ============================================
// RIPPLE EFFECT
// ============================================

function createRipple(event) {
    const button = event.currentTarget;
    
    const circle = document.createElement('span');
    const diameter = Math.max(button.clientWidth, button.clientHeight);
    const radius = diameter / 2;
    
    const rect = button.getBoundingClientRect();
    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = `${event.clientX - rect.left - radius}px`;
    circle.style.top = `${event.clientY - rect.top - radius}px`;
    circle.classList.add('ripple-effect');
    
    const ripple = button.getElementsByClassName('ripple-effect')[0];
    if (ripple) {
        ripple.remove();
    }
    
    button.appendChild(circle);
}

function initRippleEffect() {
    const rippleButtons = document.querySelectorAll('.ripple');
    rippleButtons.forEach(button => {
        button.addEventListener('click', createRipple);
    });
}

// ============================================
// SMOOTH SCROLL
// ============================================

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ============================================
// ANIMATED COUNTER
// ============================================

function animateCounter(element, target, duration = 1000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start);
        }
    }, 16);
}

// ============================================
// PARALLAX EFFECT
// ============================================

function initParallax() {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.parallax');
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
}

// ============================================
// EVENT LISTENERS
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize scroll reveal animations
    initScrollReveal();
    
    // Initialize ripple effect
    initRippleEffect();
    
    // Initialize smooth scroll
    initSmoothScroll();
    
    // Initialize parallax
    initParallax();
    // Setup burger menu
    const burger = document.getElementById('burger');
    if (burger) {
        burger.addEventListener('click', toggleMobileMenu);
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
        const burger = document.getElementById('burger');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
            if (!burger.contains(e.target) && !mobileMenu.contains(e.target)) {
                closeMobileMenu();
            }
        }
    });
    
    // Close auth modal when clicking outside
    const authModal = document.getElementById('authModal');
    if (authModal) {
        authModal.addEventListener('click', (e) => {
            if (e.target === authModal) {
                closeAuthModal();
            }
        });
    }
    
    // Handle escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const authModal = document.getElementById('authModal');
            if (authModal && !authModal.classList.contains('hidden')) {
                closeAuthModal();
            }
            
            const mobileMenu = document.getElementById('mobileMenu');
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                closeMobileMenu();
            }
        }
    });
    
    // Prevent form submission (demo)
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Демо-версия. Функция в разработке.');
        });
    });
    
    // Add hover effects to icons
    const iconElements = document.querySelectorAll('svg');
    iconElements.forEach(icon => {
        if (icon.closest('a, button')) {
            icon.style.transition = 'transform 0.3s ease';
            icon.parentElement.addEventListener('mouseenter', () => {
                icon.style.transform = 'scale(1.1)';
            });
            icon.parentElement.addEventListener('mouseleave', () => {
                icon.style.transform = 'scale(1)';
            });
        }
    });
});

// ============================================
// TOOLTIP FUNCTIONALITY
// ============================================

function createTooltip(element, text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.style.cssText = `
        position: absolute;
        background: rgba(26, 26, 26, 0.95);
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1000;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        min-width: 260px;   /* делаем тултип шире по горизонтали */
        max-width: 260px;   /* фиксируем ширину, чтобы текст помещался в 2 строки */
        white-space: normal;
    `;
    
    document.body.appendChild(tooltip);
    
    element.addEventListener('mouseenter', (e) => {
        const rect = element.getBoundingClientRect();
        tooltip.style.left = `${rect.left + rect.width / 2}px`;
        tooltip.style.top = `${rect.top - 40}px`;
        tooltip.style.transform = 'translateX(-50%)';
        tooltip.style.opacity = '1';
    });
    
    element.addEventListener('mouseleave', () => {
        tooltip.style.opacity = '0';
    });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', () => {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        createTooltip(element, element.dataset.tooltip);
    });
});

console.log('%cGameCoins Shop', 'color: #000000; font-size: 24px; font-weight: bold; background: linear-gradient(135deg, #00ff88 0%, #00cc66 100%); padding: 10px;');
console.log('%cВиртуальная валюта для игр ⚡', 'color: #00ff88; font-size: 16px; font-weight: bold;');

