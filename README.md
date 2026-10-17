# PantauKP - Sistem Pemantauan Magang dan KP (Laravel 12 + MySQL + Bootstrap 5)

Aplikasi bagi dosen pembimbing untuk memantau logbook, bukti kegiatan, dan laporan KP mahasiswa bimbingan.

> **Status verifikasi:** kode ini ditulis tanpa PHP/Composer/MySQL di lingkungan penulis, jadi **belum pernah dijalankan atau diuji**.
> Jalankan `php artisan test` setelah instalasi. Jika ada error, kirim pesan error lengkapnya agar bisa diperbaiki.

## 1. Persyaratan
- PHP 8.2+ (ekstensi: pdo_mysql, pdo_sqlite untuk test, mbstring, fileinfo, openssl, xml, ctype, json)
- Composer 2
- MySQL 8 / MariaDB 10.4+ (XAMPP/Laragon sudah cukup)
- Node.js **tidak diperlukan**: Bootstrap, Bootstrap Icons, dan Chart.js dimuat dari CDN (jsDelivr), jadi tidak ada `npm run build`. Perangkat perlu internet saat membuka aplikasi.

## 2. Membuat proyek dan memasang kode
Folder zip ini adalah **lapisan (overlay)** di atas proyek Laravel baru. Autentikasi dibuat sendiri (tanpa Breeze) agar tidak bentrok dengan Tailwind.

```bash
composer create-project laravel/laravel kp-monitor "^12.0"
cd kp-monitor
```
Ekstrak isi zip ke dalam folder `kp-monitor` dan **pilih Replace/Timpa** bila ditanya (file yang ditimpa: `bootstrap/app.php`, `routes/web.php`, `app/Models/User.php`, `app/Providers/AppServiceProvider.php`, migration users, `UserFactory`, `phpunit.xml`, `ExampleTest.php`).
Alternatif lewat terminal: `unzip -o kp-monitor-overlay.zip -d .` (Linux/macOS) atau `tar -xf kp-monitor-overlay.zip` (Windows 10+).

## 3. Konfigurasi `.env`
Salin isi `.env.kp-snippet` ke `.env` (ganti baris yang sama, terutama bagian `DB_*`). Lalu:
```bash
php artisan key:generate   # jika APP_KEY masih kosong
```

## 4. Database - pilih SALAH SATU

**Opsi A (disarankan): migration + seeder**
1. Buat database kosong: `CREATE DATABASE kp_monitor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
2. `php artisan migrate:fresh --seed`

**Opsi B: import SQL**
1. Buat database kosong `kp_monitor` (phpMyAdmin: *New*, collation `utf8mb4_unicode_ci`).
2. Pilih database itu, tab *Import*, pilih `database/kp_monitor.sql` (atau `mysql -u root kp_monitor < database/kp_monitor.sql`).
3. `php artisan migrate` (hanya membuat tabel bawaan Laravel yang belum ada; tabel aplikasi sudah tercatat di tabel `migrations`).

Jika login akun demo gagal pada Opsi B (hash password pada file SQL tidak cocok), jalankan:
`php artisan tinker --execute="App\Models\User::query()->get()->each(fn(\$u) => \$u->update(['password' => 'password']));"`

## 5. Menjalankan
```bash
php artisan serve
```
Buka http://127.0.0.1:8000

## 6. Akun demo (data fiktif; password semua: `password`, **wajib diganti** bila dipakai sungguhan)
| Peran | Email | Catatan |
|---|---|---|
| **Admin** | admin@kp.test | kelola pengguna (tambah/ubah/reset password/nonaktifkan/hapus) |
| Dosen | dosen@kp.test | membimbing 5 mahasiswa |
| Dosen 2 | dosen2@kp.test | membimbing 1 mahasiswa (untuk menguji pembatasan akses) |
| Mahasiswa | mahasiswa1@kp.test ... mahasiswa6@kp.test | mahasiswa1-5 milik Dosen 1, mahasiswa6 milik Dosen 2 |

Kondisi data demo: Andi (mahasiswa1) punya logbook menunggu dan perlu revisi; Siti punya 2 logbook menunggu + 1 draft + laporan menunggu; Budi punya laporan perlu revisi; Dewi (selesai) laporan disetujui setelah 2 versi; Rizky belum mulai magang.

Akun admin baru: `php artisan app:buat-admin "Nama" email`. Akun dosen baru dibuat lewat `php artisan app:buat-dosen "Nama" email@kampus.ac.id NIDN`. Registrasi publik tidak ada. Mahasiswa dibuat oleh dosen lewat menu *Mahasiswa Bimbingan > Tambah Mahasiswa*.

## 6b. Role Admin, tema, dan sidebar
- **Admin** (`/admin/...`): dashboard, kelola pengguna (cari/filter peran & status, tambah dosen/mahasiswa/admin, ubah data, pindah dosen pembimbing, reset password, aktif/nonaktif, hapus), dan riwayat aktivitas seluruh sistem. Akun nonaktif tidak bisa login dan langsung dikeluarkan. Admin tidak bisa menghapus/menonaktifkan akun sendiri; dosen yang masih punya bimbingan/riwayat pemeriksaan tidak bisa dihapus (nonaktifkan saja).
- **Ganti password**: semua role lewat *Profil & Password* (juga dari menu avatar).
- **Light / Dark / System**: tombol matahari/bulan di topbar; pilihan tersimpan di browser dan tanpa kedip saat halaman dimuat.
- **Sidebar**: tombol lingkaran di header sidebar untuk ciutkan/perluas (tersimpan); saat ciut, arahkan kursor untuk melebarkannya sementara. Di layar kecil sidebar berupa panel geser.

**Upgrade dari versi sebelumnya (database sudah ada):** timpa file overlay, lalu `php artisan migrate` (menambah kolom `is_active` dan role admin), kemudian `php artisan app:buat-admin "Nama" email@contoh.ac.id`. Atau import `database/kp_monitor_upgrade_admin.sql` sekali saja (membuat `admin@kp.test`).

## 7. Pengujian
```bash
php artisan test
```
Test memakai SQLite in-memory (lihat `phpunit.xml`), **tidak menyentuh database MySQL Anda**. Cakupan: login/logout, pembatasan role, isolasi dosen/mahasiswa (IDOR), alur logbook (draft, kirim, setuju, revisi, kirim ulang), riwayat pemeriksaan, lampiran (tipe/ukuran/otorisasi unduh), laporan PDF berversi, status tidak sah, audit log tidak bisa diubah.

## 8. Struktur utama
```
app/Enums            status logbook, laporan, role
app/Models           12 model + relasi
app/Policies         InternshipPolicy, LogbookPolicy, InternshipReportPolicy (aturan akses di backend)
app/Services         LogbookService, ReportService, StudentService, ActivityLogger (logika bisnis + transaksi)
app/Http/Controllers AuthController, ProfileController, FileController, ActivityController, Dosen/*, Mahasiswa/*
app/Http/Requests    validasi (LogbookRequest, ReviewRequest, UploadReportRequest, StoreStudentRequest)
database/migrations  skema; database/seeders/demo-data.json = sumber data demo (seeder dan SQL)
resources/views      layout, komponen Blade (status-badge, stat-card, empty-state, input), halaman per role
storage/app/private  file privat (lampiran dan laporan); hanya bisa diunduh lewat /files/... dengan otorisasi
```

## 9. Aturan bisnis ringkas
- Logbook: Draft -> Menunggu -> Disetujui / Perlu revisi -> (edit + kirim ulang) -> Menunggu. Menunggu/Disetujui terkunci untuk mahasiswa.
- Setiap pemeriksaan menyimpan **snapshot isi logbook** + komentar, jadi riwayat tidak hilang setelah revisi.
- Laporan: hanya PDF maks 10 MB; tiap unggahan = versi baru; hanya versi terbaru yang diperiksa; feedback melekat pada versinya.
- Progres = logbook disetujui / target logbook (default: jumlah hari kerja periode magang), maks 100%.
- Aktivitas dicatat di `activity_logs`; tidak ada route ubah/hapus dan model memblokir update/delete.

## 10. Catatan keamanan
Lampiran disimpan di disk privat; CSRF aktif; password di-hash bcrypt; kolom `role` tidak mass-assignable; login dibatasi 10x/menit. Untuk produksi: set `APP_DEBUG=false`, ganti semua password demo, gunakan HTTPS, dan sesuaikan `upload_max_filesize`/`post_max_size` di php.ini (minimal 12M).
