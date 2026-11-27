<?php
require_once __DIR__ . '/../includes/auth.php';
// Prototype: session is auto-created by auth.php, so just bounce to dashboard.
redirect('/index.php');