<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    private $host = 'localhost';
    private $port = '3308';
    private $socket = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
    private $dbname = 'gofund';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            // First connect without database to create it if needed
            $this->pdo = new PDO(
                "mysql:unix_socket={$this->socket};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Then connect to the specific database
            $this->pdo = new PDO(
                "mysql:unix_socket={$this->socket};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            $currentDb = $this->pdo->query("SELECT DATABASE()")->fetchColumn();
            error_log("Connected to database: " . $currentDb);
            
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function setupDatabase() {
        $tables = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                religion ENUM('islam', 'hindu', 'christian', 'buddhist', 'other') DEFAULT 'other',
                user_type ENUM('admin', 'donor', 'fundraiser') DEFAULT 'fundraiser',
                is_admin BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS help_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(200) NOT NULL,
                description TEXT NOT NULL,
                category VARCHAR(50) NOT NULL,
                goal_amount DECIMAL(10,2) NOT NULL,
                current_amount DECIMAL(10,2) DEFAULT 0,
                status ENUM('pending', 'approved', 'rejected', 'completed', 'closed') DEFAULT 'pending',
                image_path VARCHAR(255),
                document_path VARCHAR(255),
                location VARCHAR(200),
                is_approved BOOLEAN DEFAULT FALSE,
                admin_verified BOOLEAN DEFAULT FALSE,
                admin_notes TEXT,
                verified_by INT NULL,
                verified_at TIMESTAMP NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                completion_date TIMESTAMP NULL,
                beneficiary_count INT DEFAULT 0,
                donor_count INT DEFAULT 0,
                impact_summary TEXT,
                before_image VARCHAR(255),
                after_image VARCHAR(255),
                additional_images TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
            )",
            
            "CREATE TABLE IF NOT EXISTS donations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                request_id INT NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                donor_name VARCHAR(100),
                message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (request_id) REFERENCES help_requests(id) ON DELETE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                request_id INT NOT NULL,
                comment TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (request_id) REFERENCES help_requests(id) ON DELETE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS status_updates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                request_id INT NOT NULL,
                update_text TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (request_id) REFERENCES help_requests(id) ON DELETE CASCADE
            )"
        ];
        
        foreach ($tables as $table) {
            $this->pdo->exec($table);
        }
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE is_admin = 1");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $stmt = $this->pdo->prepare("INSERT INTO users (first_name, last_name, email, password, is_admin) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute(['Admin', 'User', 'admin@example.com', password_hash('admin123', PASSWORD_DEFAULT)]);
        }
    }
} 