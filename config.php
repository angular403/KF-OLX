<?php
/**
 * Database Configuration for KF OLX
 * Using PDO with MySQL
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'kf_olx');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('APP_NAME', 'KF OLX');
define('APP_URL', 'http://localhost/kf-olx');
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start Session
session_start();

// Database Connection Class
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $pdo;
    private $stmt;
    
    // Get database connection
    public function getConnection() {
        $this->pdo = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->pdo;
    }
}

// User Class
class User {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Register new user
    public function register($name, $email, $password) {
        try {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Login user
    public function login($email, $password) {
        try {
            $sql = "SELECT id, name, email, password FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Get user by ID
    public function getUserById($id) {
        try {
            $sql = "SELECT id, name, email, created_at FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Check if email exists
    public function emailExists($email) {
        try {
            $sql = "SELECT id FROM users WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Update user profile
    public function updateProfile($id, $name, $email) {
        try {
            $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
}

// Category Class
class Category {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all categories
    public function getAllCategories() {
        try {
            $sql = "SELECT * FROM categories ORDER BY name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Get category by ID
    public function getCategoryById($id) {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Add new category
    public function addCategory($name, $icon) {
        try {
            $sql = "INSERT INTO categories (name, icon) VALUES (:name, :icon)";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':icon', $icon);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
}

// Ad Class
class Ad {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create new ad
    public function createAd($user_id, $category_id, $title, $description, $price, $location) {
        try {
            $sql = "INSERT INTO ads (user_id, category_id, title, description, price, location) 
                    VALUES (:user_id, :category_id, :title, :description, :price, :location)";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':location', $location);
            
            $stmt->execute();
            
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Get all ads with pagination
    public function getAllAds($limit = 10, $offset = 0) {
        try {
            $sql = "SELECT a.*, u.name as user_name, c.name as category_name, c.icon as category_icon
                    FROM ads a
                    LEFT JOIN users u ON a.user_id = u.id
                    LEFT JOIN categories c ON a.category_id = c.id
                    ORDER BY a.created_at DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Get ad by ID with images
    public function getAdById($id) {
        try {
            $sql = "SELECT a.*, u.name as user_name, u.email as user_email, c.name as category_name
                    FROM ads a
                    LEFT JOIN users u ON a.user_id = u.id
                    LEFT JOIN categories c ON a.category_id = c.id
                    WHERE a.id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Get ads by user
    public function getAdsByUser($user_id, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT a.*, c.name as category_name, c.icon as category_icon
                    FROM ads a
                    LEFT JOIN categories c ON a.category_id = c.id
                    WHERE a.user_id = :user_id
                    ORDER BY a.created_at DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Get ads by category
    public function getAdsByCategory($category_id, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT a.*, u.name as user_name, c.name as category_name
                    FROM ads a
                    LEFT JOIN users u ON a.user_id = u.id
                    LEFT JOIN categories c ON a.category_id = c.id
                    WHERE a.category_id = :category_id
                    ORDER BY a.created_at DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Search ads
    public function searchAds($keyword, $category_id = null, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT a.*, u.name as user_name, c.name as category_name, c.icon as category_icon
                    FROM ads a
                    LEFT JOIN users u ON a.user_id = u.id
                    LEFT JOIN categories c ON a.category_id = c.id
                    WHERE (a.title LIKE :keyword OR a.description LIKE :keyword)";
            
            $params = [':keyword' => '%' . $keyword . '%'];
            
            if ($category_id) {
                $sql .= " AND a.category_id = :category_id";
                $params[':category_id'] = $category_id;
            }
            
            $sql .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Update ad
    public function updateAd($id, $title, $description, $price, $location, $category_id) {
        try {
            $sql = "UPDATE ads SET title = :title, description = :description, price = :price, 
                    location = :location, category_id = :category_id WHERE id = :id";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Delete ad
    public function deleteAd($id) {
        try {
            // First delete ad images
            $imageModel = new AdImage($this->conn);
            $imageModel->deleteImagesByAd($id);
            
            // Then delete ad
            $sql = "DELETE FROM ads WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Count total ads
    public function countAds() {
        try {
            $sql = "SELECT COUNT(*) as total FROM ads";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['total'];
        } catch(PDOException $e) {
            return 0;
        }
    }
}

// Ad Image Class
class AdImage {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Add image to ad
    public function addImage($ad_id, $image_path) {
        try {
            $sql = "INSERT INTO ad_images (ad_id, image_path) VALUES (:ad_id, :image_path)";
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':ad_id', $ad_id);
            $stmt->bindParam(':image_path', $image_path);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Get images by ad ID
    public function getImagesByAd($ad_id) {
        try {
            $sql = "SELECT * FROM ad_images WHERE ad_id = :ad_id ORDER BY id ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ad_id', $ad_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Delete image
    public function deleteImage($id) {
        try {
            // Get image path first
            $sql = "SELECT image_path FROM ad_images WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $image = $stmt->fetch();
            
            if ($image && file_exists($image['image_path'])) {
                unlink($image['image_path']);
            }
            
            // Delete from database
            $sql = "DELETE FROM ad_images WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Delete all images for an ad
    public function deleteImagesByAd($ad_id) {
        try {
            // Get all images first
            $sql = "SELECT image_path FROM ad_images WHERE ad_id = :ad_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ad_id', $ad_id);
            $stmt->execute();
            
            $images = $stmt->fetchAll();
            
            // Delete files
            foreach ($images as $image) {
                if (file_exists($image['image_path'])) {
                    unlink($image['image_path']);
                }
            }
            
            // Delete from database
            $sql = "DELETE FROM ad_images WHERE ad_id = :ad_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ad_id', $ad_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }
}

// Helper Functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function formatPrice($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Baru saja';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' menit lalu';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' jam lalu';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' hari lalu';
    } else {
        return formatDate($datetime);
    }
}

// File Upload Function
function uploadFile($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 5242880) {
    $result = ['success' => false, 'message' => '', 'file_path' => ''];
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $result['message'] = 'Tidak ada file yang diupload';
        return $result;
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        $result['message'] = 'Ukuran file terlalu besar. Maksimal ' . ($max_size / 1024 / 1024) . 'MB';
        return $result;
    }
    
    // Check file type
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        $result['message'] = 'Tipe file tidak diizinkan. Hanya: ' . implode(', ', $allowed_types);
        return $result;
    }
    
    // Create directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = time() . '_' . generateRandomString(10) . '.' . $file_ext;
    $target_path = $target_dir . $filename;
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $result['success'] = true;
        $result['file_path'] = $target_path;
    } else {
        $result['message'] = 'Gagal mengupload file';
    }
    
    return $result;
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Check if database connection is successful
if (!$db) {
    die("Database connection failed. Please check your configuration.");
}

// Initialize classes
$user = new User($db);
$category = new Category($db);
$ad = new Ad($db);
$adImage = new AdImage($db);

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user
function getCurrentUser() {
    if (isLoggedIn()) {
        global $user;
        return $user->getUserById($_SESSION['user_id']);
    }
    return null;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Initialize categories for use in templates
$categories = $category->getAllCategories();
?>
