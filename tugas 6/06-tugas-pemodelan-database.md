# Tugas Mandiri Modul 6
## Perancangan ERD E-Library Kampus
 
**Nama :** Anisah Raihanah Amar <br>
**NIM :** D121241104 <br>
**Mata Kuliah :** Pemrograman Website <br>
 
---
 
## 1. Skenario dan Ruang Lingkup Sistem
 
Sistem E-Library kampus dirancang untuk mengelola dan mendata aktivitas peminjaman buku oleh mahasiswa di perpustakaan. Sistem ini memfasilitasi pencatatan data secara terintegrasi agar setiap riwayat transaksi peminjaman dan pengembalian dapat dilacak dengan akurat, termasuk mencatat buku yang dipinjam lebih dari satu kali pada waktu yang berbeda.
 
Berdasarkan skenario operasional tersebut, perancangan *Entity-Relationship Diagram* (ERD) ini melibatkan empat entitas utama:
 
| Entitas | Peran dalam Sistem | Kardinalitas & Relasi |
| :--- | :--- | :--- |
| **Mahasiswa** | Bertindak sebagai aktor/pengguna yang melakukan transaksi peminjaman buku perpustakaan. | **One-to-Many** terhadap Peminjaman (satu mahasiswa dapat meminjam buku berkali-kali). |
| **Buku** | Bertindak sebagai objek utama (koleksi perpustakaan) yang dipinjamkan kepada mahasiswa. | **One-to-Many** terhadap Peminjaman (satu buku dapat dicatat dalam banyak transaksi peminjaman berbeda). |
| **Penerbit** | Bertindak sebagai pihak/instansi yang memproduksi dan menerbitkan judul buku. | **One-to-Many** terhadap Buku (satu penerbit dapat menerbitkan banyak judul buku). |
| **Peminjaman** | Bertindak sebagai entitas transaksional yang mencatat riwayat kapan buku dipinjam dan dikembalikan. | Menyelesaikan relasi **Many-to-Many** antara Mahasiswa dan Buku menjadi entitas penghubung tersendiri. |

---

## 2. Simulasi Normalisasi (UNF → 1NF → 2NF → 3NF)
 
### 2.1 UNF (Unnormalized Form)
 
Semua data mahasiswa, buku, penerbit, dan peminjaman dicatat dalam satu tabel besar, sehingga terjadi penumpukan banyak nilai dalam satu sel (kelompok berulang) pada kolom data buku yang dipinjam.
 
| NIM | Nama | Jurusan | Buku Dipinjam (Kode, Judul, Penulis, Penerbit, Kode Penerbit, Tgl Pinjam, Tgl Kembali) |
|---|---|---|---|
| D121241001 | Park Chanyeol | Teknik Arsitektur | {BK001, Konsep dan Desain Sistem Rangka Momen Khusus (SRMK) Beton Bertulang Tahan Gempa Berdasarkan SNI 2012 dan 2013, Yudha Lesmana, Deepublish, PN01, 2026-08-01, 2026-08-10}, {BK002, Sistem Utilitas Bangunan untuk Arsitek, Sugeng Triyadi & Andi Harapan, Erlangga, PN02, 2026-08-05, NULL} |
| D121241002 | Mikasa Ackerman | Teknik Arsitektur | {BK001, Konsep dan Desain Sistem Rangka Momen Khusus (SRMK) Beton Bertulang Tahan Gempa Berdasarkan SNI 2012 dan 2013, Yudha Lesmana, Deepublish, PN01, 2026-08-03, NULL} |
 
Kolom terakhir memuat lebih dari satu nilai dalam satu sel, sehingga tabel belum memenuhi syarat paling dasar sekalipun.
 
### 2.2 Konversi ke 1NF
 
Kelompok berulang dihilangkan dengan memecah baris peminjaman — setiap sel kini hanya berisi satu nilai (atomik). Namun, data mahasiswa, buku, dan penerbit masih ditulis berulang-ulang untuk setiap baris peminjaman.
 
| NIM | Kode_Buku | Nama_Mhs | Jurusan | Judul | Penulis | Nama_Penerbit | Kode_Penerbit | Tgl_Pinjam | Tgl_Kembali |
|---|---|---|---|---|---|---|---|---|---|
| D121241001 | BK001 | Park Chanyeol | Teknik Arsitektur | Konsep dan Desain Sistem Rangka Momen Khusus (SRMK) Beton Bertulang Tahan Gempa Berdasarkan SNI 2012 dan 2013 | Yudha Lesmana | Deepublish | PN01 | 2026-08-01 | 2026-08-10 |
| D121241001 | BK002 | Park Chanyeol | Teknik Arsitektur | Sistem Utilitas Bangunan untuk Arsitek | Sugeng Triyadi & Andi Harapan | Erlangga | PN02 | 2026-08-05 | NULL |
| D121241002 | BK001 | Akbar | Teknik Arsitektur | Konsep dan Desain Sistem Rangka Momen Khusus (SRMK) Beton Bertulang Tahan Gempa Berdasarkan SNI 2012 dan 2013 | Yudha Lesmana | Deepublish | PN01 | 2026-08-03 | NULL |
 
Redundansi masih terlihat jelas: nama & jurusan Park Chanyeol ditulis ulang di dua baris, begitu pula judul, penulis, dan penerbit buku `BK001` ditulis ulang untuk setiap mahasiswa yang meminjamnya.
 
### 2.3 Konversi ke 2NF
 
Ketergantungan parsial dihilangkan. Atribut yang menjadi entitas tersendiri dipisah menjadi **Tabel Mahasiswa** dan **Tabel Buku**, sementara peristiwa peminjaman dipisah menjadi **Tabel Peminjaman** menggunakan *Foreign Key* NIM dan Kode Buku.
 
**Tabel `mahasiswa`** (nim, nama_mahasiswa, jurusan, no_telepon)
 
| NIM | Nama_Mahasiswa | Jurusan | No_Telepon |
|---|---|---|---|
| D121241001 | Park Chanyeol | Teknik Arsitektur | 0827111992 |
| D121241002 | Mikasa Ackerman | Teknik Arsitektur | 0810021992 |
 
**Tabel `buku`** (kode_buku, judul, penulis, nama_penerbit, kode_penerbit)
 
| Kode_Buku | Judul | Penulis | Nama_Penerbit | Kode_Penerbit |
|---|---|---|---|---|
| BK001 | Konsep dan Desain Sistem Rangka Momen Khusus (SRMK) Beton Bertulang Tahan Gempa Berdasarkan SNI 2012 dan 2013 | Yudha Lesmana | Deepublish | PN01 |
| BK002 | Sistem Utilitas Bangunan untuk Arsitek | Sugeng Triyadi & Andi Harapan | Erlangga | PN02 |
 
**Tabel `peminjaman`** (nim, kode_buku, tgl_pinjam, tgl_kembali)
 
| NIM | Kode_Buku | Tgl_Pinjam | Tgl_Kembali |
|---|---|---|---|
| D121241001 | BK001 | 2026-08-01 | 2026-08-10 |
| D121241001 | BK002 | 2026-08-05 | NULL |
| D121241002 | BK001 | 2026-08-03 | NULL |
 
**Masalah tersisa:** `nama_penerbit` pada tabel `buku` bergantung pada `kode_penerbit`, padahal `kode_penerbit` bukan kunci utama tabel `buku` — ia hanya atribut biasa. Inilah ketergantungan transitif (`kode_buku → kode_penerbit → nama_penerbit`).
 
### 2.4 Konversi ke 3NF
 
Ketergantungan transitif dihilangkan. Atribut penerbit pada tabel `buku` dipisah menjadi **tabel `penerbit`** yang berdiri sendiri, dihubungkan ke tabel `buku` melalui *Foreign Key* `kode_penerbit`.
 
**Tabel `penerbit`** (kode_penerbit, nama_penerbit, alamat, no_telepon)
 
| Kode_Penerbit | Nama_Penerbit |
|---|---|
| PN01 | Deepublish |
| PN02 | Erlangga |
 
**Tabel `buku`** (versi final — lihat struktur lengkap di bagian 4)
 
| Kode_Buku | Judul | Penulis | Kode_Penerbit (FK) |
|---|---|---|---|
| BK001 | Konsep dan Desain Sistem Rangka Momen Khusus (SRMK) Beton Bertulang Tahan Gempa Berdasarkan SNI 2012 dan 2013 | Yudha Lesmana | PN01 |
| BK002 | Sistem Utilitas Bangunan untuk Arsitek | Sugeng Triyadi & Andi Harapan | PN02 |
 
Skema kini memenuhi 3NF: tidak ada redundansi yang tidak perlu — perubahan nama penerbit cukup dilakukan pada satu baris di tabel `penerbit`, dan tidak ada atribut non-kunci yang bergantung pada atribut non-kunci lainnya.