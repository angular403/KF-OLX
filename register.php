<?php
require_once 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $firstName = sanitizeInput($_POST['firstName'] ?? '');
    $lastName = sanitizeInput($_POST['lastName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $whatsapp = sanitizeInput($_POST['whatsapp'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $terms = isset($_POST['terms']) ? 1 : 0;
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    if (empty($firstName)) {
        $errors[] = 'Nama depan harus diisi';
    }
    
    // if (empty($lastName)) {
    //     $errors[] = 'Nama belakang harus diisi';
    // }
    
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Email tidak valid';
    } elseif ($user->emailExists($email)) {
        $errors[] = 'Email sudah terdaftar';
    } 
    
    if (empty($whatsapp)) {
        $errors[] = 'Nomor Whatsapp harus diisi';
    } elseif (!preg_match('/^[0-9]{10,13}$/', preg_replace('/[^0-9]/', '', $whatsapp))) {
        $errors[] = 'Nomor Whatsapp tidak valid';
    }
    
    if (empty($password)) {
        $errors[] = 'Password harus diisi';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Password tidak cocok';
    }
    
    // if (!$terms) {
    //     $errors[] = 'Anda harus menyetujui syarat dan ketentuan';
    // }
    
    if (empty($errors)) {
        // Combine first and last name
        $fullName = $firstName . ' ' . $lastName;
        
        // Register user
        if ($user->register($fullName, $email, $password)) {
            // Set success message
            $_SESSION['success_message'] = 'Pendaftaran berhasil! Silakan login.';
            
            // Redirect to login page
            header('Location: login.php');
            exit();
        } else {
            $error_message = 'Pendaftaran gagal. Silakan coba lagi.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - KF OLX</title>
    <meta name="description" content="Daftar akun KF OLX gratis dan mulai jual beli online aman dan mudah">
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;

        }
        
        /* Navbar Styles */
        .navbar {
            background: white;
            box-shadow: var(--shadow-sm);
            padding: 0;
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
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
        
        /* Register Container */
        .register-container {
            margin-top: 80px;
            width: 100%;
            max-width: 1200px;
        }
        
        .register-card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: flex;
            min-height: 700px;
        }
        
        /* Left Side - Register Form */
        .register-form-side {
            flex: 1;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-height: 700px;
            overflow-y: auto;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .register-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1e293b;
        }
        
        .register-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
        }
        
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
        
        .form-control {
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
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }
        
        .form-control:hover {
            border-color: #cbd5e1;
        }
        
        .form-control.is-invalid {
            border-color: var(--danger-color);
            background: rgba(239, 68, 68, 0.02);
        }
        
        .form-control.is-valid {
            border-color: var(--success-color);
            background: rgba(16, 185, 129, 0.02);
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
        
        .valid-feedback {
            color: var(--success-color);
            font-size: 0.8rem;
            margin-top: 6px;
            display: none;
        }
        
        .valid-feedback.show {
            display: block;
        }
        
        .input-group {
            position: relative;
            margin-bottom: 0;
        }
        
        .input-group .form-control {
            padding-left: 48px;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            z-index: 1;
            font-size: 1.1rem;
            pointer-events: none;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            z-index: 2;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
        }
        
        .password-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .password-strength {
            margin-top: 8px;
        }
        
        .strength-meter {
            height: 4px;
            border-radius: 2px;
            background: var(--border-color);
            overflow: hidden;
        }
        
        .strength-meter-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { background: var(--danger-color); width: 33%; }
        .strength-medium { background: var(--warning-color); width: 66%; }
        .strength-strong { background: var(--success-color); width: 100%; }
        
        .strength-text {
            font-size: 0.75rem;
            margin-top: 4px;
        }
        
        .form-check {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 4px 0;
        }
        
        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            margin-right: 12px;
            margin-top: 2px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-check-input:hover {
            border-color: var(--primary-color);
        }
        
        .form-check-label {
            font-size: 0.875rem;
            color: #1e293b;
            line-height: 1.5;
            cursor: pointer;
            flex: 1;
        }
        
        .form-check-label a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .form-check-label a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .btn-register {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 24px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .btn-register:disabled {
            background: var(--secondary-color);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-register:hover::before {
            left: 100%;
        }
        
        .divider {
            text-align: center;
            margin: 32px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-color);
        }
        
        .divider span {
            background: white;
            padding: 0 16px;
            color: var(--text-muted);
            font-size: 0.875rem;
            position: relative;
        }
        
        .social-login {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
        }
        
        .btn-social {
            flex: 1;
            border: 2px solid var(--border-color);
            background: white;
            padding: 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.3s ease;
        }
        
        .btn-social:hover {
            border-color: var(--primary-color);
            background: var(--light-bg);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-google {
            color: #ea4335;
        }
        
        .btn-facebook {
            color: #1877f2;
        }
        
        .login-link {
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        /* Right Side - Hero */
        .register-hero-side {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .register-hero-side::before {
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
            text-align: center;
        }
        
        .hero-icon {
            font-size: 4rem;
            margin-bottom: 24px;
            opacity: 0.9;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 16px;
        }
        
        .hero-description {
            font-size: 1.125rem;
            margin-bottom: 32px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .hero-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
            max-width: 300px;
        }
        
        .hero-feature {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .hero-feature i {
            font-size: 1.25rem;
            opacity: 0.9;
        }
        
        .hero-feature-text {
            font-size: 0.875rem;
            opacity: 0.9;
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
        .btn-register.loading {
            position: relative;
            color: transparent;
        }
        
        .btn-register.loading::after {
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
        @media (max-width: 992px) {
            .register-card {
                flex-direction: column;
                max-width: 500px;
                margin: 0 auto;
            }
            
            .register-hero-side {
                display: none;
            }
            
            .register-form-side {
                padding: 32px 24px;
                max-height: none;
            }
        }
        
        @media (max-width: 576px) {
            .register-form-side {
                padding: 24px 16px;
            }
            
            .register-title {
                font-size: 1.5rem;
            }
            
            .social-login {
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
                        <a class="nav-link" href="#"><i class="bi bi-grid"></i> Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="post-ad.php"><i class="bi bi-plus-circle"></i> Pasang Iklan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-heart"></i> Favorit</a>
                    </li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <a href="login.php" class="btn btn-outline-primary px-4 py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </a>
                    <a href="register.php" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-person-plus me-2"></i>Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Register Container -->
    <div class="register-container">
        <div class="register-card">
            <!-- Left Side - Register Form -->
            <div class="register-form-side">
                <div class="register-header">
                    <h1 class="register-title">Buat Akun Baru</h1>
                    <p class="register-subtitle">Bergabunglah dengan jutaan pengguna KF OLX</p>
                </div>
                
                <!-- Alert Messages -->
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <form id="registerForm" method="POST" action="register.php" novalidate>
                    <div class="row">
                    

                            <div class="form-group">
                                <br>
                                <label for="firstName" class="form-label">Nama Lengkap</label>
                                <div class="input-group">
                                    <i class="bi bi-person input-icon"></i>
                                    <input type="text" autocomplete="off"  class="form-control" id="firstName" name="firstName" placeholder="Masukkan nama depan" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>" required>
                                    <div class="invalid-feedback">Nama Lengkap Anda harus diisi</div>
                                </div>
                            </div>
                       
                
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" class="form-control"  autocomplete="off" id="email" name="email" placeholder="Masukkan email Anda" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            <div class="invalid-feedback">Email tidak valid</div>
                        </div>
                    </div>
                    

                    <div class="form-group">
                        <label for="whatsapp" class="form-label">Whatsapp</label>
                        <div class="input-group">
                            <i class="bi bi-phone input-icon"></i>
                            <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="(+62)" value="<?php echo isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : ''; ?>" required>
                            <div class="invalid-feedback">Nomor Whatsapp tidak valid</div>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="strength-meter">
                                <div class="strength-meter-fill" id="strengthMeter"></div>
                            </div>
                            <div class="strength-text" id="strengthText"></div>
                        </div>
                        <div class="invalid-feedback">Password minimal 8 karakter</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Konfirmasi password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Password tidak cocok</div>
                    </div>
                    
                    <!-- <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> required>
                        <label class="form-check-label" for="terms">
                            Saya menyetujui <a href="#">Syarat dan Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> KF OLX
                        </label>
                        <div class="invalid-feedback">Anda harus menyetujui syarat dan ketentuan</div>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter" <?php echo isset($_POST['newsletter']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="newsletter">
                            Saya ingin menerima newsletter dan penawaran menarik dari KF OLX
                        </label>
                    </div> -->
                    
                    <button type="submit" class="btn-register" id="registerBtn">
                        <i class="bi bi-person-plus me-2"></i>
                        Daftar Sekarang
                    </button>
                </form>

                <div class="login-link">
                    Sudah punya akun? <a href="login.php">Masuk di sini</a>
                </div>
            </div>
            
            <!-- Right Side - Hero -->
            <!-- <div class="register-hero-side">
                <div class="hero-content">
                    <i class="bi bi-person-plus hero-icon"></i>
                    <h2 class="hero-title">Bergabung dengan Komunitas Terpercaya</h2>
                    <p class="hero-description">
                        Daftar sekarang dan nikmati kemudahan jual beli online dengan jutaan pengguna terpercaya di seluruh Indonesia.
                    </p>
                    
                    <div class="hero-features">
                        <div class="hero-feature">
                            <i class="bi bi-shield-check"></i>
                            <span class="hero-feature-text">Keamanan data terjamin</span>
                        </div>
                        <div class="hero-feature">
                            <i class="bi bi-gift"></i>
                            <span class="hero-feature-text">Dapatkan penawaran eksklusif</span>
                        </div>
                        <div class="hero-feature">
                            <i class="bi bi-trophy"></i>
                            <span class="hero-feature-text">Bangun reputasi penjual</span>
                        </div>
                        <div class="hero-feature">
                            <i class="bi bi-lightning"></i>
                            <span class="hero-feature-text">Transaksi cepat & mudah</span>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const passwordIcon = document.getElementById(fieldId + 'Icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'bi bi-eye';
            }
        }
        
        // Password strength checker
        function checkPasswordStrength(password) {
            const strengthMeter = document.getElementById('strengthMeter');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            
            strengthMeter.className = 'strength-meter-fill';
            
            if (password.length === 0) {
                strengthMeter.style.width = '0';
                strengthText.textContent = '';
            } else if (strength < 3) {
                strengthMeter.classList.add('strength-weak');
                strengthText.textContent = 'Lemah';
                strengthText.style.color = 'var(--danger-color)';
            } else if (strength < 4) {
                strengthMeter.classList.add('strength-medium');
                strengthText.textContent = 'Sedang';
                strengthText.style.color = 'var(--warning-color)';
            } else {
                strengthMeter.classList.add('strength-strong');
                strengthText.textContent = 'Kuat';
                strengthText.style.color = 'var(--success-color)';
            }
        }
        
        // Real-time password strength checking
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
        
        // Handle form submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const form = e.target;
            const registerBtn = document.getElementById('registerBtn');
            
            // Clear previous validation
            form.classList.remove('was-validated');
            
            // Check password match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                document.getElementById('confirmPassword').classList.add('is-invalid');
                e.preventDefault();
                return;
            } else {
                document.getElementById('confirmPassword').classList.remove('is-invalid');
            }
            
            // Form validation
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                e.preventDefault();
                return;
            }
            
            // Show loading state
            registerBtn.classList.add('loading');
            registerBtn.disabled = true;
            
            // Form will be submitted normally
        });
        
        // Social login handlers
        document.querySelectorAll('.btn-social').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const provider = this.classList.contains('btn-google') ? 'Google' : 'Facebook';
                console.log(`Register with ${provider}`);
                // Implement social registration logic here
            });
        });
        
        // Real-time validation
        document.getElementById('email').addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        document.getElementById('phone').addEventListener('blur', function() {
            const phoneRegex = /^[0-9]{10,13}$/;
            if (this.value && !phoneRegex.test(this.value.replace(/[^0-9]/g, ''))) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>
