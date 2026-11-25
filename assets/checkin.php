<?php
// assets/checkin.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
csrf_verify();

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = required([
        'asset_name'    => 'Asset name',
        'owner_name'    => 'Owner name',
        'owner_contact' => 'Owner contact',
    ]);

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $refNo = generateReferenceNumber($pdo);

            // Insert asset
            $stmt = $pdo->prepare(
                "INSERT INTO assets
                 (reference_no, name, category_id, serial_no, description,
                  owner_name, owner_contact, photo_path, status, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Inside', NOW())"
            );
            $stmt->execute([
                $refNo,
                trim($_POST['asset_name']),
                $_POST['category_id'] ?: null,
                trim($_POST['serial_no'] ?? ''),
                trim($_POST['description'] ?? ''),
                trim($_POST['owner_name']),
                trim($_POST['owner_contact']),
                null, // photo_path handled below
            ]);
            $assetId = (int)$pdo->lastInsertId();

            // Log movement
            $stmt = $pdo->prepare(
                "INSERT INTO movements (asset_id, type, officer_id, timestamp, remarks)
                 VALUES (?, 'IN', ?, NOW(), ?)"
            );
            $stmt->execute([$assetId, $_SESSION['user_id'], trim($_POST['remarks'] ?? '')]);

            // (Optional) handle photo upload here later
            // ...

            // Audit log
            $stmt = $pdo->prepare(
                "INSERT INTO audit_logs (user_id, action, entity, entity_id, timestamp)
                 VALUES (?, 'CHECK_IN', 'asset', ?, NOW())"
            );
            $stmt->execute([$_SESSION['user_id'], $assetId]);

            $pdo->commit();
            $success = "Asset checked in successfully. Reference: $refNo";
        } catch (Exception $ex) {
            $pdo->rollBack();
            $errors[] = 'Could not save asset. ' . $ex->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Check-In Asset</title>
</head>
<body>
  <h1>Check-In Asset</h1>
  <p><a href="/index.php">← Back to dashboard</a></p>

  <?php if ($success): ?>
    <p style="color:green"><strong><?= e($success) ?></strong></p>
  <?php endif; ?>

  <?php if ($errors): ?>
    <ul style="color:red">
      <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post" action="" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <label>Asset name *<br><input type="text" name="asset_name" required></label><br><br>
    <label>Serial number<br><input type="text" name="serial_no"></label><br><br>
    <label>Description<br><textarea name="description"></textarea></label><br><br>
    <label>Owner name *<br><input type="text" name="owner_name" required></label><br><br>
    <label>Owner contact *<br><input type="tel" name="owner_contact" required></label><br><br>
    <label>Remarks<br><input type="text" name="remarks"></label><br><br>

    <!-- Photo capture hint for smartphone -->
    <label>Photo (optional)<br>
      <input type="file" name="photo" accept="image/*" capture="environment">
    </label><br><br>

    <button type="submit">Check In</button>
  </form>
</body>
</html>