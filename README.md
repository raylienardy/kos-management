# 🏢 KosManagement - Sistem Manajemen Kos/Kontrakan

Website manajemen kos berbasis **PHP & MySQL** yang mempertemukan pemilik kos dengan calon penyewa. Fitur mencakup pencarian kamar, booking, pembayaran, verifikasi admin, dan laporan.

![PHP](https://img.shields.io/badge/PHP-7.4+-blue) ![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange) ![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple) ![License](https://img.shields.io/badge/license-MIT-green)

---

## ✨ Fitur Utama

### 🧑‍🤝‍🧑 Pengunjung

- Melihat daftar kamar dengan foto, harga, fasilitas
- Pencarian berdasarkan nama, lokasi, rentang harga
- Halaman detail kamar
- Registrasi akun

### 👤 Penyewa

- Login & manajemen profil
- Booking kamar (pilih tanggal masuk, durasi)
- Upload bukti pembayaran
- Riwayat booking & status (pending/diterima/ditolak)

### 🔧 Admin

- Dashboard ringkasan (kamar tersedia, booking pending, pemasukan)
- CRUD kamar & fasilitas (multi-select, upload foto)
- Verifikasi pembayaran (valid/tolak) → otomatis update status kamar
- Manajemen data penyewa
- Laporan penghuni aktif & pemasukan bulanan

---

## 🛠️ Teknologi

- **Backend**: PHP Native (PDO), MySQL
- **Frontend**: Bootstrap 5, HTML5, CSS3
- **Tools**: XAMPP/Laragon, Git

---

## 🚀 Instalasi (Localhost)

1. **Clone repo**  
   `git clone https://github.com/username/kos-management.git`
2. **Database**
   - Buat database `db_kos` di MySQL.
   - Import file `database/db_kos.sql` ke database tersebut.
3. **Konfigurasi**  
   Buka `config/database.php`, sesuaikan `host`, `username`, `password`.
4. **Upload folders**  
   Pastikan folder `uploads/kamar/` dan `uploads/pembayaran/` ada dan **writable**.
5. **Jalankan**  
   Akses `http://localhost/kos-management/`

---

## 🔑 Akun Demo

| Role    | Email              | Password   |
| ------- | ------------------ | ---------- |
| Admin   | `admin@kos.com`    | `admin123` |
| Penyewa | `penyewa@demo.com` | `admin123` |

---

## 📁 Struktur Proyek

```

kos-management/
├── assets/ # CSS, JS, gambar statis
├── config/ # Koneksi database
├── includes/ # Navbar, sidebar, footer
├── auth/ # Login, registrasi, logout
├── user/ # Dashboard penyewa, booking, riwayat
├── admin/ # Dashboard admin, CRUD, verifikasi, laporan
├── uploads/ # Foto kamar, bukti transfer
├── database/ # SQL dump siap import
├── index.php # Halaman utama daftar kamar
├── detail.php # Detail kamar
└── README.md

```

---

## 📸 Screenshot (Opsional)

_Tambahkan tangkapan layar halaman utama, dashboard admin, dll._

---

## 📝 Tugas

Proyek ini dibuat sebagai bagian dari mata kuliah **Rekayasa Perangkat Lunak (RPL)** semester 3.

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
