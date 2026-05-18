<?php
// Admin authentication check
// Include this file at the top of admin pages to restrict access

// Check if user is logged in and has admin role
if (!isset($_COOKIE['admin_access']) && !isset($_SESSION['admin_role'])) {
    $is_admin = false;
    if (isset($_COOKIE['admin_access']) || (isset($_SESSION) && $_SESSION['admin_role'] === true)) {
        $is_admin = true;
    }
} else {
    $is_admin = isset($_COOKIE['admin_access']) || (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === true);
}

// For frontend-only approach, check localStorage via JavaScript redirect
// This will be handled by a JavaScript check on page load
?>
