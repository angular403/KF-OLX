<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();


try {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT ads.*, 
                          (SELECT image_path FROM ad_images WHERE ad_id=ads.id LIMIT 1) AS image_path, 
                          categories.name AS category_name 
                          FROM ads 
                          JOIN categories ON ads.category_id=categories.id 
                          WHERE ads.user_id=:user_id 
                          ORDER BY ads.created_at DESC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Iklan Saya - OLX Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --olx-orange: #e67e22;
            --olx-dark: #2c3e50;
            --olx-light: #ecf0f1;
            --olx-gray: #95a5a6;
            --olx-success: #27ae60;
            --light-bg: #f5f5f5;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--olx-orange);
        }
        
        header {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        header nav {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        header nav a {
            margin-left: 20px;
            text-decoration: none;
            color: var(--olx-dark);
            font-weight: 500;
        }
        
        header nav a:hover {
            color: var(--olx-orange);
        }
        
        .btn {
            background-color: var(--olx-orange);
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #d35400;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--olx-orange);
            border: 2px solid var(--olx-orange);
        }
        
        .btn-outline:hover {
            background: var(--olx-orange);
            color: white;
        }
        
        .product-section {
            padding: 30px 0;
        }
        
        .product-section h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--olx-dark);
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .product-card a {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: var(--light-bg);
        }
        
        .product-card h3 {
            padding: 1rem 1rem 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-card .price {
            padding: 0 1rem 0.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--olx-orange);
        }
        
        .product-card .location {
            padding: 0 1rem 1rem;
            font-size: 0.875rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .product-card .location::before {
            content: "📍";
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .header-flex {
                flex-direction: column;
                gap: 1rem;
            }
            
            header nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="container header-flex">
        <div class="logo">OLX Clone</div>
        <nav>
            <a href="index.php">Beranda</a>
            <a href="post-ad.php">Pasang Iklan</a>
            <a href="my_ads.php">Iklan Saya</a>
            <a href="logout.php" class="btn">Logout</a>
        </nav>
    </div>
</header>
<section class="product-section">
    <div class="container">
        <h2>Iklan Saya</h2>
        <div class="product-grid">
            <?php if (empty($ads)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                    <h3>Belum Ada Iklan</h3>
                    <p>Anda belum memasang iklan apa pun.</p>
                    <a href="post-ad.php" class="btn" style="margin-top: 1rem;">Pasang Iklan Pertama</a>
                </div>
            <?php else: ?>
                <?php foreach($ads as $row): ?>
                <div class="product-card">
                    <a href="detail.php?id=<?= $row['id'] ?>">
                        <img src="<?= $row['image_path'] ? 'uploads/ads/'.$row['image_path'] : 'uploads/ads/noimage.png' ?>" alt="<?= htmlspecialchars($row['title']) ?>" onerror="this.src='uploads/ads/asus.jpg'; this.onerror=null;">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p class="price">Rp <?= number_format($row['price'],0,',','.') ?></p>
                        <p class="location"><?= htmlspecialchars($row['location']) ?></p>
                    </a>
                    <div style="margin-top:10px; padding: 0 1rem 1rem; display: flex; gap: 8px;">
                        <a href="edit_ad.php?id=<?= $row['id'] ?>" class="btn" style="padding:5px 12px;font-size:0.95em; flex: 1;">Edit</a>
                        <a href="delete_ad.php?id=<?= $row['id'] ?>" class="btn btn-outline" style="padding:5px 12px;font-size:0.95em; flex: 1;" onclick="return confirm('Yakin ingin menghapus iklan ini?')">Hapus</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
</body>
</html>