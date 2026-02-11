-- KF OLX Locations Setup Script
-- File ini berisi SQL queries untuk membuat table locations dan mengisi data

-- Create locations table
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    province VARCHAR(50) NOT NULL,
    city VARCHAR(50) NOT NULL,
    district VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_province (province),
    INDEX idx_city (city),
    INDEX idx_active (is_active)
);

-- Add location_id to ads table (if not exists)
ALTER TABLE ads ADD COLUMN location_id INT NULL AFTER location;

-- Add foreign key constraint
ALTER TABLE ads ADD CONSTRAINT fk_ads_location 
FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL;

-- Insert Locations Data
INSERT INTO locations (name, province, city, district) VALUES 
('Jakarta Pusat', 'DKI Jakarta', 'Jakarta', 'Menteng'),
('Jakarta Utara', 'DKI Jakarta', 'Jakarta', 'Kelapa Gading'),
('Jakarta Barat', 'DKI Jakarta', 'Jakarta', 'Kebon Jeruk'),
('Jakarta Selatan', 'DKI Jakarta', 'Jakarta', 'Kebayoran Baru'),
('Jakarta Timur', 'DKI Jakarta', 'Jakarta', 'Cipayung'),

('Bandung', 'Jawa Barat', 'Bandung', 'Coblong'),
('Bogor', 'Jawa Barat', 'Bogor', 'Bogor Tengah'),
('Depok', 'Jawa Barat', 'Depok', 'Beji'),
('Bekasi', 'Jawa Barat', 'Bekasi', 'Bekasi Barat'),
('Cimahi', 'Jawa Barat', 'Cimahi', 'Cimahi Tengah'),
('Sukabumi', 'Jawa Barat', 'Sukabumi', 'Cisaat'),
('Cirebon', 'Jawa Barat', 'Cirebon', 'Kejaksan'),

('Tangerang', 'Banten', 'Tangerang', 'Tangerang'),
('Tangerang Selatan', 'Banten', 'Tangerang Selatan', 'Serpong'),

('Semarang', 'Jawa Tengah', 'Semarang', 'Semarang Tengah'),
('Surakarta (Solo)', 'Jawa Tengah', 'Surakarta', 'Laweyan'),
('Magelang', 'Jawa Tengah', 'Magelang', 'Magelang Tengah'),
('Pekalongan', 'Jawa Tengah', 'Pekalongan', 'Pekalongan Utara'),
('Salatiga', 'Jawa Tengah', 'Salatiga', 'Salatiga'),
('Tegal', 'Jawa Tengah', 'Tegal', 'Tegal Barat'),

('Yogyakarta', 'DI Yogyakarta', 'Yogyakarta', 'Gondokusuman'),

('Surabaya', 'Jawa Timur', 'Surabaya', 'Gubeng'),
('Malang', 'Jawa Timur', 'Malang', 'Klojen'),
('Kediri', 'Jawa Timur', 'Kediri', 'Kediri Kota'),
('Madiun', 'Jawa Timur', 'Madiun', 'Kartoharjo'),
('Jember', 'Jawa Timur', 'Jember', 'Patrang'),
('Batu', 'Jawa Timur', 'Batu', 'Batu'),
('Blitar', 'Jawa Timur', 'Blitar', 'Sukorejo'),
('Probolinggo', 'Jawa Timur', 'Probolinggo', 'Kanigaran'),
('Pasuruan', 'Jawa Timur', 'Pasuruan', 'Panggungrejo'),
('Mojokerto', 'Jawa Timur', 'Mojokerto', 'Mojokerto'),

('Medan', 'Sumatera Utara', 'Medan', 'Medan Baru'),
('Binjai', 'Sumatera Utara', 'Binjai', 'Binjai Kota'),
('Tebingtinggi', 'Sumatera Utara', 'Tebingtinggi', 'Tebingtinggi Kota'),
('Pematangsiantar', 'Sumatera Utara', 'Pematangsiantar', 'Siantar Barat'),
('Padangsidimpuan', 'Sumatera Utara', 'Padangsidimpuan', 'Padangsidimpuan Hutaimbaru'),

('Padang', 'Sumatera Barat', 'Padang', 'Padang Barat'),
('Bukittinggi', 'Sumatera Barat', 'Bukittinggi', 'Bukittinggi'),
('Payakumbuh', 'Sumatera Barat', 'Payakumbuh', 'Payakumbuh Barat'),
('Pariaman', 'Sumatera Barat', 'Pariaman', 'Pariaman Tengah'),
('Solok', 'Sumatera Barat', 'Solok', 'Tanjung Harapan'),

('Pekanbaru', 'Riau', 'Pekanbaru', 'Pekanbaru Kota'),
('Dumai', 'Riau', 'Dumai', 'Dumai Kota'),
('Bengkalis', 'Riau', 'Bengkalis', 'Bengkalis'),

('Palembang', 'Sumatera Selatan', 'Palembang', 'Ilir Barat I'),
('Prabumulih', 'Sumatera Selatan', 'Prabumulih', 'Prabumulih Timur'),
('Lubuklinggau', 'Sumatera Selatan', 'Lubuklinggau', 'Lubuklinggau Timur I'),
('Pagar Alam', 'Sumatera Selatan', 'Pagar Alam', 'Pagar Alam Utara'),

('Bandar Lampung', 'Lampung', 'Bandar Lampung', 'Tanjabung'),
('Metro', 'Lampung', 'Metro', 'Metro Pusat'),

('Denpasar', 'Bali', 'Denpasar', 'Denpasar Barat'),
('Badung', 'Bali', 'Badung', 'Kuta'),
('Gianyar', 'Bali', 'Gianyar', 'Gianyar'),
('Tabanan', 'Bali', 'Tabanan', 'Tabanan'),

('Banjarmasin', 'Kalimantan Selatan', 'Banjarmasin', 'Banjarmasin Tengah'),
('Balikpapan', 'Kalimantan Timur', 'Balikpapan', 'Balikpapan Kota'),
('Samarinda', 'Kalimantan Timur', 'Samarinda', 'Samarinda Ulu'),
('Pontianak', 'Kalimantan Barat', 'Pontianak', 'Pontianak Kota'),
('Palangkaraya', 'Kalimantan Tengah', 'Palangkaraya', 'Pahandut'),

('Makassar', 'Sulawesi Selatan', 'Makassar', 'Makassar'),
('Kendari', 'Sulawesi Tenggara', 'Kendari', 'Kendari'),
('Palu', 'Sulawesi Tengah', 'Palu', 'Palu'),
('Manado', 'Sulawesi Utara', 'Manado', 'Wanea'),
('Gorontalo', 'Gorontalo', 'Gorontalo', 'Kota Tengah'),

('Mataram', 'Nusa Tenggara Barat', 'Mataram', 'Mataram'),
('Kupang', 'Nusa Tenggara Timur', 'Kupang', 'Kupang'),

('Jayapura', 'Papua', 'Jayapura', 'Jayapura Selatan'),
('Sorong', 'Papua Barat', 'Sorong', 'Sorong');

-- Verify insertion
SELECT * FROM locations ORDER BY province, city, name;

-- Get all provinces
SELECT DISTINCT province FROM locations ORDER BY province;

-- Get cities by province
SELECT DISTINCT city FROM locations WHERE province = 'DKI Jakarta' ORDER BY city;

-- Update existing ads (optional mapping)
-- UPDATE ads SET location_id = 1 WHERE location LIKE '%Jakarta%';

-- Query ads with location info
SELECT a.*, l.name as location_name, l.city, l.province
FROM ads a
LEFT JOIN locations l ON a.location_id = l.id
ORDER BY a.created_at DESC;
