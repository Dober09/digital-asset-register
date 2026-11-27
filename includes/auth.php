<?php
// includes/auth.php
// PROTOTYPE ONLY: fake session so APIs/UI work without a login system.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-populate a prototype session so the UI has a "logged in" user.
if (!isset($_SESSION['security_id'])) {
    $_SESSION['security_id'] = 'SEC001';
    $_SESSION['name']        = 'Test Officer';
    $_SESSION['surname']     = 'Officer';
    $_SESSION['role']        = 'security';   // 'security' | 'admin'
}

function current_security_id(): string {
    return $_SESSION['security_id'] ?? 'SEC001';
}

function current_user_id(): string {
    // Placeholder valid 13-digit SA ID (8001015009087 passes Luhn).
    return '8001015009087';
}

function is_logged_in(): bool {
    return isset($_SESSION['security_id']);
}

function redirect(string $to): void {
    header("Location: $to");
    exit;
}

function e($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}