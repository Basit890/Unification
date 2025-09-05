<?php
class Comment {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($userId, $requestId, $comment) {
        $stmt = $this->db->prepare("
            INSERT INTO comments (user_id, request_id, comment) 
            VALUES (?, ?, ?)
        ");
        $result = $stmt->execute([$userId, $requestId, $comment]);
        return $result ? $this->db->lastInsertId() : false;
    }
    
    public function getByRequestId($requestId) {
        $stmt = $this->db->prepare("
            SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.profile_picture
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.request_id = ? 
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$requestId]);
        return $stmt->fetchAll();
    }
    
    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.profile_picture, hr.title as request_title
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            JOIN help_requests hr ON c.request_id = hr.id 
            ORDER BY c.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.profile_picture
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getStats() {
        $stats = [];
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM comments");
        $stats['total'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(DISTINCT request_id) FROM comments");
        $stats['unique_requests'] = $stmt->fetchColumn();
        
        return $stats;
    }
} 