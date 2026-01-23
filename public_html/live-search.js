document.addEventListener('DOMContentLoaded', () => {
    const searchInputs = document.querySelectorAll('.header-search-input');

    const debounce = (fn, delay = 200) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), delay);
        };
    };

    async function searchApi(query) {
        try {
            const res = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();
            return data.results || [];
        } catch (error) {
            console.error('Error fetching search data:', error);
            return [];
        }
    }

    async function showSearchResults(query, inputElement) {
        const resultsContainer = inputElement.parentElement.querySelector('.search-results');
        if (!resultsContainer) return;

        if (!query) {
            resultsContainer.classList.add('hidden');
            return;
        }

        const filteredData = await searchApi(query);

        if (filteredData.length > 0) {
            resultsContainer.innerHTML = filteredData.map(item => {
                const slug = item.slug || item.id;
                const url = item.type === 'game'
                    ? `/game/${slug}`
                    : `/gift-card/${slug}`;
                return `
                    <a href="${url}" class="search-result-item">
                        <img src="${item.image}" alt="${item.name}" class="w-8 h-10 object-cover rounded-md" onerror="this.style.display='none'">
                        <span>${item.name}</span>
                    </a>
                `;
            }).join('');
            resultsContainer.classList.remove('hidden');
        } else {
            resultsContainer.classList.add('hidden');
        }
    }

    searchInputs.forEach(input => {
        const parent = input.parentElement;
        const resultsContainer = document.createElement('div');
        resultsContainer.className = 'search-results hidden';
        parent.appendChild(resultsContainer);

        input.addEventListener('input', debounce(() => {
            showSearchResults(input.value, input);
        }, 200));
    });

    document.addEventListener('click', (e) => {
        const isClickInsideSearch = Array.from(searchInputs).some(input => input.contains(e.target) || input.parentElement.querySelector('.search-results').contains(e.target));
        if (!isClickInsideSearch) {
            document.querySelectorAll('.search-results').forEach(container => {
                container.classList.add('hidden');
            });
        }
    });
});
