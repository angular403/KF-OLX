<?php
include 'config.php';

$database = new Database();
$pdo = $database->getConnection();

try {
    // Check if ads table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM ads");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $ads_count = $result['count'];
    
    echo "Jumlah iklan saat ini: " . $ads_count . "\n";
    
    if ($ads_count == 0) {
        echo "Database kosong. Membuat data sample...\n";
        
        // Insert sample ads
        $sample_ads = [
            [
                'title' => 'iPhone 13 Pro Max 256GB',
                'description' => 'iPhone 13 Pro Max kondisi like new, complete set, garansi resmi',
                'price' => 15000000,
                'location' => 'Jakarta Pusat',
                'category_id' => 1,
                'user_id' => 1
            ],
            [
                'title' => 'Laptop Gaming ASUS ROG',
                'description' => 'ASUS ROG Strix G15, RTX 3060, Intel i7, 16GB RAM',
                'price' => 18500000,
                'location' => 'Surabaya',
                'category_id' => 2,
                'user_id' => 1
            ],
            [
                'title' => 'Motorola G32 4/128GB',
                'description' => 'HP Android Motorola G32, mulus, normal, no minus',
                'price' => 2500000,
                'location' => 'Bandung',
                'category_id' => 1,
                'user_id' => 1
            ],
            [
                'title' => 'Sepeda Polygon Xtrada',
                'description' => 'Sepeda gunung Polygon Xtrada 27 speed, kondisi 90%',
                'price' => 3500000,
                'location' => 'Yogyakarta',
                'category_id' => 3,
                'user_id' => 1
            ],
            [
                'title' => 'PS5 Digital Edition',
                'description' => 'PlayStation 5 Digital Edition, like new, 2 controller',
                'price' => 6500000,
                'location' => 'Medan',
                'category_id' => 4,
                'user_id' => 1
            ]
        ];
        
        foreach ($sample_ads as $ad) {
            $sql = "INSERT INTO ads (title, description, price, location, category_id, user_id, created_at) 
                    VALUES (:title, :description, :price, :location, :category_id, :user_id, NOW())";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':title', $ad['title']);
            $stmt->bindParam(':description', $ad['description']);
            $stmt->bindParam(':price', $ad['price']);
            $stmt->bindParam(':location', $ad['location']);
            $stmt->bindParam(':category_id', $ad['category_id']);
            $stmt->bindParam(':user_id', $ad['user_id']);
            
            if ($stmt->execute()) {
                $ad_id = $pdo->lastInsertId();
                echo "✓ Iklan '{$ad['title']}' berhasil ditambahkan (ID: {$ad_id})\n";
                
                // Add sample image
                $image_sql = "INSERT INTO ad_images (ad_id, image_path) VALUES (:ad_id, :image_path)";
                $image_stmt = $pdo->prepare($image_sql);
                $image_path = 'noimage.png';
                $image_stmt->bindParam(':ad_id', $ad_id);
                $image_stmt->bindParam(':image_path', $image_path);
                $image_stmt->execute();
            } else {
                echo "✗ Gagal menambahkan iklan '{$ad['title']}'\n";
            }
        }
        
        echo "\nData sample berhasil dibuat!\n";
    } else {
        echo "Database sudah memiliki data.\n";
    }
    
    // Test query from index.php
    echo "\nTesting query dari index.php...\n";
    $sql = "SELECT ads.*, categories.name AS category_name, 
            (SELECT image_path FROM ad_images WHERE ad_id = ads.id LIMIT 1) AS image_path
            FROM ads 
            JOIN categories ON ads.category_id = categories.id
            ORDER BY ads.created_at DESC
            LIMIT 12";
    
    $stmt = $pdo->query($sql);
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Query menghasilkan " . count($ads) . " iklan:\n";
    foreach ($ads as $ad) {
        echo "- {$ad['title']} (Rp " . number_format($ad['price'], 0, ',', '.') . ") - Image: " . ($ad['image_path'] ?? 'NULL') . "\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
