<?php
declare(strict_types=1);

/* Kelas GuestBook */
class GuestBook
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /* Menyimpan satu pesan */
    public function save(string $nama, string $email, string $pesan): bool
    {
        $sql = "INSERT INTO buku_tamu (nama, email, pesan, tanggal_kirim)
                VALUES (:nama, :email, :pesan, NOW())";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nama'  => trim($nama),
            'email' => trim($email),
            'pesan' => trim($pesan),
        ]);
    }

    /* Mengambil daftar pesan terbaru */
    public function getAll(int $limit = 100): array
    {
        $sql = "SELECT id, nama, email, pesan, tanggal_kirim
                FROM buku_tamu
                ORDER BY tanggal_kirim DESC, id DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}