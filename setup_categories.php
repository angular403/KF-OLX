<?php
require_once 'config.php';

// Categories data with icons
$categories = [
    ['Elektronik', 'bi-phone'],
    ['Kendaraan', 'bi-car-front'],
    ['Properti', 'bi-house-door'],
    ['Fashion', 'bi-bag'],
    ['Hobi & Olahraga', 'bi-controller'],
    ['Buku & Alat Tulis', 'bi-book'],
    ['Kesehatan & Kecantikan', 'bi-heart-pulse'],
    ['Makanan & Minuman', 'bi-cup-hot'],
    ['Rumah Tangga', 'bi-house'],
    ['Jasa', 'bi-briefcase'],
    ['Hewan Peliharaan', 'bi-heart'],
    ['Komputer & Aksesoris', 'bi-pc-display'],
    ['Gaming', 'bi-controller'],
    ['Mainan Anak', 'bi-joystick'],
    ['Musik & Film', 'bi-music-note'],
    ['Alat Musik', 'bi-music-note-beamed'],
    ['Kamera', 'bi-camera'],
    ['Perlengkapan Bayi', 'bi-emoji-smile'],
    ['Lainnya', 'bi-three-dots']
];

echo "<h1>Setup Categories - KF OLX</h1>";
echo "<p>Mengisi data categories ke database...</p>";

try {
    // Clear existing categories (optional - uncomment if you want to reset)
    // $pdo->exec("TRUNCATE TABLE categories");
    
    // Insert categories
    $stmt = $pdo->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
    
    $inserted_count = 0;
    $skipped_count = 0;
    
    foreach ($categories as $category) {
        $name = $category[0];
        $icon = $category[1];
        
        // Check if category already exists
        $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $check_stmt->execute([$name]);
        
        if ($check_stmt->rowCount() == 0) {
            // Insert new category
            if ($stmt->execute([$name, $icon])) {
                $inserted_count++;
                echo "<div style='color: green;'>✓ Berhasil menambah kategori: <strong>{$name}</strong> ({$icon})</div>";
            } else {
                echo "<div style='color: red;'>✗ Gagal menambah kategori: <strong>{$name}</strong></div>";
            }
        } else {
            $skipped_count++;
            echo "<div style='color: orange;'>⚠ Kategori sudah ada: <strong>{$name}</strong> (dilewati)</div>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Summary:</h3>";
    echo "<div style='color: green;'>✓ Kategori berhasil ditambahkan: <strong>{$inserted_count}</strong></div>";
    echo "<div style='color: orange;'>⚠ Kategori yang dilewati (sudah ada): <strong>{$skipped_count}</strong></div>";
    echo "<div style='color: blue;'>📊 Total kategori dalam database: <strong>" . ($inserted_count + $skipped_count) . "</strong></div>";
    
    // Display current categories
    echo "<hr>";
    echo "<h3>Kategori Saat Ini:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Nama Kategori</th><th>Icon</th><th>Preview Icon</th></tr>";
    
    $all_categories = $category->getAllCategories();
    foreach ($all_categories as $cat) {
        echo "<tr>";
        echo "<td>{$cat['id']}</td>";
        echo "<td><strong>{$cat['name']}</strong></td>";
        echo "<td><code>{$cat['icon']}</code></td>";
        echo "<td><i class='{$cat['icon']}'></i></td>";
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

echo "-- Clear existing categories (optional)\n";
echo "TRUNCATE TABLE categories;\n\n";

echo "-- Insert categories\n";
foreach ($categories as $category) {
    $name = addslashes($category[0]);
    $icon = addslashes($category[1]);
    echo "INSERT INTO categories (name, icon) VALUES ('{$name}', '{$icon}');\n";
}

echo "</pre>";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Categories - KF OLX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            padding: 20px;
            background: #f8fafc;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        table {
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            vertical-align: middle;
        }
        pre {
            font-size: 12px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Content will be displayed above -->
    </div>
</body>
</html>
