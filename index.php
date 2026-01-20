<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KF OLX - Platform Jual Beli Terpercaya di Indonesia</title>
    <meta name="description" content="Jual beli online aman dan mudah di KF OLX. Temukan ribuan produk berkualitas dengan harga terbaik.">
    
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
        
        .navbar-nav .nav-link {CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(100)
);

CREATE TABLE ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    location VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE ad_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (ad_id) REFERENCES ads(id)
);
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
        
        .btn-login {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 0 60px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,133.3C960,128,1056,96,1152,90.7C1248,85,1344,107,1392,117.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        .search-container {
            background: white;
            border-radius: 16px;
            padding: 8px;
            box-shadow: var(--shadow-lg);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .search-input {
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 12px;
            flex: 1;
        }
        
        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .search-select {
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            background: var(--light-bg);
            min-width: 200px;
        }
        
        .btn-search {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-search:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        /* Category Section */
        .category-section {
            padding: 60px 0;
        }
        
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1e293b;
        }
        
        .section-subtitle {
            color: var(--text-muted);
            margin-bottom: 3rem;
        }
        
        .category-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }
        
        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }
        
        .category-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 28px;
        }
        
        .category-name {
            font-weight: 600;
            margin-bottom: 4px;
            color: #1e293b;
        }
        
        .category-count {
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        /* Products Section */
        .products-section {
            padding: 60px 0;
            background: var(--light-bg);
        }
        
        .product-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .product-image {
            position: relative;
            padding-top: 75%;
            background: var(--light-bg);
            overflow: hidden;
        }
        
        .product-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-new {
            background: var(--success-color);
            color: white;
        }
        
        .badge-featured {
            background: var(--danger-color);
            color: white;
        }
        
        .product-body {
            padding: 20px;
        }
        
        .product-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 8px;
            color: #1e293b;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-description {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 12px;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        .product-location {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            text-align: center;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }
        
        .cta-subtitle {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            color: rgba(255,255,255,0.9);
        }
        
        .btn-cta {
            background: white;
            color: var(--primary-color);
            border: none;
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.125rem;
            transition: all 0.3s ease;
        }
        
        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        /* Footer */
        footer {
            background: #1e293b;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-brand {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-description {
            color: #94a3b8;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .footer-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: white;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        
        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-links {
            display: flex;
            gap: 12px;
            margin-top: 1rem;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .app-buttons {
            display: flex;
            gap: 12px;
            margin-top: 1rem;
        }
        
        .app-button {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .app-button:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 40px;
            padding-top: 30px;
            text-align: center;
            color: #94a3b8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .search-container {
                flex-direction: column;
                gap: 8px;
            }
            
            .search-select {
                width: 100%;
            }
            
            .btn-search {
                width: 100%;
            }
            
            .cta-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-shop"></i> KF OLX
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-house"></i> Beranda</a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-grid"></i> Kategori</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-plus-circle"></i> Pasang Iklan</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-heart"></i> Favorit</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-person-circle"></i> Masuk</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-person-plus"></i> Daftar</a>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="text-center mb-4">Temukan Barang Impian Anda</h1>
                    <p class="text-center mb-4">Jual beli online terpercaya di seluruh Indonesia</p>
                    
                    <form class="d-flex">
                        <input class="form-control form-control-lg me-2" type="search" placeholder="Cari barang yang Anda inginkan..." aria-label="Search">
                        <select class="form-select form-select-lg me-2" style="max-width: 200px;">
                            <option selected>Semua Kategori</option>
                            <option value="1">Elektronik</option>
                            <option value="2">Kendaraan</option>
                            <option value="3">Properti</option>
                            <option value="4">Fashion</option>
                            <option value="5">Hobi & Olahraga</option>
                        </select>
                        <button class="btn btn-warning btn-lg" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4">Kategori Populer</h2>
            <div class="row g-3">
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card category-card text-center p-3">
                        <i class="bi bi-phone category-icon text-primary"></i>
                        <h6 class="mb-0">Elektronik</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card category-card text-center p-3">
                        <i class="bi bi-car-front category-icon text-success"></i>
                        <h6 class="mb-0">Kendaraan</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card category-card text-center p-3">
                        <i class="bi bi-house-door category-icon text-info"></i>
                        <h6 class="mb-0">Properti</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card category-card text-center p-3">
                        <i class="bi bi-bag category-icon text-warning"></i>
                        <h6 class="mb-0">Fashion</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card category-card text-center p-3">
                        <i class="bi bi-controller category-icon text-danger"></i>
                        <h6 class="mb-0">Hobi</h6>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card category-card text-center p-3">
                        <i class="bi bi-book category-icon text-secondary"></i>
                        <h6 class="mb-0">Buku</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Ads Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Iklan Terbaru</h2>
                <a href="#" class="btn btn-outline-primary">Lihat Semua →</a>
            </div>
            
            <div class="row g-4">
                <!-- Ad Card 1 -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ad-card">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200/4CAF50/FFFFFF?text=Product+1" class="ad-image" alt="Product">
                            <span class="featured-badge">TERLARIS</span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">iPhone 13 Pro Max 256GB</h6>
                            <p class="card-text text-muted small">Mulus, lengkap, garansi resmi...</p>
                            <div class="price-tag mb-2">Rp 15.000.000</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="location-badge">
                                    <i class="bi bi-geo-alt"></i> Jakarta
                                </span>
                                <small class="text-muted">2 jam lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ad Card 2 -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ad-card">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200/2196F3/FFFFFF?text=Product+2" class="ad-image" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Honda Vario 125 2022</h6>
                            <p class="card-text text-muted small">Kilometer rendah, service rutin...</p>
                            <div class="price-tag mb-2">Rp 18.500.000</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="location-badge">
                                    <i class="bi bi-geo-alt"></i> Bandung
                                </span>
                                <small class="text-muted">5 jam lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ad Card 3 -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ad-card">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200/FF9800/FFFFFF?text=Product+3" class="ad-image" alt="Product">
                            <span class="featured-badge">BARU</span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Samsung TV 55" 4K Smart</h6>
                            <p class="card-text text-muted small">Masih garansi, kondisi sempurna...</p>
                            <div class="price-tag mb-2">Rp 8.500.000</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="location-badge">
                                    <i class="bi bi-geo-alt"></i> Surabaya
                                </span>
                                <small class="text-muted">1 hari lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ad Card 4 -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ad-card">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200/9C27B0/FFFFFF?text=Product+4" class="ad-image" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">MacBook Air M1 2020</h6>
                            <p class="card-text text-muted small">Like new, bonus keyboard mouse...</p>
                            <div class="price-tag mb-2">Rp 12.000.000</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="location-badge">
                                    <i class="bi bi-geo-alt"></i> Yogyakarta
                                </span>
                                <small class="text-muted">2 hari lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ad Card 5 -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ad-card">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200/E91E63/FFFFFF?text=Product+5" class="ad-image" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">PlayStation 5 + 2 Joystick</h6>
                            <p class="card-text text-muted small">Dapat 5 game, kondisi 95%...</p>
                            <div class="price-tag mb-2">Rp 7.500.000</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="location-badge">
                                    <i class="bi bi-geo-alt"></i> Medan
                                </span>
                                <small class="text-muted">3 hari lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ad Card 6 -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ad-card">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200/00BCD4/FFFFFF?text=Product+6" class="ad-image" alt="Product">
                            <span class="featured-badge">MURAH</span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">IKEA Sofa Bed 3 Seater</h6>
                            <p class="card-text text-muted small">Warna abu-abu, bisa jadi tempat tidur...</p>
                            <div class="price-tag mb-2">Rp 2.500.000</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="location-badge">
                                    <i class="bi bi-geo-alt"></i> Semarang
                                </span>
                                <small class="text-muted">4 hari lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ad Card 7 -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ad-card">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200/795548/FFFFFF?text=Product+7" class="ad-image" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Canon EOS R6 + Lens Kit</h6>
                            <p class="card-text text-muted small">Body only 8 bulan pakai, mulus...</p>
                            <div class="price-tag mb-2">Rp 28.000.000</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="location-badge">
                                    <i class="bi bi-geo-alt"></i> Bali
                                </span>
                                <small class="text-muted">5 hari lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ad Card 8 -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card ad-card">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/300x200/607D8B/FFFFFF?text=Product+8" class="ad-image" alt="Product">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Gaming PC RTX 3070</h6>
                            <p class="card-text text-muted small">Spec tinggi, bisa main semua game...</p>
                            <div class="price-tag mb-2">Rp 22.000.000</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="location-badge">
                                    <i class="bi bi-geo-alt"></i> Malang
                                </span>
                                <small class="text-muted">1 minggu lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-4">Ingin Jual Barang Anda?</h2>
                    <p class="mb-4">Pasang iklan Anda sekarang juga dan jangkau jutaan pembeli di seluruh Indonesia</p>
                    <button class="btn btn-primary-custom btn-lg">
                        <i class="bi bi-plus-circle"></i> Pasang Iklan Gratis
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="bi bi-shop"></i> KF OLX</h5>
                    <p>Platform jual beli online terpercaya di Indonesia. Aman, mudah, dan cepat.</p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-youtube fs-5"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Perusahaan</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">Tentang Kami</a></li>
                        <li><a href="#" class="text-white-50">Karir</a></li>
                        <li><a href="#" class="text-white-50">Hubungi Kami</a></li>
                        <li><a href="#" class="text-white-50">Blog</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Bantuan</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">Cara Berjualan</a></li>
                        <li><a href="#" class="text-white-50">Cara Berbelanja</a></li>
                        <li><a href="#" class="text-white-50">Keamanan</a></li>
                        <li><a href="#" class="text-white-50">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6>Download Aplikasi</h6>
                    <p>Dapatkan pengalaman terbaik dengan aplikasi mobile kami</p>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-light">
                            <i class="bi bi-apple"></i> App Store
                        </button>
                        <button class="btn btn-outline-light">
                            <i class="bi bi-google-play"></i> Google Play
                        </button>
                    </div>
                </div>
            </div>
            <hr class="bg-white-50">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 KF OLX. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
