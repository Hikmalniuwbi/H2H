# Hearts2Hearts (H2H) Web Portal & Sistem Manajemen Fanbase S2U

Portal web resmi girl group Hearts2Hearts (H2H) berbasis PHP Native dan MySQL, terintegrasi dengan dashboard administrasi AdminLTE 4.

---

## Fitur Utama

### Portal Publik
- Beranda: Informasi grup, navigasi, rilis terbaru, dan tautan sosial media.
- Profil Member: Informasi interaktif profil anggota grup H2H.
- Musik: Katalog album, daftar lagu, dan audio player.
- Merchandise Store: Belanja merchandise resmi dengan stok real-time dan alur checkout.
- Jadwal Acara: Informasi jadwal konser, fan meeting, dan kuota tiket.
- Berita: Publikasi berita resmi grup.
- S2U Fan Feedback: Formulir pesan masukan khusus anggota fanbase S2U (wajib login, data terisi otomatis).

### Panel Admin (`/admin`)
- Dashboard ringkasan data dan widget masukan fanbase S2U terbaru.
- Manajemen CRUD lengkap: Album, Lagu, Member, Merchandise, Kategori Acara, Acara, Berita, Galeri, Pesanan, dan Feedback.
- Utilitas tabel: Pencarian kata kunci, filter kategori, pilihan limit data per halaman (5-50), pengurutan kolom, dan paginasi.
- Hapus aman (Safe Delete): Konfirmasi modal, pembersihan data relasi (foreign key cascade), dan penghapusan file fisik uploads.
- Antarmuka panel admin terstandarisasi dalam bahasa Inggris.

---

## Teknologi

- Backend: PHP 8.1+
- Basis Data: MySQL 8.0+ / MariaDB
- Dashboard Admin: AdminLTE 4 (Bootstrap 5.3)
- Frontend: HTML5, CSS3, JavaScript Native

---

## Panduan Instalasi

1. Clone repositori:
   ```bash
   git clone https://github.com/Hikmalniuwbi/H2H.git
   cd H2H
   ```

2. Buat dan impor basis data:
   ```sql
   CREATE DATABASE db_hearts2hearts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   ```bash
   mysql -u root -p db_hearts2hearts < database/db_hearts2hearts.sql
   ```

3. Sesuaikan konfigurasi database jika diperlukan:
   ```bash
   cp config/database.example.php config/database.php
   ```

4. Jalankan aplikasi melalui web server lokal (Laragon/XAMPP) atau server bawaan PHP:
   ```bash
   php -S localhost:8000
   ```
   - Portal Publik: `http://localhost:8000`
   - Panel Admin: `http://localhost:8000/admin`

---

## Akun Bawaan

| Peran | Email | Password | Akses |
|---|---|---|---|
| Super Admin | admin@h2h.com | admin123 | Akses penuh dashboard admin & CRUD |
| S2U Fan | fan@h2h.com | admin123 | Checkout merchandise & kirim feedback |

---

## Pengujian Otomatis

Jalankan perintah berikut untuk menguji fungsi sistem:

```bash
php tests/test_delete_functionality.php
php tests/test_admin_features.php
php tests/test_s2u_feedback_verification.php
```
