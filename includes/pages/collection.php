<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../backend/connect.php';

$paypalURL    = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
$paypalID     = 'sb-s43oyo30555884@business.example.com';
$scheme       = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host         = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl      = $scheme . '://' . $host . '/cleckbasket';
$paypalReturn = $baseUrl . '/checkout_success.php';
$paypalCancel = $baseUrl . '/checkout_cancel.php';

const SLOT_CAPACITY = 20;
$slotsByDate = [];
$dateKeys    = [];

// Auto-insert collection slots for the next 3 days if they don't already exist.
// Runs on every page load — safe to call repeatedly (checks before inserting).
function ensureUpcomingSlots($conn) {
    $slotTimes = ['10:00 - 13:00', '13:00 - 16:00', '16:00 - 19:00'];

    for ($day = 1; $day <= 3; $day++) {
        $bind_day = $day;

        // Count slots already in DB for this date offset
        $chk = oci_parse($conn,
            "SELECT COUNT(*) AS cnt FROM collection_slot
             WHERE TRUNC(slot_date) = TRUNC(SYSDATE) + :day_offset");
        oci_bind_by_name($chk, ':day_offset', $bind_day);
        oci_execute($chk);
        $row = oci_fetch_assoc($chk);
        oci_free_statement($chk);

        if ((int)($row['CNT'] ?? 0) > 0) continue; // already exists

        foreach ($slotTimes as $time) {
            $bind_off1 = $day;
            $bind_off2 = $day;
            $bind_time = $time;

            $ins = oci_parse($conn,
                "INSERT INTO collection_slot (slot_day, slot_date, slot_time)
                 VALUES (
                     RTRIM(TO_CHAR(TRUNC(SYSDATE) + :day_off1, 'Day')),
                     TRUNC(SYSDATE) + :day_off2,
                     :slot_time
                 )");
            oci_bind_by_name($ins, ':day_off1', $bind_off1);
            oci_bind_by_name($ins, ':day_off2', $bind_off2);
            oci_bind_by_name($ins, ':slot_time', $bind_time);
            oci_execute($ins);
            oci_free_statement($ins);
        }
    }
    oci_commit($conn);
}

$conn = getDBConnection();
if ($conn) {
    ensureUpcomingSlots($conn);

    $sql = "SELECT cs.collection_slot_id, cs.slot_day, cs.slot_time,
                   TO_CHAR(cs.slot_date, 'YYYY-MM-DD') AS date_key,
                   NVL(COUNT(o.order_id), 0) AS booked_count
            FROM collection_slot cs
            LEFT JOIN orders o ON cs.collection_slot_id = o.collection_slot_id
            WHERE cs.slot_date >= TRUNC(SYSDATE)
            GROUP BY cs.collection_slot_id, cs.slot_day, cs.slot_time, cs.slot_date
            ORDER BY cs.slot_date, cs.slot_time";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $todayObj    = (new DateTime())->setTime(0, 0, 0);
    $tomorrowObj = (clone $todayObj)->modify('+1 day');
    $now         = new DateTime();

    while ($row = oci_fetch_assoc($stmt)) {
        $dk      = $row['DATE_KEY'];
        $dateObj = (new DateTime($dk))->setTime(0, 0, 0);

        if (!isset($slotsByDate[$dk])) {
            if ($dateObj == $todayObj) {
                $lbl = 'Today, ' . (new DateTime($dk))->format('j M');
            } elseif ($dateObj == $tomorrowObj) {
                $lbl = 'Tomorrow, ' . (new DateTime($dk))->format('j M');
            } else {
                $lbl = trim($row['SLOT_DAY']) . ', ' . (new DateTime($dk))->format('j M');
            }
            $slotsByDate[$dk] = ['label' => $lbl, 'slots' => []];
            $dateKeys[] = $dk;
        }

        $startStr    = explode(' - ', $row['SLOT_TIME'])[0];
        $slotStart   = new DateTime($dk . ' ' . $startStr);
        $secondsAway = $slotStart->getTimestamp() - $now->getTimestamp();
        $isClosed    = $secondsAway < 86400; // disable if starts within 24 hours

        $booked    = (int)$row['BOOKED_COUNT'];
        $left      = max(0, SLOT_CAPACITY - $booked);
        $isFull    = $left === 0;

        $startHour = (int)explode(':', $startStr)[0];
        $slotLabel = $startHour < 12 ? 'MORNING' : ($startHour < 16 ? 'AFTERNOON' : 'EVENING');

        $slotsByDate[$dk]['slots'][] = [
            'id'     => (int)$row['COLLECTION_SLOT_ID'],
            'label'  => $slotLabel,
            'time'   => $row['SLOT_TIME'],
            'left'   => $left,
            'booked' => $booked,
            'total'  => SLOT_CAPACITY,
            'urgent' => !$isClosed && !$isFull && $left <= 3,
            'closed' => $isClosed,
            'full'   => $isFull,
        ];
    }
    oci_free_statement($stmt);
    oci_close($conn);
}
?>
<?php include __DIR__ . '/../header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/cleckbasket/assets/css/collection.css">

<main class="collection-page">
    <div class="collection-container">

        <header class="collection-header">
            <h1 class="page-title">Select Collection Slot</h1>
            <p class="page-subtitle">Please choose a preferred time to pick up your CleckBasket order.</p>
        </header>

        <?php if (empty($dateKeys)): ?>
            <div style="padding:2rem;text-align:center;color:#888;">No upcoming collection slots available.</div>
        <?php else: ?>

        <!-- Date tabs rendered from DB -->
        <section class="date-tabs" id="date-tabs">
            <?php foreach ($dateKeys as $i => $dk): ?>
                <button class="date-tab<?= $i === 0 ? ' active' : '' ?>"
                        data-date="<?= htmlspecialchars($dk) ?>">
                    <?= htmlspecialchars($slotsByDate[$dk]['label']) ?>
                </button>
            <?php endforeach; ?>
        </section>

        <!-- Slot cards — filled by JS -->
        <section class="slot-cards" id="slot-cards"></section>

        <!-- Availability table — filled by JS -->
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
                    <tbody id="avail-table-body"></tbody>
                </table>
            </div>
        </section>

        <?php endif; ?>
    </div>

    <!-- Hidden PayPal form — submitted by JS on confirm -->
    <form id="paypalForm" action="<?= htmlspecialchars($paypalURL) ?>" method="post" style="display:none;">
        <input type="hidden" name="business"      value="<?= htmlspecialchars($paypalID) ?>">
        <input type="hidden" name="cmd"           value="_xclick">
        <input type="hidden" name="item_name"     value="CleckBasket Order">
        <input type="hidden" name="currency_code" value="GBP">
        <input type="hidden" name="quantity"      value="1">
        <input type="hidden" name="amount"        value="0.00">
        <input type="hidden" name="return"        value="<?= htmlspecialchars($paypalReturn) ?>">
        <input type="hidden" name="cancel_return" value="<?= htmlspecialchars($paypalCancel) ?>">
    </form>

    <!-- Confirmation bar -->
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
                    <span class="selection-value" id="selection-value">—</span>
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
const slotsByDate = <?= json_encode($slotsByDate, JSON_UNESCAPED_UNICODE) ?>;
const dateKeys    = <?= json_encode($dateKeys) ?>;

let selectedDate     = dateKeys[0] || null;
let selectedSlotIdx  = -1;

function firstOpenIdx(dk) {
    if (!dk || !slotsByDate[dk]) return -1;
    const idx = slotsByDate[dk].slots.findIndex(s => !s.closed && !s.full);
    return idx === -1 ? 0 : idx;
}

if (selectedDate) selectedSlotIdx = firstOpenIdx(selectedDate);

// ── Render slot cards ─────────────────────────────────────────────
function renderCards() {
    const container = document.getElementById('slot-cards');
    if (!container || !selectedDate) return;
    const slots = slotsByDate[selectedDate].slots;
    container.innerHTML = '';

    slots.forEach((slot, i) => {
        const unavailable = slot.closed || slot.full;
        const isSel       = i === selectedSlotIdx && !unavailable;
        // Progress bar always reflects real bookings, not forced 0/100
        const fillPct     = Math.round((slot.booked / slot.total) * 100);

        const urgentBadge = slot.urgent
            ? `<span class="status-badge urgent">
                <svg width="8" height="10" viewBox="0 0 8 10" fill="none">
                  <path d="M4 0C1.79 0 0 1.79 0 4C0 6.21 1.79 8 4 8C6.21 8 8 6.21 8 4C8 1.79 6.21 0 4 0ZM4.5 6H3.5V5H4.5V6ZM4.5 4H3.5V2H4.5V4Z" fill="#93000A"/>
                </svg>ALMOST FULL</span>`
            : '';

        // Distinct badges: CLOSED = within 24 h, FULL = 20/20 booked
        const statusBadge = slot.closed ? `<span class="status-badge urgent">CLOSED</span>`
                          : slot.full   ? `<span class="status-badge urgent">FULL</span>`
                          : '';

        let availText, availCount;
        if (slot.closed) {
            availText  = 'Closes within 24 hrs';
            availCount = 'Not available';
        } else if (slot.full) {
            availText  = 'No spots remaining';
            availCount = `0 / ${slot.total} left`;
        } else {
            availText  = slot.urgent ? 'Filling up fast!' : 'Spots available';
            availCount = `${slot.left} / ${slot.total} left`;
        }

        const card = document.createElement('div');
        card.className = 'slot-card' + (isSel ? ' selected' : '') + (unavailable ? ' full' : '');
        card.style.cursor  = unavailable ? 'not-allowed' : 'pointer';
        card.style.opacity = unavailable ? '0.45' : '1';
        card.dataset.idx   = i;

        card.innerHTML = `
            ${isSel ? '<div class="selected-badge">SELECTED</div>' : ''}
            <div class="card-content">
                <div class="card-top">
                    <div class="label-row">
                        <span class="slot-label">${slot.label}</span>
                        ${urgentBadge}${statusBadge}
                    </div>
                    <h3 class="slot-time">${slot.time}</h3>
                </div>
                <div class="card-bottom">
                    <div class="slot-availability">
                        <span class="availability-text">${availText}</span>
                        <span class="availability-count">${availCount}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill ${slot.urgent || slot.full ? 'urgent' : ''}" style="width:${fillPct}%"></div>
                    </div>
                </div>
            </div>`;

        card.addEventListener('click', () => {
            if (unavailable) return;
            selectedSlotIdx = i;
            renderCards();
            renderTable();
            updateBar();
        });

        container.appendChild(card);
    });

    updateBar();
}

// ── Render availability table ─────────────────────────────────────
function renderTable() {
    const tbody = document.getElementById('avail-table-body');
    if (!tbody || !selectedDate) return;
    const slots = slotsByDate[selectedDate].slots;
    tbody.innerHTML = '';

    slots.forEach(slot => {
        const fillPct     = slot.booked / slot.total;
        const demand      = fillPct >= 0.85 ? 'Very High' : fillPct >= 0.6 ? 'High' : 'Moderate';
        const statusClass = slot.closed ? 'status-pill closed'
                          : slot.full   ? 'status-pill fast'
                          : slot.urgent ? 'status-pill fast'
                          : 'status-pill available';
        const statusText  = slot.closed ? 'CLOSED'
                          : slot.full   ? 'FULL'
                          : slot.urgent ? 'FAST'
                          : 'AVAILABLE';
        tbody.innerHTML += `
            <tr>
                <td class="col-time">${slot.time}</td>
                <td class="col-avail">${slot.left} / ${slot.total} left</td>
                <td class="col-demand">${demand}</td>
                <td class="col-status"><span class="${statusClass}">${statusText}</span></td>
            </tr>`;
    });
}

// ── Update confirmation bar ───────────────────────────────────────
function updateBar() {
    const el = document.getElementById('selection-value');
    if (!el || !selectedDate) return;
    const slots = slotsByDate[selectedDate] ? slotsByDate[selectedDate].slots : [];
    const slot  = slots[selectedSlotIdx];
    if (slot && !slot.closed && !slot.full) {
        el.textContent = slotsByDate[selectedDate].label + ' • ' + slot.time + ' (' + slot.left + ' / ' + slot.total + ' left)';
    } else {
        el.textContent = 'No slot selected';
    }
}

// ── Date tab clicks ───────────────────────────────────────────────
document.querySelectorAll('.date-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const dk = tab.dataset.date;
        if (!dk) return;
        selectedDate    = dk;
        selectedSlotIdx = firstOpenIdx(dk);
        document.querySelectorAll('.date-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        renderCards();
        renderTable();
        updateBar();
    });
});

// ── Confirm / Back ────────────────────────────────────────────────
document.querySelector('.btn-confirm')?.addEventListener('click', async () => {
    const slots = slotsByDate[selectedDate]?.slots || [];
    const slot  = slots[selectedSlotIdx];
    if (!slot || slot.closed || slot.full) {
        alert('Please select an available slot first.');
        return;
    }
    localStorage.setItem('selected_slot_id',    slot.id);
    localStorage.setItem('selected_slot_label', slotsByDate[selectedDate].label + ' • ' + slot.time);

    const cartItems = JSON.parse(localStorage.getItem('invoice_items') || localStorage.getItem('cart') || '[]');
    const subtotal  = cartItems.reduce((sum, item) => sum + (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1), 0);
    // FIXED: apply coupon discount before tax when sending amount to PayPal
    const couponPct = parseInt(localStorage.getItem('coupon_percent') || '0', 10);
    const discount  = couponPct > 0 ? parseFloat((subtotal * couponPct / 100).toFixed(2)) : 0;
    const total     = ((subtotal - discount) * 1.05).toFixed(2);

    // Save order to DB before redirecting to PayPal
    try {
        await fetch('/cleckbasket/backend/place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ slot_id: slot.id, cart_items: cartItems })
        });
    } catch (_) { /* non-fatal: order may still save */ }

    const form = document.getElementById('paypalForm');
    form.querySelector('[name=amount]').value    = total > 0 ? total : '1.00';
    form.querySelector('[name=item_name]').value = 'CleckBasket – ' + slotsByDate[selectedDate].label + ' ' + slot.time;
    form.submit();
});

document.querySelector('.btn-back')?.addEventListener('click', () => {
    window.location.href = '/cleckbasket/includes/cart/shopping_cart.php';
});

// ── Init ─────────────────────────────────────────────────────────
if (selectedDate) {
    renderCards();
    renderTable();
    updateBar();
}
</script>

<?php include __DIR__ . '/../footer.php'; ?>