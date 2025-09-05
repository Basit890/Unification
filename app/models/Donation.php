<?php
class Donation {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($userId, $requestId, $amount) {
        $this->db->beginTransaction();
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO donations (user_id, request_id, amount) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, $requestId, $amount]);
            
            $stmt = $this->db->prepare("
                UPDATE help_requests 
                SET current_amount = current_amount + ? 
                WHERE id = ?
            ");
            $stmt->execute([$amount, $requestId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT d.*, hr.title 
            FROM donations d 
            JOIN help_requests hr ON d.request_id = hr.id 
            WHERE d.user_id = ? 
            ORDER BY d.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getByRequestId($requestId) {
        $stmt = $this->db->prepare("
            SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as donor_name 
            FROM donations d 
            JOIN users u ON d.user_id = u.id 
            WHERE d.request_id = ? 
            ORDER BY d.created_at DESC
        ");
        $stmt->execute([$requestId]);
        return $stmt->fetchAll();
    }
    
    public function getDonorIdsByRequestId($requestId) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT user_id 
            FROM donations 
            WHERE request_id = ?
        ");
        $stmt->execute([$requestId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as donor_name, hr.title as request_title 
            FROM donations d 
            JOIN users u ON d.user_id = u.id 
            JOIN help_requests hr ON d.request_id = hr.id 
            ORDER BY d.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getStats() {
        $stats = [];
        
        $stmt = $this->db->query("SELECT SUM(amount) FROM donations");
        $stats['total_amount'] = $stmt->fetchColumn() ?: 0;
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM donations");
        $stats['total_count'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT AVG(amount) FROM donations");
        $stats['average_amount'] = $stmt->fetchColumn() ?: 0;
        
        return $stats;
    }
    
    public function getRecent($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as donor_name, hr.title as request_title 
            FROM donations d 
            JOIN users u ON d.user_id = u.id 
            JOIN help_requests hr ON d.request_id = hr.id 
            ORDER BY d.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as donor_name, hr.title as request_title 
            FROM donations d 
            JOIN users u ON d.user_id = u.id 
            JOIN help_requests hr ON d.request_id = hr.id 
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getTopDonors($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT
                u.id,
                CONCAT(u.first_name, ' ', u.last_name) as donor_name,
                SUM(d.amount) as total_donated,
                COUNT(d.id) as donation_count
            FROM donations d 
            JOIN users u ON d.user_id = u.id 
            GROUP BY u.id, u.first_name, u.last_name
            ORDER BY total_donated DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
} 