// Main JS Logic for CleckBasket State Management (No Backend)

const STORAGE_KEYS = {
  cart: "cart",
  wishlist: "wishlist",
  isLogin: "is_login",
  userName: "user_name",
};

document.addEventListener("DOMContentLoaded", () => {
  initAuthState();
  initCartState();
  initWishlistState();
  attachCartListeners();
  attachWishlistListeners();
  attachProductDetailListeners();
  initFruitSlider();
});

// --- AUTHENTICATION STATE ---
function initAuthState() {
  const isLogin = localStorage.getItem(STORAGE_KEYS.isLogin) === "true";
  const userName = localStorage.getItem(STORAGE_KEYS.userName) || "Anoushka";

  // Update Header UI
  const userGreeting = document.querySelector(".user-greeting");
  const userBtn = document.querySelector(".user-btn");

  // Handle Login Page Logic
  const authForm = document.querySelector(".auth-form");
  if (authForm && window.location.pathname.includes("signup.php")) {
    authForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const emailInput = document.getElementById("email").value;
      // Fake login
      localStorage.setItem(STORAGE_KEYS.isLogin, "true");
      localStorage.setItem(STORAGE_KEYS.userName, emailInput.split("@")[0]);
      window.location.href = "/cleckbasket/includes/pages/homepage.php";
    });
  }

  // Handle Logout Logic (if profile page has a logout button)
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      localStorage.removeItem(STORAGE_KEYS.isLogin);
      localStorage.removeItem(STORAGE_KEYS.userName);
      window.location.href = "/cleckbasket/includes/pages/homepage.php";
    });
  }
}

// --- CART STATE ---
function getCart() {
  const cart = localStorage.getItem(STORAGE_KEYS.cart);
  return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
  localStorage.setItem(STORAGE_KEYS.cart, JSON.stringify(cart));
  updateCartBadge();
}

function updateCartBadge() {
  const cart = getCart();
  const count = cart.reduce((total, item) => total + item.quantity, 0);
  const badge = document.getElementById("cartCount");
  if (badge) {
    badge.textContent = count;
    badge.style.display = count > 0 ? "flex" : "none";
  }
}

function initCartState() {
  updateCartBadge();
}

function attachCartListeners() {
  document.querySelectorAll(".product-card").forEach((card) => {
    const addBtn = card.querySelector(".add-to-cart, .btn-add-cart");
    const qtyInput = card.querySelector(".quantity-input");

    if (!addBtn) {
      return;
    }

    addBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      const product = extractProductFromCard(card);
      if (!product) {
        return;
      }

      const qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;
      addToCart({ ...product, quantity: qty });
      showToast(`${qty}x ${product.name} added to cart`);
    });
  });

  document.querySelectorAll(".fruit-card").forEach((card) => {
    const addBtns = card.querySelectorAll(".fruit-add-btn");
    addBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const product = extractProductFromFruit(card);
        if (!product) {
          return;
        }

        addToCart({ ...product, quantity: 1 });
        showToast(`1x ${product.name} added to cart`);
      });
    });
  });
}

function addToCart(product) {
  const cart = getCart();
  const existing = cart.find((item) => item.name === product.name);

  if (existing) {
    existing.quantity += product.quantity;
  } else {
    cart.push(product);
  }

  saveCart(cart);
}

function parsePrice(text) {
  if (!text) {
    return 0;
  }
  const value = parseFloat(text.replace(/[^0-9.]/g, ""));
  return Number.isFinite(value) ? value : 0;
}

function extractProductFromCard(card) {
  if (!card) {
    return null;
  }

  const nameEl = card.querySelector(".product-card-name, h3");
  const priceEl = card.querySelector(".product-card-price, .price");
  const imgEl = card.querySelector("img");

  if (!nameEl || !priceEl || !imgEl) {
    return null;
  }

  return {
    id:    card.dataset.productId ? parseInt(card.dataset.productId, 10) : null,
    name:  nameEl.textContent.trim(),
    price: parsePrice(priceEl.textContent),
    image: imgEl.src,
  };
}

function extractProductFromFruit(card) {
  const nameEl = card.querySelector("h3");
  const priceEl = card.querySelector(".fruit-price");
  const imgEl = card.querySelector("img");

  if (!nameEl || !priceEl || !imgEl) {
    return null;
  }

  return {
    id:    card.dataset.productId ? parseInt(card.dataset.productId, 10) : null,
    name:  nameEl.textContent.trim(),
    price: parsePrice(priceEl.textContent),
    image: imgEl.src,
  };
}

function getWishlist() {
  const wishlist = localStorage.getItem(STORAGE_KEYS.wishlist);
  return wishlist ? JSON.parse(wishlist) : [];
}

function saveWishlist(wishlist) {
  localStorage.setItem(STORAGE_KEYS.wishlist, JSON.stringify(wishlist));
  updateWishlistBadge();
}

function updateWishlistBadge() {
  const count = getWishlist().length;
  const badge = document.getElementById("favCount");
  if (badge) {
    badge.textContent = count;
    badge.style.display = count > 0 ? "flex" : "none";
  }
}

function initWishlistState() {
  updateWishlistBadge();
  const wishlist = getWishlist();
  document.querySelectorAll(".wishlist, .btn-fav, .btn-wish").forEach((btn) => {
    const product = resolveProductForButton(btn);
    if (!product) {
      return;
    }

    const isSaved = wishlist.some((item) => item.name === product.name);
    btn.classList.toggle("is-active", isSaved);
    btn.setAttribute("aria-pressed", isSaved ? "true" : "false");
  });
}

function attachWishlistListeners() {
  document.querySelectorAll(".wishlist, .btn-fav, .btn-wish").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const product = resolveProductForButton(btn);
      if (!product) {
        return;
      }

      toggleWishlistItem(product, btn);
    });
  });
}

function resolveProductForButton(btn) {
  const card = btn.closest(".product-card");
  if (card) {
    return extractProductFromCard(card);
  }

  return extractProductFromDetail();
}

function toggleWishlistItem(product, btn) {
  const wishlist = getWishlist();
  const index = wishlist.findIndex((item) => item.name === product.name);

  if (index >= 0) {
    wishlist.splice(index, 1);
    showToast(`${product.name} removed from wishlist`);
    if (btn) {
      btn.classList.remove("is-active");
      btn.setAttribute("aria-pressed", "false");
    }
  } else {
    wishlist.push(product);
    showToast(`${product.name} saved to wishlist`);
    if (btn) {
      btn.classList.add("is-active");
      btn.setAttribute("aria-pressed", "true");
    }
  }

  saveWishlist(wishlist);
}

// --- PRODUCT DETAILS ---
function attachProductDetailListeners() {
  document.querySelectorAll(".product-card, .fruit-card").forEach((card) => {
    card.style.cursor = "pointer";
    card.addEventListener("click", () => {
      const product = card.classList.contains("fruit-card")
        ? extractProductFromFruit(card)
        : extractProductFromCard(card);

      if (!product) {
        return;
      }

      const productId = card.dataset.productId;
      if (productId) {
        window.location.href = `/cleckbasket/includes/pages/product_detail.php?id=${productId}`;
      }
    });
  });
}

function extractProductFromDetail() {
  const nameEl = document.querySelector(".details h1");
  const priceEl = document.querySelector(".details .price");
  const imgEl = document.querySelector(".product-image");

  if (!nameEl || !priceEl || !imgEl) {
    return null;
  }

  return {
    name: nameEl.textContent.trim(),
    price: parsePrice(priceEl.textContent),
    image: imgEl.src,
  };
}

function showToast(message) {
  if (!message) {
    return;
  }

  ensureToastStyles();

  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);
  }

  const toast = document.createElement("div");
  toast.className = "toast";
  toast.textContent = message;
  container.appendChild(toast);

  requestAnimationFrame(() => {
    toast.classList.add("is-visible");
  });

  setTimeout(() => {
    toast.classList.remove("is-visible");
    toast.addEventListener("transitionend", () => toast.remove(), {
      once: true,
    });
  }, 2200);
}

function ensureToastStyles() {
  if (document.querySelector("style[data-toast]")) {
    return;
  }

  const style = document.createElement("style");
  style.setAttribute("data-toast", "true");
  style.textContent = `
        .toast-container {
            position: fixed;
            right: 24px;
            bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 9999;
            pointer-events: none;
        }
        .toast {
            background: #1f1f1f;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.2s ease, transform 0.2s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .toast.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
    `;
  document.head.appendChild(style);
}

function initFruitSlider() {
  const list = document.querySelector("[data-fruits-list]");
  const prevBtn = document.querySelector(".fruit-prev");
  const nextBtn = document.querySelector(".fruit-next");

  if (!list || !prevBtn || !nextBtn) {
    return;
  }

  const cards = list.querySelectorAll(".fruit-card");
  if (cards.length <= 5) {
    return;
  }

  prevBtn.classList.add("is-visible");
  nextBtn.classList.add("is-visible");

  const getScrollStep = () => {
    const card = list.querySelector(".fruit-card");
    if (!card) {
      return 220;
    }

    const gapValue = window.getComputedStyle(list).gap || "0";
    const gap = Number.parseFloat(gapValue) || 0;
    return card.getBoundingClientRect().width + gap;
  };

  const updateNavState = () => {
    const maxScrollLeft = list.scrollWidth - list.clientWidth;
    prevBtn.disabled = list.scrollLeft <= 0;
    nextBtn.disabled = list.scrollLeft >= maxScrollLeft - 1;
  };

  prevBtn.addEventListener("click", () => {
    list.scrollBy({ left: -getScrollStep(), behavior: "smooth" });
  });

  nextBtn.addEventListener("click", () => {
    list.scrollBy({ left: getScrollStep(), behavior: "smooth" });
  });

  list.addEventListener("scroll", updateNavState);
  window.addEventListener("resize", updateNavState);
  updateNavState();
}
