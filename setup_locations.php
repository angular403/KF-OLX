<?php
require_once 'config.php';

// Create locations table if not exists
$create_table_sql = "
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
)";

try {
    $pdo->exec($create_table_sql);
    echo "<div style='color: green;'>✓ Table locations berhasil dibuat atau sudah ada</div>";
} catch (PDOException $e) {
    echo "<div style='color: red;'>✗ Error membuat table: " . $e->getMessage() . "</div>";
}

// Indonesia locations data
$locations = [
    // Jakarta
    ['Jakarta Pusat', 'DKI Jakarta', 'Jakarta', 'Menteng'],
    ['Jakarta Utara', 'DKI Jakarta', 'Jakarta', 'Kelapa Gading'],
    ['Jakarta Barat', 'DKI Jakarta', 'Jakarta', 'Kebon Jeruk'],
    ['Jakarta Selatan', 'DKI Jakarta', 'Jakarta', 'Kebayoran Baru'],
    ['Jakarta Timur', 'DKI Jakarta', 'Jakarta', 'Cipayung'],
    
    // Jawa Barat
    ['Bandung', 'Jawa Barat', 'Bandung', 'Coblong'],
    ['Bogor', 'Jawa Barat', 'Bogor', 'Bogor Tengah'],
    ['Depok', 'Jawa Barat', 'Depok', 'Beji'],
    ['Bekasi', 'Jawa Barat', 'Bekasi', 'Bekasi Barat'],
    ['Tangerang', 'Banten', 'Tangerang', 'Tangerang'],
    ['Tangerang Selatan', 'Banten', 'Tangerang Selatan', 'Serpong'],
    ['Cimahi', 'Jawa Barat', 'Cimahi', 'Cimahi Tengah'],
    ['Sukabumi', 'Jawa Barat', 'Sukabumi', 'Cisaat'],
    ['Cirebon', 'Jawa Barat', 'Cirebon', 'Kejaksan'],
    ['Bogor', 'Jawa Barat', 'Bogor', 'Bogor Selatan'],
    
    // Jawa Tengah
    ['Semarang', 'Jawa Tengah', 'Semarang', 'Semarang Tengah'],
    ['Surakarta (Solo)', 'Jawa Tengah', 'Surakarta', 'Laweyan'],
    ['Yogyakarta', 'DI Yogyakarta', 'Yogyakarta', 'Gondokusuman'],
    ['Magelang', 'Jawa Tengah', 'Magelang', 'Magelang Tengah'],
    ['Pekalongan', 'Jawa Tengah', 'Pekalongan', 'Pekalongan Utara'],
    ['Salatiga', 'Jawa Tengah', 'Salatiga', 'Salatiga'],
    ['Tegal', 'Jawa Tengah', 'Tegal', 'Tegal Barat'],
    
    // Jawa Timur
    ['Surabaya', 'Jawa Timur', 'Surabaya', 'Gubeng'],
    ['Malang', 'Jawa Timur', 'Malang', 'Klojen'],
    ['Kediri', 'Jawa Timur', 'Kediri', 'Kediri Kota'],
    ['Madiun', 'Jawa Timur', 'Madiun', 'Kartoharjo'],
    ['Jember', 'Jawa Timur', 'Jember', 'Patrang'],
    ['Batu', 'Jawa Timur', 'Batu', 'Batu'],
    ['Blitar', 'Jawa Timur', 'Blitar', 'Sukorejo'],
    ['Probolinggo', 'Jawa Timur', 'Probolinggo', 'Kanigaran'],
    ['Pasuruan', 'Jawa Timur', 'Pasuruan', 'Panggungrejo'],
    ['Mojokerto', 'Jawa Timur', 'Mojokerto', 'Mojokerto'],
    
    // Sumatera Utara
    ['Medan', 'Sumatera Utara', 'Medan', 'Medan Baru'],
    ['Binjai', 'Sumatera Utara', 'Binjai', 'Binjai Kota'],
    ['Tebingtinggi', 'Sumatera Utara', 'Tebingtinggi', 'Tebingtinggi Kota'],
    ['Pematangsiantar', 'Sumatera Utara', 'Pematangsiantar', 'Siantar Barat'],
    ['Padangsidimpuan', 'Sumatera Utara', 'Padangsidimpuan', 'Padangsidimpuan Hutaimbaru'],
    
    // Sumatera Barat
    ['Padang', 'Sumatera Barat', 'Padang', 'Padang Barat'],
    ['Bukittinggi', 'Sumatera Barat', 'Bukittinggi', 'Bukittinggi'],
    ['Payakumbuh', 'Sumatera Barat', 'Payakumbuh', 'Payakumbuh Barat'],
    ['Pariaman', 'Sumatera Barat', 'Pariaman', 'Pariaman Tengah'],
    ['Solok', 'Sumatera Barat', 'Solok', 'Tanjung Harapan'],
    
    // Riau
    ['Pekanbaru', 'Riau', 'Pekanbaru', 'Pekanbaru Kota'],
    ['Dumai', 'Riau', 'Dumai', 'Dumai Kota'],
    ['Bengkalis', 'Riau', 'Bengkalis', 'Bengkalis'],
    
    // Sumatera Selatan
    ['Palembang', 'Sumatera Selatan', 'Palembang', 'Ilir Barat I'],
    ['Prabumulih', 'Sumatera Selatan', 'Prabumulih', 'Prabumulih Timur'],
    ['Lubuklinggau', 'Sumatera Selatan', 'Lubuklinggau', 'Lubuklinggau Timur I'],
    ['Pagar Alam', 'Sumatera Selatan', 'Pagar Alam', 'Pagar Alam Utara'],
    
    // Lampung
    ['Bandar Lampung', 'Lampung', 'Bandar Lampung', 'Tanjabung'],
    ['Metro', 'Lampung', 'Metro', 'Metro Pusat'],
    
    // Bali
    ['Denpasar', 'Bali', 'Denpasar', 'Denpasar Barat'],
    ['Badung', 'Bali', 'Badung', 'Kuta'],
    ['Gianyar', 'Bali', 'Gianyar', 'Gianyar'],
    ['Tabanan', 'Bali', 'Tabanan', 'Tabanan'],
    
    // Kalimantan
    ['Banjarmasin', 'Kalimantan Selatan', 'Banjarmasin', 'Banjarmasin Tengah'],
    ['Balikpapan', 'Kalimantan Timur', 'Balikpapan', 'Balikpapan Kota'],
    ['Samarinda', 'Kalimantan Timur', 'Samarinda', 'Samarinda Ulu'],
    ['Pontianak', 'Kalimantan Barat', 'Pontianak', 'Pontianak Kota'],
    ['Palangkaraya', 'Kalimantan Tengah', 'Palangkaraya', 'Pahandut'],
    
    // Sulawesi
    ['Makassar', 'Sulawesi Selatan', 'Makassar', 'Makassar'],
    ['Kendari', 'Sulawesi Tenggara', 'Kendari', 'Kendari'],
    ['Palu', 'Sulawesi Tengah', 'Palu', 'Palu'],
    ['Manado', 'Sulawesi Utara', 'Manado', 'Wanea'],
    ['Gorontalo', 'Gorontalo', 'Gorontalo', 'Kota Tengah'],
    
    // Nusa Tenggara
    ['Mataram', 'Nusa Tenggara Barat', 'Mataram', 'Mataram'],
    ['Kupang', 'Nusa Tenggara Timur', 'Kupang', 'Kupang'],
    
    // Papua
    ['Jayapura', 'Papua', 'Jayapura', 'Jayapura Selatan'],
    ['Sorong', 'Papua Barat', 'Sorong', 'Sorong'],
];

echo "<h1>Setup Locations - KF OLX</h1>";
echo "<p>Mengisi data locations ke database...</p>";

try {
    // Insert locations
    $stmt = $pdo->prepare("INSERT INTO locations (name, province, city, district) VALUES (?, ?, ?, ?)");
    
    $inserted_count = 0;
    $skipped_count = 0;
    
    foreach ($locations as $location) {
        $name = $location[0];
        $province = $location[1];
        $city = $location[2];
        $district = $location[3];
        
        // Check if location already exists
        $check_stmt = $pdo->prepare("SELECT id FROM locations WHERE name = ? AND province = ?");
        $check_stmt->execute([$name, $province]);
        
        if ($check_stmt->rowCount() == 0) {
            // Insert new location
            if ($stmt->execute([$name, $province, $city, $district])) {
                $inserted_count++;
                echo "<div style='color: green;'>✓ Berhasil menambah lokasi: <strong>{$name}</strong> ({$province})</div>";
            } else {
                echo "<div style='color: red;'>✗ Gagal menambah lokasi: <strong>{$name}</strong></div>";
            }
        } else {
            $skipped_count++;
            echo "<div style='color: orange;'>⚠ Lokasi sudah ada: <strong>{$name}</strong> (dilewati)</div>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Summary:</h3>";
    echo "<div style='color: green;'>✓ Lokasi berhasil ditambahkan: <strong>{$inserted_count}</strong></div>";
    echo "<div style='color: orange;'>⚠ Lokasi yang dilewati (sudah ada): <strong>{$skipped_count}</strong></div>";
    echo "<div style='color: blue;'>📊 Total lokasi dalam database: <strong>" . ($inserted_count + $skipped_count) . "</strong></div>";
    
    // Display current locations by province
    echo "<hr>";
    echo "<h3>Lokasi Saat Ini (per Provinsi):</h3>";
    
    $all_locations = $pdo->query("SELECT * FROM locations ORDER BY province, city, name")->fetchAll(PDO::FETCH_ASSOC);
    $current_province = '';
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Nama Lokasi</th><th>Kota</th><th>Provinsi</th><th>Kecamatan</th></tr>";
    
    foreach ($all_locations as $loc) {
        echo "<tr>";
        echo "<td>{$loc['id']}</td>";
        echo "<td><strong>{$loc['name']}</strong></td>";
        echo "<td>{$loc['city']}</td>";
        echo "<td>{$loc['province']}</td>";
        echo "<td>{$loc['district']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<p><a href='index.php' style='background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Kembali ke Beranda</a></p>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 10px; border: 1px solid #ff0000;'>";
    echo "<strong>Error Database:</strong> " . $e->getMessage();
    echo "</div>";
}

// Manual SQL queries for reference
echo "<hr>";
echo "<h3>Manual SQL Queries (untuk reference):</h3>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";

echo "-- Create locations table\n";
echo $create_table_sql . "\n\n";

echo "-- Clear existing locations (optional)\n";
echo "TRUNCATE TABLE locations;\n\n";

echo "-- Insert locations\n";
foreach ($locations as $location) {
    $name = addslashes($location[0]);
    $province = addslashes($location[1]);
    $city = addslashes($location[2]);
    $district = addslashes($location[3]);
    echo "INSERT INTO locations (name, province, city, district) VALUES ('{$name}', '{$province}', '{$city}', '{$district}');\n";
}

echo "\n-- Get all locations\n";
echo "SELECT * FROM locations ORDER BY province, city, name;\n";

echo "\n-- Get locations by province\n";
echo "SELECT DISTINCT province FROM locations ORDER BY province;\n";

echo "\n-- Get cities by province\n";
echo "SELECT DISTINCT city FROM locations WHERE province = 'DKI Jakarta' ORDER BY city;\n";

echo "</pre>";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Locations - KF OLX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            padding: 20px;
            background: #f8fafc;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            margin-top: 15px;
            font-size: 14px;
        }
        th, td {
            text-align: left;
            vertical-align: middle;
        }
        pre {
            font-size: 11px;
            overflow-x: auto;
            max-height: 400px;
        }
        .status-item {
            padding: 8px;
            margin: 5px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Content will be displayed above -->
    </div>
</body>
</html>
