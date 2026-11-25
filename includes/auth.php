<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) session_start();

/** Require the user to be logged in */
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /auth/login.php');
        exit;
    }
}

/** Require a specific role (e.g. 'admin') */
function require_role(string $role): void {
    require_login();
    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        die('Access denied.');
    }
}

/** Is the current user logged in? */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}