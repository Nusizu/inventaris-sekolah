# Sistem Inventaris Sekolah

Aplikasi web untuk mencatat barang inventaris sekolah beserta proses peminjaman dan pengembaliannya secara digital dan terpusat. Aplikasi ini menggantikan buku catatan manual yang sering menimbulkan masalah: tidak ada catatan jelas kapan barang harus dikembalikan, barang rusak tanpa diketahui siapa peminjam terakhirnya, dan stok yang tidak akurat.

Aplikasi dibangun dengan **Laravel**, **Blade**, dan **Laravel Breeze**, dengan dua jenis pengguna:

- **Admin**: pengelola inventaris.
- **User**: guru atau siswa yang meminjam barang.

## Daftar Isi

1. [Fitur](#fitur)
2. [Teknologi](#teknologi)
3. [Prasyarat](#prasyarat)
4. [Instalasi](#instalasi)
5. [Akun Uji (Hasil Seeder)](#akun-uji-hasil-seeder)
6. [Data Seeder](#data-seeder)
7. [Hak Akses per Role](#hak-akses-per-role)
8. [Aturan Bisnis](#aturan-bisnis)
9. [Struktur Database](#struktur-database)
10. [Daftar Route Utama](#daftar-route-utama)
11. [Struktur Folder](#struktur-folder)
12. [Standar Kualitas](#standar-kualitas)
13. [Keamanan dan Performa](#keamanan-dan-performa)
14. [Pemecahan Masalah](#pemecahan-masalah)
15. [Catatan untuk Penggunaan Sebenarnya](#catatan-untuk-penggunaan-sebenarnya)
16. [Pengembangan Lanjutan](#pengembangan-lanjutan)

## Fitur

### Admin

- **Dashboard**: total barang, total stok, jumlah peminjaman yang sedang berjalan, jumlah yang terlambat, barang paling sering dipinjam, dan user paling aktif.
- **Kategori**: tambah, ubah, hapus, cari, dan filter.
- **Barang**: tambah, ubah, hapus (soft delete), dengan kategori, kode unik, stok, dan kondisi. Dilengkapi pencarian serta filter kategori dan kondisi.
- **Peminjaman**: menambahkan peminjaman untuk user, menghapus data peminjaman (soft delete), pencarian, dan filter status. Stok berkurang otomatis.
- **Pengembalian**: memproses pengembalian dengan tanggal, kondisi barang, dan catatan. Stok bertambah otomatis. Riwayat menampilkan keterangan tepat waktu atau terlambat.
- **Manajemen User**: tambah, ubah, hapus akun, dan atur role, dengan pengaman (tidak dapat menghapus atau mengubah role akun sendiri, dan tidak dapat menghapus user yang sudah memiliki riwayat).
- **Activity Log**: jejak audit aktivitas penting, dengan filter aksi, pengguna, dan rentang tanggal.

### User

- **Dashboard** pribadi: peminjaman yang sedang berjalan, terlambat, sudah dikembalikan, dan total, plus peminjaman terbaru.
- **Daftar Barang**: melihat barang beserta ketersediaannya, dengan pencarian, filter kategori, dan filter ketersediaan.
- **Pengajuan peminjaman**: stok berkurang otomatis.
- **Riwayat peminjaman** milik sendiri, dengan pencarian dan filter status.

### Umum

- Login dan register (Laravel Breeze), ubah profil dan password.
- Menu dan halaman berbeda sesuai role.
- Validasi berbahasa Indonesia, angka dengan pemisah ribuan, dan tanggal berformat Indonesia (contoh: `29 Okt 2026`).
- Tampilan responsif untuk mobile, tablet, dan desktop.
- Halaman error (403, 404, 419, 500) berbahasa Indonesia.

## Teknologi

| Komponen | Keterangan |
|---|---|
| Framework | Laravel 12 (minimal Laravel 11) |
| Bahasa | PHP 8.2+ |
| View | Blade |
| Autentikasi | Laravel Breeze (Blade dengan Alpine.js) |
| Database | MySQL |
| Frontend | Tailwind CSS, Alpine.js, Vite |
| Pemilih tanggal | flatpickr (lokal Indonesia) |

## Prasyarat

Pastikan perangkat sudah memiliki:

- **PHP 8.2 atau lebih baru**, dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, dan `fileinfo` (semuanya aktif secara bawaan pada XAMPP).
- **Composer 2**
- **Node.js 20 LTS atau lebih baru** beserta npm
- **MySQL** atau MariaDB (misalnya dari XAMPP atau Laragon)
- **Git**

Cek versi:

```bash
php -v
composer -V
node -v
npm -v
```

## Instalasi

### 1. Unduh kode

```bash
git clone https://github.com/<username-github>/inventaris-sekolah.git
cd inventaris-sekolah
```

### 2. Pasang dependensi

```bash
composer install
npm install
```

### 3. Siapkan file lingkungan

Salin file contoh, lalu buat kunci aplikasi.

Windows (PowerShell atau CMD):

```bash
copy .env.example .env
```

Linux atau macOS:

```bash
cp .env.example .env
```

Lalu:

```bash
php artisan key:generate
```

### 4. Buat database

Nyalakan **MySQL** (misalnya lewat XAMPP Control Panel), lalu buat database kosong bernama `inventaris_sekolah`. Bisa lewat phpMyAdmin (menu SQL) atau terminal:

```sql
CREATE DATABASE inventaris_sekolah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Sesuaikan koneksi database

Buka file `.env` dan pastikan bagian berikut sesuai dengan MySQL di perangkat kamu:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventaris_sekolah
DB_USERNAME=root
DB_PASSWORD=
```

Pada XAMPP bawaan, `DB_USERNAME` adalah `root` dan `DB_PASSWORD` dikosongkan.

### 6. Jalankan migrasi dan seeder

```bash
php artisan migrate --seed
```

Perintah ini membuat seluruh tabel dan mengisi data contoh (lihat bagian [Data Seeder](#data-seeder)). Untuk mengulang dari awal kapan saja (menghapus semua data):

```bash
php artisan migrate:fresh --seed
```

### 7. Kompilasi aset frontend

Untuk pemakaian biasa atau penilaian, cukup satu kali:

```bash
npm run build
```

### 8. Jalankan aplikasi

```bash
php artisan serve
```

Buka **http://127.0.0.1:8000** di browser. Halaman utama otomatis mengarah ke halaman login.

### Mode pengembangan (opsional)

Jika ingin mengubah tampilan dan melihat hasilnya langsung, jalankan dua terminal berdampingan:

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

## Akun Uji (Hasil Seeder)

Setelah `php artisan migrate --seed`, tersedia tiga akun berikut:

| Role | Nama | Email | Password |
|---|---|---|---|
| Admin | Administrator | `admin@inventaris.test` | `password` |
| User | User Satu | `user1@inventaris.test` | `password` |
| User | User Dua | `user2@inventaris.test` | `password` |

> Akun dan password di atas hanya untuk pengujian. Ganti atau hapus sebelum aplikasi dipakai di lingkungan sebenarnya.

Pendaftaran akun baru lewat halaman **Daftar** selalu menghasilkan role **User**. Akun Admin hanya dapat dibuat oleh Admin lewat menu Manajemen User.

## Data Seeder

| Tabel | Jumlah | Keterangan |
|---|---|---|
| `users` | 3 | 1 Admin dan 2 User |
| `categories` | 3 | Alat Lab, Alat Olahraga, Elektronik |
| `items` | 10 | Tersebar di ketiga kategori, dengan variasi kondisi (baik, rusak ringan, rusak berat) |
| `loans` | 6 | 2 sedang dipinjam, 3 sudah dikembalikan, 1 terlambat |
| `return_logs` | 3 | Sesuai peminjaman yang sudah dikembalikan |
| `activity_logs` | 7 | Contoh aktivitas Admin dan User |

Angka acuan di Dashboard Admin tepat setelah seeder: Total Barang **10**, Total Stok **123**, Sedang Dipinjam **2**, Terlambat **1**.

## Hak Akses per Role

| Menu atau halaman | Admin | User |
|---|:---:|:---:|
| Dashboard | ✓ (statistik seluruh sistem) | ✓ (statistik pribadi) |
| Kategori | ✓ | ✗ |
| Barang (kelola) | ✓ | ✗ |
| Daftar Barang (lihat ketersediaan) | ✗ | ✓ |
| Peminjaman (kelola semua) | ✓ | ✗ |
| Peminjaman Saya (riwayat dan pengajuan) | ✗ | ✓ |
| Pengembalian | ✓ | ✗ |
| Manajemen User | ✓ | ✗ |
| Activity Log | ✓ | ✗ |
| Profil dan ubah password | ✓ | ✓ |

Akses dibatasi di level route dengan middleware `role`. Mengakses halaman yang bukan haknya menghasilkan halaman **403**.

## Aturan Bisnis

- Stok barang **berkurang** saat peminjaman dibuat (oleh Admin maupun pengajuan User), dan **bertambah** saat pengembalian diproses.
- Permintaan melebihi stok ditolak. Proses stok memakai database transaction dan penguncian baris agar tidak menjadi minus saat ada permintaan bersamaan.
- Barang berkondisi **rusak berat** atau berstok **0** tidak dapat dipinjam.
- Status peminjaman: `dipinjam`, `dikembalikan`, `terlambat`. Peminjaman yang melewati tanggal jatuh tempo berubah menjadi `terlambat` secara otomatis saat halaman peminjaman atau dashboard dibuka.
- Satu peminjaman hanya dapat dikembalikan satu kali.
- Menghapus peminjaman yang **belum dikembalikan** mengembalikan stok barangnya. Menghapus peminjaman yang sudah dikembalikan tidak mengubah stok, dan catatan pengembaliannya ikut di-soft-delete.
- Barang yang sedang dipinjam tidak dapat dihapus.
- Kategori yang masih memiliki barang (termasuk yang sudah dihapus) tidak dapat dihapus.
- Kondisi barang tidak berubah otomatis setelah pengembalian. Admin memperbaruinya lewat menu Barang bila perlu.
- User yang sudah memiliki riwayat peminjaman atau aktivitas tidak dapat dihapus. Admin tidak dapat menghapus atau mengubah role akunnya sendiri.
- Data `items`, `loans`, dan `return_logs` memakai **soft delete** (kolom `deleted_at`).
- Aktivitas penting dicatat ke `activity_logs`: tambah, ubah, dan hapus kategori, barang, peminjaman, user, serta pengembalian.

## Struktur Database

Enam tabel utama:

| Tabel | Kolom utama | Relasi |
|---|---|---|
| `users` | `id`, `name`, `email` (unik), `password`, `role` (`admin` atau `user`), `email_verified_at`, `remember_token`, `timestamps` | Memiliki banyak `loans` dan `activity_logs` |
| `categories` | `id`, `name` (unik), `timestamps` | Memiliki banyak `items` |
| `items` | `id`, `category_id`, `name`, `code` (unik), `stock`, `condition` (`baik`, `rusak ringan`, `rusak berat`), `timestamps`, `deleted_at` | Milik satu `category`, memiliki banyak `loans` |
| `loans` | `id`, `user_id`, `item_id`, `quantity`, `loan_date`, `due_date`, `status`, `timestamps`, `deleted_at` | Milik satu `user` dan satu `item`, memiliki satu `return_log` |
| `return_logs` | `id`, `loan_id` (unik), `returned_at`, `condition_on_return`, `notes`, `timestamps`, `deleted_at` | Milik satu `loan` |
| `activity_logs` | `id`, `user_id`, `action`, `description`, `timestamps` | Milik satu `user` |

Relasi Eloquent:

| Relasi | Jenis |
|---|---|
| `Category` ke `Item` | One to Many |
| `User` ke `Loan` | One to Many |
| `Item` ke `Loan` | One to Many |
| `Loan` ke `ReturnLog` | One to One |
| `User` ke `ActivityLog` | One to Many |

Selain itu terdapat tabel bawaan Laravel (`sessions`, `cache`, `jobs`, dan sejenisnya) serta indeks tambahan pada kolom yang sering difilter atau diurutkan (status dan jatuh tempo peminjaman, waktu dan aksi log, role user, dan lainnya).

## Daftar Route Utama

**Umum**

| Method | URL | Nama route | Keterangan |
|---|---|---|---|
| GET | `/` | `home` | Mengarah ke login atau dashboard sesuai role |
| GET | `/login`, `/register` | `login`, `register` | Halaman masuk dan daftar |
| POST | `/logout` | `logout` | Keluar |
| GET | `/dashboard` | `dashboard` | Mengarah ke dashboard sesuai role |
| GET, PATCH | `/profile` | `profile.edit`, `profile.update` | Profil |
| PUT | `/password` | `password.update` | Ubah password |

**Admin** (prefix `/admin`, nama `admin.*`)

| URL | Nama route | Keterangan |
|---|---|---|
| `/admin/dashboard` | `admin.dashboard` | Dashboard Admin |
| `/admin/categories` | `admin.categories.*` | CRUD kategori |
| `/admin/items` | `admin.items.*` | CRUD barang |
| `/admin/loans` | `admin.loans.index`, `.create`, `.store`, `.destroy` | Peminjaman |
| `/admin/loans/{loan}/return` | `admin.loans.return.create`, `.store` | Proses pengembalian |
| `/admin/return-logs` | `admin.return-logs.index` | Riwayat pengembalian |
| `/admin/users` | `admin.users.*` | Manajemen user |
| `/admin/activity-logs` | `admin.activity-logs.index` | Activity Log |

**User** (prefix `/user`, nama `user.*`)

| URL | Nama route | Keterangan |
|---|---|---|
| `/user/dashboard` | `user.dashboard` | Dashboard User |
| `/user/items` | `user.items.index` | Daftar barang |
| `/user/loans` | `user.loans.index`, `.create`, `.store` | Riwayat dan pengajuan peminjaman |

Daftar lengkap dapat dilihat dengan `php artisan route:list --except-vendor`.

## Struktur Folder

```
inventaris-sekolah/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/      Dashboard, Category, Item, Loan, ReturnLog, User, ActivityLog
│   │   │   ├── User/       Dashboard, Item, Loan
│   │   │   └── Auth/       Bawaan Breeze (login, register, logout, ubah password)
│   │   ├── Middleware/     RoleMiddleware, SecurityHeaders
│   │   └── Requests/       Form Request untuk setiap form
│   ├── Models/             User, Category, Item, Loan, ReturnLog, ActivityLog
│   ├── Providers/
│   └── Services/           LoanService, ActivityLogger
├── database/
│   ├── migrations/
│   └── seeders/
├── lang/id/                Pesan validasi, autentikasi, dan pagination berbahasa Indonesia
├── resources/
│   ├── css/, js/
│   └── views/              admin/, user/, auth/, profile/, layouts/, components/, errors/, loans/
├── routes/                 web.php, auth.php
└── README.md
```

Konvensi penamaan: tabel berbentuk jamak dan snake_case, model berbentuk tunggal dan PascalCase, controller berakhiran `Controller`, view dikelompokkan per folder modul, dan seluruh nama teknis dalam Bahasa Inggris.

## Standar Kualitas

| No | Ketentuan | Penerapan |
|---|---|---|
| 1 | Tidak ada error di tampilan maupun log | Diuji untuk kedua role, termasuk data kosong dan input tidak wajar |
| 2 | Tidak ada tombol atau aksi yang tidak berfungsi | Seluruh tombol diuji, dan fitur Breeze yang tidak dipakai dihapus |
| 3 | Form fokus otomatis ke field pertama | Atribut `autofocus` pada semua form |
| 4 | Placeholder pada setiap input | Semua input dan textarea memiliki placeholder |
| 5 | Tanda bintang pada field wajib dan Form Request Validation | Tanda `*` merah, dengan validasi backend di class Form Request |
| 6 | Tabel dengan pagination dan pencarian atau filter | Pagination Laravel dikombinasikan dengan pencarian dan filter |
| 7 | Angka dengan pemisah ribuan format Indonesia | `number_format($angka, 0, ',', '.')`, misalnya `1.200` |
| 8 | Tanggal berformat Indonesia | `29 Okt 2026`, termasuk pemilih tanggal berbahasa Indonesia |
| 9 | Tampilan bersih, rapi, dan responsif | Tailwind CSS, diuji di mobile, tablet, dan desktop |
| 10 | Login dan Register dengan Laravel Breeze | Tersedia |
| 11 | Menu dan halaman sesuai role | Menu, route, dan middleware dibedakan untuk Admin dan User |
| 12 | Route, URL, dan breadcrumb rapi dan konsisten | Pola `admin.*` dan `user.*`, serta breadcrumb di setiap halaman |

## Keamanan dan Performa

**Keamanan**

- Proteksi CSRF pada seluruh form, dan seluruh output di-escape otomatis oleh Blade.
- Password disimpan dalam bentuk hash (bcrypt). Percobaan login dibatasi oleh rate limiting bawaan Breeze.
- Validasi di sisi server memakai Form Request, dan penyimpanan data memakai `validated()` sehingga tidak ada mass assignment dari input mentah.
- Akses dibatasi per role di level route. Role tidak dapat dipilih saat mendaftar.
- Seluruh query memakai Eloquent dengan parameter terikat.
- Header keamanan HTTP: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, dan `Permissions-Policy`.
- Fitur bawaan Breeze yang tidak dipakai (lupa password, verifikasi email, hapus akun mandiri) dihapus agar tidak ada form yang tidak berfungsi.
- Pemeriksaan kerentanan paket dilakukan dengan `composer audit` dan `npm audit`.

**Performa**

- Relasi dimuat dengan eager loading (`with`, `withCount`) di seluruh halaman daftar.
- Pada lingkungan non-produksi, `Model::preventLazyLoading()` aktif sebagai penjaga agar query N+1 tidak muncul.
- Indeks database ditambahkan pada kolom yang sering difilter atau diurutkan.

## Pemecahan Masalah

| Gejala | Penyebab dan solusi |
|---|---|
| `SQLSTATE[HY000] [2002] ... actively refused it` | MySQL belum menyala. Nyalakan di XAMPP Control Panel |
| `SQLSTATE[HY000] [1049] Unknown database` | Database `inventaris_sekolah` belum dibuat. Lihat langkah 4 instalasi |
| Halaman tampil tanpa gaya, atau `Vite manifest not found` | Aset belum dikompilasi. Jalankan `npm run build` (atau `npm run dev`) |
| `No application encryption key has been specified` | Jalankan `php artisan key:generate` |
| Perubahan `.env` atau konfigurasi tidak terbaca | Jalankan `php artisan optimize:clear`, lalu restart `php artisan serve` |
| `Class ... not found` setelah menambah atau mengganti nama file | Jalankan `composer dump-autoload` |
| Tanggal atau nama bulan tampil berbahasa Inggris | Pastikan `APP_LOCALE=id` di `.env`, lalu `php artisan config:clear` |
| Ingin mengembalikan data awal | Jalankan `php artisan migrate:fresh --seed` |
| Port 8000 sudah dipakai | Jalankan `php artisan serve --port=8001` |

## Catatan untuk Penggunaan Sebenarnya

Konfigurasi bawaan ditujukan untuk pengembangan lokal. Untuk penggunaan sebenarnya:

- Ubah `.env`: `APP_ENV=production` dan `APP_DEBUG=false`.
- Ganti atau hapus akun seeder, dan jangan memakai password `password`.
- Gunakan HTTPS, lalu set `SESSION_SECURE_COOKIE=true`.
- Jangan menyertakan file `.env` ke repository (sudah diabaikan oleh `.gitignore`).
- Jalankan `composer install --no-dev --optimize-autoloader` dan `npm run build`.

## Pengembangan Lanjutan

Fitur berikut dari daftar pengembangan opsional sudah tersedia: dashboard statistik (barang paling sering dipinjam dan user paling aktif), serta pencarian dan filter barang berdasarkan kategori, kondisi, atau ketersediaan stok.

Ide yang belum dikerjakan:

- Alur persetujuan (approval) peminjaman oleh Admin sebelum barang dianggap resmi dipinjam.
- Notifikasi atau pengingat (misalnya lewat email) saat mendekati `due_date`.
- Export laporan peminjaman ke PDF atau Excel.
- Barcode atau QR Code untuk mempercepat peminjaman dan pengembalian.
- Grafik tren peminjaman per bulan di dashboard Admin.
- Role management yang lebih lanjut dengan paket `spatie/laravel-permission`.

## Pembuat

Dibuat oleh **Nursiraj** sebagai proyek Sistem Inventaris Sekolah (Laravel, Blade, Breeze, dan MySQL).
