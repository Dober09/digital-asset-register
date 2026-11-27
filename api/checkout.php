<?php
// api/checkout.php — POST
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Use POST.', 405);
}

$in = input();
if (empty($in['asset_id']) && empty($in['asset_identifier'])) {
    json_fail('Provide asset_id or asset_identifier.', 422);
}

try {
    ensure_placeholder_security($pdo);
    $pdo->beginTransaction();

    // Resolve asset
    if (!empty($in['asset_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM `DARS DB`.`Asset` WHERE `idAsset` = ?");
        $stmt->execute([(int)$in['asset_id']]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM `DARS DB`.`Asset` WHERE `Asset_Identifier` = ?");
        $stmt->execute([$in['asset_identifier']]);
    }
    $asset = $stmt->fetch();
    if (!$asset) json_fail('Asset not found.', 404);

    // Find latest "open" log (TimeOut sentinel 00:00:00 = still inside)
    $stmt = $pdo->prepare(
        "SELECT `LogID` FROM `DARS DB`.`Asset_Control`
         WHERE `Asset` = ? AND `TimeOut` = '00:00:00'
         ORDER BY `LogID` DESC LIMIT 1"
    );
    $stmt->execute([$asset['idAsset']]);
    $logId = $stmt->fetchColumn();
    if (!$logId) json_fail('Asset is not currently checked in.', 409);

    // Close the log row
    $stmt = $pdo->prepare(
        "UPDATE `DARS DB`.`Asset_Control`
         SET `TimeOut` = CURTIME(), `Security` = ?
         WHERE `LogID` = ?"
    );
    $stmt->execute([current_security_id(), $logId]);

    $pdo->commit();

    json_ok([
        'message'    => 'Asset checked out.',
        'asset_id'   => (int)$asset['idAsset'],
        'log_id'     => (int)$logId,
        'logged_by'  => current_security_id(),
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_fail('Database error: ' . $e->getMessage(), 500);
}