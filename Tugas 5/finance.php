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
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
        die('Kesalahan Keamanan: Token CSRF tidak cocok.');
    }

    $rawType   = $_POST['type'] ?? '';
    $rawAmount = $_POST['amount'] ?? '';

    $type = match ($rawType) {
        'deposit', 'penarikan' => $rawType,
        default => null,
    };

    if ($type === null) {
        $errors[] = 'Jenis transaksi tidak valid.';
    }

    $amount = null;
    if (!is_string($rawAmount) || $rawAmount === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $rawAmount)) {
        $errors[] = 'Jumlah transaksi harus berupa angka desimal positif (contoh: 150000 atau 150000.50).';
    } else {
        $amount = (float) $rawAmount;
        if ($amount <= 0) {
            $errors[] = 'Jumlah transaksi harus lebih besar dari nol.';
        }
    }

    if (empty($errors)) {
        try {
            $transaction = new Transaction((int) $_SESSION['next_id'], $type, $amount);
            $newBalance  = $transaction->process((float) $_SESSION['balance']);

            $_SESSION['balance'] = $newBalance;
            $_SESSION['history'][] = $transaction->toArray();
            $_SESSION['next_id']++;

            $successMessage = 'Transaksi berhasil diproses.';

            // Regenerasi token CSRF setelah sukses untuk keamanan tambahan
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (RuntimeException | InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$csrfToken = htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
$balance   = (float) $_SESSION['balance'];
$history   = $_SESSION['history'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Manajemen Keuangan Sederhana</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 640px; margin: 40px auto; padding: 0 16px; color: #222; }
        h1 { font-size: 1.4rem; }
        .balance { font-size: 1.1rem; margin: 16px 0; }
        .error, .success { list-style: none; padding: 10px 14px; border-radius: 6px; margin: 10px 0; }
        .error { background: #fdecea; color: #b00020; }
        .success { background: #e8f5e9; color: #1b5e20; }
        form { border: 1px solid #ddd; padding: 16px; border-radius: 8px; margin-bottom: 24px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        select, input[type=text] { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; }
        button { margin-top: 16px; padding: 10px 18px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Sistem Manajemen Keuangan Sederhana</h1>

    <p class="balance">
        Saldo saat ini:
        <strong>Rp <?= htmlspecialchars(number_format($balance, 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?></strong>
    </p>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($successMessage !== ''): ?>
        <p class="success"><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <label for="type">Jenis Transaksi</label>
        <select name="type" id="type" required>
            <option value="deposit">Deposit</option>
            <option value="penarikan">Penarikan</option>
        </select>

        <label for="amount">Jumlah (Rp)</label>
        <input type="text" name="amount" id="amount" placeholder="Contoh: 150000.00" required>

        <button type="submit">Proses Transaksi</button>
    </form>

    <h2>Riwayat Transaksi</h2>
    <?php if (empty($history)): ?>
        <p>Belum ada transaksi.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis</th>
                    <th>Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_reverse($history) as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $item['id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $item['type'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(number_format((float) $item['amount'], 2, ',', '.'), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>