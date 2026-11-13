-- database/seed.sql
-- Data Inisial Kategori & Produk Aksara Mahasiswa Berdampak Unsulbar

-- 1. Kategori
INSERT INTO categories (id, name, slug, description, badge_color, icon) VALUES
(1, 'Olahan Pangan Jamur', 'olahan-pangan-jamur', 'Aneka pangan bergizi, gurih, dan higienis hasil olahan inovasi jamur tiram segar binaan mahasiswa Unsulbar.', 'emerald', 'utensils'),
(2, 'Produk Pupuk Organik', 'pupuk-organik-limbah', 'Pupuk organik hayati dan pupuk padat dekomposisi ramah lingkungan dari pemanfaatan limbah baglog jamur.', 'amber', 'leaf'),
(3, 'Produk Paket Budidaya', 'produk-paket-budidaya', 'Media tanam baglog berkualitas dan bibit unggul jamur tiram putih untuk budidaya produktif.', 'sky', 'sprout');

-- 2. Daftar 9 Produk Resmi
INSERT INTO products (id, category_id, name, slug, description, price, stock, unit, image_url, is_featured, is_active) VALUES
-- a. Bagian Olahan Pangan
(1, 1, 'Nugget Jamur Tiram', 'nugget-jamur-tiram', 'Nugget sehat vegetarian berbasis jamur tiram segar kaya serat dan protein nabati pilihan. Cocok untuk lauk sehat praktis keluarga.', 25000, 40, 'pcs', 'assets/images/products/nugget-jamur.jpeg', 1, 1),
(2, 1, 'Bakso Jamur Tiram', 'bakso-jamur-tiram', 'Bakso kenyal berbahan jamur tiram segar racikan bumbu khas Majene, nikmat gurih alami tanpa bahan pengawet sintetis.', 25000, 35, 'pcs', 'assets/images/products/bakso-jamur.jpeg', 1, 1),
(3, 1, 'Kaldu Jamur Tiram', 'kaldu-jamur-tiram', 'Penyedap rasa alami bubuk kaldu jamur tiram non-MSG. Menambah cita rasa gurih lezat dan sehat pada segala masakan.', 10000, 50, 'pcs', 'assets/images/products/kaldu-jamur.jpeg', 0, 1),
(4, 1, 'Rendang Jamur Tiram', 'rendang-jamur-tiram', 'Olahan rendang jamur tiram dengan rempah autentik Nusantara, tekstur empuk meresap bumbu kaya rasa, siap saji.', 35000, 25, 'pcs', 'assets/images/products/rendang-jamur.jpeg', 1, 1),
(5, 1, 'Jamur Crispy Gurih Renyah', 'jamur-crispy-gurih', 'Camilan keripik jamur tiram renyah dengan taburan bumbu gurih khas. Renyah tahan lama dan rendah kolesterol.', 15000, 60, 'pcs', 'assets/images/products/jamur-crispy.jpeg', 1, 1),

-- b. Produk Pupuk Organik
(6, 2, 'Pupuk Organik Cair (POC) Nutrisi Jamur', 'poc-pupuk-organik-cair', 'Pupuk Organik Cair (POC) kaya mikroorganisme efektif penyubur tanaman. Memacu pertumbuhan akar, daun, dan tunas bunga secara alami.', 30000, 45, 'karung', 'assets/images/products/poc.jpeg', 1, 1),
(7, 2, 'Pupuk Padat Kompos Organik Baglog', 'pupuk-padat-kompos-baglog', 'Pupuk kompos padat hasil fermentasi aerobik limbah baglog jamur tiram yang diperkaya trichoderma untuk memperbaiki kesuburan tanah.', 38000, 50, 'karung', 'assets/images/products/pupuk-padat.jpeg', 1, 1),

-- c. Produk Paket Budidaya
(8, 3, 'Baglog Jamur Tiram Siap Tumbuh', 'baglog-jamur-tiram-siap-tumbuh', 'Media tanam baglog jamur tiram putih berkualitas dengan miselium penuh merata, siap dipelihara dan dipanen di rumah atau kumbung.', 6000, 150, 'banglog', 'assets/images/products/Baglog-Jamur-Tiram.png', 1, 1),
(9, 3, 'Bibit Jamur Tiram Unggul F2', 'bibit-jamur-tiram-unggul-f2', 'Bibit jamur tiram murni turunan F2 hasil inokulasi laboratorium terpadu Unsulbar. Daya tumbuh cepat, viabilitas tinggi, dan tahan kontaminasi.', 6000, 500, 'pcs', 'assets/images/products/bibit-jamur-tiram.jpeg', 1, 1);

-- 3. Isi Tabel Khusus produk_budidaya
INSERT INTO produk_budidaya (id, product_id, name, slug, description, price, stock, unit, image_url, is_featured, is_active) VALUES
(1, 8, 'Baglog Jamur Tiram Siap Tumbuh', 'baglog-jamur-tiram-siap-tumbuh', 'Media tanam baglog jamur tiram putih berkualitas dengan miselium penuh merata, siap dipelihara dan dipanen di rumah atau kumbung.', 6000, 150, 'banglog', 'assets/images/products/Baglog-Jamur-Tiram.png', 1, 1),
(2, 9, 'Bibit Jamur Tiram Unggul F2', 'bibit-jamur-tiram-unggul-f2', 'Bibit jamur tiram murni turunan F2 hasil inokulasi laboratorium terpadu Unsulbar. Daya tumbuh cepat, viabilitas tinggi, dan tahan kontaminasi.', 6000, 500, 'pcs', 'assets/images/products/bibit-jamur-tiram.jpeg', 1, 1);

-- 4. Pengguna Awal (Admin & Pelanggan Mahasiswa)
-- Password default admin: admin123
-- Password default mahasiswa: password123
INSERT INTO users (id, full_name, email, phone, address, maps_url, password_hash, role) VALUES
(1, 'Administrator Aksara', 'admin@unsulbar.ac.id', '082190001234', 'Gedung Rektorat Lt. 2 Universitas Sulawesi Barat, Majene', 'https://www.google.com/maps/search/?api=1&query=-3.5284459,118.9654162', '$2y$10$0cQ3HK4nceqrgRPWZUyZI.dHbJHQS.at5qDFe0OU1wiIuv8WrV9WK', 'admin'),
(2, 'Andi Siti Nurhaliza', 'mahasiswa@unsulbar.ac.id', '081245678910', 'Asrama Mahasiswa Putri Unsulbar Kamar B-12, Majene', 'https://www.google.com/maps/search/?api=1&query=-3.531200,118.967800', '$2y$10$mrJeRKavP8XCJ4cIsEBCfusSAdyfFiIuc4cQq5I0jZVlWiwdTyz16', 'customer');

-- 5. Contoh Transaksi Awal dengan Maps dan Rincian Pembayaran
INSERT INTO orders (id, order_code, user_id, customer_name, customer_phone, customer_address, maps_url, delivery_lat, delivery_lng, notes, payment_method, payment_status, order_status, payment_proof, subtotal, shipping_fee, total_amount, wa_sent_at) VALUES
(1, 'AKS-20261109-1001', 2, 'Andi Siti Nurhaliza', '081245678910', 'Asrama Mahasiswa Unsulbar Gedung B No. 12, Majene', 'https://www.google.com/maps/search/?api=1&query=-3.5312,118.9678', -3.5312, 118.9678, 'Titip di resepsionis asrama jika belum di kamar', 'QRIS', 'PAID', 'PROCESSED', 'assets/images/branding/portal-perpus.jpeg', 50000, 5000, 55000, CURRENT_TIMESTAMP),
(2, 'AKS-20261109-2002', 2, 'Muh. Fadhil Ramadhan', '085299887766', 'Fakultas Teknik Gedung Kuliah Terpadu Lantai 2', 'https://www.google.com/maps/search/?api=1&query=-3.5284,118.9654', -3.5284, 118.9654, 'Hubungi via WA saat kurir tiba di lobi', 'COD', 'UNPAID', 'SHIPPING', NULL, 30000, 5000, 35000, CURRENT_TIMESTAMP);

INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, subtotal) VALUES
(1, 1, 'Nugget Jamur Tiram', 25000, 2, 50000),
(2, 6, 'Pupuk Organik Cair (POC) Nutrisi Jamur', 30000, 1, 30000);

INSERT INTO order_logs (order_id, status_from, status_to, notes) VALUES
(1, NULL, 'PENDING', 'Pesanan dibuat. Bukti bayar QRIS berhasil diunggah.'),
(1, 'PENDING', 'PROCESSED', 'Pembayaran QRIS telah diverifikasi. Pesanan sedang disiapkan tim.'),
(2, NULL, 'PENDING', 'Pesanan baru metode COD dibuat.'),
(2, 'PENDING', 'SHIPPING', 'Pesanan diserahkan ke kurir menuju lokasi Fakultas Teknik.');
