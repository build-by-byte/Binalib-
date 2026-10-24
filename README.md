# BinaLib — Sistem Informasi Perpustakaan Digital

BinaLib adalah aplikasi sistem informasi perpustakaan yang dikembangkan untuk mendukung pengelolaan layanan perpustakaan di SMK Bina Rahayu. Aplikasi ini membantu pengelolaan data buku, peminjaman, pengembalian, serta penyediaan koleksi buku digital agar dapat diakses dengan lebih mudah.

Proyek ini menggunakan PHP Native dan MySQL/MariaDB sehingga dapat dijalankan pada server sekolah yang mendukung layanan web berbasis PHP.

## Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Teknologi](#teknologi)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Struktur Proyek](#struktur-proyek)
- [Persiapan Database](#persiapan-database)
- [Konfigurasi Aplikasi](#konfigurasi-aplikasi)
- [Menjalankan di Lingkungan Lokal](#menjalankan-di-lingkungan-lokal)
- [Deployment ke Server Sekolah](#deployment-ke-server-sekolah)
- [Pembaruan Aplikasi](#pembaruan-aplikasi)
- [Keamanan](#keamanan)
- [Backup dan Pemulihan](#backup-dan-pemulihan)
- [Troubleshooting](#troubleshooting)
- [Lisensi](#lisensi)

## Tentang Proyek

BinaLib dikembangkan sebagai sistem informasi perpustakaan untuk membantu proses administrasi perpustakaan sekolah yang sebelumnya dilakukan secara manual.

Aplikasi ini dirancang untuk mendukung digitalisasi layanan perpustakaan, meningkatkan efisiensi pencatatan, dan menyediakan akses terhadap koleksi buku digital.

### Tujuan

- Mendigitalisasi pengelolaan data perpustakaan.
- Mempermudah pencatatan peminjaman dan pengembalian buku.
- Memudahkan pengelolaan koleksi buku digital.
- Mendukung kegiatan literasi melalui akses buku elektronik.
- Membantu petugas perpustakaan dalam mengelola data dan aktivitas pengguna.

## Fitur Utama

### 1. Dashboard

Menyediakan ringkasan informasi yang diperlukan untuk memantau aktivitas perpustakaan.

### 2. Manajemen Data Buku

Mendukung pengelolaan koleksi buku fisik, termasuk pencatatan dan pembaruan informasi buku.

### 3. Peminjaman Buku

Mendukung pencatatan peminjaman buku fisik oleh pengguna perpustakaan.

### 4. Pengembalian Buku

Mendukung pencatatan pengembalian buku untuk membantu petugas memperbarui status sirkulasi.

### 5. Identifikasi Pengguna

Mendukung pencatatan identitas anggota perpustakaan dan penggunaan UID kartu siswa apabila fitur tersebut tersedia dalam konfigurasi aplikasi.

### 6. E-Book

Menyediakan pengelolaan koleksi buku digital, termasuk informasi buku dan berkas PDF yang digunakan untuk membaca buku elektronik.

### 7. Riwayat Aktivitas

Mendukung peninjauan aktivitas perpustakaan sesuai data yang tersedia dan fitur yang telah diimplementasikan.

> Catatan: Ketersediaan dan perilaku setiap fitur mengikuti implementasi pada versi aplikasi yang di-deploy.

## Teknologi

Teknologi yang digunakan dalam proyek ini meliputi:

| Teknologi | Kegunaan |
|---|---|
| PHP Native | Logika aplikasi sisi server |
| MySQL/MariaDB | Penyimpanan data |
| HTML5 | Struktur halaman web |
| CSS3 | Tampilan antarmuka |
| JavaScript | Interaksi pada halaman web |
| PDF.js | Mendukung tampilan dokumen PDF apabila digunakan pada implementasi |
| Apache atau web server kompatibel | Menjalankan aplikasi PHP |

## Persyaratan Sistem

Sebelum melakukan instalasi, pastikan server memiliki komponen berikut:

- Sistem operasi Linux atau sistem operasi lain yang mendukung PHP.
- Apache atau web server yang kompatibel dengan PHP.
- PHP dengan versi yang sesuai dengan kode aplikasi.
- Ekstensi PHP `pdo`, `pdo_mysql`, dan ekstensi lain yang digunakan aplikasi.
- MySQL atau MariaDB.
- Git untuk mengambil source code dari GitHub.
- Akses terminal atau SSH untuk administrasi server.
- Hak akses untuk membuat database dan mengatur direktori aplikasi.

Versi PHP dan ekstensi yang benar-benar diperlukan perlu disesuaikan dengan source code yang digunakan.

## Struktur Proyek

Contoh struktur direktori proyek:

```text
binalib/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   └── database.php
├── database/
│   ├── binalib_ebook.sql
│   ├── migration_ebook_submissions.sql
│   └── verify_ebook_admin_edit.sql
├── uploads/
│   ├── ebook/
│   └── covers/
├── index.php
├── README.md
└── .gitignore
```

Struktur di atas merupakan gambaran dan bukan jaminan bahwa seluruh direktori atau nama file tersebut tersedia dalam setiap versi repository. Sesuaikan dengan struktur aktual proyek sebelum menjalankan perintah instalasi.

**Penting:** `bls_ebook` adalah nama database, sedangkan `binalib_ebook.sql` merupakan nama file SQL yang digunakan dalam contoh dokumentasi ini. Nama database dan nama file SQL tidak harus sama.

## Persiapan Database

Database yang digunakan aplikasi ini adalah:

```text
Nama database: bls_ebook
```

### 1. Membuat Database

Masuk ke MySQL atau MariaDB menggunakan akun yang memiliki izin membuat database.

```sql
CREATE DATABASE bls_ebook
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Jika database sudah tersedia, tidak perlu membuat database baru.

### 2. Mengimpor Struktur Database

Pastikan file SQL yang akan digunakan memang tersedia di repository dan sesuai dengan versi aplikasi.

Jika file SQL berada di `database/binalib_ebook.sql`, jalankan:

```bash
mysql -u USER_DATABASE -p bls_ebook < database/binalib_ebook.sql
```

Ganti `USER_DATABASE` dengan username MySQL yang sesuai. Masukkan password ketika diminta.

Perintah tersebut mengimpor isi file SQL ke database `bls_ebook`.

**Perhatian:** Pastikan file SQL tidak berisi perintah yang menghapus atau membuat ulang database yang tidak seharusnya diubah. Periksa isinya terlebih dahulu sebelum mengimpor.

### 3. Menjalankan Migration

Jika aplikasi memiliki migration tambahan, periksa apakah migration tersebut memang diperlukan dan belum pernah diterapkan.

Contoh apabila file berikut tersedia:

```bash
mysql -u USER_DATABASE -p bls_ebook < database/migration_ebook_submissions.sql
```

Jangan menjalankan migration berulang kali tanpa memastikan apakah skripnya aman untuk dieksekusi ulang.

File `verify_ebook_admin_edit.sql`, jika memang tersedia, perlu diperiksa terlebih dahulu untuk mengetahui apakah isinya berupa pemeriksaan, perubahan skema, atau tindakan lain.

### 4. Membuat Pengguna Database

Untuk server produksi, sebaiknya aplikasi menggunakan akun database tersendiri dengan hak akses terbatas.

Contoh:

```sql
CREATE USER 'binalib_user'@'localhost'
IDENTIFIED BY 'GANTI_DENGAN_PASSWORD_KUAT';

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX
ON bls_ebook.*
TO 'binalib_user'@'localhost';

FLUSH PRIVILEGES;
```

Hak akses di atas merupakan contoh. Sesuaikan dengan kebutuhan aplikasi, proses instalasi, dan migration yang digunakan. Jangan memberikan hak akses administratif penuh kepada akun aplikasi jika tidak diperlukan.

Jika database berada di server terpisah, sesuaikan host pada akun database dan konfigurasi koneksi.

## Konfigurasi Aplikasi

Aplikasi harus dikonfigurasi agar dapat terhubung ke database pada server tujuan.

### 1. Konfigurasi Database

Periksa file konfigurasi database, misalnya:

```text
config/database.php
```

Jika aplikasi menggunakan environment variable, konfigurasi yang dibutuhkan dapat berupa:

```text
BLS_DB_HOST=localhost
BLS_DB_PORT=3306
BLS_DB_NAME=bls_ebook
BLS_DB_USER=binalib_user
BLS_DB_PASSWORD=password_database
```

Nilai tersebut merupakan contoh konfigurasi dan bukan kredensial produksi.

Jika aplikasi sudah menggunakan `getenv()` untuk membaca variabel lingkungan, pastikan variabel tersedia di lingkungan proses PHP yang menjalankan aplikasi.

Jangan mengasumsikan bahwa file `.env` otomatis dibaca oleh PHP. File tersebut hanya dapat digunakan jika aplikasi memiliki mekanisme untuk membacanya atau server mengatur variabel lingkungan dengan benar.

Jika aplikasi menggunakan konfigurasi langsung dalam file PHP, sesuaikan dengan pola konfigurasi yang sudah diterapkan pada source code.

### 2. Konfigurasi Upload

Jika aplikasi menyediakan fitur unggah PDF dan sampul buku, pastikan direktori upload tersedia dan dapat ditulis oleh proses web server.

Contoh direktori:

```text
uploads/
├── ebook/
└── covers/
```

Buat direktori tersebut hanya jika memang digunakan oleh implementasi aplikasi.

Contoh penyesuaian kepemilikan direktori pada server Linux:

```bash
sudo chown -R www-data:www-data uploads
```

Perintah ini sesuai untuk lingkungan yang menggunakan pengguna dan grup web server `www-data`. Sesuaikan jika server menggunakan akun lain.

Hindari memberikan izin `777` secara permanen. Gunakan kepemilikan dan permission yang cukup untuk kebutuhan aplikasi.

### 3. Konfigurasi Web Server

Arahkan document root web server ke direktori aplikasi yang benar.

Jika aplikasi memiliki direktori publik khusus, arahkan document root ke direktori tersebut. Jika tidak, pastikan file konfigurasi, file SQL, log, dan direktori internal lainnya tidak dapat diakses secara langsung melalui browser.

Jangan membuka akses publik ke:

- File konfigurasi database.
- File `.env`.
- File dump database `.sql`.
- File log.
- Direktori cadangan.
- File internal yang tidak diperlukan untuk tampilan web.

## Menjalankan di Lingkungan Lokal

Untuk pengembangan lokal, kamu dapat menggunakan Apache, PHP, dan MySQL/MariaDB melalui lingkungan seperti XAMPP atau LAMPP.

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/REPOSITORY.git
```

Ganti `USERNAME/REPOSITORY` dengan alamat repository BinaLib yang sebenarnya.

Masuk ke direktori proyek:

```bash
cd REPOSITORY
```

### 2. Siapkan Database

Buat database `bls_ebook`, lalu impor file SQL yang sesuai dengan versi aplikasi.

Pastikan konfigurasi koneksi database pada lingkungan lokal sudah benar.

### 3. Jalankan Aplikasi

Jika menggunakan Apache, tempatkan aplikasi di direktori web server dan buka melalui alamat lokal yang sesuai dengan konfigurasi Apache.

Contoh:

```text
http://localhost/REPOSITORY/
```

Alamat tersebut merupakan contoh. URL aktual bergantung pada nama direktori dan konfigurasi virtual host.

## Deployment ke Server Sekolah

Bagian ini menjelaskan alur deployment menggunakan GitHub sebagai tempat penyimpanan source code dan server sekolah sebagai tempat menjalankan aplikasi.

### 1. Siapkan Repository GitHub

Pastikan repository berisi source code yang akan digunakan untuk deployment.

Sebelum melakukan push, periksa file yang akan dikirim:

```bash
git status
```

Pastikan kredensial, file konfigurasi sensitif, database produksi, serta file unggahan pengguna yang tidak perlu masuk ke repository tidak ikut terkirim.

Jika `.gitignore` belum tersedia, buat file tersebut sesuai kebutuhan proyek.

Contoh isi `.gitignore`:

```gitignore
# Environment dan konfigurasi sensitif
.env
.env.*
!.env.example

# Log
*.log
logs/

# File sementara
*.tmp
*.swp
.DS_Store

# Backup database
*.sql.bak
*.dump

# Cache
cache/
tmp/
```

Jangan langsung mengecualikan semua file SQL jika repository memang perlu menyimpan migration atau file SQL awal yang tidak mengandung data sensitif.

File PDF, sampul buku, dan data unggahan produksi sebaiknya dikelola sesuai kebijakan penyimpanan sekolah. Jika file-file tersebut memang perlu berada di server, jangan mengandalkan GitHub sebagai satu-satunya penyimpanan.

Jika file sensitif sudah pernah di-commit, menambahkan nama file ke `.gitignore` saja tidak menghapusnya dari riwayat Git. File tersebut perlu ditangani secara terpisah, termasuk mengganti kredensial yang terlanjur terekspos.

### 2. Push Source Code ke GitHub

Jika repository lokal belum memiliki remote:

```bash
git remote add origin https://github.com/USERNAME/REPOSITORY.git
```

Periksa remote:

```bash
git remote -v
```

Periksa branch aktif:

```bash
git branch --show-current
```

Jika branch yang digunakan adalah `main`, lakukan:

```bash
git add .
git commit -m "Prepare BinaLib deployment"
git push -u origin main
```

Jika repository menggunakan branch lain, sesuaikan nama branch pada perintah di atas.

Jika remote `origin` sudah tersedia, jangan menambahkan remote yang sama lagi.

### 3. Siapkan Server Sekolah

Pastikan server sekolah memiliki:

- PHP dan ekstensi yang diperlukan.
- Apache atau web server kompatibel.
- MySQL/MariaDB atau akses ke server database.
- Git.
- Direktori untuk aplikasi.
- Direktori untuk unggahan pengguna.
- Backup database dan mekanisme pemulihan.

Koordinasikan dengan administrator sekolah sebelum memasang paket, mengubah konfigurasi web server, atau membuat akun database.

### 4. Clone Repository ke Server

Masuk ke server menggunakan SSH sesuai akses yang diberikan administrator sekolah.

Contoh:

```bash
ssh USER_SERVER@ALAMAT_SERVER
```

Ganti placeholder sesuai informasi server yang sebenarnya.

Pilih direktori aplikasi yang sudah disiapkan administrator. Contoh:

```bash
cd /var/www
```

Kemudian clone repository:

```bash
sudo git clone https://github.com/USERNAME/REPOSITORY.git binalib
```

Jika direktori tujuan tidak dapat ditulis oleh akun yang digunakan, ikuti kebijakan permission server. Hindari menjalankan seluruh proses aplikasi sebagai `root`.

Masuk ke direktori proyek:

```bash
cd binalib
```

Untuk repository privat, gunakan metode autentikasi Git yang disetujui administrator, seperti SSH deploy key atau metode lain yang aman. Jangan menyimpan personal access token langsung di URL remote atau file proyek.

### 5. Siapkan Database Server

Buat atau gunakan database yang disediakan administrator sekolah dengan nama:

```text
bls_ebook
```

Pastikan akun database memiliki hak akses yang sesuai dan aplikasi dapat terhubung ke database tersebut.

Jika database sekolah dikelola di server terpisah, gunakan host database yang diberikan administrator. Jangan menganggap database selalu berada di `localhost`.

Impor struktur database dari file SQL yang sudah diverifikasi:

```bash
mysql -u USER_DATABASE -p bls_ebook < database/binalib_ebook.sql
```

Gunakan perintah tersebut hanya jika file SQL memang tersedia dan diperlukan untuk instalasi awal. Jika database sudah berisi data operasional, jangan mengimpor ulang sebelum memastikan dampaknya.

Terapkan migration tambahan hanya sesuai kebutuhan versi aplikasi.

### 6. Atur Konfigurasi Produksi

Konfigurasikan koneksi database menggunakan mekanisme konfigurasi yang benar-benar dipakai aplikasi.

Contoh nilai konfigurasi:


BLS_DB_HOST=HOST_DATABASE
BLS_DB_PORT=3306
BLS_DB_NAME=bls_ebook
BLS_DB_USER=USER_DATABASE
BLS_DB_PASSWORD=PASSWORD_DATABASE

Jangan menyimpan password produksi di repository GitHub.

Pastikan proses PHP pada server dapat membaca variabel lingkungan tersebut. Jika menggunakan Apache, PHP-FPM, container, atau service manager, mekanisme konfigurasi variabel dapat berbeda.

### 7. Atur Permission Direktori

Pastikan web server dapat membaca source code dan menulis hanya pada direktori yang membutuhkan operasi tulis.

Direktori yang biasanya membutuhkan izin tulis adalah direktori upload dan penyimpanan sementara, jika digunakan aplikasi.

Hindari memberikan hak tulis ke seluruh direktori proyek jika tidak diperlukan.

### 8. Konfigurasikan Web Server

Administrator server perlu mengatur virtual host atau konfigurasi web server agar domain atau alamat yang ditentukan mengarah ke aplikasi.

Pastikan:

- Document root mengarah ke direktori aplikasi yang tepat.
- PHP berjalan dengan versi dan ekstensi yang sesuai.
- File internal tidak dapat diunduh secara langsung.
- HTTPS diaktifkan jika aplikasi diakses melalui jaringan yang mendukungnya.
- Permission direktori sesuai.
- Log error tersedia untuk proses debugging.

Jika sekolah menggunakan domain atau subdomain