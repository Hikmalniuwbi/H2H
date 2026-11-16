# Hearts2Hearts (H2H) Web Portal & Sistem Manajemen Fanbase S2U

Portal web resmi dan platform fanbase untuk girl group **Hearts2Hearts (H2H)** yang terintegrasi dengan dashboard administratif **AdminLTE 4**. Dibangun menggunakan PHP Native modern, basis data MySQL, dan antarmuka front-end responsif.

---

## 🌟 Fitur Utama

### 1. Pengalaman Pengguna & Fanbase Publik
- **Beranda (Home & Hero Section)**: Pengenalan grup, navigasi cepat, update terbaru, serta tautan media sosial resmi.
- **Profil Member (About Members)**: Halaman profil interaktif para member (Jiwoo, Ian, Carmen, Stella, Yuha, Yeon) lengkap dengan MBTI, zodiak, fakta unik, dan posisi.
- **Musik & Diskografi**: Katalog album dan daftar lagu dengan pemutar audio terintegrasi.
- **Toko Merchandise**: Katalog produk berlabel resmi (Lightstick, Photocard, Pakaian, Aksesori) dengan pemantauan stok real-time dan alur checkout.
- **Jadwal Acara & Tiket (Events)**: Informasi konser mendatang, fan meeting, dan festival beserta pemantauan kuota tiket secara langsung.
- **Berita Terkini (News)**: Pengumuman resmi grup dan siaran pers.
- **S2U Fan Feedback**: Formulir pesan & masukan khusus untuk member fanbase terverifikasi S2U, dilengkapi sistem proteksi login otomatis dan pengisian data profil otomatis (*prefilling*).

### 2. Panel Administrasi AdminLTE 4 (`/admin`)
- **Ringkasan Dashboard**: Indikator metrik ringkas, status modul basis data, serta widget pesan masukan terbaru dari fanbase S2U.
- **Manajemen CRUD Lengkap**: Pengelolaan data Album, Lagu (Songs), Member, Merchandise, Kategori Acara, Acara (Events), Berita (News), Galeri, Pesanan (Orders), dan Masukan (Feedbacks).
- **Utilitas Data Grid Tabel**:
  - 🔍 **Pencarian Langsung (Live Search)**: Pencarian kata kunci multi-kolom yang mempertahankan status query URL.
  - 🎯 **Filter Kontekstual**: Penyaringan dinamis berdasarkan kategori, status, dan relasi data.
  - 📄 **Pemilihan Jumlah Baris (Per-Page Selection)**: Pilihan tampilan data 5, 10, 15, 25, dan 50 baris per halaman.
  - ↕️ **Pengurutan Kolom Interaktif (Sorting)**: Header tabel dapat diklik dengan indikator arah (`ASC` / `DESC`).
  - 📑 **Paginasi Pintar (Smart Pagination)**: Paginasi navigasi yang mempertahankan parameter filter dan pencarian.
- **Operasi Hapus Aman (Safe Delete)**:
  - Modal konfirmasi dengan identifikasi rekaman data dinamis (`#ID (nama_tabel)`).
  - Pembersihan relasi bertingkat otomatis (*cascade cleanup*) untuk mencegah galat foreign key lock pada MySQL (`order_items`, `songs`, `events`, `gallery`, `feedbacks`).
  - Penghapusan file fisik dari folder `uploads/` saat rekaman data dihapus.
  - Dukungan bundle lokal Bootstrap 5 yang siap digunakan secara offline dengan fallback CDN.
- **Lokalisasi**: Antarmuka admin panel disajikan secara terstandar dalam bahasa Inggris (label, tabel, navigasi, form, pesan validasi, dan notifikasi).

---

## 🛠️ Tumpukan Teknologi (Tech Stack)

- **Backend**: PHP 8.1+ (Native OOP & Prosedural)
- **Basis Data**: MySQL 8.0+ / MariaDB (Ekstensi `mysqli` dengan *prepared statements*)
- **Dashboard Admin**: AdminLTE 4 (Bootstrap 5.3, Bootstrap Icons)
- **Frontend Publik**: HTML5, Vanilla JavaScript, CSS3 Responsif (Grid & Flexbox)
- **Pengujian**: Skrip pengujian otomatis berbasis PHP CLI

---

## 🚀 Instalasi & Konfigurasi

### Prasyarat Sistem
- PHP versi 8.1 atau yang lebih baru dengan ekstensi `mysqli` aktif
- MySQL 8.0 atau MariaDB
- Server lokal (Laragon, XAMPP, atau Apache/Nginx)

### Langkah-langkah Instalasi

1. **Clone Repositori**
   ```bash
   git clone https://github.com/Hikmalniuwbi/H2H.git
   cd H2H
   ```

2. **Konfigurasi Basis Data**
   - Buat basis data MySQL baru bernama `db_hearts2hearts`:
     ```sql
     CREATE DATABASE `db_hearts2hearts` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   - Impor struktur tabel dan data awal:
     ```bash
     mysql -u root -p db_hearts2hearts < database/db_hearts2hearts.sql
     ```
   - Salin file konfigurasi jika menggunakan kredensial basis data khusus:
     ```bash
     cp config/database.example.php config/database.php
     ```

3. **Menjalankan Aplikasi**
   - Jika menggunakan Laragon atau XAMPP, pastikan folder proyek berada di direktori web root (`www/H2H` atau `htdocs/H2H`).
   - Atau jalankan menggunakan server bawaan PHP CLI:
     ```bash
     php -S localhost:8000
     ```
   - Halaman Publik: `http://localhost:8000` atau `http://localhost/H2H`
   - Panel Admin: `http://localhost:8000/admin` atau `http://localhost/H2H/admin`

---

## 🔑 Akun Bawaan (Default Credentials)

| Peran (Role) | Email | Kata Sandi | Hak Akses |
|---|---|---|---|
| **Super Administrator** | `admin@h2h.com` | `admin123` | Akses penuh dashboard AdminLTE 4 & seluruh operasi CRUD |
| **S2U Fan (Customer)** | `fan@h2h.com` | `admin123` | Akses checkout toko merchandise & pengiriman masukan S2U |

---

## 🧪 Pengujian Otomatis (Automated Testing)

Jalankan rangkaian pengujian otomatis berikut melalui terminal untuk memvalidasi fungsionalitas sistem:

```bash
# Uji fungsi penghapusan aman & penanganan cascade foreign key
php tests/test_delete_functionality.php

# Uji fitur tabel AdminLTE 4 (pencarian, filter, sorting, paginasi, dashboard)
php tests/test_admin_features.php

# Uji rebranding S2U, proteksi autentikasi, dan persistensi pesan masukan
php tests/test_s2u_feedback_verification.php
```

---

## 📂 Struktur Direktori

```text
H2H/
├── admin/                    # Panel dashboard manajemen AdminLTE 4
│   ├── assets/               # File CSS/JS AdminLTE dan bundle Bootstrap 5
│   ├── config/               # Definisi skema tabel dan konfigurasi kolom
│   ├── includes/             # Fungsi pembantu dan utilitas admin
│   ├── dashboard.php         # Tampilan ringkasan dashboard & widget statistik
│   ├── index.php             # Pengendali daftar data & formulir tambah
│   ├── edit.php              # Tampilan pembaruan data rekaman
│   ├── delete.php            # Pengendali hapus aman dengan pembersihan cascade
│   └── login.php             # Autentikasi masuk administrator
├── assets/                   # Aset statis publik (logo, foto member, audio lagu, sampul album)
├── config/                   # Konfigurasi koneksi basis data global
├── database/                 # Berkas skema lengkap & dump data awal (db_hearts2hearts.sql)
├── includes/                 # Komponen navbar publik, footer, auth guard, dan fungsi global
├── tests/                    # Rangkaian pengujian otomatis berbasis PHP CLI
├── uploads/                  # Berkas media yang diunggah (album, galeri, member, merchandise, dll.)
├── about.php                 # Profil anggota dan latar belakang grup
├── checkout.php              # Formulir checkout merchandise dengan proteksi autentikasi
├── events.php                # Jadwal konser dan fan meeting
├── feedback.php              # Formulir masukan fanbase S2U dengan proteksi autentikasi
├── index.php                 # Halaman beranda utama
├── music.php                 # Diskografi dan pemutar audio
├── news.php                  # Pengumuman dan berita resmi band
├── orders.php                # Riwayat pesanan merchandise pengguna
├── store.php                 # Katalog toko merchandise resmi
└── style.css                 # Lembar gaya CSS responsif antarmuka publik
```

---

## 📄 Lisensi & Hak Cipta
Proyek ini dikembangkan untuk tujuan pembelajaran, portofolio, dan demonstrasi teknis. Semua materi musik dan media visual merupakan hak milik dari pemegang hak cipta masing-masing.
