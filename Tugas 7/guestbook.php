<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/src/GuestBook.php';

/* Koneksi PDO */
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';  
$db   = getenv('DB_NAME') ?: 'perpustakaan_db';
$user = getenv('DB_USER') ?: 'root';  
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

/* Helper sanitasi keluaran */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Token CSRF */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$old    = ['nama' => '', 'email' => '', 'pesan' => ''];
$flash  = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

/* Proses form */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        $errors['umum'] = 'Token CSRF tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $old = [
            'nama'  => (string) ($_POST['nama']  ?? ''),
            'email' => (string) ($_POST['email'] ?? ''),
            'pesan' => (string) ($_POST['pesan'] ?? ''),
        ];

        $errors = $guestBook->validate($old['nama'], $old['email'], $old['pesan']);

        if (!$errors) {
            try {
                $guestBook->save($old['nama'], $old['email'], $old['pesan']);

                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['flash'] = 'Terima kasih, pesan Anda berhasil dikirim.';
                header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
                exit;
            } catch (PDOException $e) {
                error_log('Guestbook save error: ' . $e->getMessage());
                $errors['umum'] = 'Pesan gagal disimpan. Silakan coba lagi nanti.';
            }
        }
    }
}

/* Ambil data untuk ditampilkan */
try {
    $messages = $guestBook->getAll();
} catch (PDOException $e) {
    error_log('Guestbook fetch error: ' . $e->getMessage());
    $messages = [];
    $errors['umum'] = $errors['umum'] ?? 'Daftar pesan tidak dapat dimuat.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Tamu Perpustakaan</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 860px; margin: 2rem auto; padding: 0 1rem; color: #222; }
        h1, h2 { margin-bottom: .5rem; }
        form { display: grid; gap: .9rem; margin-bottom: 2rem; }
        label { font-weight: 600; display: block; margin-bottom: .25rem; }
        input, textarea { width: 100%; padding: .55rem; border: 1px solid #bbb; border-radius: 6px; box-sizing: border-box; font: inherit; }
        button { padding: .65rem 1.2rem; border: 0; border-radius: 6px; background: #1f6feb; color: #fff; font: inherit; cursor: pointer; justify-self: start; }
        .error { color: #b42318; font-size: .9rem; margin-top: .2rem; }
        .alert { padding: .7rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert.ok  { background: #e6f4ea; color: #1e6b34; }
        .alert.err { background: #fdecea; color: #b42318; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: .55rem; text-align: left; vertical-align: top; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Buku Tamu Perpustakaan</h1>

    <?php if ($flash): ?>
        <div class="alert ok"><?= e($flash) ?></div>
    <?php endif; ?>
    <?php if (isset($errors['umum'])): ?>
        <div class="alert err"><?= e($errors['umum']) ?></div>
    <?php endif; ?>

    <form method="post" action="" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

        <div>
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" value="<?= e($old['nama']) ?>" maxlength="100">
            <?php if (isset($errors['nama'])): ?><div class="error"><?= e($errors['nama']) ?></div><?php endif; ?>
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" maxlength="100">
            <?php if (isset($errors['email'])): ?><div class="error"><?= e($errors['email']) ?></div><?php endif; ?>
        </div>

        <div>
            <label for="pesan">Pesan</label>
            <textarea id="pesan" name="pesan" rows="4"><?= e($old['pesan']) ?></textarea>
            <?php if (isset($errors['pesan'])): ?><div class="error"><?= e($errors['pesan']) ?></div><?php endif; ?>
        </div>

        <button type="submit">Kirim Pesan</button>
    </form>

    <h2>Daftar Pesan</h2>
    <table>
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
                    <td><?= e($row['nama']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= nl2br(e($row['pesan'])) ?></td>
                    <td><?= e($row['tanggal_kirim']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>