<?php include __DIR__ . '/../header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/cleckbasket/assets/css/collection.css">

<main class="collection-page">
    <div class="collection-container">
        
        <!-- Header Section -->
        <header class="collection-header">
            <h1 class="page-title">Select Collection Slot</h1>
            <p class="page-subtitle">Please choose a preferred time to pick up your CleckBasket order.</p>
        </header>

        <!-- Date Selection Tabs -->
        <section class="date-tabs">
            <button class="date-tab active">Today, Oct 25</button>
            <button class="date-tab">Tomorrow, Oct 26</button>
            <button class="date-tab">Friday, Oct 27</button>
        </section>

        <!-- Time Slot Cards -->
        <section class="slot-cards">
            <!-- Morning Slot -->
            <div class="slot-card">
                <div class="card-content">
                    <div class="card-top">
                        <span class="slot-label">MORNING</span>
                        <h3 class="slot-time">10:00 - 13:00</h3>
                    </div>
                    <div class="card-bottom">
                        <div class="slot-availability">
                            <span class="availability-text">Slots available</span>
                            <span class="availability-count">8 left</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 40%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Afternoon Slot (Selected) -->
            <div class="slot-card selected">
                <div class="selected-badge">SELECTED</div>
                <div class="card-content">
                    <div class="card-top">
                        <span class="slot-label">AFTERNOON</span>
                        <h3 class="slot-time">13:00 - 16:00</h3>
                    </div>
                    <div class="card-bottom">
                        <div class="slot-availability">
                            <span class="availability-text">Slots available</span>
                            <span class="availability-count">5 left</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 10%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Evening Slot -->
            <div class="slot-card">
                <div class="card-content">
                    <div class="card-top">
                        <div class="label-row">
                            <span class="slot-label">EVENING</span>
                            <span class="status-badge urgent">
                                <svg width="8" height="10" viewBox="0 0 8 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 0C1.79 0 0 1.79 0 4C0 6.21 1.79 8 4 8C6.21 8 8 6.21 8 4C8 1.79 6.21 0 4 0ZM4.5 6H3.5V5H4.5V6ZM4.5 4H3.5V2H4.5V4Z" fill="#93000A"/>
                                </svg>
                                ALMOST FULL
                            </span>
                        </div>
                        <h3 class="slot-time">16:00 - 19:00</h3>
                    </div>
                    <div class="card-bottom">
                        <div class="slot-availability">
                            <span class="availability-text">Slots available</span>
                            <span class="availability-count">2 left</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill urgent" style="width: 5%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Detailed Availability Table -->
        <section class="availability-table-section">
            <h2 class="table-title">Daily Overview</h2>
            <div class="table-wrapper">
                <table class="availability-table">
                    <thead>
                        <tr>
                            <th>TIME SLOT</th>
                            <th>AVAILABILITY</th>
                            <th>DEMAND</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-time">10:00 - 13:00</td>
                            <td class="col-avail">8 slots</td>
                            <td class="col-demand">Moderate</td>
                            <td class="col-status">
                                <span class="status-pill available">AVAILABLE</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-time">13:00 - 16:00</td>
                            <td class="col-avail">5 slots</td>
                            <td class="col-demand">High</td>
                            <td class="col-status">
                                <span class="status-pill fast">FAST</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-time">16:00 - 19:00</td>
                            <td class="col-avail">2 slots</td>
                            <td class="col-demand">Very High</td>
                            <td class="col-status">
                                <span class="status-pill fast">FAST</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    <!-- Footer - Selection Confirmation Bar -->
    <div class="confirmation-bar">
        <div class="confirmation-container">
            <div class="selection-info">
                <div class="icon-circle">
                    <svg width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 2H15V0H13V2H5V0H3V2H2C0.89 2 0.00999999 2.9 0.00999999 4L0 18C0 19.1 0.89 20 2 20H16C17.1 20 18 19.1 18 18V4C18 2.9 17.1 2 16 2ZM16 18H2V7H16V18ZM4 9H9V14H4V9Z" fill="#101F0E"/>
                    </svg>
                </div>
                <div class="selection-text">
                    <span class="selection-label">SELECTED SLOT</span>
                    <span class="selection-value">Wednesday, Oct 25 &bull; 13:00 - 16:00</span>
                </div>
            </div>
            <div class="confirmation-actions">
                <button class="btn-back">BACK</button>
                <button class="btn-confirm">CONFIRM SLOT</button>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Slot data per date ──────────────────────────────────────────
    const slotData = {
        'today': [
            { label: 'MORNING',   time: '10:00 - 13:00', left: 8,  total: 20, urgent: false },
            { label: 'AFTERNOON', time: '13:00 - 16:00', left: 5,  total: 20, urgent: false },
            { label: 'EVENING',   time: '16:00 - 19:00', left: 2,  total: 20, urgent: true  },
        ],
        'tomorrow': [
            { label: 'MORNING',   time: '10:00 - 13:00', left: 15, total: 20, urgent: false },
            { label: 'AFTERNOON', time: '13:00 - 16:00', left: 12, total: 20, urgent: false },
            { label: 'EVENING',   time: '16:00 - 19:00', left: 9,  total: 20, urgent: false },
        ],
        'friday': [
            { label: 'MORNING',   time: '10:00 - 13:00', left: 3,  total: 20, urgent: false },
            { label: 'AFTERNOON', time: '13:00 - 16:00', left: 1,  total: 20, urgent: true  },
            { label: 'EVENING',   time: '16:00 - 19:00', left: 0,  total: 20, urgent: true  },
        ],
    };

    const dateLabelMap = {
        'today':    'Today, Oct 25',
        'tomorrow': 'Tomorrow, Oct 26',
        'friday':   'Friday, Oct 27',
    };

    let selectedDate = 'today';
    let selectedSlotIndex = 1; // afternoon selected by default
    

    // ── DOM refs ────────────────────────────────────────────────────
    const dateTabs      = document.querySelectorAll('.date-tab');
    const slotCards     = document.querySelectorAll('.slot-card');
    const selectionVal  = document.querySelector('.selection-value');
    const confirmBtn    = document.querySelector('.btn-confirm');
    const backBtn       = document.querySelector('.btn-back');

    // ── Render cards for current date/selection ─────────────────────
    function renderCards() {
        const slots = slotData[selectedDate];

        slotCards.forEach((card, i) => {
            const slot = slots[i];
            const isSel = i === selectedSlotIndex;
            const isFull = slot.left === 0;

            // Base classes
            card.className = 'slot-card';
            if (isSel && !isFull)  card.classList.add('selected');
            if (isFull)            card.classList.add('full');
            card.style.cursor = isFull ? 'not-allowed' : 'pointer';
            card.style.opacity = isFull ? '0.5' : '1';

            // Fill percentage (inverted — bar shows how full it is)
            const fillPct = Math.round(((slot.total - slot.left) / slot.total) * 100);

            // Build urgent badge HTML
            const urgentBadge = slot.urgent && !isFull ? `
                <span class="status-badge urgent">
                    <svg width="8" height="10" viewBox="0 0 8 10" fill="none">
                        <path d="M4 0C1.79 0 0 1.79 0 4C0 6.21 1.79 8 4 8C6.21 8 8 6.21 8 4C8 1.79 6.21 0 4 0ZM4.5 6H3.5V5H4.5V6ZM4.5 4H3.5V2H4.5V4Z" fill="#93000A"/>
                    </svg>
                    ALMOST FULL
                </span>` : '';

            const fullBadge = isFull ? `<span class="status-badge urgent">FULL</span>` : '';

            card.innerHTML = `
                ${isSel && !isFull ? '<div class="selected-badge">SELECTED</div>' : ''}
                <div class="card-content">
                    <div class="card-top">
                        <div class="label-row">
                            <span class="slot-label">${slot.label}</span>
                            ${urgentBadge}${fullBadge}
                        </div>
                        <h3 class="slot-time">${slot.time}</h3>
                    </div>
                    <div class="card-bottom">
                        <div class="slot-availability">
                            <span class="availability-text">${isFull ? 'No slots available' : 'Slots available'}</span>
                            <span class="availability-count">${isFull ? '0 left' : slot.left + ' left'}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill ${slot.urgent || isFull ? 'urgent' : ''}" style="width: ${fillPct}%;"></div>
                        </div>
                    </div>
                </div>
            `;
        });

        updateConfirmationBar();
        rebindCardClicks();
    }

    // ── Update bottom bar text ──────────────────────────────────────
    function updateConfirmationBar() {
        const slots = slotData[selectedDate];
        const slot  = slots[selectedSlotIndex];
        selectionVal.textContent = `${dateLabelMap[selectedDate]} • ${slot.time}`;
    }

    // ── Card click binding ──────────────────────────────────────────
    function rebindCardClicks() {
        slotCards.forEach((card, i) => {
            card.addEventListener('click', () => {
    const slot = slotData[selectedDate][i];
    if (slot.left === 0) return;
    if (selectedSlotIndex === i) return; // already selected, no double decrement

    // Give back the previously selected slot's count
    slotData[selectedDate][selectedSlotIndex].left += 1;

    // Take from the newly selected slot
    slot.left -= 1;

    selectedSlotIndex = i;
    renderCards();
});
        });
    }

    // ── Date tab clicks ─────────────────────────────────────────────
    const dateKeys = ['today', 'tomorrow', 'friday'];
    dateTabs.forEach((tab, i) => {
        tab.addEventListener('click', () => {
            selectedDate = dateKeys[i];
            selectedSlotIndex = 0; // reset to first available
            // auto-pick first non-full slot
            const slots = slotData[selectedDate];
            for (let j = 0; j < slots.length; j++) {
                if (slots[j].left > 0) { selectedSlotIndex = j; break; }
            }

            dateTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            renderCards();
        });
    });

    // ── Confirm / Back ──────────────────────────────────────────────
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            alert('Slot confirmed successfully!');
            window.location.href = '/cleckbasket/includes/cart/invoice.php';
        });
    }
    if (backBtn) {
        backBtn.addEventListener('click', () => {
            window.location.href = '/cleckbasket/includes/cart/shopping_cart.php';
        });
    }

    // ── Init ────────────────────────────────────────────────────────
    renderCards();
});
</script>

<?php include __DIR__ . '/../footer.php'; ?>
