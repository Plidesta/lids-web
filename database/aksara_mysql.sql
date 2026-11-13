-- database/aksara_mysql.sql
-- Skema & Data Database MySQL / MariaDB untuk Deployment Hosting cPanel
-- E-Commerce Aksara Mahasiswa Berdampak Universitas Sulawesi Barat

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS order_logs;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS produk_budidaya;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- 1. Tabel categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    badge_color VARCHAR(30) DEFAULT 'emerald',
    icon VARCHAR(50) DEFAULT 'tag',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(12, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 50,
    unit VARCHAR(50) DEFAULT 'bungkus',
    image_url TEXT,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel khusus produk_budidaya
CREATE TABLE produk_budidaya (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(12, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 100,
    unit VARCHAR(50) DEFAULT 'pcs',
    image_url TEXT,
    is_featured TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(50),
    address TEXT,
    maps_url TEXT,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(30) DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_code VARCHAR(50) NOT NULL UNIQUE,
    user_id INT,
    customer_name VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(50) NOT NULL,
    customer_address TEXT NOT NULL,
    maps_url TEXT,
    delivery_lat DECIMAL(10, 8),
    delivery_lng DECIMAL(11, 8),
    delivery_distance DECIMAL(8, 2) DEFAULT 5.0,
    notes TEXT,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(50) DEFAULT 'UNPAID',
    order_status VARCHAR(50) DEFAULT 'PENDING',
    payment_proof TEXT,
    payment_proof_uploaded_at DATETIME,
    subtotal DECIMAL(12, 2) NOT NULL,
    shipping_fee DECIMAL(12, 2) NOT NULL DEFAULT 15000,
    service_fee DECIMAL(12, 2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(12, 2) NOT NULL,
    wa_sent_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel order_items
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    unit_price DECIMAL(12, 2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabel order_logs
CREATE TABLE order_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status_from VARCHAR(50),
    status_to VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indeks
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_orders_code ON orders(order_code);
CREATE INDEX idx_orders_user ON orders(user_id);

-- DATA AWAL (SEEDS)
INSERT INTO categories (id, name, slug, description, badge_color, icon) VALUES
(1, 'Olahan Pangan Jamur', 'olahan-pangan-jamur', 'Aneka pangan bergizi, gurih, dan higienis hasil olahan inovasi jamur tiram segar binaan mahasiswa Unsulbar.', 'emerald', 'utensils'),
(2, 'Produk Pupuk Organik', 'pupuk-organik-limbah', 'Pupuk organik hayati dan pupuk padat dekomposisi ramah lingkungan dari pemanfaatan limbah baglog jamur.', 'amber', 'leaf'),
(3, 'Produk Paket Budidaya', 'produk-paket-budidaya', 'Media tanam baglog berkualitas dan bibit unggul jamur tiram putih untuk budidaya produktif.', 'sky', 'sprout');

INSERT INTO products (id, category_id, name, slug, description, price, stock, unit, image_url, is_featured, is_active) VALUES
(1, 1, 'Nugget Jamur Tiram', 'nugget-jamur-tiram', 'Nugget sehat vegetarian berbasis jamur tiram segar kaya serat dan protein nabati pilihan.', 25000, 40, 'pcs', 'assets/images/products/nugget-jamur.jpeg', 1, 1),
(2, 1, 'Bakso Jamur Tiram', 'bakso-jamur-tiram', 'Bakso kenyal berbahan jamur tiram segar racikan bumbu khas Majene, nikmat gurih alami tanpa bahan pengawet sintetis.', 25000, 35, 'pcs', 'assets/images/products/bakso-jamur.jpeg', 1, 1),
(3, 1, 'Kaldu Jamur Tiram', 'kaldu-jamur-tiram', 'Penyedap rasa alami bubuk kaldu jamur tiram non-MSG. Menambah cita rasa gurih lezat dan sehat pada segala masakan.', 10000, 50, 'pcs', 'assets/images/products/kaldu-jamur.jpeg', 0, 1),
(4, 1, 'Rendang Jamur Tiram', 'rendang-jamur-tiram', 'Olahan rendang jamur tiram dengan rempah autentik Nusantara, tekstur empuk meresap bumbu kaya rasa, siap saji.', 35000, 25, 'pcs', 'assets/images/products/rendang-jamur.jpeg', 1, 1),
(5, 1, 'Jamur Crispy Gurih Renyah', 'jamur-crispy-gurih', 'Camilan keripik jamur tiram renyah dengan taburan bumbu gurih khas. Renyah tahan lama dan rendah kolesterol.', 15000, 60, 'pcs', 'assets/images/products/jamur-crispy.jpeg', 1, 1),
(6, 2, 'Pupuk Organik Cair (POC) Nutrisi Jamur', 'poc-pupuk-organik-cair', 'Pupuk Organik Cair (POC) kaya mikroorganisme efektif penyubur tanaman. Memacu pertumbuhan akar, daun, dan tunas bunga secara alami.', 30000, 45, 'karung', 'assets/images/products/poc.jpeg', 1, 1),
(7, 2, 'Pupuk Padat Kompos Organik Baglog', 'pupuk-padat-kompos-baglog', 'Pupuk kompos padat hasil fermentasi aerobik limbah baglog jamur tiram yang diperkaya trichoderma untuk memperbaiki kesuburan tanah.', 38000, 50, 'karung', 'assets/images/products/pupuk-padat.jpeg', 1, 1),
(8, 3, 'Baglog Jamur Tiram Siap Tumbuh', 'baglog-jamur-tiram-siap-tumbuh', 'Media tanam baglog jamur tiram putih berkualitas dengan miselium penuh merata, siap dipelihara dan dipanen di rumah atau kumbung.', 6000, 150, 'banglog', 'assets/images/products/Baglog-Jamur-Tiram.png', 1, 1),
(9, 3, 'Bibit Jamur Tiram Unggul F2', 'bibit-jamur-tiram-unggul-f2', 'Bibit jamur tiram murni turunan F2 hasil inokulasi laboratorium terpadu Unsulbar. Daya tumbuh cepat, viabilitas tinggi, dan tahan kontaminasi.', 6000, 500, 'pcs', 'assets/images/products/bibit-jamur-tiram.jpeg', 1, 1);

INSERT INTO produk_budidaya (id, product_id, name, slug, description, price, stock, unit, image_url, is_featured, is_active) VALUES
(1, 8, 'Baglog Jamur Tiram Siap Tumbuh', 'baglog-jamur-tiram-siap-tumbuh', 'Media tanam baglog jamur tiram putih berkualitas dengan miselium penuh merata, siap dipelihara dan dipanen di rumah atau kumbung.', 6000, 150, 'banglog', 'assets/images/products/Baglog-Jamur-Tiram.png', 1, 1),
(2, 9, 'Bibit Jamur Tiram Unggul F2', 'bibit-jamur-tiram-unggul-f2', 'Bibit jamur tiram murni turunan F2 hasil inokulasi laboratorium terpadu Unsulbar. Daya tumbuh cepat, viabilitas tinggi, dan tahan kontaminasi.', 6000, 500, 'pcs', 'assets/images/products/bibit-jamur-tiram.jpeg', 1, 1);

INSERT INTO users (id, full_name, email, phone, address, maps_url, password_hash, role) VALUES
(1, 'Administrator Aksara', 'admin@unsulbar.ac.id', '082190001234', 'Gedung Rektorat Lt. 2 Universitas Sulawesi Barat, Majene', 'https://www.google.com/maps/search/?api=1&query=-3.5284459,118.9654162', '$2y$10$0cQ3HK4nceqrgRPWZUyZI.dHbJHQS.at5qDFe0OU1wiIuv8WrV9WK', 'admin'),
(2, 'Andi Siti Nurhaliza', 'mahasiswa@unsulbar.ac.id', '081245678910', 'Asrama Mahasiswa Putri Unsulbar Kamar B-12, Majene', 'https://www.google.com/maps/search/?api=1&query=-3.531200,118.967800', '$2y$10$mrJeRKavP8XCJ4cIsEBCfusSAdyfFiIuc4cQq5I0jZVlWiwdTyz16', 'customer');

SET FOREIGN_KEY_CHECKS = 1;
