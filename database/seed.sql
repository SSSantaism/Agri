-- =============================================
-- Panenly Seed Data
-- =============================================
-- Default password for all users: password123
-- Hash generated with PHP password_hash('password123', PASSWORD_DEFAULT)

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

USE panenly;

-- =============================================
-- USERS
-- =============================================
INSERT INTO users (name, email, password_hash, phone, address, role, seller_status, store_name, store_location) VALUES
-- Buyer (password: password123)
('Budi Santoso', 'buyer@panenly.com', '$2y$12$5LjSxAEGl0tsZ6q8uEXm0ecvMKPgUU0ECdv0w8EkfEODAey8AOyZO', '+62 812-3456-7890', 'Jl. Merdeka No. 123, Kecamatan Sukajadi, Kota Bandung, Jawa Barat 40161', 'buyer', NULL, NULL, NULL),

-- Sellers (password: password123)
('Pak Tono', 'seller@panenly.com', '$2y$12$5LjSxAEGl0tsZ6q8uEXm0ecvMKPgUU0ECdv0w8EkfEODAey8AOyZO', '+62 812-9876-5432', 'Jl. Apel No. 45, Batu, Malang, Jawa Timur', 'seller', 'approved', 'Toko Pak Tono', 'Batu, Malang'),
('Bu Siti', 'busiti@panenly.com', '$2y$12$5LjSxAEGl0tsZ6q8uEXm0ecvMKPgUU0ECdv0w8EkfEODAey8AOyZO', '+62 813-1234-5678', 'Jl. Wortel No. 10, Berastagi, Karo, Sumatera Utara', 'seller', 'approved', 'Kebun Bu Siti', 'Berastagi, Karo'),
('Kang Dadan', 'kangdadan@panenly.com', '$2y$12$5LjSxAEGl0tsZ6q8uEXm0ecvMKPgUU0ECdv0w8EkfEODAey8AOyZO', '+62 815-5555-6666', 'Jl. Hidroponik No. 8, Cisarua, Bogor, Jawa Barat', 'seller', 'approved', 'Hidroponik Kang Dadan', 'Cisarua, Bogor'),
('Koperasi Tani Makmur', 'koperasi@panenly.com', '$2y$12$5LjSxAEGl0tsZ6q8uEXm0ecvMKPgUU0ECdv0w8EkfEODAey8AOyZO', '+62 816-7777-8888', 'Jl. Sawah No. 1, Cianjur, Jawa Barat', 'seller', 'approved', 'Koperasi Tani Makmur', 'Cianjur, Jabar'),

-- Pending Sellers
('Kebun Sayur Lembang', 'kebun.lembang@email.com', '$2y$12$5LjSxAEGl0tsZ6q8uEXm0ecvMKPgUU0ECdv0w8EkfEODAey8AOyZO', '+62 811-2233-4455', 'Jl. Lembang No. 20, Lembang, Bandung Barat', 'seller', 'pending', 'Kebun Sayur Lembang', 'Lembang, Bandung'),
('Koperasi Tani Makmur Sentosa', 'koperasi.makmur@email.com', '$2y$12$5LjSxAEGl0tsZ6q8uEXm0ecvMKPgUU0ECdv0w8EkfEODAey8AOyZO', '+62 812-9988-7766', 'Jl. Tani Makmur No. 5, Garut, Jawa Barat', 'seller', 'pending', 'Koperasi Tani Makmur Sentosa', 'Garut, Jabar'),

-- Admin (password: password123)
('Admin Panenly', 'admin@panenly.com', '$2y$12$5LjSxAEGl0tsZ6q8uEXm0ecvMKPgUU0ECdv0w8EkfEODAey8AOyZO', '+62 800-0000-0000', 'Kantor Pusat Panenly', 'admin', NULL, NULL, NULL);

-- =============================================
-- CATEGORIES
-- =============================================
INSERT INTO categories (name, icon) VALUES
('Sayuran', '🥬'),
('Buah-buahan', '🍎'),
('Beras & Biji', '🌾'),
('Rempah', '🧄'),
('Daging', '🥩'),
('Telur & Susu', '🥚');

-- =============================================
-- PRODUCTS
-- =============================================
INSERT INTO products (seller_id, category_id, name, weight, price, original_price, stock, quality, harvest_method, description, image_url, badge, preorder_available, preorder_date, rating_avg, sold_count) VALUES
-- Pak Tono's products (seller_id = 2)
(2, 2, 'Apel Fuji Segar (Petik Langsung)', '1 kg', 35000, 45000, 45, 'Grade A (Premium)', 'Petik Tangan (Hand-picked)', 'Apel Fuji langsung dipetik dari kebun kami di dataran tinggi Batu, Malang. Memiliki tekstur yang renyah dengan tingkat kemanisan yang tinggi dan kandungan air yang melimpah. Bebas dari pestisida berlebih dan aman dikonsumsi langsung setelah dicuci.', 'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?q=80&w=600&auto=format&fit=crop', NULL, 1, '2026-05-15', 4.9, 210),

(2, 1, 'Tomat Cherry Organik Segar', '500 gram', 15000, 20000, 80, 'Organik', 'Petik Tangan', 'Tomat cherry organik yang ditanam tanpa pestisida kimia. Rasanya manis alami, cocok untuk salad, pasta, atau dimakan langsung sebagai camilan sehat.', 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?q=80&w=600&auto=format&fit=crop', 'Diskon 25%', 0, NULL, 4.8, 120),

-- Bu Siti's products (seller_id = 3)
(3, 1, 'Wortel Manis Berastagi', '1 kg', 12000, NULL, 120, 'Grade A', 'Panen Mesin', 'Wortel manis khas Berastagi, Sumatera Utara. Dikenal dengan rasa manisnya yang alami dan warna oranye cerah. Cocok untuk jus, sup, atau tumisan.', 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?q=80&w=600&auto=format&fit=crop', 'Terlaris', 0, NULL, 4.9, 340),

-- Kang Dadan's products (seller_id = 4)
(4, 1, 'Bayam Hijau Hidroponik', '250 gram', 8000, 10000, 60, 'Hidroponik Premium', 'Petik Tangan', 'Bayam hijau segar hasil budidaya hidroponik. Lebih bersih, bebas tanah, dan nutrisi lebih terjaga. Daun lebar dan batang renyah, sempurna untuk tumis bawang atau sayur bening.', 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?q=80&w=600&auto=format&fit=crop', NULL, 0, NULL, 4.7, 85),

-- Koperasi Tani Makmur's products (seller_id = 5)
(5, 3, 'Beras Merah Organik Premium', '2 kg', 45000, 50000, 200, 'Organik Bersertifikasi', 'Panen Tradisional', 'Beras merah organik premium dari sawah-sawah Cianjur. Ditanam secara tradisional tanpa pupuk kimia. Kaya serat, vitamin B, dan mineral. Cocok untuk diet sehat dan penderita diabetes.', 'https://images.unsplash.com/photo-1586201375761-83865001e8ac?q=80&w=600&auto=format&fit=crop', NULL, 0, NULL, 4.6, 500),

-- Additional products
(2, 2, 'Jeruk Segar Batu Malang', '1 kg', 25000, 30000, 30, 'Grade A', 'Petik Tangan', 'Jeruk manis segar langsung dari kebun di Batu, Malang. Kulit tipis, banyak air, dan rasa manis segar alami.', 'https://images.unsplash.com/photo-1547514701-42782101795e?q=80&w=600&auto=format&fit=crop', 'Diskon 17%', 0, NULL, 4.7, 95),

(3, 1, 'Brokoli Segar Premium', '500 gram', 18000, NULL, 40, 'Grade A', 'Petik Tangan', 'Brokoli segar premium dengan kuntum padat dan hijau segar. Kaya antioksidan dan vitamin C. Cocok untuk ditumis, dikukus, atau dijadikan sup.', 'https://images.unsplash.com/photo-1459411552884-841db9b3cc2a?q=80&w=600&auto=format&fit=crop', NULL, 0, NULL, 4.8, 67),

(4, 1, 'Selada Keriting Hidroponik', '200 gram', 6000, NULL, 90, 'Hidroponik', 'Petik Tangan', 'Selada keriting hidroponik, daun renyah dan segar. Sempurna untuk salad, burger, atau sandwich. Dicuci dan langsung siap makan.', 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?q=80&w=600&auto=format&fit=crop', NULL, 0, NULL, 4.5, 150);

-- =============================================
-- SAMPLE CART ITEMS (Buyer: Budi - id=1)
-- =============================================
INSERT INTO cart_items (user_id, product_id, quantity) VALUES
(1, 1, 2);  -- 2 kg Apel Fuji

-- =============================================
-- SAMPLE ORDERS
-- =============================================
INSERT INTO orders (order_number, buyer_id, seller_id, recipient_name, recipient_phone, shipping_address, payment_method, subtotal, shipping_cost, total, status, tracking_number, created_at) VALUES
('PNL-20260524-0001', 1, 2, 'Budi Santoso', '+62 812-3456-7890', 'Jl. Merdeka No. 123, Kecamatan Sukajadi, Kota Bandung, Jawa Barat 40161', 'qris', 70000, 15000, 85000, 'shipped', 'PNL-8923749823', '2026-05-24 20:15:00'),
('PNL-20260510-0002', 1, 3, 'Budi Santoso', '+62 812-3456-7890', 'Jl. Merdeka No. 123, Kecamatan Sukajadi, Kota Bandung, Jawa Barat 40161', 'transfer', 12000, 15000, 27000, 'completed', 'PNL-7712345678', '2026-05-10 10:30:00');

-- =============================================
-- SAMPLE ORDER ITEMS
-- =============================================
INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES
(1, 1, 2, 35000),  -- 2 kg Apel Fuji in Order 1
(2, 3, 1, 12000);  -- 1 kg Wortel in Order 2

-- =============================================
-- SAMPLE ORDER TRACKING
-- =============================================
INSERT INTO order_tracking (order_id, status_text, location, created_at) VALUES
-- Order 1 tracking
(1, 'Pesanan Dibuat', NULL, '2026-05-24 20:15:00'),
(1, 'Pesanan diproses penjual', 'Batu, Malang', '2026-05-25 08:30:00'),
(1, 'Pesanan dibawa oleh kurir', 'Gudang Transit Bandung', '2026-05-25 10:00:00'),
(1, 'Kurir sedang menuju lokasi Anda', 'Jl. Merdeka Indah', '2026-05-25 13:45:00'),
-- Order 2 tracking
(2, 'Pesanan Dibuat', NULL, '2026-05-10 10:30:00'),
(2, 'Pesanan diproses penjual', 'Berastagi, Karo', '2026-05-10 14:00:00'),
(2, 'Pesanan dibawa oleh kurir', 'Gudang Transit Medan', '2026-05-11 08:00:00'),
(2, 'Paket telah diterima', 'Bandung', '2026-05-12 11:00:00');

-- =============================================
-- SAMPLE REVIEWS
-- =============================================
INSERT INTO reviews (product_id, user_id, order_id, rating, comment, created_at) VALUES
(1, 1, 1, 5, 'Buahnya sangat segar dan manis. Pengirimannya juga cepat dan aman. Mantap Pak Tono!', '2026-05-26 10:00:00'),
(3, 1, 2, 5, 'Suka banget belanja disini, harganya jauh lebih murah dari supermarket dan kualitasnya grade A beneran.', '2026-05-13 09:00:00');

-- =============================================
-- SAMPLE MESSAGES
-- =============================================
INSERT INTO messages (sender_id, receiver_id, message, is_read, created_at) VALUES
(1, 2, 'Halo bapak, apakah stok apel fuji petik langsung masih ada 2 kg?', 1, '2026-05-25 09:00:00'),
(2, 1, 'Masih pak, apelnya baru dipanen pagi ini. Sangat segar! Silakan langsung checkout saja.', 1, '2026-05-25 09:05:00'),
(1, 2, 'Oke pak, saya sudah transfer via QRIS ya. Mohon segera diproses.', 1, '2026-05-25 09:10:00'),
(2, 1, 'Terima kasih ya pak sudah belanja di toko kami. Pesanan akan dikirim siang ini.', 1, '2026-05-25 09:15:00'),
(3, 1, 'Besok wortelnya siap kirim bu', 1, '2026-05-24 15:00:00');
