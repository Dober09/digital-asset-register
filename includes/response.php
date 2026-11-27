<?php
// includes/response.php

function json_ok(array $data = [], int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true] + $data, JSON_PRETTY_PRINT);
    exit;
}

function json_fail(string $message, int $code = 400, array $extra = []): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => $message] + $extra, JSON_PRETTY_PRINT);
    exit;
}

/** Accept both JSON body and normal form-encoded POST. */
function input(): array {
    if (!empty($_POST)) return $_POST;
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}