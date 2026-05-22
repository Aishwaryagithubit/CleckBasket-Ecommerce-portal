<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$current_path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$current_page = strtolower(basename($current_path ?? ''));

function is_active_nav(string $page, string $current_page): string {
    return $page === $current_page ? ' active' : '';
}

$session_user = $_SESSION['user_name'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CleckBasket</title>
    <link rel="stylesheet" href="/cleckbasket/assets/css/header.css">
    <script src="/cleckbasket/assets/js/main.js" defer></script>

    <style>
        /* ═══════════════════════════════════
           SEARCH DROPDOWN SHELL
        ═══════════════════════════════════ */
        .search-wrapper { position: relative; }

        .search-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            max-width: 98vw;
            background: #fff;
            border: 1px solid #e3e3e3;
            border-radius: 14px;
            box-shadow: 0 12px 40px rgba(0,0,0,.13), 0 2px 8px rgba(0,0,0,.06);
            z-index: 9999;
            display: none;
            animation: sdIn .17s ease;
            overflow: hidden;
        }

        @keyframes sdIn {
            from { opacity:0; transform:translateX(-50%) translateY(-6px); }
            to   { opacity:1; transform:translateX(-50%) translateY(0); }
        }

        .search-dropdown.active { display: flex; }

        /* ═══════════════════════════════════
           RESULTS COLUMN
        ═══════════════════════════════════ */
        .sd-results-panel {
            flex: 1;
            min-width: 0;
            border-right: 1px solid #f0f0f0;
            max-height: 440px;
            overflow-y: auto;
        }

        .sd-results-panel::-webkit-scrollbar { width: 4px; }
        .sd-results-panel::-webkit-scrollbar-thumb { background:#ddd; border-radius:4px; }

        .sd-section-label {
            padding: 10px 14px 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #999;
            border-bottom: 1px solid #f5f5f5;
        }

        .sd-results-list { list-style:none; margin:0; padding:4px 0; }

        .sd-result-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            transition: background .12s;
        }

        .sd-result-item:hover, .sd-result-item.focused { background: #f5faf2; }

        .sd-result-thumb {
            width: 40px; height: 40px;
            border-radius: 8px;
            background: #f0f4ec;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }

        .sd-result-info { flex:1; min-width:0; }

        .sd-result-name {
            font-size: 13px; font-weight: 600; color: #222;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .sd-result-name mark {
            background: #d4edda; color: #2d6a2d;
            border-radius: 2px; padding: 0 1px; font-weight: 700;
        }

        .sd-result-meta {
            font-size: 11px; color: #999; margin-top: 1px;
            display: flex; align-items: center; gap: 5px;
        }

        .sd-result-star { font-size: 10px; }
        .sd-result-star.on  { color: #f5a623; }
        .sd-result-star.off { color: #ddd; }

        .sd-result-price {
            font-size: 13px; font-weight: 700; color: #3a7d2c;
            white-space: nowrap; flex-shrink: 0;
        }

        .sd-no-results {
            padding: 28px 16px; text-align: center;
            color: #bbb; font-size: 13px;
        }

        .sd-footer {
            padding: 10px 14px;
            border-top: 1px solid #f0f0f0;
            text-align: center;
        }

        .sd-view-all {
            font-size: 12px; font-weight: 600; color: #3a7d2c;
            text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
        }

        .sd-view-all:hover { color: #2d6022; }

        /* ═══════════════════════════════════
           FILTER PANEL
        ═══════════════════════════════════ */
        .sd-filter-panel {
            width: 210px; flex-shrink: 0;
            padding: 14px 16px;
            max-height: 440px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 18px;
        }

        .sd-filter-panel::-webkit-scrollbar { width: 4px; }
        .sd-filter-panel::-webkit-scrollbar-thumb { background:#ddd; border-radius:4px; }

        .sd-ftitle {
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .07em;
            color: #888; margin-bottom: 8px;
        }

        /* Category chips */
        .sd-chip-grid { display: flex; flex-wrap: wrap; gap: 5px; }

        .sd-chip {
            padding: 4px 10px; border-radius: 20px;
            border: 1px solid #e0e0e0; font-size: 12px; color: #444;
            cursor: pointer; background: #fff;
            transition: background .12s, border-color .12s, color .12s;
            user-select: none;
        }

        .sd-chip:hover { border-color: #3a7d2c; color: #3a7d2c; }

        .sd-chip.selected {
            background: #eaf5e4; border-color: #3a7d2c;
            color: #2d6022; font-weight: 600;
        }

        /* Rating rows */
        .sd-rating-row {
            display: flex; align-items: center; gap: 7px;
            padding: 5px 0; cursor: pointer;
        }

        .sd-rating-row.selected .sd-rlabel { color: #3a7d2c; font-weight: 600; }
        .sd-rating-row.selected .sd-dot { background: #3a7d2c; }

        .sd-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #ddd; flex-shrink: 0;
            transition: background .12s;
        }

        .sd-stars-row { display: flex; gap: 1px; }
        .sd-sr-star { font-size: 12px; }
        .sd-sr-star.on  { color: #f5a623; }
        .sd-sr-star.off { color: #ddd; }

        .sd-rlabel { font-size: 12px; color: #666; transition: color .12s; }

        /* Price inputs */
        .sd-price-row { display: flex; align-items: center; gap: 6px; }

        .sd-price-input {
            flex: 1; width: 0;
            height: 32px; border: 1px solid #e0e0e0;
            border-radius: 8px; padding: 0 9px;
            font-size: 13px; color: #333;
            outline: none; background: #fff;
            transition: border-color .15s;
        }

        .sd-price-input:focus {
            border-color: #3a7d2c;
            box-shadow: 0 0 0 3px rgba(58,125,44,.1);
        }

        .sd-price-sep { font-size: 12px; color: #bbb; flex-shrink: 0; }

        /* Action buttons */
        .sd-actions { display: flex; gap: 7px; }

        .sd-btn {
            flex: 1; height: 32px; border-radius: 8px;
            font-size: 12px; font-weight: 600; cursor: pointer; border: none;
            transition: background .13s;
        }

        .sd-btn-apply { background: #3a7d2c; color: #fff; }
        .sd-btn-apply:hover { background: #2d6022; }

        .sd-btn-reset {
            background: #f2f2f2; color: #666;
            border: 1px solid #e0e0e0;
        }

        .sd-btn-reset:hover { background: #e8e8e8; }

        /* ═══════════════════════════════════
           SKELETON
        ═══════════════════════════════════ */
        .sd-skeleton { padding: 8px 14px; }
        .sd-skel-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .sd-skel-box {
            border-radius: 8px;
            background: linear-gradient(90deg,#f0f0f0 25%,#e6e6e6 50%,#f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.2s infinite;
        }
        .sd-skel-thumb { width:40px; height:40px; flex-shrink:0; }
        .sd-skel-lines { flex:1; display:flex; flex-direction:column; gap:6px; }
        .sd-skel-line  { height:10px; border-radius:4px; }
        .sd-skel-line.s { width: 55%; }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ═══════════════════════════════════
           MOBILE DROPDOWN
        ═══════════════════════════════════ */
        .mobile-search-wrapper { position: relative; }

        .mobile-search-dropdown {
            position: absolute;
            top: calc(100% + 6px); left: 0; right: 0;
            background: #fff;
            border: 1px solid #e3e3e3; border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            z-index: 9999; display: none;
            max-height: 360px; overflow-y: auto;
            animation: mobIn .17s ease;
        }

        @keyframes mobIn {
            from { opacity:0; transform:translateY(-4px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .mobile-search-dropdown.active { display: block; }
    </style>
</head>
<body>

<!-- TOP BANNER -->
<div class="top-banner">
    <a href="/cleckbasket/includes/pages/shop.php?cat=bakery"  style="text-decoration: none; color: white;"> Sweet deals just came out of the oven 10–15% OFF bakery items!</a>
</div>

<!-- HEADER -->
<header class="site-header">
    <div class="header-inner">

        <!-- LEFT -->
        <div class="header-left">
            <button class="hamburger-btn" id="hamburgerBtn" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
            <a href="/cleckbasket/includes/pages/homepage.php" class="logo-link">
                <img src="/cleckbasket/assets/images/logo.png" alt="CleckBasket Logo" class="logo-img">
            </a>
        </div>

        <!-- CENTER: Nav -->
        <nav class="main-nav" id="mainNav">
            <ul class="nav-list">
                <li><a href="/cleckbasket/includes/pages/homepage.php"   class="nav-link<?php echo is_active_nav('homepage.php',   $current_page); ?>">Home</a></li>
                <li><a href="/cleckbasket/includes/pages/shop.php"       class="nav-link<?php echo is_active_nav('shop.php',       $current_page); ?>">Shop</a></li>
                <li><a href="/cleckbasket/includes/pages/about.php"      class="nav-link<?php echo is_active_nav('about.php',      $current_page); ?>">About Us</a></li>
                <li><a href="/cleckbasket/includes/pages/contactsus.php" class="nav-link<?php echo is_active_nav('contactsus.php', $current_page); ?>">Contact Us</a></li>
            </ul>

            <!-- Mobile extras -->
            <div class="mobile-nav-extras">
                <div class="mobile-search-wrapper">
                    <form class="mobile-search-form" id="mobileSearchForm" action="/cleckbasket/search.php" method="GET" autocomplete="off">
                        <input type="text" name="q" id="mobileSearchInput" class="mobile-search-input"
                               placeholder="Search products..." aria-label="Search products" autocomplete="off">
                        <button type="submit" class="mobile-search-btn" aria-label="Search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </form>
                    <div class="mobile-search-dropdown" id="mobileSearchDropdown"></div>
                </div>

                <a href="/cleckbasket/includes/pages/login.php" class="mobile-profile-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>My Account</span>
                </a>
            </div>
        </nav>

        <!-- RIGHT -->
        <div class="header-right">

            <!-- Desktop Search with Rich Dropdown -->
            <div class="search-wrapper" id="desktopSearchWrapper">
                <form class="search-form" id="desktopSearchForm" action="/cleckbasket/search.php" method="GET" autocomplete="off">
                    <input type="text" name="q" id="desktopSearchInput" class="search-input"
                           placeholder="What do you need today?" aria-label="Search products"
                           autocomplete="off" role="combobox" aria-expanded="false">
                    <button type="submit" class="search-btn" aria-label="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>
                </form>

                <!-- Rich dropdown -->
                <div class="search-dropdown" id="desktopSearchDropdown">

                    <!-- Results panel -->
                    <div class="sd-results-panel" id="sdResultsPanel">
                        <div class="sd-no-results">Start typing to search products</div>
                    </div>

                    <!-- Filter panel -->
                    <div class="sd-filter-panel">

                        <!-- Category -->
                        <div>
                            <div class="sd-ftitle">Category</div>
                            <div class="sd-chip-grid" id="sdCategoryChips"></div>
                        </div>

                        <!-- Rating -->
                        <div>
                            <div class="sd-ftitle">Rating</div>
                            <div id="sdRatingOptions"></div>
                        </div>

                        <!-- Price -->
                        <div>
                            <div class="sd-ftitle">Price (£)</div>
                            <div class="sd-price-row">
                                <input type="number" id="sdPriceMin" class="sd-price-input" placeholder="Min" min="0" step="0.01">
                                <span class="sd-price-sep">—</span>
                                <input type="number" id="sdPriceMax" class="sd-price-input" placeholder="Max" min="0" step="0.01">
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="sd-actions">
                            <button type="button" class="sd-btn sd-btn-apply" id="sdApplyBtn">Apply</button>
                            <button type="button" class="sd-btn sd-btn-reset" id="sdResetBtn">Reset</button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- User -->
            <a href="<?php echo $session_user ? '/cleckbasket/includes/pages/profile.php' : '/cleckbasket/includes/pages/customer_register.php'; ?>" class="icon-btn user-btn" aria-label="My Account">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span class="user-greeting"><?php echo $session_user ? 'Hi, ' . htmlspecialchars($session_user) . '!' : 'Sign Up'; ?></span>
            </a>

            <!-- Cart -->
            <a href="/cleckbasket/includes/cart/shopping_cart.php" class="icon-btn cart-btn" aria-label="Shopping Cart">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="badge" id="cartCount">2</span>
            </a>

            <!-- Wishlist -->
            <a href="/cleckbasket/includes/pages/wishlist.php" class="icon-btn wishlist-btn" aria-label="Wishlist">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                             a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23
                             l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <span class="badge" id="favCount" style="display:none">0</span>
            </a>
        </div>

    </div>
</header>


<script>
document.addEventListener('DOMContentLoaded', function () {


    /* ── Filter state ── */
    const applied  = { cats: new Set(), minRating: null, minPrice: null, maxPrice: null };
    const pending  = { cats: new Set(), minRating: null };

    /* ── DOM refs ── */
    const dropdown     = document.getElementById('desktopSearchDropdown');
    const searchInput  = document.getElementById('desktopSearchInput');
    const searchForm   = document.getElementById('desktopSearchForm');
    const resultsPanel = document.getElementById('sdResultsPanel');
    const chipGrid     = document.getElementById('sdCategoryChips');
    const ratingBox    = document.getElementById('sdRatingOptions');
    const priceMin     = document.getElementById('sdPriceMin');
    const priceMax     = document.getElementById('sdPriceMax');
    const applyBtn     = document.getElementById('sdApplyBtn');
    const resetBtn     = document.getElementById('sdResetBtn');

    let currentQuery  = '';
    let debounceTimer = null;
    let focusedIdx    = -1;

    /* ── Load real categories from DB ── */
    fetch('/cleckbasket/backend/search.php')
        .then(r => r.json())
        .then(data => {
            (data.categories || []).forEach(cat => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'sd-chip';
                btn.textContent = cat;
                btn.addEventListener('click', () => {
                    btn.classList.toggle('selected');
                    btn.classList.contains('selected') ? pending.cats.add(cat) : pending.cats.delete(cat);
                });
                chipGrid.appendChild(btn);
            });
        });

    /* ── Build rating rows ── */
    function starRowHtml(n) {
        return Array.from({length:5},(_,i) =>
            `<span class="sd-sr-star ${i<n?'on':'off'}">★</span>`).join('');
    }

    [5,4,3,2,1].forEach(r => {
        const row = document.createElement('div');
        row.className = 'sd-rating-row';
        row.dataset.r = r;
        row.innerHTML = `
            <span class="sd-dot"></span>
            <span class="sd-stars-row">${starRowHtml(r)}</span>
            <span class="sd-rlabel">${r === 5 ? '5 only' : r + ' & up'}</span>`;
        row.addEventListener('click', () => {
            ratingBox.querySelectorAll('.sd-rating-row').forEach(el => el.classList.remove('selected'));
            row.classList.add('selected');
            pending.minRating = r;
        });
        ratingBox.appendChild(row);
    });

    /* ── Apply / Reset ── */
    applyBtn.addEventListener('click', () => {
        applied.cats      = new Set(pending.cats);
        applied.minRating = pending.minRating;
        applied.minPrice  = priceMin.value ? parseFloat(priceMin.value) : null;
        applied.maxPrice  = priceMax.value ? parseFloat(priceMax.value) : null;
        if (currentQuery) renderResults(currentQuery);
    });

    resetBtn.addEventListener('click', () => {
        applied.cats.clear(); applied.minRating = null;
        applied.minPrice = null; applied.maxPrice = null;
        pending.cats.clear(); pending.minRating = null;
        priceMin.value = ''; priceMax.value = '';
        chipGrid.querySelectorAll('.sd-chip').forEach(c => c.classList.remove('selected'));
        ratingBox.querySelectorAll('.sd-rating-row').forEach(r => r.classList.remove('selected'));
        if (currentQuery) renderResults(currentQuery);
    });

    /* ── Helpers ── */
    function hl(text, q) {
        if (!q) return text;
        const esc = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp(`(${esc})`, 'gi'), '<mark>$1</mark>');
    }

    function starsHtml(n, size) {
        return Array.from({length:5},(_,i) =>
            `<span class="sd-result-star ${i<Math.round(n)?'on':'off'}" style="font-size:${size}px">★</span>`
        ).join('');
    }

    /* ── Skeleton ── */
    function skeleton() {
        resultsPanel.innerHTML = `<div class="sd-skeleton">${[1,2,3].map(()=>`
            <div class="sd-skel-row">
                <div class="sd-skel-box sd-skel-thumb"></div>
                <div class="sd-skel-lines">
                    <div class="sd-skel-box sd-skel-line"></div>
                    <div class="sd-skel-box sd-skel-line s"></div>
                </div>
            </div>`).join('')}</div>`;
    }

    /* ── Render results (live DB search) ── */
    async function renderResults(query) {
        const q = query.trim();
        if (!q) {
            resultsPanel.innerHTML = `<div class="sd-no-results">Start typing to search products</div>`;
            return;
        }

        const url = new URL('/cleckbasket/backend/search.php', window.location.origin);
        url.searchParams.set('q', q);
        if (applied.cats.size)         url.searchParams.set('cats',       [...applied.cats].join(','));
        if (applied.minRating)         url.searchParams.set('min_rating', applied.minRating);
        if (applied.minPrice !== null) url.searchParams.set('min_price',  applied.minPrice);
        if (applied.maxPrice !== null) url.searchParams.set('max_price',  applied.maxPrice);

        try {
            const res     = await fetch(url);
            const data    = await res.json();
            const results = data.products || [];

            if (!results.length) {
                resultsPanel.innerHTML = `<div class="sd-no-results">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.5" style="display:block;margin:0 auto 8px;opacity:.3">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    No results for "<strong>${q}</strong>"
                </div>`;
                return;
            }

            const shown     = results.slice(0, 8);
            const more      = results.length - shown.length;
            const searchUrl = `/cleckbasket/search.php?q=${encodeURIComponent(query)}`;

            resultsPanel.innerHTML = `
                <div class="sd-section-label">Products (${results.length}${results.length >= 30 ? '+' : ''})</div>
                <ul class="sd-results-list" role="listbox">
                    ${shown.map((p, i) => `
                    <li>
                        <a href="/cleckbasket/includes/pages/product_detail.php?id=${p.id}"
                           class="sd-result-item" data-idx="${i}">
                            <div class="sd-result-thumb">
                                ${p.image
                                    ? `<img src="/cleckbasket/assets/images/${p.image}" style="width:100%;height:100%;object-fit:cover;border-radius:8px" onerror="this.style.display='none'">`
                                    : '<span style="font-size:20px">🛒</span>'}
                            </div>
                            <div class="sd-result-info">
                                <div class="sd-result-name">${hl(p.name, query)}</div>
                                <div class="sd-result-meta">
                                    <span>${p.category}</span>
                                    <span style="display:flex;gap:1px">${starsHtml(p.rating, 10)}</span>
                                    <span>${p.rating > 0 ? p.rating.toFixed(1) : 'No reviews'}</span>
                                </div>
                            </div>
                            <div class="sd-result-price">£${p.price.toFixed(2)}</div>
                        </a>
                    </li>`).join('')}
                </ul>
                ${more > 0 ? `<div class="sd-footer">
                    <a href="${searchUrl}" class="sd-view-all">View all ${results.length}${results.length >= 30 ? '+' : ''} results →</a>
                </div>` : ''}`;

            focusedIdx = -1;
        } catch (err) {
            resultsPanel.innerHTML = `<div class="sd-no-results">Search unavailable. Please try again.</div>`;
        }
    }

    /* ── Open / close ── */
    function open()  { dropdown.classList.add('active');    searchInput.setAttribute('aria-expanded','true');  }
    function close() { dropdown.classList.remove('active'); searchInput.setAttribute('aria-expanded','false'); focusedIdx=-1; }

    /* ── Search input ── */
    searchInput.addEventListener('input', function () {
        currentQuery = this.value;
        clearTimeout(debounceTimer);
        if (!currentQuery.trim()) { close(); return; }
        skeleton(); open();
        debounceTimer = setTimeout(() => renderResults(currentQuery), 180);
    });

    searchInput.addEventListener('focus', function () {
        open();
    });

    /* ── Keyboard nav ── */
    searchInput.addEventListener('keydown', function (e) {
        if (!dropdown.classList.contains('active')) return;
        const items = resultsPanel.querySelectorAll('.sd-result-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusedIdx = Math.min(focusedIdx+1, items.length-1);
            items.forEach((el,i) => el.classList.toggle('focused', i===focusedIdx));
            if (items[focusedIdx]) items[focusedIdx].scrollIntoView({block:'nearest'});
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusedIdx = Math.max(focusedIdx-1, 0);
            items.forEach((el,i) => el.classList.toggle('focused', i===focusedIdx));
        } else if (e.key === 'Enter' && focusedIdx >= 0 && items[focusedIdx]) {
            e.preventDefault(); items[focusedIdx].click();
        } else if (e.key === 'Escape') { close(); this.blur(); }
    });

    searchForm.addEventListener('submit', () => close());

    /* ── Outside click ── */
    document.addEventListener('click', e => {
        if (!document.getElementById('desktopSearchWrapper').contains(e.target)) close();
    });

    /* ── Tablet toggle ── */
    searchForm.addEventListener('click', e => {
        if (window.innerWidth <= 1145 && !searchInput.classList.contains('visible')) {
            e.preventDefault();
            searchInput.classList.add('visible');
            searchInput.focus();
        }
    });
    document.addEventListener('click', e => {
        if (window.innerWidth > 1145) return;
        if (!searchForm.contains(e.target)) searchInput.classList.remove('visible');
    });

    /* ═══ MOBILE SEARCH ═══ */
    const mobInput    = document.getElementById('mobileSearchInput');
    const mobDropdown = document.getElementById('mobileSearchDropdown');
    const mobForm     = document.getElementById('mobileSearchForm');
    let mobTimer = null;

    if (mobInput && mobDropdown) {
        mobInput.addEventListener('input', function () {
            const q = this.value.trim();
            clearTimeout(mobTimer);
            if (!q) { mobDropdown.classList.remove('active'); mobDropdown.innerHTML=''; return; }

            mobDropdown.innerHTML = `<div class="sd-skeleton">${[1,2].map(()=>`
                <div class="sd-skel-row">
                    <div class="sd-skel-box sd-skel-thumb"></div>
                    <div class="sd-skel-lines">
                        <div class="sd-skel-box sd-skel-line"></div>
                        <div class="sd-skel-box sd-skel-line s"></div>
                    </div>
                </div>`).join('')}</div>`;
            mobDropdown.classList.add('active');

            mobTimer = setTimeout(async () => {
                try {
                    const res  = await fetch(`/cleckbasket/backend/search.php?q=${encodeURIComponent(q)}`);
                    const data = await res.json();
                    const results = (data.products || []).slice(0, 6);

                    if (!results.length) {
                        mobDropdown.innerHTML = `<div class="sd-no-results">No results for "<strong>${q}</strong>"</div>`;
                        return;
                    }

                    function hl2(t, qry) {
                        const esc = qry.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
                        return t.replace(new RegExp(`(${esc})`,'gi'),'<mark>$1</mark>');
                    }

                    mobDropdown.innerHTML = `<ul class="sd-results-list">
                        ${results.map(p=>`<li>
                            <a href="/cleckbasket/includes/pages/product_detail.php?id=${p.id}" class="sd-result-item">
                                <div class="sd-result-thumb">
                                    ${p.image
                                        ? `<img src="/cleckbasket/assets/images/${p.image}" style="width:100%;height:100%;object-fit:cover;border-radius:8px" onerror="this.style.display='none'">`
                                        : '<span style="font-size:20px">🛒</span>'}
                                </div>
                                <div class="sd-result-info">
                                    <div class="sd-result-name">${hl2(p.name,q)}</div>
                                    <div class="sd-result-meta">${p.category} · £${p.price.toFixed(2)}</div>
                                </div>
                                <div class="sd-result-price">£${p.price.toFixed(2)}</div>
                            </a>
                        </li>`).join('')}
                    </ul>`;
                } catch (err) {
                    mobDropdown.innerHTML = `<div class="sd-no-results">Search unavailable.</div>`;
                }
            }, 200);
        });

        document.addEventListener('click', e => {
            if (!mobInput.closest('.mobile-search-wrapper').contains(e.target))
                mobDropdown.classList.remove('active');
        });

        mobForm.addEventListener('submit', () => mobDropdown.classList.remove('active'));
    }

    /* ═══ HAMBURGER ═══ */
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mainNav      = document.getElementById('mainNav');
    if (hamburgerBtn && mainNav) {
        hamburgerBtn.addEventListener('click', () => {
            mainNav.classList.toggle('nav-open');
            hamburgerBtn.classList.toggle('active');
        });
    }

    /* ═══ AUTH STATE ═══ */
    const KEYS = { isLogin:'is_login', userName:'user_name' };
    const sessionUser  = <?php echo json_encode($session_user); ?>;
    const isLogin  = sessionUser || localStorage.getItem(KEYS.isLogin) === 'true';
    const userName = sessionUser || localStorage.getItem(KEYS.userName) || '';
    const userGreeting = document.querySelector('.user-greeting');
    const userBtn      = document.querySelector('.user-btn');

    if (userGreeting && userBtn) {
        if (isLogin) {
            userGreeting.textContent = `Hi, ${userName}!`;
            userBtn.href = '/cleckbasket/includes/pages/profile.php';

            const logoutLink = document.createElement('a');
            logoutLink.href = '#';
            logoutLink.className = 'icon-btn mobile-profile-link';
            logoutLink.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg><span>Logout</span>`;
            logoutLink.addEventListener('click', e => {
                e.preventDefault();
                localStorage.removeItem(KEYS.isLogin);
                localStorage.removeItem(KEYS.userName);
                window.location.href = '/cleckbasket/backend/logout.php';
            });
            userBtn.insertAdjacentElement('afterend', logoutLink);
        } else {
            userGreeting.textContent = 'Sign Up';
            userBtn.href = '/cleckbasket/includes/pages/customer_register.php';
            localStorage.removeItem(KEYS.isLogin);
            localStorage.removeItem(KEYS.userName);

            const loginLink = document.createElement('a');
            loginLink.href = '/cleckbasket/includes/pages/login.php';
            loginLink.className = 'icon-btn mobile-profile-link';
            loginLink.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg><span>Login</span>`;
            userBtn.insertAdjacentElement('afterend', loginLink);
        }
    }

});
</script>
</body>
</html>