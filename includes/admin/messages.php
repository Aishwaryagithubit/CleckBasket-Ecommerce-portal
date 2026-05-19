<?php
require_once 'admin_auth.php';

$success = '';
$error   = '';

// Mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_read') {
    $mid  = (int)$_POST['message_id'];
    $conn = getDBConnection();
    if ($conn) {
        $s = oci_parse($conn, "UPDATE contact_message SET is_read = 1 WHERE message_id = :id");
        oci_bind_by_name($s, ':id', $mid);
        if (oci_execute($s)) oci_commit($conn);
        oci_free_statement($s);
        oci_close($conn);
    }
}

// Delete message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $mid  = (int)$_POST['message_id'];
    $conn = getDBConnection();
    if ($conn) {
        $s = oci_parse($conn, "DELETE FROM contact_message WHERE message_id = :id");
        oci_bind_by_name($s, ':id', $mid);
        if (oci_execute($s)) {
            oci_commit($conn);
            $success = 'Message deleted.';
        }
        oci_free_statement($s);
        oci_close($conn);
    }
}

// Load messages
$messages   = [];
$unread_cnt = 0;
$conn = getDBConnection();
if ($conn) {
    $s = oci_parse($conn,
        "SELECT message_id, first_name, last_name, email, contact_number,
                subject, message AS msg_body, is_read,
                TO_CHAR(sent_date, 'DD Mon YYYY HH24:MI') AS sent_fmt
         FROM contact_message
         ORDER BY sent_date DESC");
    oci_execute($s);
    while ($row = oci_fetch_assoc($s)) {
        $messages[] = $row;
        if ((int)$row['IS_READ'] === 0) $unread_cnt++;
    }
    oci_free_statement($s);
    oci_close($conn);
}

$subject_labels = [
    'general'     => 'General Inquiry',
    'order'       => 'Order Issue',
    'partnership' => 'Partnership',
    'feedback'    => 'Feedback',
    'other'       => 'Other',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages — Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        .msg-table { width:100%; border-collapse:collapse; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.06); }
        .msg-table th { background:#f8f9fa; padding:14px 16px; text-align:left; font-size:13px; color:#666; font-weight:600; border-bottom:1px solid #eee; }
        .msg-table td { padding:14px 16px; font-size:14px; color:#333; border-bottom:1px solid #f0f0f0; vertical-align:top; }
        .msg-table tr:last-child td { border-bottom:none; }
        .msg-table tr.unread td { background:#fafff8; font-weight:600; }
        .msg-table tr.unread td:first-child { border-left:3px solid #4caf50; }

        .badge-unread { display:inline-block; background:#e53935; color:#fff; font-size:11px; font-weight:700; padding:2px 7px; border-radius:20px; margin-left:6px; }
        .badge-read   { display:inline-block; background:#e0e0e0; color:#666; font-size:11px; font-weight:600; padding:2px 7px; border-radius:20px; }

        .subject-chip { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; background:#e8f5e9; color:#2e7d32; }

        .msg-actions { display:flex; gap:8px; }
        .btn-sm { padding:5px 12px; border:none; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer; }
        .btn-read   { background:#e8f5e9; color:#2e7d32; }
        .btn-read:hover { background:#c8e6c9; }
        .btn-delete { background:#ffebee; color:#c62828; }
        .btn-delete:hover { background:#ffcdd2; }


        .toggle-btn { background:none; border:none; cursor:pointer; color:#1976d2; font-size:13px; padding:0; }
        .toggle-btn:hover { text-decoration:underline; }

        .empty-state { text-align:center; padding:60px 20px; color:#aaa; }
        .empty-state i { font-size:48px; margin-bottom:16px; display:block; }
    </style>
</head>
<body>
<div class="admin-container">
    <?php admin_sidebar('messages'); ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Contact Messages
                <?php if ($unread_cnt > 0): ?>
                    <span class="badge-unread"><?= $unread_cnt ?> new</span>
                <?php endif; ?>
            </h1>
            <div class="user-info"><span><?= h($_SESSION['admin_username']) ?></span></div>
        </header>

        <div class="content">
            <?php if ($success): ?>
                <div style="background:#e8f5e9;color:#2e7d32;padding:14px;border-radius:8px;margin-bottom:20px;"><?= h($success) ?></div>
            <?php endif; ?>

            <!-- Summary cards -->
            <section class="metrics" style="grid-template-columns:repeat(3,1fr);margin-bottom:28px;">
                <div class="metric-card">
                    <div class="metric-icon" style="background:#e3f2fd;color:#1565c0;"><i class="fas fa-envelope"></i></div>
                    <div class="metric-info"><span class="metric-value"><?= count($messages) ?></span><span class="metric-label">Total Messages</span></div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon" style="background:#fce4ec;color:#c62828;"><i class="fas fa-envelope-open"></i></div>
                    <div class="metric-info"><span class="metric-value"><?= $unread_cnt ?></span><span class="metric-label">Unread</span></div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="fas fa-check-circle"></i></div>
                    <div class="metric-info"><span class="metric-value"><?= count($messages) - $unread_cnt ?></span><span class="metric-label">Read</span></div>
                </div>
            </section>

            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No contact messages yet.</p>
                </div>
            <?php else: ?>
            <table class="msg-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $i => $msg): ?>
                    <?php $is_unread = (int)$msg['IS_READ'] === 0; ?>
                    <tr class="<?= $is_unread ? 'unread' : '' ?>" id="row-<?= $msg['MESSAGE_ID'] ?>">
                        <td>
                            <button class="toggle-btn" onclick="toggleBody(<?= $msg['MESSAGE_ID'] ?>)">
                                <?= h(trim($msg['FIRST_NAME'] . ' ' . $msg['LAST_NAME'])) ?>
                            </button>
                            <?php if ($msg['CONTACT_NUMBER']): ?>
                                <div style="font-size:12px;color:#999;margin-top:3px;"><?= h($msg['CONTACT_NUMBER']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= h($msg['EMAIL']) ?></td>
                        <td><span class="subject-chip"><?= h($subject_labels[$msg['SUBJECT']] ?? ucfirst($msg['SUBJECT'])) ?></span></td>
                        <td style="white-space:nowrap;"><?= h($msg['SENT_FMT']) ?></td>
                        <td>
                            <?php if ($is_unread): ?>
                                <span class="badge-unread">New</span>
                            <?php else: ?>
                                <span class="badge-read">Read</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="msg-actions">
                                <?php if ($is_unread): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="message_id" value="<?= (int)$msg['MESSAGE_ID'] ?>">
                                    <button type="submit" class="btn-sm btn-read"><i class="fas fa-check"></i> Mark Read</button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this message?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="message_id" value="<?= (int)$msg['MESSAGE_ID'] ?>">
                                    <button type="submit" class="btn-sm btn-delete"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr id="body-<?= $msg['MESSAGE_ID'] ?>" style="display:none;">
                        <td colspan="6" style="padding:16px 20px;background:#f9f9f9;font-size:14px;color:#333;line-height:1.7;border-bottom:1px solid #eee;">
                            <?= nl2br(h($msg['MSG_BODY'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
function toggleBody(id) {
    const bodyRow = document.getElementById('body-' + id);
    const isHidden = bodyRow.style.display === 'none' || bodyRow.style.display === '';
    bodyRow.style.display = isHidden ? 'table-row' : 'none';

    // Auto mark as read when opening
    if (isHidden) {
        const row = document.getElementById('row-' + id);
        if (row && row.classList.contains('unread')) {
            fetch('messages.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=mark_read&message_id=' + id
            }).then(() => {
                row.classList.remove('unread');
                const badge = row.querySelector('.badge-unread');
                if (badge) {
                    badge.className = 'badge-read';
                    badge.textContent = 'Read';
                }
            });
        }
    }
}
</script>
</body>
</html>
