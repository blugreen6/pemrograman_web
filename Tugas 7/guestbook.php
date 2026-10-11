<?php
declare(strict_types=1);

require_once __DIR__ . '/src/GuestBook.php';

/* Koneksi PDO */
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';   // ganti ke '3307' bila port MySQL XAMPP dipindah
$db   = getenv('DB_NAME') ?: 'perpustakaan_db';
$user = getenv('DB_USER') ?: 'root';   // produksi: pakai akun khusus (lihat schema.sql)
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('Database Connection Error: ' . $e->getMessage());
    http_response_code(500);
    die('Sistem mengalami kegagalan teknis.');
}

$guestBook = new GuestBook($pdo);

/* Proses form */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guestBook->save(
        (string) ($_POST['nama']  ?? ''),
        (string) ($_POST['email'] ?? ''),
        (string) ($_POST['pesan'] ?? '')
    );

    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

/* Ambil data untuk ditampilkan */
$messages = $guestBook->getAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Tamu Perpustakaan</title>
</head>
<body>
    <h1>Buku Tamu Perpustakaan</h1>

    <form method="post" action="" novalidate>
        <p>
            <label for="nama">Nama</label><br>
            <input type="text" id="nama" name="nama" maxlength="100">
        </p>
        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" maxlength="100">
        </p>
        <p>
            <label for="pesan">Pesan</label><br>
            <textarea id="pesan" name="pesan" rows="4"></textarea>
        </p>
        <button type="submit">Kirim Pesan</button>
    </form>

    <h2>Daftar Pesan</h2>
    <table border="1" cellpadding="6">
        <thead>
            <tr><th>No</th><th>Nama</th><th>Email</th><th>Pesan</th><th>Tanggal Kirim</th></tr>
        </thead>
        <tbody>
        <?php if (!$messages): ?>
            <tr><td colspan="5">Belum ada pesan.</td></tr>
        <?php else: ?>
            <?php foreach ($messages as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['pesan'] ?></td>
                    <td><?= $row['tanggal_kirim'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>