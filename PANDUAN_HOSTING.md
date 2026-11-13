# 🚀 PANDUAN LENGKAP PENYEBARAN (DEPLOYMENT) KE HOSTING
### Website E-Commerce Aksara Mahasiswa Berdampak - Universitas Sulawesi Barat

Dokumen ini berisi panduan langkah-demi-langkah untuk mengunggah dan menghubungkan website Aksara ke layanan hosting web (cPanel, Hostinger, Niagahoster, DomaiNesia, IDCloudHost, atau VPS).

---

## 📋 DAFTAR FITUR TERBARU YANG TELAH DISEMPURNAKAN
1. **Database Relasional & Multi-Driver**:
   - Mendukung **SQLite** (siap pakai tanpa konfigurasi database tambahan) dan **MySQL / MariaDB** (standar produksi cPanel).
   - Kategori ke-3: **Produk Paket Budidaya** (`produk-paket-budidaya`).
   - Tabel khusus: `produk_budidaya` (Baglog & Bibit Jamur Tiram).
2. **Katalog 9 Produk Resmi**:
   - Olahan Pangan: *Nugget Jamur Tiram (Rp 25.000)*, *Bakso Jamur Tiram (Rp 25.000)*, *Kaldu Jamur Tiram (Rp 10.000)*, *Rendang Jamur Tiram (Rp 35.000)*, *Jamur Crispy Gurih (Rp 15.000)*.
   - Pupuk Organik: *POC Cair (Rp 30.000)*, *Pupuk Padat Kompos (Rp 38.000)*.
   - Paket Budidaya: *Baglog Jamur (Rp 6.000)*, *Bibit Jamur Tiram (Rp 600)*.
3. **Penyelarasan Gambar Otomatis**:
   - Semua gambar produk di folder `public/assets/images/products/` telah diselaraskan ke database.
4. **Fitur Alamat Pengantaran & Navigasi Google Maps Kurir**:
   - Tombol **Atur Alamat** di Header dan halaman Keranjang.
   - Fitur **Deteksi GPS Otomatis** (Browser Geolocation) yang otomatis menghasilkan tautan Google Maps navigasi.
   - Tautan Google Maps otomatis disertakan dalam draf WhatsApp kurir sehingga kurir bisa langsung klik rute peta navigasi.
5. **Rincian Pembayaran Lengkap & Bukti Bayar**:
   - Rincian belanja transparan (Subtotal, Biaya Kurir Kampus Rp 5.000, Biaya Layanan Gratis, Total Tagihan).
   - Pilihan metode: **COD Tunai**, **QRIS Dinamis**, dan **Transfer Bank** (BRI, BSI, Mandiri dengan tombol salin rekening).
   - Form unggah file bukti pembayaran (JPG, PNG, PDF) saat checkout, pada modal sukses, maupun di halaman pelacakan (*Order Tracking*).
6. **Autentikasi & Checkout Real (Bukan Prototype)**:
   - Sesi terisolasi, enkripsi password `PASSWORD_BCRYPT`, transaksi stok atomik di database.
   - Panel khusus Administrator: `?page=admin-orders` (Login sebagai `admin@unsulbar.ac.id` / password `admin123`) untuk memantau semua pesanan masuk, melihat lokasi maps pembeli, memverifikasi bukti transfer, dan mengubah status pesanan.

---

## 🛠️ OPSI 1: DEPLOYMENT MENGGUNAKAN CPANEL (PALING BANYAK DIGUNAKAN)

### Langkah 1: Siapkan File ZIP Project
1. Kompres seluruh isi folder `project-aksara-web` menjadi file `.zip` (misal `project-aksara-web.zip`).
   *(Pastikan file `.htaccess`, `.env`, folder `app`, `database`, `public`, dan `views` ikut terkompres)*.

### Langkah 2: Unggah ke File Manager cPanel
1. Masuk ke **cPanel** akun hosting Anda.
2. Buka menu **File Manager**.
3. Masuk ke direktori root website Anda:
   - Jika website adalah domain utama: masuk ke folder `public_html/`.
   - Jika subdomain / addon domain: masuk ke folder domain terkait (misal `public_html/aksara/`).
4. Klik tombol **Upload** di bagian atas, pilih file `.zip` Anda, lalu tunggu hingga 100% selesai.
5. Klik kanan file `.zip` di File Manager, pilih **Extract**.

### Langkah 3: Konfigurasi Database MySQL di cPanel
*(Catatan: Jika ingin langsung jalan tanpa buat MySQL, website sudah otomatis berjalan dengan driver SQLite lokal. Jika ingin menggunakan MySQL cPanel, ikuti langkah ini:)*

1. Di cPanel, buka menu **MySQL Database Wizard**.
2. **Step 1 - Buat Nama Database**:
   - Misal: `u1234567_aksara` -> Klik *Next Step*.
3. **Step 2 - Buat User Database & Password**:
   - Misal username: `u1234567_admin`.
   - Masukkan password yang kuat (simpan password ini).
   - Klik *Create User*.
4. **Step 3 - Hak Akses**:
   - Centang **ALL PRIVILEGES** -> Klik *Make Changes*.
5. **Step 4 - Impor Data SQL**:
   - Kembali ke cPanel, buka **phpMyAdmin**.
   - Pilih database yang baru dibuat di bilah kiri.
   - Klik tab **Import** di bagian atas.
   - Klik tombol **Choose File**, pilih file:
     `database/aksara_mysql.sql`
   - Klik **Go** / **Kirim** di bagian bawah.
   - Seluruh tabel, kategori, 9 produk, dan data awal akan otomatis terimpor.

### Langkah 4: Sesuaikan File `.env` di Hosting
1. Di File Manager cPanel, cari file `.env` di root project.
   *(Jika file tidak terlihat, klik icon **Settings** di pojok kanan atas File Manager, centang **Show Hidden Files (dotfiles)**)*.
2. Klik kanan file `.env` -> pilih **Edit**.
3. Ubah bagian berikut sesuai data hosting Anda:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://nama-domain-anda.com

   # Ubah driver ke 'mysql'
   DB_DRIVER=mysql

   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=u1234567_aksara
   DB_USERNAME=u1234567_admin
   DB_PASSWORD=PasswordDatabaseAnda

   # Nomor WhatsApp Kurir Kampus (Ganti dengan nomor kurir aktif)
   COURIER_PHONE=6282190001234
   ```
4. Klik **Save Changes**.

---

## 🔒 KEAMANAN & HAK AKSES PERMISSION DIREKTORI
Pastikan izin akses direktori (CHMOD) di cPanel adalah sebagai berikut:
- Folder umum: `755`
- File umum: `644`
- Folder penyimpanan upload bukti bayar: `public/uploads/proofs/` -> `755` atau `775` (dapat ditulis web server).
- Folder sesi: `database/sessions/` -> `755` atau `775`.

---

## 🔑 AKUN DEFAULT UNTUK PENGUJIAN DI HOSTING
1. **Akun Administrator (Untuk cek pesanan masuk & bukti bayar)**:
   - Email: `admin@unsulbar.ac.id`
   - Password: `admin123`
   - URL Akses Panel Admin: `https://domain-anda.com/?page=admin-orders`
2. **Akun Pelanggan Contoh**:
   - Email: `mahasiswa@unsulbar.ac.id`
   - Password: `password123`

---

## 💬 MENGUBAH NOMOR WHATSAPP KURIR
Untuk mengubah nomor WhatsApp kurir tujuan pengantaran:
- Cukup buka file `.env` dan perbarui nilai:
  `COURIER_PHONE=628xxxxxxxxxx` (Gunakan format awalan 62 tanpa tanda + atau spasi).
