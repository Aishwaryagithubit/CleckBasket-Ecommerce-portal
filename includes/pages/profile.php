<?php /* Customer Profile Page — Frontend Only */ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Account — FreshCart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="/cleckbasket/assets/css/profile.css" />
</head>

<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <main class="profile-page-wrapper">
        <div class="profile-container" style="max-width: 1440px; margin: 0 auto;">
            
            <header class="profile-main-header">
                <h1 class="welcome-text" id="profile-welcome">Welcome, Anoushka</h1>
                <p class="cur-date" id="profile-date">Tue, 07 June 2026</p>
            </header>

            <div class="profile-grid">
                
                <!-- LEFT COLUMN: Profile Form -->
                <div class="profile-left-col">
                    <div class="profile-card profile-form-card">
                        
                        <div class="profile-cover-brown"></div>
                        
                        <div class="profile-user-bar">
                            <div class="user-avatar">
                                <svg width="90" height="90" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="45" cy="45" r="45" fill="#666666"/>
                                    <path d="M45 42C51.6274 42 57 36.6274 57 30C57 23.3726 51.6274 18 45 18C38.3726 18 33 23.3726 33 30C33 36.6274 38.3726 42 45 42Z" fill="#CCCCCC"/>
                                    <path d="M21.5 66.5C21.5 56.835 32.0213 49 45 49C57.9787 49 68.5 56.835 68.5 66.5C68.5 69.5376 66.0376 72 63 72H27C23.9624 72 21.5 69.5376 21.5 66.5Z" fill="#CCCCCC"/>
                                </svg>
                            </div>
                            
                            <div class="user-info-text">
                                <h2 id="profile-name">Anoushka karki</h2>
                                <p id="profile-email">anoushka@gmail.com</p>
                            </div>

                            <button class="btn-edit-profile-blue" id="profile-edit-btn">Edit</button>
                        </div>

                        <div class="profile-form-wrap">
                            
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" class="input-grey" id="profile-full-name" value="Anoushka Karki" readonly />
                            </div>

                            <div class="form-group">
                                <label>Contact Number</label>
                                <div class="input-with-icon">
                                    <input type="text" class="input-grey" id="profile-phone" value="9867543378" readonly />
                                    <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <div class="input-with-icon">
                                    <input type="text" class="input-grey" id="profile-email-input" value="anoushka@gmail.com" readonly />
                                    <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Default Address</label>
                                <div class="input-with-icon">
                                    <input type="text" class="input-grey" id="profile-address" value="" readonly />
                                    <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </div>
                            </div>

                            <h3 class="subsection-title">My email Address</h3>
                            
                            <div class="email-address-row">
                                <div class="email-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                </div>
                                <div class="email-details">
                                    <p class="email-val" id="profile-email-secondary">anoushka@gmail.com</p>
                                    <p class="email-time">1 month ago</p>
                                </div>
                            </div>

                            <button class="btn-add-email" id="profile-save-btn" type="button">Save Changes</button>
                            
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Orders and Activities -->
                <div class="profile-right-col">
                    
                    <!-- Recent Orders -->
                    <div class="profile-card recent-orders-card">
                        <h2 class="card-title">Recent Orders</h2>
                        
                        <div class="order-list" id="profile-orders">
                            <div class="order-item">
                                <img src="/cleckbasket/assets/images/mango.png" alt="Mangoes" class="order-img" />
                                <div class="order-info">
                                    <h4 class="order-name">Fresh Mangoes</h4>
                                    <p class="order-price">$10.99</p>
                                    <div class="order-stars">★</div>
                                </div>
                            </div>
                            
                            <div class="order-item">
                                <img src="/cleckbasket/assets/images/banana.png" alt="Bananas" class="order-img" />
                                <div class="order-info">
                                    <h4 class="order-name">Yellow Bananas</h4>
                                    <p class="order-price">$8.50</p>
                                    <div class="order-stars">★</div>
                                </div>
                            </div>

                            <div class="order-item">
                                <img src="/cleckbasket/assets/images/watermelon.png" alt="Watermelons" class="order-img" />
                                <div class="order-info">
                                    <h4 class="order-name">Watermelons</h4>
                                    <p class="order-price">$20.5</p>
                                    <div class="order-stars">★</div>
                                </div>
                            </div>

                            <div class="order-item">
                                <img src="/cleckbasket/assets/images/banana.png" alt="Bananas" class="order-img" />
                                <div class="order-info">
                                    <h4 class="order-name">Yellow Bananas</h4>
                                    <p class="order-price">$8.50</p>
                                    <div class="order-stars">★</div>
                                </div>
                            </div>
                            
                            <div class="order-item">
                                <img src="/cleckbasket/assets/images/mango.png" alt="Mangoes" class="order-img" />
                                <div class="order-info">
                                    <h4 class="order-name">Fresh Mangoes</h4>
                                    <p class="order-price">$10.99</p>
                                    <div class="order-stars">★</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activities -->
                    <div class="profile-card activities-card">
                        <h2 class="card-title">Activities</h2>
                        
                        <ul class="activities-list">
                            <li>
                                <div class="activity-left">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <span>Recent Orders</span>
                                </div>
                                <span class="badge-green" id="badge-orders">5</span>
                            </li>
                            <li>
                                <div class="activity-left">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2">
                                        <circle cx="9" cy="21" r="1"/>
                                        <circle cx="20" cy="21" r="1"/>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                    </svg>
                                    <span>My Cart</span>
                                </div>
                                <span class="badge-green" id="badge-cart">6</span>
                            </li>
                            <li>
                                <div class="activity-left">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#E53935" stroke="#E53935" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                    </svg>
                                    <span>My Wishlist</span>
                                </div>
                                <span class="badge-green" id="badge-wishlist">7</span>
                            </li>
                            <li style="cursor: pointer;" id="logout-btn">
                                <div class="activity-left">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#E53935" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span style="color: #E53935; font-weight: 500;">Logout</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn()) {
                window.location.href = '/cleckbasket/includes/pages/login.php';
                return;
            }

            const profile = loadProfile();
            hydrateProfile(profile);
            hydrateOrders();
            hydrateBadges();
            hydrateDate();
            setEditingState(false);

            const editBtn = document.getElementById('profile-edit-btn');
            const saveBtn = document.getElementById('profile-save-btn');

            if (editBtn) {
                editBtn.addEventListener('click', () => {
                    const isEditing = editBtn.dataset.editing === 'true';
                    setEditingState(!isEditing);
                });
            }

            if (saveBtn) {
                saveBtn.addEventListener('click', () => {
                    const updated = readProfileInputs();
                    saveProfile(updated);
                    hydrateProfile(updated);
                    setEditingState(false);
                });
            }

            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', () => {
                    localStorage.removeItem('is_login');
                    // Optional: remove other user data
                    // localStorage.removeItem('user_name');
                    // localStorage.removeItem('user_email');
                    window.location.href = '/cleckbasket/includes/pages/login.php';
                });
            }
        });

        function isLoggedIn() {
            return localStorage.getItem('is_login') === 'true';
        }

        function hydrateDate() {
            const dateEl = document.getElementById('profile-date');
            if (!dateEl) {
                return;
            }

            const now = new Date();
            const formatted = now.toLocaleDateString('en-GB', {
                weekday: 'short',
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
            dateEl.textContent = formatted;
        }

        function loadProfile() {
            const stored = localStorage.getItem('profile_data');
            if (stored) {
                try {
                    return applyFallbackProfile(JSON.parse(stored));
                } catch (error) {
                    return buildDefaultProfile();
                }
            }
            return buildDefaultProfile();
        }

        function buildDefaultProfile() {
            const storedName = localStorage.getItem('user_name') || '';
            const storedEmail = localStorage.getItem('user_email') || '';
            return applyFallbackProfile({
                name: storedName,
                email: storedEmail,
                phone: '',
                address: ''
            });
        }

        function applyFallbackProfile(profile) {
            return {
                name: profile.name || localStorage.getItem('user_name') || '',
                email: profile.email || localStorage.getItem('user_email') || '',
                phone: profile.phone || '',
                address: profile.address || ''
            };
        }

        function saveProfile(profile) {
            localStorage.setItem('profile_data', JSON.stringify(profile));
            if (profile.address) {
                localStorage.setItem('delivery_address', profile.address);
            }
        }

        function hydrateProfile(profile) {
            setText('profile-welcome', profile.name ? `Welcome, ${profile.name}` : 'Welcome');
            setText('profile-name', profile.name || '');
            setText('profile-email', profile.email || '');
            setText('profile-email-secondary', profile.email || '');

            setInputValue('profile-full-name', profile.name);
            setInputValue('profile-email-input', profile.email);
            setInputValue('profile-phone', profile.phone);
            setInputValue('profile-address', profile.address || localStorage.getItem('delivery_address') || '');
        }

        function readProfileInputs() {
            return {
                name: getInputValue('profile-full-name'),
                email: getInputValue('profile-email-input'),
                phone: getInputValue('profile-phone'),
                address: getInputValue('profile-address')
            };
        }

        function setEditingState(isEditing) {
            const editBtn = document.getElementById('profile-edit-btn');
            const saveBtn = document.getElementById('profile-save-btn');

            if (editBtn) {
                editBtn.dataset.editing = isEditing ? 'true' : 'false';
                editBtn.textContent = isEditing ? 'Cancel' : 'Edit';
            }

            if (saveBtn) {
                saveBtn.disabled = !isEditing;
                saveBtn.style.opacity = isEditing ? '1' : '0.6';
            }

            ['profile-full-name', 'profile-email-input', 'profile-phone', 'profile-address'].forEach((id) => {
                const input = document.getElementById(id);
                if (input) {
                    input.readOnly = !isEditing;
                    input.classList.toggle('is-editing', isEditing);
                }
            });
        }

        function hydrateOrders() {
            const list = document.getElementById('profile-orders');
            if (!list) {
                return;
            }

            const stored = localStorage.getItem('recent_orders');
            let orders = [];

            if (stored) {
                try {
                    orders = JSON.parse(stored);
                } catch (error) {
                    orders = [];
                }
            }

            if (!Array.isArray(orders) || orders.length === 0) {
                list.innerHTML = '<p class="order-empty">No recent orders yet.</p>';
                return;
            }

            list.innerHTML = '';
            orders.slice(0, 5).forEach((order) => {
                const item = document.createElement('div');
                item.className = 'order-item';
                item.innerHTML = `
                    <img src="${order.image || '/cleckbasket/assets/images/banana.png'}" alt="${order.name || 'Order'}" class="order-img" />
                    <div class="order-info">
                        <h4 class="order-name">${order.name || 'Order item'}</h4>
                        <p class="order-price">£${Number(order.price || 0).toFixed(2)}</p>
                        <div class="order-stars">★</div>
                    </div>
                `;
                list.appendChild(item);
            });
        }

        function hydrateBadges() {
            const orders = JSON.parse(localStorage.getItem('recent_orders') || '[]');
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');

            setText('badge-orders', Array.isArray(orders) ? orders.length : 0);
            setText('badge-cart', Array.isArray(cart) ? cart.reduce((sum, item) => sum + (item.quantity || 0), 0) : 0);
            setText('badge-wishlist', Array.isArray(wishlist) ? wishlist.length : 0);
        }

        function setText(id, value) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = value;
            }
        }

        function setInputValue(id, value) {
            const input = document.getElementById(id);
            if (input) {
                input.value = value || '';
            }
        }

        function getInputValue(id) {
            const input = document.getElementById(id);
            return input ? input.value.trim() : '';
        }
    </script>

    <?php include __DIR__ . '/../footer.php'; ?>
</body>
</html>