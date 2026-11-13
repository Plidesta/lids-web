# Website E-Commerce & Pemesanan "Aksara Mahasiswa Berdampak"
**Universitas Sulawesi Barat (Unsulbar)**  
*Fokus Produk: Olahan Pangan Jamur Tiram & Pupuk Organik Limbah Jamur Tiram*

---

## 🌟 Tentang Proyek
Platform digital resmi untuk mendukung program hilirisasi inovasi agro-industri sirkular mahasiswa Universitas Sulawesi Barat. Sistem ini memfasilitasi pemesanan produk pangan olahan jamur tiram (Keripik Krispi, Nugget, Abon, Baglog DIY) serta pupuk organik kompos hayati dan pupuk cair (POC) hasil dekomposisi limbah baglog.

## 🚀 Fitur Utama Sesuai PRD
1. **Identitas & Branding Resmi Unsulbar**:
   - Palet warna: Merah Marun Unsulbar (`#7B1113`), Kuning Emas (`#EAB308`), dan Hijau Organik (`#10B981`).
   - Switch Mode Gelap/Terang (Dark/Light mode switch) tersimpan di local storage.
   - Efek hover interaktif di seluruh elemen tombol, tautan, dan kartu produk.
2. **Tab Dashboard (Beranda)**:
   - Header & Navigasi Atas lengkap dengan logo kegiatan, identitas Unsulbar, dan status autentikasi.
   - Visi & definisi sirkular ekonomi program Aksara Mahasiswa Berdampak.
   - Katalog produk unggulan dengan badge stok real-time.
   - Galeri dokumentasi kegiatan di kumbung dan laboratorium Unsulbar.
   - Spanduk & promo bundling menarik (Diskon 20% & Gratis Ongkir Kampus).
3. **Tab Pemesanan (Katalog Pintar)**:
   - Copywriting persuasif penarik minat pembeli.
   - Pencarian cerdas berbasis teks (live search tanpa reload).
   - Filter multi-kategori (Olahan Pangan Jamur / Pupuk Limbah Jamur).
   - Filter rentang harga dinamis dan saklar ketersediaan stok.
   - Kartu produk interaktif dengan tombol "+ Keranjang" dan "Beli Sekarang".
   - Notifikasi animasi pop-up (Toast Animation) instan saat produk ditambahkan.
4. **Tab Keranjang & Checkout (Cart & Checkout)**:
   - Manajemen keranjang reaktif: tambah/kurang kuantitas, hapus item, kalkulasi otomatis.
   - Validasi harga dan stok **murni di sisi server (Server-Side Price & Stock Validation)** dengan database transaction atomik.
   - Pilihan metode bayar otomatis: QRIS Dinamis, COD (Bayar di Tempat), dan Transfer Bank.
   - **Integrasi WhatsApp Kurir Otomatis**: Pesanan langsung diterjemahkan ke draf pesan WhatsApp siap kirim ke nomor kurir kampus via tautan `wa.me`.
5. **Tab Kontak & Informasi Tim**:
   - Kontak resmi telepon, email, dan integrasi Google Maps lokasi kampus Padhang-Padhang Majene.
   - Profil himpunan mahasiswa multidisiplin yang berkolaborasi dalam tim.
   - Video dokumentasi proyek responsif (embedded player).
6. **Tab Pelacakan Status Pesanan (Order Tracking)**:
   - Pelacakan transparan 4 tahap: Menunggu Pembayaran → Diproses → Dalam Pengiriman → Selesai.
   - Pencarian berdasarkan kode pesanan resmi (contoh: `#AKS-20261028-1001`).
   - Riwayat transaksi akun pengguna yang sedang login.
7. **Standar Keamanan OWASP**:
   - Password Hashing Bcrypt (Cost 12).
   - 100% Prepared Statements via PDO SQLite/MySQL (Bebas SQL Injection).
   - Sanitasi output XSS ketat dengan `htmlspecialchars()` dan header CSP.
   - Proteksi Anti-CSRF Token berbasis sesi.

---

## 🛠️ Persyaratan & Cara Menjalankan

### Cara 1: Menggunakan PHP Built-in Server (Instan)
Buka terminal di root folder proyek ini dan jalankan perintah:
```bash
php -S localhost:8000 -t public
```
Kemudian buka browser Anda dan akses:
👉 **`http://localhost:8000`**

### Cara 2: Menggunakan XAMPP Apache
1. Pindahkan folder `AKSARA WEBSITE` ke direktori `C:\xampp\htdocs\aksara` (atau gunakan VirtualHost).
2. Jalankan Apache di panel kontrol XAMPP.
3. Akses via browser: `http://localhost/aksara/public/`.

---

## 👥 Akun Contoh untuk Pengujian
| Role | Email | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin@unsulbar.ac.id` | `admin123` |
| **Mahasiswa / Pelanggan** | `mahasiswa@unsulbar.ac.id` | `user123` |

### Kode Pesanan Contoh untuk Fitur Tracking
- `AKS-20261028-1001` (Menunggu Pembayaran)
- `AKS-20261028-2002` (Diproses)
- `AKS-20261028-3003` (Dalam Pengiriman)
- `AKS-20261028-4004` (Selesai)
