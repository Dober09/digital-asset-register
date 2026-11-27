<?php
// api/checkin.php — POST
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Use POST.', 405);
}

$in = input();
$missing = require_fields($in, [
    'id_num', 'name', 'surname', 'email',
    'asset_identifier', 'asset_descriptor'
]);
if ($missing) {
    json_fail('Missing required fields.', 422, ['missing' => $missing]);
}

if (!validate_sa_id($in['id_num'])) {
    json_fail('Owner id_num is not a valid 13-digit SA ID.', 422);
}

try {
    ensure_placeholder_security($pdo);
    $pdo->beginTransaction();

    // Does this asset already exist?
    $stmt = $pdo->prepare("SELECT `idAsset`,`Owner` FROM `DARS DB`.`Asset` WHERE `Asset_Identifier` = ?");
    $stmt->execute([$in['asset_identifier']]);
    $existing = $stmt->fetch();

    if ($existing) {
        $assetId = (int)$existing['idAsset'];
        $ownerId = $existing['Owner'];

        // Refuse if it's already inside (open log row)
        $stmt = $pdo->prepare(
            "SELECT 1 FROM `DARS DB`.`Asset_Control`
             WHERE `Asset` = ? AND `TimeOut` = '00:00:00' LIMIT 1"
        );
        $stmt->execute([$assetId]);
        if ($stmt->fetchColumn()) {
            $pdo->rollBack();
            json_fail('Asset is already checked in.', 409);
        }
    } else {
        // First time this identifier is seen → create the Asset row
        $ownerId = find_or_create_user($pdo, $in);
        $stmt = $pdo->prepare(
            "INSERT INTO `DARS DB`.`Asset` (`Owner`,`Asset_Identifier`,`Asset_Descriptor`)
             VALUES (?,?,?)"
        );
        $stmt->execute([$ownerId, $in['asset_identifier'], $in['asset_descriptor']]);
        $assetId = (int)$pdo->lastInsertId();
    }

    // Open a new movement log. TimeOut = '00:00:00' sentinel = still inside.
    $stmt = $pdo->prepare(
        "INSERT INTO `DARS DB`.`Asset_Control`
         (`Date`,`TimeOut`,`TimeIn`,`Asset`,`Security`)
         VALUES (CURDATE(), '00:00:00', CURTIME(), ?, ?)"
    );
    $stmt->execute([$assetId, current_security_id()]);

    $pdo->commit();

    json_ok([
        'message'    => 'Asset checked in.',
        'asset_id'   => $assetId,
        'identifier' => $in['asset_identifier'],
        'owner_id'   => $ownerId,
        'logged_by'  => current_security_id(),
    ], 201);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if ($e->getCode() === '23000') {
        json_fail('Asset identifier already exists.', 409);
    }
    json_fail('Database error: ' . $e->getMessage(), 500);
}