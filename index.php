<?php
include 'config.php';
if (!isset($_SESSION)) {
    session_start();
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

try {
    // Ambil lokasi unik dari ads
    $locations = [];
    $loc_stmt = $pdo->query("SELECT DISTINCT location FROM ads WHERE location IS NOT NULL AND location != '' ORDER BY location ASC");
    $locations = $loc_stmt->fetchAll(PDO::FETCH_COLUMN);

    $where = [];
    $params = [];

    if (isset($_GET['title']) && $_GET['title'] != '') {
        $where[] = "ads.title LIKE :title";
        $params[':title'] = '%' . $_GET['title'] . '%';
    }
    if (isset($_GET['location']) && $_GET['location'] != '') {
        $where[] = "ads.location LIKE :location";
        $params[':location'] = '%' . $_GET['location'] . '%';
    }
    if (isset($_GET['category_id']) && $_GET['category_id'] != '') {
        $where[] = "ads.category_id = :category_id";
        $params[':category_id'] = intval($_GET['category_id']);
    }

    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "SELECT ads.*, categories.name AS category_name, 
            (SELECT image_path FROM ad_images WHERE ad_id = ads.id LIMIT 1) AS image_path
            FROM ads 
            JOIN categories ON ads.category_id = categories.id
            $where_sql
            ORDER BY ads.created_at DESC
            LIMIT 12";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => &$val) {
        if ($key === ':category_id') {
            $stmt->bindParam($key, $val, PDO::PARAM_INT);
        } else {
            $stmt->bindParam($key, $val);
        }
    }
    $stmt->execute();
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil kategori untuk menu
    $cat_stmt = $pdo->query("SELECT * FROM categories");
    $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil nama kategori jika ada filter
    $cat_name = '';
    if (isset($_GET['category_id']) && $_GET['category_id'] != '') {
        $cat_id = intval($_GET['category_id']);
        $cat_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = :id");
        $cat_stmt->bindParam(':id', $cat_id, PDO::PARAM_INT);
        $cat_stmt->execute();
        $cat_result = $cat_stmt->fetch(PDO::FETCH_ASSOC);
        $cat_name = $cat_result['name'] ?? '';
    }
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLX Clone - Jual Beli Mudah</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #000000ff;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }   
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #1e293b;
            line-height: 1.6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Header Styles */
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--light-bg);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        header nav {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        header nav a {
            color: var(--light-bg);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        header nav a:hover {
            color: var(--primary-color);
            background: var(--primary-dark);
        }
        
        .btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }
        
        /* Search Section */
        .search-section {
            padding: 3rem 0;
            text-align: center;
        }
        
        .search-section form {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
        }
        
        .search-section input,
        .search-section select,
        .search-section button {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .search-section input,
        .search-section select {
            background: white;
        }
        
        .search-section input:focus,
        .search-section select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .search-section button {
            background: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        
        .search-section button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        /* Category Section */
        .category-section {
            padding: 4rem 0;
            background: white;
        }
        
        .category-section h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: #1e293b;
        }
        
        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .category-item {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem 1rem;
            text-align: center;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .category-item:hover {
            border-color: var(--primary-color);
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .category-item.active {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }
        
        .category-item img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .category-item span {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        /* Product Section */
        .product-section {
            padding: 4rem 0;
            background: var(--light-bg);
        }
        
        .product-section h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: #1e293b;
        }
        
        .product-section p {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 2rem;
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
            color: var(--primary-color);
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
        
        /* Footer */
        footer {
            background: #1e293b;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        
        footer p {
            margin: 0;
            opacity: 0.8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-flex {
                flex-direction: column;
                gap: 1rem;
            }
            
            header nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .search-section form {
                flex-direction: column;
                gap: 1rem;
            }
            
            .search-section input,
            .search-section select,
            .search-section button {
                width: 100%;
            }
            
            .categories {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 1rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-flex">
                <div class="logo">OLX Clone</div>
                <nav>
                    <a href="index.php">Beranda</a>
                    <a href="post-ad.php">Pasang Iklan</a>
                    <a href="my_ads.php">Iklan Saya</a>
                    <a href="profile.php" class="active">Profile Saya</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span>Halo, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="logout.php" class="btn">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn">Login</a>
                        <a href="register.php" class="btn btn-outline">Register</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Search Bar -->
    <section class="search-section">
        <div class="container">
            <form method="GET" action="index.php" style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <input type="text" name="title" value="<?= isset($_GET['title']) ? htmlspecialchars($_GET['title']) : '' ?>" placeholder="Judul barang...">
                <input type="text" name="location" value="<?= isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '' ?>" placeholder="Lokasi...">
                <select name="location">
                    <option value="">Semua Lokasi</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= htmlspecialchars($loc) ?>" <?= (isset($_GET['location']) && $_GET['location'] == $loc) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Cari</button>
            </form>
        </div>
    </section>

    <!-- Kategori -->
    <section class="category-section">
        <div class="container">
            <h2>Kategori Populer</h2>
            <div class="categories">
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?category_id=<?= $cat['id'] ?>"
                        class="category-item<?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? ' active' : '' ?>"
                        style="text-align:center; text-decoration:none;">
                        <?php
                        $icon_path = 'uploads/ads/' . $cat['icon'];
                        if (!$cat['icon'] || !file_exists($icon_path)) {
                            $placeholder = 'https://placehold.co/48x48/2196f3/fff?text=' . urlencode(substr($cat['name'], 0, 2));
                            echo '<img src="' . $placeholder . '" alt="' . htmlspecialchars($cat['name']) . '">';
                        } else {
                            echo '<img src="' . $icon_path . '" alt="' . htmlspecialchars($cat['name']) . '">';
                        }
                        ?>
                        <span><?= htmlspecialchars($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Daftar Produk/Iklan -->
    <section class="product-section">
        <div class="container">
            <h2>Daftar Iklan Terbaru</h2>

            <?php
            $info = [];
            if (isset($_GET['title']) && $_GET['title'] != '') $info[] = "Judul: <b>" . htmlspecialchars($_GET['title']) . "</b>";
            if (isset($_GET['location']) && $_GET['location'] != '') $info[] = "Lokasi: <b>" . htmlspecialchars($_GET['location']) . "</b>";
            if (isset($_GET['category_id']) && $_GET['category_id'] != '') {
                $info[] = "Kategori: <b>" . htmlspecialchars($cat_name) . "</b>";
            }
            if ($info) echo "<p>Filter: " . implode(', ', $info) . "</p>";
            ?>

            <?php
            if (isset($_GET['category_id']) && $_GET['category_id'] != '') {
                echo "<p>Menampilkan iklan untuk kategori: <b>" . htmlspecialchars($cat_name) . "</b></p>";
            }
            ?>

            <div class="product-grid">
                <?php if (empty($ads)): ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                        <h3>Belum ada iklan</h3>
                        <p>Belum ada iklan yang tersedia saat ini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($ads as $row): ?>
                        <div class="product-card">
                            <a href="detail.php?id=<?= $row['id'] ?>">
                                <?php 
                                $image_src = $row['image_path'] ? 'uploads/ads/' . $row['image_path'] : 'uploads/ads/noimage.png';
                                $alt_text = htmlspecialchars($row['title']);
                                ?>
                                <img src="<?= $image_src ?>" 
                                     alt="<?= $alt_text ?>" 
                                     onerror="this.src='uploads/ads/noimage.png'; this.onerror=null;"
                                     style="width: 100%; height: 200px; object-fit: cover; background: var(--light-bg);">
                                <h3><?= htmlspecialchars($row['title']) ?></h3>
                                <p class="price">Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
                                <p class="location"><?= htmlspecialchars($row['location']) ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2026 OLX Clone. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>