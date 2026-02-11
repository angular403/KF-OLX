<?php
require_once 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Email tidak valid';
    }
    
    if (empty($password)) {
        $errors[] = 'Password harus diisi';
    }
    
    if (empty($errors)) {
        // Attempt login
        $user_data = $user->login($email, $password);
        
        if ($user_data) {
            // Set session
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['user_name'] = $user_data['name'];
            $_SESSION['user_email'] = $user_data['email'];
            $_SESSION['logged_in'] = true;
            
            // Set remember me cookie if checked
            if ($remember) {
                $remember_token = generateRandomString(32);
                setcookie('remember_token', $remember_token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                // You might want to store this token in database for better security
            }
            
            // Redirect to dashboard or home
            $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
            unset($_SESSION['redirect_url']);
            header('Location: ' . $redirect_url);
            exit();
        } else {
            $error_message = 'Email atau password salah';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Check if user is already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - KF OLX</title>
    <meta name="description" content="Masuk ke akun KF OLX Anda untuk jual beli online aman dan mudah">
    
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
        
        /* Login Container */
        .login-container {
            margin-top: 80px;
            width: 100%;
            max-width: 1200px;
        }
        
        .login-card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: flex;
            min-height: 600px;
        }
        
        /* Left Side - Login Form */
        .login-form-side {
            flex: 1;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1e293b;
        }
        
        .login-subtitle {
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
        }
        
        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            z-index: 1;
        }
        
        .input-group .form-control {
            padding-left: 48px;
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
            z-index: 1;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .form-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        
        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            margin-right: 8px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-check-label {
            font-size: 0.875rem;
            color: #1e293b;
        }
        
        .forgot-link {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .btn-login {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 24px;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
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
        
        .signup-link {
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        .signup-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .signup-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        /* Right Side - Hero */
        .login-hero-side {
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
        
        .login-hero-side::before {
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
        .btn-login.loading {
            position: relative;
            color: transparent;
        }
        
        .btn-login.loading::after {
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
            .login-card {
                flex-direction: column;
                max-width: 500px;
                margin: 0 auto;
            }
            
            .login-hero-side {
                display: none;
            }
            
            .login-form-side {
                padding: 32px 24px;
            }
        }
        
        @media (max-width: 576px) {
            .login-form-side {
                padding: 24px 16px;
            }
            
            .login-title {
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
                        <a class="nav-link" href="#"><i class="bi bi-plus-circle"></i> Pasang Iklan</a>
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

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Left Side - Login Form -->
            <div class="login-form-side">
                <div class="login-header">
                    <h1 class="login-title">Selamat Datang Kembali!</h1>
                    <p class="login-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
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
                
                <form id="loginForm" method="POST" action="login.php">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password Anda" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-check d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="remember">
                                Ingat saya
                            </label>
                        </div>
                        <a href="#" class="forgot-link">Lupa password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Masuk
                    </button>
                </form>
                
                <div class="divider">
                    <span>atau masuk dengan</span>
                </div>
                
                <div class="social-login">
                    <a href="#" class="btn-social btn-google">
                        <i class="bi bi-google"></i>
                        Google
                    </a>
                    <a href="#" class="btn-social btn-facebook">
                        <i class="bi bi-facebook"></i>
                        Facebook
                    </a>
                </div>
                
                <div class="signup-link">
                    Belum punya akun? <a href="register.php">Daftar sekarang</a>
                </div>
            </div>
            
            <!-- Right Side - Hero -->
            <div class="login-hero-side">
                <div class="hero-content">
                    <i class="bi bi-shop hero-icon"></i>
                    <h2 class="hero-title">Jual Beli Aman & Mudah</h2>
                    <p class="hero-description">
                        Bergabunglah dengan jutaan pengguna yang percaya pada KF OLX untuk transaksi online yang aman dan nyaman.
                    </p>
                    
                    <div class="hero-features">
                        <div class="hero-feature">
                            <i class="bi bi-shield-check"></i>
                            <span class="hero-feature-text">Transaksi aman terjamin</span>
                        </div>
                        <div class="hero-feature">
                            <i class="bi bi-people"></i>
                            <span class="hero-feature-text">Jutaan pengguna terpercaya</span>
                        </div>
                        <div class="hero-feature">
                            <i class="bi bi-lightning"></i>
                            <span class="hero-feature-text">Proses cepat dan mudah</span>
                        </div>
                        <div class="hero-feature">
                            <i class="bi bi-headset"></i>
                            <span class="hero-feature-text">Support 24/7 siap membantu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'bi bi-eye';
            }
        }
        
        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const form = e.target;
            const loginBtn = document.getElementById('loginBtn');
            
            // Form validation
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return;
            }
            
            // Show loading state
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
            
            // Form will be submitted normally to PHP backend
        });
        
        // Social login handlers
        document.querySelectorAll('.btn-social').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const provider = this.classList.contains('btn-google') ? 'Google' : 'Facebook';
                console.log(`Login with ${provider}`);
                // Implement social login logic here
            });
        });
        
        // Forgot password handler
        document.querySelector('.forgot-link').addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Forgot password clicked');
            // Implement forgot password logic here
        });
    </script>
</body>
</html>
