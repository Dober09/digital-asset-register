<?php
// api/search.php — GET
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/response.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_fail('Use GET.', 405);
}

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    json_fail('Query parameter "q" is required.', 422);
}

$like = "%$q%";
$stmt = $pdo->prepare(
    "SELECT a.`idAsset`, a.`Asset_Identifier`, a.`Asset_Descriptor`,
            u.`ID_num` AS owner_id, u.`Name`, u.`Surname`, u.`Email`
     FROM `DARS DB`.`Asset` a
     JOIN `DARS DB`.`User` u ON u.`ID_num` = a.`Owner`
     WHERE a.`Asset_Identifier` LIKE ?
        OR a.`Asset_Descriptor` LIKE ?
        OR u.`Name` LIKE ?
        OR u.`Surname` LIKE ?
     ORDER BY a.`idAsset` DESC
     LIMIT 50"
);
$stmt->execute([$like, $like, $like, $like]);
$rows = $stmt->fetchAll();

json_ok(['count' => count($rows), 'results' => $rows]);