<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/Transaction.php';

if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 0.0;
}
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}
if (!isset($_SESSION['next_id'])) {
    $_SESSION['next_id'] = 1;
}

$balance = (float) $_SESSION['balance'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Manajemen Keuangan Sederhana</title>
</head>
<body>
    <h1>Sistem Manajemen Keuangan Sederhana</h1>
    <p>Saldo saat ini: Rp <?= htmlspecialchars(number_format($balance, 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?></p>
</body>
</html>