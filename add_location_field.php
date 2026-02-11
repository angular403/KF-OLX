<?php
require_once 'config.php';

echo "<h1>Add Location Field to Ads Table - KF OLX</h1>";
echo "<p>Menambahkan location_id field ke ads table...</p>";

try {
    // Add location_id field to ads table
    $alter_sql = "ALTER TABLE ads ADD COLUMN location_id INT NULL AFTER location";
    
    // Check if column already exists
    $check_sql = "SHOW COLUMNS FROM ads LIKE 'location_id'";
    $result = $pdo->query($check_sql);
    
    if ($result->rowCount() == 0) {
        // Add the column
        $pdo->exec($alter_sql);
        echo "<div style='color: green;'>✓ Berhasil menambahkan location_id field ke ads table</div>";
        
        // Add foreign key constraint
        $fk_sql = "ALTER TABLE ads ADD CONSTRAINT fk_ads_location 
                   FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL";
        
        try {
            $pdo->exec($fk_sql);
            echo "<div style='color: green;'>✓ Berhasil menambahkan foreign key constraint</div>";
        } catch (PDOException $e) {
            echo "<div style='color: orange;'>⚠ Warning: Gagal menambahkan foreign key - " . $e->getMessage() . "</div>";
        }
        
        // Update existing ads with location mapping (optional)
        echo "<h3>Mapping Existing Location Data:</h3>";
        
        // Get existing ads with location text
        $existing_ads = $pdo->query("SELECT id, location FROM ads WHERE location IS NOT NULL AND location_id IS NULL")->fetchAll();
        
        $mapped_count = 0;
        foreach ($existing_ads as $ad) {
            // Try to find matching location
            $location_name = trim($ad['location']);
            
            // Search for location by name or city
            $search_sql = "SELECT id FROM locations WHERE 
                          name LIKE :query OR city LIKE :query LIMIT 1";
            $stmt = $pdo->prepare($search_sql);
            $search_query = "%{$location_name}%";
            $stmt->bindParam(':query', $search_query);
            $stmt->execute();
            
            $location = $stmt->fetch();
            
            if ($location) {
                // Update ad with location_id
                $update_sql = "UPDATE ads SET location_id = :location_id WHERE id = :ad_id";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->bindParam(':location_id', $location['id']);
                $update_stmt->bindParam(':ad_id', $ad['id']);
                
                if ($update_stmt->execute()) {
                    $mapped_count++;
                    echo "<div style='color: blue;'>✓ Mapped ad ID {$ad['id']} ({$location_name}) → Location ID {$location['id']}</div>";
                }
            }
        }
        
        echo "<div style='color: green;'><strong>Total ads mapped: {$mapped_count}</strong></div>";
        
    } else {
        echo "<div style='color: orange;'>⚠ location_id field sudah ada di ads table</div>";
    }
    
    // Show current table structure
    echo "<hr>";
    echo "<h3>Current Ads Table Structure:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    $columns = $pdo->query("DESCRIBE ads")->fetchAll();
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>{$column['Field']}</strong></td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show sample data with location info
    echo "<hr>";
    echo "<h3>Sample Ads with Location Info:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Title</th><th>Location (Text)</th><th>Location ID</th><th>Location Name</th></tr>";
    
    $sample_ads = $pdo->query("
        SELECT a.id, a.title, a.location, a.location_id, l.name as location_name, l.city, l.province
        FROM ads a 
        LEFT JOIN locations l ON a.location_id = l.id 
        ORDER BY a.id DESC 
        LIMIT 10
    ")->fetchAll();
    
    foreach ($sample_ads as $ad) {
        echo "<tr>";
        echo "<td>{$ad['id']}</td>";
        echo "<td>" . htmlspecialchars(substr($ad['title'], 0, 50)) . "...</td>";
        echo "<td>" . htmlspecialchars($ad['location']) . "</td>";
        echo "<td>{$ad['location_id']}</td>";
        echo "<td>";
        if ($ad['location_name']) {
            echo htmlspecialchars($ad['location_name']) . ", " . htmlspecialchars($ad['city']);
        } else {
            echo "<span style='color: red;'>Not mapped</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<p><a href='index.php' style='background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Kembali ke Beranda</a></p>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 10px; border: 1px solid #ff0000;'>";
    echo "<strong>Error:</strong> " . $e->getMessage();
    echo "</div>";
}

// Manual SQL for reference
echo "<hr>";
echo "<h3>Manual SQL Commands:</h3>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";

echo "-- Add location_id column to ads table\n";
echo "ALTER TABLE ads ADD COLUMN location_id INT NULL AFTER location;\n\n";

echo "-- Add foreign key constraint\n";
echo "ALTER TABLE ads ADD CONSTRAINT fk_ads_location \n";
echo "FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL;\n\n";

echo "-- Update existing ads (example)\n";
echo "UPDATE ads SET location_id = 1 WHERE location LIKE '%Jakarta%';\n\n";

echo "-- Query ads with location info\n";
echo "SELECT a.*, l.name as location_name, l.city, l.province\n";
echo "FROM ads a\n";
echo "LEFT JOIN locations l ON a.location_id = l.id\n";
echo "ORDER BY a.created_at DESC;";

echo "</pre>";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Location Field - KF OLX</title>
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
