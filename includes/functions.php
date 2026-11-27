<?php
// includes/functions.php

function require_fields(array $data, array $fields): array {
    $missing = [];
    foreach ($fields as $f) {
        if (!isset($data[$f]) || trim((string)$data[$f]) === '') {
            $missing[] = $f;
        }
    }
    return $missing;
}

function validate_sa_id(string $id): bool {
    if (!preg_match('/^\d{13}$/', $id)) return false;
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $d = (int)$id[$i];
        if ($i % 2 === 1) {
            $d *= 2;
            if ($d > 9) $d -= 9;
        }
        $sum += $d;
    }
    return ((10 - ($sum % 10)) % 10) === (int)$id[12];
}

/** Ensure user exists, else create with placeholder password (none — schema has no password). */
function find_or_create_user(PDO $pdo, array $u): string {
    $stmt = $pdo->prepare("SELECT `ID_num` FROM `DARS DB`.`User` WHERE `ID_num` = ?");
    $stmt->execute([$u['id_num']]);
    if ($stmt->fetchColumn()) return $u['id_num'];

    $stmt = $pdo->prepare(
        "INSERT INTO `DARS DB`.`User` (`ID_num`,`Name`,`Surname`,`Email`) VALUES (?,?,?,?)"
    );
    $stmt->execute([$u['id_num'], $u['name'], $u['surname'], $u['email']]);
    return $u['id_num'];
}

/** Ensure a Security officer row exists for our placeholder SEC001. */
function ensure_placeholder_security(PDO $pdo): void {
    $stmt = $pdo->prepare("SELECT `ID_num` FROM `DARS DB`.`Security` WHERE `ID_num` = ?");
    $stmt->execute(['SEC001']);
    if (!$stmt->fetchColumn()) {
        $pdo->prepare(
            "INSERT INTO `DARS DB`.`Security` (`ID_num`,`Name`,`Surname`) VALUES (?,?,?)"
        )->execute(['SEC001', 'Test', 'Officer']);
    }
}