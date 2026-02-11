-- KF OLX Categories Insert Script
-- File ini berisi SQL queries untuk mengisi data categories ke database

-- Clear existing categories (optional - uncomment jika ingin reset data)
-- TRUNCATE TABLE categories;

-- Insert Categories Data
INSERT INTO categories (name, icon) VALUES 
('Elektronik', 'bi-phone'),
('Kendaraan', 'bi-car-front'),
('Properti', 'bi-house-door'),
('Fashion', 'bi-bag'),
('Hobi & Olahraga', 'bi-controller'),
('Buku & Alat Tulis', 'bi-book'),
('Kesehatan & Kecantikan', 'bi-heart-pulse'),
('Makanan & Minuman', 'bi-cup-hot'),
('Rumah Tangga', 'bi-house'),
('Jasa', 'bi-briefcase'),
('Hewan Peliharaan', 'bi-heart'),
('Komputer & Aksesoris', 'bi-pc-display'),
('Gaming', 'bi-controller'),
('Mainan Anak', 'bi-joystick'),
('Musik & Film', 'bi-music-note'),
('Alat Musik', 'bi-music-note-beamed'),
('Kamera', 'bi-camera'),
('Perlengkapan Bayi', 'bi-emoji-smile'),
('Lainnya', 'bi-three-dots');

-- Verify insertion
SELECT * FROM categories ORDER BY id;

-- Individual insert statements (jika perlu insert satu per satu)
INSERT INTO categories (name, icon) VALUES ('Elektronik', 'bi-phone');
INSERT INTO categories (name, icon) VALUES ('Kendaraan', 'bi-car-front');
INSERT INTO categories (name, icon) VALUES ('Properti', 'bi-house-door');
INSERT INTO categories (name, icon) VALUES ('Fashion', 'bi-bag');
INSERT INTO categories (name, icon) VALUES ('Hobi & Olahraga', 'bi-controller');
INSERT INTO categories (name, icon) VALUES ('Buku & Alat Tulis', 'bi-book');
INSERT INTO categories (name, icon) VALUES ('Kesehatan & Kecantikan', 'bi-heart-pulse');
INSERT INTO categories (name, icon) VALUES ('Makanan & Minuman', 'bi-cup-hot');
INSERT INTO categories (name, icon) VALUES ('Rumah Tangga', 'bi-house');
INSERT INTO categories (name, icon) VALUES ('Jasa', 'bi-briefcase');
INSERT INTO categories (name, icon) VALUES ('Hewan Peliharaan', 'bi-heart');
INSERT INTO categories (name, icon) VALUES ('Komputer & Aksesoris', 'bi-pc-display');
INSERT INTO categories (name, icon) VALUES ('Gaming', 'bi-controller');
INSERT INTO categories (name, icon) VALUES ('Mainan Anak', 'bi-joystick');
INSERT INTO categories (name, icon) VALUES ('Musik & Film', 'bi-music-note');
INSERT INTO categories (name, icon) VALUES ('Alat Musik', 'bi-music-note-beamed');
INSERT INTO categories (name, icon) VALUES ('Kamera', 'bi-camera');
INSERT INTO categories (name, icon) VALUES ('Perlengkapan Bayi', 'bi-emoji-smile');
INSERT INTO categories (name, icon) VALUES ('Lainnya', 'bi-three-dots');
