# Tugas Mandiri Modul 6
## Perancangan ERD E-Library Kampus
 
**Nama :** Anisah Raihanah Amar
**NIM :** D121241104
**Mata Kuliah :** Pemrograman Website
 
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
