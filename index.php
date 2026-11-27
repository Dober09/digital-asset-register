<?php
// index.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (!is_logged_in()) {
    redirect('/auth/login.php');
}

$totalAssets = (int)$pdo->query(
    "SELECT COUNT(*) FROM `DARS DB`.`Asset`"
)->fetchColumn();

$inside = (int)$pdo->query(
    "SELECT COUNT(*) FROM `DARS DB`.`Asset_Control` WHERE `TimeOut` = '00:00:00'"
)->fetchColumn();

$totalLogs = (int)$pdo->query(
    "SELECT COUNT(*) FROM `DARS DB`.`Asset_Control`"
)->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Digital Asset Register</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
    .stat { display:inline-block; padding:1rem 1.5rem; margin:.25rem; background:#f4f4f4; border-radius:8px; }
    .stat strong { font-size:1.6rem; display:block; }
  </style>
</head>
<body>
  <h1>Welcome, <?= e($_SESSION['name'] ?? 'User') ?></h1>
  <p>Role: <?= e($_SESSION['role'] ?? 'security') ?></p>

  <h2>At a glance</h2>
  <div class="stat"><strong><?= $totalAssets ?></strong>Total assets</div>
  <div class="stat"><strong><?= $inside ?></strong>Currently inside</div>
  <div class="stat"><strong><?= $totalLogs ?></strong>Movement logs</div>

  <h2>Quick actions</h2>
  <ul>
    <li><a href="/assets/checkin.php">Check-In Asset</a></li>
    <li><a href="/assets/checkout.php">Check-Out Asset</a></li>
    <li><a href="/assets/search.php">Search Assets</a></li>
    <li><a href="/assets/transfer.php">Transfer Ownership</a></li>
    <li><a href="/assets/log.php">View Movement Log</a></li>
  </ul>

  <p><a href="/auth/logout.php">Logout</a></p>
</body>
</html>