<?php
// api/transfer.php — POST
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Use POST.', 405);
}

$in = input();
$missing = require_fields($in, [
    'asset_id', 'new_owner_id', 'new_owner_name',
    'new_owner_surname', 'new_owner_email'
]);
if ($missing) json_fail('Missing fields.', 422, ['missing' => $missing]);

if (!validate_sa_id($in['new_owner_id'])) {
    json_fail('new_owner_id is not a valid 13-digit SA ID.', 422);
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT `Owner` FROM `DARS DB`.`Asset` WHERE `idAsset` = ?");
    $stmt->execute([(int)$in['asset_id']]);
    $originalOwner = $stmt->fetchColumn();
    if (!$originalOwner) json_fail('Asset not found.', 404);

    if ($originalOwner === $in['new_owner_id']) {
        json_fail('New owner is the same as current owner.', 409);
    }

    // Ensure new owner exists
    find_or_create_user($pdo, [
        'id_num'  => $in['new_owner_id'],
        'name'    => $in['new_owner_name'],
        'surname' => $in['new_owner_surname'],
        'email'   => $in['new_owner_email'],
    ]);

    // Update asset owner
    $pdo->prepare("UPDATE `DARS DB`.`Asset` SET `Owner` = ? WHERE `idAsset` = ?")
        ->execute([$in['new_owner_id'], (int)$in['asset_id']]);

    // Log transfer
    $pdo->prepare(
        "INSERT INTO `DARS DB`.`Asset_Transfer`
         (`Date`,`Original_Owner`,`New_Owner`) VALUES (NOW(),?,?)"
    )->execute([$originalOwner, $in['new_owner_id']]);

    $pdo->commit();
    json_ok([
        'message' => 'Ownership transferred.',
        'asset_id' => (int)$in['asset_id'],
        'from' => $originalOwner,
        'to' => $in['new_owner_id'],
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_fail('Database error: ' . $e->getMessage(), 500);
}