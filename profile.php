<?php
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

$user_id = $_SESSION['user_id'];

try {
    // Ambil data user menggunakan PDO
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('User tidak ditemukan!');window.location='login.php';</script>";
        exit;
    }

    // Proses update jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Validasi current password
        if (!password_verify($current_password, $user['password'])) {
            echo "<script>alert('Password saat ini salah!');</script>";
        } else {
            // Cek apakah email sudah digunakan oleh user lain
            $email_check = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $email_check->bindParam(':email', $email);
            $email_check->bindParam(':id', $user_id, PDO::PARAM_INT);
            $email_check->execute();
            
            if ($email_check->fetch()) {
                echo "<script>alert('Email sudah digunakan oleh user lain!');</script>";
            } else {
                // Update data dasar
                $update_stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
                $update_stmt->bindParam(':name', $name);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $update_stmt->execute();

                // Update password jika ada
                if (!empty($new_password)) {
                    if ($new_password !== $confirm_password) {
                        echo "<script>alert('Password baru dan konfirmasi tidak cocok!');</script>";
                    } elseif (strlen($new_password) < 6) {
                        echo "<script>alert('Password baru minimal 6 karakter!');</script>";
                    } else {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $pass_update = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                        $pass_update->bindParam(':password', $hashed_password);
                        $pass_update->bindParam(':id', $user_id, PDO::PARAM_INT);
                        $pass_update->execute();
                        
                        // Update session
                        $_SESSION['user_name'] = $name;
                        
                        echo "<script>alert('Profile dan password berhasil diupdate!');window.location='profile.php';</script>";
                        exit;
                    }
                } else {
                    // Update session
                    $_SESSION['user_name'] = $name;
                    
                    echo "<script>alert('Profile berhasil diupdate!');window.location='profile  .php';</script>";
                    exit;
                }
            }
        }
    }

} catch(PDOException $e) {
    echo "<script>alert('Terjadi kesalahan: ".addslashes($e->getMessage())."');</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - OLX Clone</title>
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
        
        .form-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 16px;
            font-family: inherit;
            box-sizing: border-box;
        }
        
        .form-container input:focus {
            outline: none;
            border-color: var(--olx-orange);
            box-shadow: 0 0 0 2px rgba(230, 126, 34, 0.1);
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
        
        .password-section {
            border-top: 1px solid var(--border-color);
            margin-top: 2rem;
            padding-top: 2rem;
        }
        
        .password-section h3 {
            color: var(--olx-dark);
            margin-bottom: 1rem;
        }
        
        .info-text {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
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
            <a href="post-ad.php">Pasang Iklan</a>
            <a href="my_ads.php">Iklan Saya</a>
            <a href="edit_profile.php" style="color: var(--olx-orange); font-weight: 600;">Profile</a>
            <a href="logout.php" class="btn">Logout</a>
        </nav>
    </div>
</header>
<section class="form-section">
    <div class="container form-container">
        <h2>Edit Profile</h2>
        <form action="" method="POST">
            <label>Nama Lengkap</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            
            <div class="password-section">
                <h3>Ubah Password (Opsional)</h3>
                <p class="info-text">Kosongkan jika tidak ingin mengubah password</p>
                
                <label>Password Saat Ini</label>
                <input type="password" name="current_password" placeholder="Masukkan password saat ini">
                
                <label>Password Baru</label>
                <input type="password" name="new_password" placeholder="Minimal 6 karakter">
                
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" placeholder="Ulangi password baru">
            </div>
            
            <button type="submit">Update Profile</button>
        </form>
    </div>
</section>
</body>
</html>
