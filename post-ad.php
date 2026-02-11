<?php
require_once 'config.php';

// Check if user is logged in
requireLogin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $category_id = sanitizeInput($_POST['category'] ?? '');
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $price = sanitizeInput($_POST['price'] ?? '');
    $location = sanitizeInput($_POST['location'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($category_id)) {
        $errors[] = 'Kategori harus dipilih';
    }
    
    if (empty($title)) {
        $errors[] = 'Judul iklan harus diisi';
    } elseif (strlen($title) > 150) {
        $errors[] = 'Judul iklan maksimal 150 karakter';
    }
    
    if (empty($description)) {
        $errors[] = 'Deskripsi harus diisi';
    } elseif (strlen($description) > 2000) {
        $errors[] = 'Deskripsi maksimal 2000 karakter';
    }
    
    if (empty($price) || !is_numeric($price) || $price < 0) {
        $errors[] = 'Harga harus diisi dengan angka yang valid';
    }
    
    if (empty($location)) {
        $errors[] = 'Lokasi harus diisi';
    }
    
    // Handle image uploads
    $uploaded_images = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $files = $_FILES['images'];
        $file_count = count($files['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
                
                $upload_result = uploadFile($file, UPLOAD_PATH . 'ads/', ['jpg', 'jpeg', 'png', 'gif'], MAX_FILE_SIZE);
                
                if ($upload_result['success']) {
                    $uploaded_images[] = $upload_result['file_path'];
                } else {
                    $errors[] = $upload_result['message'];
                }
            }
        }
    }
    
    if (empty($uploaded_images)) {
        $errors[] = 'Minimal 1 foto produk harus diupload';
    }
    
    if (empty($errors)) {
        // Create ad
        $ad_id = $ad->createAd(
            $_SESSION['user_id'],
            $category_id,
            $title,
            $description,
            $price,
            $location
        );
        
        if ($ad_id) {
            // Save images
            foreach ($uploaded_images as $image_path) {
                $adImage->addImage($ad_id, $image_path);
            }
            
            // Set success message
            $_SESSION['success_message'] = 'Iklan berhasil dipasang!';
            
            // Redirect to ad detail or user ads
            header('Location: index.php');
            exit();
        } else {
            $error_message = 'Gagal memasang iklan. Silakan coba lagi.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Get categories for dropdown
$categories_list = $category->getAllCategories();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasang Iklan - KF OLX</title>
    <meta name="description" content="Pasang iklan gratis di KF OLX untuk menjual barang Anda dengan cepat dan mudah">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
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
            background: var(--light-bg);
        }
        
        /* Navbar Styles */
        .navbar {
            background: white;
            box-shadow: var(--shadow-sm);
            padding: 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--secondary-color) !important;
            padding: 1rem 1rem !important;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 2px;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
            background: var(--light-bg);
        }
        
        /* Main Container */
        .post-ad-container {
            max-width: 800px;
            margin: 120px auto 60px;
            padding: 0 20px;
        }
        
        .post-ad-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .post-ad-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #1e293b;
        }
        
        .post-ad-subtitle {
            font-size: 1.125rem;
            color: var(--text-muted);
        }
        
        /* Form Card */
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            padding: 40px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 24px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-icon {
            width: 40px;
            height: 40px;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #1e293b;
            font-size: 0.875rem;
            display: block;
        }
        
        .required {
            color: var(--danger-color);
        }
        
        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .form-control:hover, .form-select:hover {
            border-color: #cbd5e1;
        }
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
            background: rgba(239, 68, 68, 0.02);
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.8rem;
            margin-top: 6px;
            display: none;
        }
        
        .invalid-feedback.show {
            display: block;
        }
        
        /* Category Grid */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .category-item {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        
        .category-item:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
            transform: translateY(-2px);
        }
        
        .category-item.selected {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
        }
        
        .category-icon {
            font-size: 2rem;
            margin-bottom: 8px;
            color: var(--primary-color);
        }
        
        .category-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1e293b;
        }
        
        /* Image Upload */
        .image-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background: var(--light-bg);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .image-upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }
        
        .image-upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 16px;
        }
        
        .upload-text {
            font-weight: 600;
            margin-bottom: 8px;
            color: #1e293b;
        }
        
        .upload-subtext {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 16px;
        }
        
        .btn-upload {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-upload:hover {
            background: var(--primary-dark);
        }
        
        .image-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }
        
        .image-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 1;
        }
        
        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .image-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.75rem;
        }
        
        /* Price Input */
        .price-input-group {
            position: relative;
        }
        
        .price-prefix {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        
        .price-input {
            padding-left: 50px !important;
        }
        
        /* Location Input */
        .location-input-group {
            position: relative;
        }
        
        .location-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            z-index: 1;
        }
        
        .location-input {
            padding-left: 48px !important;
        }
        
        /* Character Counter */
        .char-counter {
            text-align: right;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .char-counter.warning {
            color: var(--warning-color);
        }
        
        .char-counter.danger {
            color: var(--danger-color);
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 16px;
            margin-top: 40px;
        }
        
        .btn-submit {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
        }
        
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }
        
        .btn-submit:disabled {
            background: var(--secondary-color);
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-cancel {
            background: white;
            color: var(--text-muted);
            border: 2px solid var(--border-color);
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            border-color: var(--secondary-color);
            background: var(--light-bg);
        }
        
        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        /* Loading State */
        .btn-submit.loading {
            position: relative;
            color: transparent;
        }
        
        .btn-submit.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid white;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spinner 0.8s linear infinite;
        }
        
        @keyframes spinner {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .post-ad-container {
                margin: 100px auto 40px;
                padding: 0 16px;
            }
            
            .form-card {
                padding: 24px;
            }
            
            .post-ad-title {
                font-size: 2rem;
            }
            
            .category-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-shop"></i> KF OLX
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-grid"></i> Pasang Iklan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="detail.php"><i class="bi bi-plus-circle"></i> Iklan Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-heart"></i> Favorit</a>
                    </li>
                    <li>
                             <?php if (isset($_SESSION['user_id'])): ?>
                        <span>Halo, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="logout.php" class="btn">Logout</a>
                    <?php else: ?>
                        <a href="login.html" class="btn">Login</a>
                        <a href="register.html" class="btn btn-outline">Register</a>
                    <?php endif; ?>
                    </li>
                </ul>
       
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="post-ad-container">
        <div class="post-ad-header">
            <h1 class="post-ad-title">Pasang Iklan Baru</h1>
            <p class="post-ad-subtitle">Jual barang Anda dengan cepat dan mudah</p>
        </div>
        
        <!-- Alert Messages -->
        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle me-2"></i>
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
        <?php endif; ?>
        
        <form id="postAdForm" method="POST" enctype="multipart/form-data" novalidate>
            <!-- Informasi Dasar -->
            <div class="form-card">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>
                    Informasi Dasar
                </div>
                
                <div class="form-group">
                    <label for="category" class="form-label">Kategori <span class="required">*</span></label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories_list as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category']) && $_POST['category'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Silakan pilih kategori</div>
                </div>
                
                <div class="form-group">
                    <label for="title" class="form-label">Judul Iklan <span class="required">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Contoh: iPhone 13 Pro Max 256GB Mulus" maxlength="150" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                    <div class="char-counter" id="titleCounter"><?php echo strlen($_POST['title'] ?? ''); ?> / 150</div>
                    <div class="invalid-feedback">Judul iklan harus diisi</div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi <span class="required">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="6" placeholder="Jelaskan kondisi barang, spesifikasi, kelengkapan, dan informasi penting lainnya..." maxlength="2000" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    <div class="char-counter" id="descCounter"><?php echo strlen($_POST['description'] ?? ''); ?> / 2000</div>
                    <div class="invalid-feedback">Deskripsi harus diisi</div>
                </div>
            </div>
            
            <!-- Foto Produk -->
            <div class="form-card">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="bi bi-camera"></i>
                    </div>
                    Foto Produk
                </div>
                
                <div class="image-upload-area" id="imageUploadArea">
                    <i class="bi bi-cloud-upload upload-icon"></i>
                    <div class="upload-text">Drag & drop foto di sini</div>
                    <div class="upload-subtext">atau klik untuk memilih file</div>
                    <button type="button" class="btn-upload">Pilih Foto</button>
                    <input type="file" id="imageInput" name="images[]" multiple accept="image/*" style="display: none;">
                </div>
                
                <div class="image-preview" id="imagePreview"></div>
                
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Maksimal 8 foto. Format: JPG, PNG, GIF. Maksimal 5MB per foto.
                </small>
            </div>
            
            <!-- Harga dan Lokasi -->
            <div class="form-card">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="bi bi-tag"></i>
                    </div>
                    Harga dan Lokasi
                </div>
                
                <div class="form-group">
                    <label for="price" class="form-label">Harga <span class="required">*</span></label>
                    <div class="price-input-group">
                        <span class="price-prefix">Rp</span>
                        <input type="number" class="form-control price-input" id="price" name="price" placeholder="0" min="0" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                    </div>
                    <div class="invalid-feedback">Harga harus diisi</div>
                </div>
                
                <div class="form-group">
                    <label for="location" class="form-label">Lokasi <span class="required">*</span></label>
                    <select class="form-select" id="location" name="location" required>
                        <option value="">Pilih Lokasi</option>
                        <?php 
                        // Static locations based on database structure
                        $locations = [
                            'Jakarta Pusat, DKI Jakarta',
                            'Jakarta Utara, DKI Jakarta', 
                            'Jakarta Barat, DKI Jakarta',
                            'Jakarta Selatan, DKI Jakarta',
                            'Jakarta Timur, DKI Jakarta',
                            'Bandung, Jawa Barat',
                            'Bogor, Jawa Barat',
                            'Depok, Jawa Barat',
                            'Bekasi, Jawa Barat',
                            'Cimahi, Jawa Barat',
                            'Sukabumi, Jawa Barat',
                            'Cirebon, Jawa Barat',
                            'Tangerang, Banten',
                            'Tangerang Selatan, Banten',
                            'Semarang, Jawa Tengah',
                            'Surakarta (Solo), Jawa Tengah',
                            'Magelang, Jawa Tengah',
                            'Pekalongan, Jawa Tengah',
                            'Salatiga, Jawa Tengah',
                            'Tegal, Jawa Tengah',
                            'Yogyakarta, DI Yogyakarta',
                            'Surabaya, Jawa Timur',
                            'Malang, Jawa Timur',
                            'Kediri, Jawa Timur',
                            'Madiun, Jawa Timur',
                            'Jember, Jawa Timur',
                            'Batu, Jawa Timur',
                            'Blitar, Jawa Timur',
                            'Probolinggo, Jawa Timur',
                            'Pasuruan, Jawa Timur',
                            'Mojokerto, Jawa Timur',
                            'Medan, Sumatera Utara',
                            'Binjai, Sumatera Utara',
                            'Tebingtinggi, Sumatera Utara',
                            'Pematangsiantar, Sumatera Utara',
                            'Padangsidimpuan, Sumatera Utara',
                            'Padang, Sumatera Barat',
                            'Bukittinggi, Sumatera Barat',
                            'Payakumbuh, Sumatera Barat',
                            'Pariaman, Sumatera Barat',
                            'Solok, Sumatera Barat',
                            'Pekanbaru, Riau',
                            'Dumai, Riau',
                            'Bengkalis, Riau',
                            'Palembang, Sumatera Selatan',
                            'Prabumulih, Sumatera Selatan',
                            'Lubuklinggau, Sumatera Selatan',
                            'Pagar Alam, Sumatera Selatan',
                            'Bandar Lampung, Lampung',
                            'Metro, Lampung',
                            'Denpasar, Bali',
                            'Badung, Bali',
                            'Gianyar, Bali',
                            'Tabanan, Bali',
                            'Banjarmasin, Kalimantan Selatan',
                            'Balikpapan, Kalimantan Timur',
                            'Samarinda, Kalimantan Timur',
                            'Pontianak, Kalimantan Barat',
                            'Palangkaraya, Kalimantan Tengah',
                            'Makassar, Sulawesi Selatan',
                            'Kendari, Sulawesi Tenggara',
                            'Palu, Sulawesi Tengah',
                            'Manado, Sulawesi Utara',
                            'Gorontalo, Gorontalo',
                            'Mataram, Nusa Tenggara Barat',
                            'Kupang, Nusa Tenggara Timur',
                            'Jayapura, Papua',
                            'Sorong, Papua Barat'
                        ];
                        
                        foreach ($locations as $loc): 
                        ?>
                        <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo (isset($_POST['location']) && $_POST['location'] == $loc) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($loc); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Lokasi harus diisi</div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <button type="button" class="btn-cancel" onclick="window.location.href='index.php'">
                    Batal
                </button>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-send me-2"></i>
                    Pasang Iklan
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category Selection
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.category-item').forEach(cat => cat.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('category').value = this.dataset.category;
            });
        });
        
        // Character Counter
        function updateCharCounter(inputId, counterId, maxLength) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(counterId);
            
            input.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = `${length} / ${maxLength}`;
                
                counter.classList.remove('warning', 'danger');
                if (length > maxLength * 0.9) {
                    counter.classList.add('danger');
                } else if (length > maxLength * 0.8) {
                    counter.classList.add('warning');
                }
            });
        }
        
        updateCharCounter('title', 'titleCounter', 150);
        updateCharCounter('description', 'descCounter', 2000);
        
        // Image Upload
        const imageUploadArea = document.getElementById('imageUploadArea');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        let uploadedImages = [];
        
        imageUploadArea.addEventListener('click', () => imageInput.click());
        
        imageUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadArea.classList.add('dragover');
        });
        
        imageUploadArea.addEventListener('dragleave', () => {
            imageUploadArea.classList.remove('dragover');
        });
        
        imageUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });
        
        imageInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
        
        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (uploadedImages.length >= 8) {
                    alert('Maksimal 8 foto');
                    return;
                }
                
                if (!file.type.startsWith('image/')) {
                    alert('Hanya file gambar yang diperbolehkan');
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('Maksimal 5MB per foto');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    const imageId = Date.now() + Math.random();
                    uploadedImages.push({
                        id: imageId,
                        file: file,
                        url: e.target.result
                    });
                    
                    addImagePreview(imageId, e.target.result);
                };
                reader.readAsDataURL(file);
            });
        }
        
        function addImagePreview(id, url) {
            const imageItem = document.createElement('div');
            imageItem.className = 'image-item';
            imageItem.dataset.imageId = id;
            imageItem.innerHTML = `
                <img src="${url}" alt="Preview">
                <button type="button" class="image-remove" onclick="removeImage(${id})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            imagePreview.appendChild(imageItem);
        }
        
        function removeImage(id) {
            uploadedImages = uploadedImages.filter(img => img.id !== id);
            const imageItem = document.querySelector(`[data-image-id="${id}"]`);
            if (imageItem) {
                imageItem.remove();
            }
        }
        
        // Form Submission
        document.getElementById('postAdForm').addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = document.getElementById('submitBtn');
            
            // Form validation
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return;
            }
            
            // Check if images are selected
            const imageInput = document.getElementById('imageInput');
            if (imageInput.files.length === 0) {
                e.preventDefault();
                alert('Silakan upload minimal 1 foto produk');
                return;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Form will be submitted normally to PHP backend
        });
        
        // Price formatting
        document.getElementById('price').addEventListener('input', function() {
            if (this.value < 0) {
                this.value = 0;
            }
        });
    </script>
</body>
</html>
