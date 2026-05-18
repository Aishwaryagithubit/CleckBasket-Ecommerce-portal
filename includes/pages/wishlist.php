<?php include('../header.php'); ?>

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --brown: #5A331C;
  --brown-light: #7D492C;
  --bg: #F5F5F5;
  --card-bg: #FFFFFF;
  --text-dark: #29170C;
  --text-mid: #51443E;
  --text-muted: #999;
  --border: #ECECEC;
  --green: #05A845;
  --red: #E74C3C;
  --radius: 12px;
}

body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text-dark); }

/* Page */
.wishlist-page {
  max-width: 1200px;
  margin: 40px auto 80px;
  padding: 0 24px;
}

/* Page Header */
.wishlist-page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}

.wishlist-page-header h1 {
  font-size: 32px;
  font-weight: 800;
  color: var(--brown);
  display: flex;
  align-items: center;
  gap: 12px;
}

.wishlist-page-header h1 svg { stroke: var(--brown); }

.wishlist-count-badge {
  background: var(--brown);
  color: #FFF;
  font-size: 14px;
  font-weight: 700;
  padding: 4px 14px;
  border-radius: 9999px;
}

.btn-clear-all {
  background: none;
  border: 1px solid var(--red);
  color: var(--red);
  padding: 10px 24px;
  border-radius: 9999px;
  font-family: 'DM Sans', sans-serif;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-clear-all:hover { background: var(--red); color: #FFF; }

/* Grid */
.wishlist-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 28px;
}

/* Card */
.wish-card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: box-shadow 0.25s, transform 0.25s;
  position: relative;
}

.wish-card:hover {
  box-shadow: 0 12px 32px rgba(90,51,28,0.10);
  transform: translateY(-4px);
}

/* Remove btn */
.wish-remove-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.9);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 2;
  transition: background 0.2s;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.wish-remove-btn:hover { background: var(--red); }
.wish-remove-btn:hover svg { stroke: #FFF; }
.wish-remove-btn svg { width: 18px; height: 18px; stroke: var(--red); stroke-width: 2; fill: none; }

/* Image area */
.wish-card-img {
  height: 220px;
  background: #F6F6F6;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.wish-card-img img {
  max-width: 80%;
  max-height: 80%;
  object-fit: contain;
}

/* Info */
.wish-card-info {
  padding: 20px 24px 24px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.wish-card-name {
  font-weight: 700;
  font-size: 18px;
  color: var(--text-dark);
  margin-bottom: 6px;
}

.wish-card-price {
  font-weight: 700;
  font-size: 20px;
  color: var(--green);
  margin-bottom: 20px;
}

.wish-card-actions {
  display: flex;
  gap: 12px;
  margin-top: auto;
}

.btn-move-cart {
  flex: 1;
  background: linear-gradient(135deg, var(--brown) 0%, var(--brown-light) 100%);
  color: #FFF;
  border: none;
  border-radius: 10px;
  padding: 14px 20px;
  font-family: 'DM Sans', sans-serif;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: opacity 0.2s, transform 0.15s;
}
.btn-move-cart:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-move-cart svg { width: 18px; height: 18px; stroke: #FFF; fill: none; stroke-width: 2; }

/* Empty State */
.wishlist-empty {
  text-align: center;
  padding: 100px 20px;
  display: none;
}
.wishlist-empty.show { display: block; }

.empty-icon {
  width: 100px;
  height: 100px;
  margin: 0 auto 24px;
  background: rgba(90,51,28,0.06);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.empty-icon svg { width: 48px; height: 48px; stroke: var(--brown); fill: none; stroke-width: 1.5; }

.wishlist-empty h2 {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 8px;
}

.wishlist-empty p {
  font-size: 16px;
  color: var(--text-muted);
  margin-bottom: 32px;
}

.btn-browse {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--brown);
  color: #FFF;
  border: none;
  border-radius: 9999px;
  padding: 14px 36px;
  font-family: 'DM Sans', sans-serif;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  text-decoration: none;
  transition: opacity 0.2s;
}
.btn-browse:hover { opacity: 0.9; }

/* Responsive */
@media (max-width: 600px) {
  .wishlist-page-header { flex-direction: column; gap: 16px; align-items: flex-start; }
  .wishlist-grid { grid-template-columns: 1fr 1fr; gap: 16px; }
  .wish-card-img { height: 160px; }
}
</style>

<main class="wishlist-page">

  <!-- Header -->
  <div class="wishlist-page-header">
    <h1>
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
      </svg>
      My Wishlist
      <span class="wishlist-count-badge" id="wishlistBadge">0</span>
    </h1>
    <button class="btn-clear-all" id="btnClearAll">Clear All</button>
  </div>

  <!-- Grid (rendered by JS) -->
  <div class="wishlist-grid" id="wishlistGrid"></div>

  <!-- Empty State -->
  <div class="wishlist-empty" id="wishlistEmpty">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
      </svg>
    </div>
    <h2>Your Wishlist is Empty</h2>
    <p>Start adding products you love and they'll appear here.</p>
    <a href="/cleckbasket/includes/pages/shop.php" class="btn-browse">Browse Products</a>
  </div>

</main>

<script src="/cleckbasket/assets/js/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  renderWishlist();

  // Clear All
  document.getElementById('btnClearAll').addEventListener('click', () => {
    if (!confirm('Remove all items from your wishlist?')) return;
    localStorage.setItem('wishlist', JSON.stringify([]));
    renderWishlist();
  });

  // Delegate: Remove & Move-to-Cart
  document.getElementById('wishlistGrid').addEventListener('click', (e) => {
    const removeBtn = e.target.closest('.wish-remove-btn');
    const cartBtn   = e.target.closest('.btn-move-cart');

    if (removeBtn) {
      const name = removeBtn.dataset.name;
      removeFromWishlist(name);
      renderWishlist();
      showToast(`${name} removed from wishlist`);
    }

    if (cartBtn) {
      const name  = cartBtn.dataset.name;
      const price = parseFloat(cartBtn.dataset.price);
      const image = cartBtn.dataset.image;

      // Add to cart (reuse main.js helper)
      addToCart({ name, price, image, quantity: 1 });

      // Remove from wishlist
      removeFromWishlist(name);
      renderWishlist();
      showToast(`${name} moved to cart 🛒`);
    }
  });
});

function removeFromWishlist(name) {
  let wl = JSON.parse(localStorage.getItem('wishlist') || '[]');
  wl = wl.filter(i => i.name !== name);
  localStorage.setItem('wishlist', JSON.stringify(wl));
}

function renderWishlist() {
  const grid  = document.getElementById('wishlistGrid');
  const empty = document.getElementById('wishlistEmpty');
  const badge = document.getElementById('wishlistBadge');
  const clearBtn = document.getElementById('btnClearAll');

  const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
  grid.innerHTML = '';

  badge.textContent = wishlist.length;

  if (wishlist.length === 0) {
    empty.classList.add('show');
    clearBtn.style.display = 'none';
    return;
  }

  empty.classList.remove('show');
  clearBtn.style.display = '';

  wishlist.forEach(item => {
    const price = typeof item.price === 'number' ? item.price.toFixed(2) : item.price;

    const card = document.createElement('div');
    card.className = 'wish-card';
    card.innerHTML = `
      <button class="wish-remove-btn" data-name="${esc(item.name)}" aria-label="Remove">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
      <div class="wish-card-img">
        <img src="${esc(item.image)}" alt="${esc(item.name)}" onerror="this.src='https://placehold.co/260x200/f6f6f6/ccc?text=No+Image'" />
      </div>
      <div class="wish-card-info">
        <div class="wish-card-name">${esc(item.name)}</div>
        <div class="wish-card-price">£${price}</div>
        <div class="wish-card-actions">
          <button class="btn-move-cart" data-name="${esc(item.name)}" data-price="${item.price}" data-image="${esc(item.image)}">
            <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Move to Cart
          </button>
        </div>
      </div>
    `;
    grid.appendChild(card);
  });
}

function esc(str) {
  if (!str) return '';
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}
</script>

<?php include('../footer.php'); ?>
