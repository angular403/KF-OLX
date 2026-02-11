<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

$user_id = $_SESSION['user_id'];
$id = intval($_GET['id'] ?? 0);

try {
    // Ambil data iklan menggunakan PDO
    $stmt = $pdo->prepare("SELECT * FROM ads WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $ad = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ad) {
        echo "<script>alert('Iklan tidak ditemukan atau Anda tidak berhak mengedit.');window.location='my_ads.php';</script>";
        exit;
    }

    // Proses update jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $location = $_POST['location'];
        $description = $_POST['description'];

        // Mulai transaksi
        $pdo->beginTransaction();

        try {
            // Update data iklan
            $update_stmt = $pdo->prepare("UPDATE ads SET title = :title, category_id = :category_id, 
                                         price = :price, location = :location, description = :description 
                                         WHERE id = :id AND user_id = :user_id");
            
            $update_stmt->bindParam(':title', $title);
            $update_stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $update_stmt->bindParam(':price', $price);
            $update_stmt->bindParam(':location', $location);
            $update_stmt->bindParam(':description', $description);
            $update_stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $update_stmt->execute();

            // Jika ada upload gambar baru
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid().'.'.$ext;
                $upload_path = 'uploads/ads/'.$filename;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Hapus gambar lama (opsional)
                    $img_stmt = $pdo->prepare("SELECT image_path FROM ad_images WHERE ad_id = :ad_id LIMIT 1");
                    $img_stmt->bindParam(':ad_id', $id, PDO::PARAM_INT);
                    $img_stmt->execute();
                    $old_img = $img_stmt->fetch(PDO::FETCH_COLUMN);
                    
                    if($old_img && file_exists('uploads/ads/'.$old_img)) {
                        unlink('uploads/ads/'.$old_img);
                    }
                    
                    // Update gambar
                    $update_img_stmt = $pdo->prepare("UPDATE ad_images SET image_path = :image_path WHERE ad_id = :ad_id");
                    $update_img_stmt->bindParam(':image_path', $filename);
                    $update_img_stmt->bindParam(':ad_id', $id, PDO::PARAM_INT);
                    $update_img_stmt->execute();
                }
            }

            // Commit transaksi
            $pdo->commit();
            echo "<script>alert('Iklan berhasil diupdate!');window.location='my_ads.php';</script>";
            exit;

        } catch(PDOException $e) {
            // Rollback jika terjadi error
            $pdo->rollBack();
            echo "<script>alert('Gagal mengupdate iklan: ".addslashes($e->getMessage())."');</script>";
        }
    }

    // Ambil kategori
    $cats_stmt = $pdo->prepare("SELECT * FROM categories");
    $cats_stmt->execute();
    $categories = $cats_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "<script>alert('Terjadi kesalahan: ".addslashes($e->getMessage())."');</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Iklan - OLX Clone</title>
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
            font-size: 16px;
        }
        
        .btn:hover {
            background-color: #d35400;
            color: white;
            transform: translateY(-1px);
        }
        
        .form-section {
            padding: 30px 0;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }
        
        .form-container h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--olx-dark);
        }
        
        .form-container label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--olx-dark);
        }
        
        .form-container input,
        .form-container select,
        .form-container textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 16px;
            font-family: inherit;
            box-sizing: border-box;
        }
        
        .form-container textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-container button {
            width: 100%;
            padding: 12px;
            background: var(--olx-orange);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .form-container button:hover {
            background: #d35400;
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
            
            .form-container {
                margin: 0 1rem;
                padding: 1.5rem;
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
            <a href="post.php">Pasang Iklan</a>
            <a href="my_ads.php">Iklan Saya</a>
            <a href="logout.php" class="btn">Logout</a>
        </nav>
    </div>
</header>
<section class="form-section">
    <div class="container form-container">
        <h2>Edit Iklan</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Judul Iklan</label>
            <input type="text" name="title" value="<?= htmlspecialchars($ad['title']) ?>" required>
            <label>Kategori</label>
            <select name="category_id" required>
                <option value="">Pilih Kategori</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $ad['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Harga</label>
            <input type="number" name="price" value="<?= htmlspecialchars($ad['price']) ?>" required>
            <label>Lokasi</label>
            <input type="text" name="location" value="<?= htmlspecialchars($ad['location']) ?>" required>
            <label>Deskripsi</label>
            <textarea name="description" required><?= htmlspecialchars($ad['description']) ?></textarea>
            <label>Ganti Foto Produk (opsional)</label>
            <input type="file" name="image" accept="image/*">
            <button type="submit">Update Iklan</button>
        </form>
    </div>
</section>
</body>
</html>