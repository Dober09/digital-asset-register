<?php
// includes/functions.php

/** Escape output to prevent XSS */
function e($value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Redirect helper */
function redirect(string $path): void {
    header("Location: $path");
    exit;
}

/**
 * Generate a unique reference number, e.g. AST-2025-0001
 * Uses the DB to count this year's assets.
 */
function generateReferenceNumber(PDO $pdo): string {
    $year = date('Y');
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM assets WHERE YEAR(created_at) = ?"
    );
    $stmt->execute([$year]);
    $count = (int)$stmt->fetchColumn() + 1;
    return sprintf('AST-%s-%04d', $year, $count);
}

/** Basic required-field check */
function required(array $fields): array {
    $errors = [];
    foreach ($fields as $key => $label) {
        if (trim($_POST[$key] ?? '') === '') {
            $errors[] = "$label is required.";
        }
    }
    return $errors;
}