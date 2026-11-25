<?php
// index.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (!is_logged_in()) {
    redirect('/auth/login.php');
}

$today = date('Y-m-d');

$inside = $pdo->query("SELECT COUNT(*) FROM assets WHERE status = 'Inside'")->fetchColumn();
$outToday = $pdo->prepare("SELECT COUNT(*) FROM movements WHERE type='OUT' AND DATE(timestamp)=?");
$outToday->execute([$today]);
$outToday = $outToday->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Digital Asset Register</title>
</head>
<body>
  <h1>Welcome, <?= e($_SESSION['name'] ?? 'User') ?></h1>
  <p>Role: <?= e($_SESSION['role']) ?></p>

  <ul>
    <li>Assets currently inside: <strong><?= (int)$inside ?></strong></li>
    <li>Checked out today: <strong><?= (int)$outToday ?></strong></li>
  </ul>

  <h2>Quick actions</h2>
  <ul>
    <li><a href="/assets/checkin.php">Check-In Asset</a></li>
    <li><a href="/assets/checkout.php">Check-Out Asset</a></li>
    <li><a href="/assets/search.php">Search Assets</a></li>
    <?php if ($_SESSION['role'] === 'admin'): ?>
      <li><a href="/admin/users.php">Manage Users</a></li>
    <?php endif; ?>
  </ul>

  <p><a href="/auth/logout.php">Logout</a></p>
</body>
</html>