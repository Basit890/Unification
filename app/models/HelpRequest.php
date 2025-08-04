<?php
class HelpRequest {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO help_requests (
                user_id, title, description, category, goal_amount, 
                image_path, document_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['category'],
            $data['goal_amount'],
            $data['image_path'] ?? null,
            $data['document_path'] ?? null
        ]);
    }
    
    public function getApprovedRequests($filters = []) {
        $sql = "
            SELECT hr.*, CONCAT(u.first_name, ' ', u.last_name) as user_name 
            FROM help_requests hr 
            JOIN users u ON hr.user_id = u.id 
            WHERE hr.status = 'approved'
        ";
        
        $params = [];
        
        if (!empty($filters['category'])) {
            $sql .= " AND hr.category = ?";
            $params[] = $filters['category'];
        }
        
        switch ($filters['sort'] ?? 'created_at') {
            case 'progress':
                $sql .= " ORDER BY (hr.current_amount / hr.goal_amount) DESC";
                break;
            case 'goal':
                $sql .= " ORDER BY hr.goal_amount DESC";
                break;
            default:
                $sql .= " ORDER BY hr.created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT hr.*, CONCAT(u.first_name, ' ', u.last_name) as user_name 
            FROM help_requests hr 
            JOIN users u ON hr.user_id = u.id 
            WHERE hr.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM help_requests 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT hr.*, CONCAT(u.first_name, ' ', u.last_name) as user_name 
            FROM help_requests hr 
            JOIN users u ON hr.user_id = u.id 
            ORDER BY hr.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getSuccessStories() {
        $stmt = $this->db->prepare("
            SELECT hr.*, CONCAT(u.first_name, ' ', u.last_name) as user_name 
            FROM help_requests hr 
            JOIN users u ON hr.user_id = u.id 
            WHERE hr.status IN ('completed', 'closed') 
            ORDER BY hr.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("
            UPDATE help_requests SET status = ? WHERE id = ?
        ");
        return $stmt->execute([$status, $id]);
    }
    
    public function updateAmount($id, $amount) {
        $stmt = $this->db->prepare("
            UPDATE help_requests 
            SET current_amount = current_amount + ? 
            WHERE id = ?
        ");
        return $stmt->execute([$amount, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM help_requests WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getStats() {
        $stats = [];
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM help_requests");
        $stats['total'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM help_requests WHERE status = 'pending'");
        $stats['pending'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM help_requests WHERE status = 'approved'");
        $stats['approved'] = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM help_requests WHERE status = 'completed'");
        $stats['completed'] = $stmt->fetchColumn();
        
        return $stats;
    }
    
    public function getPendingRequests() {
        $stmt = $this->db->prepare("
            SELECT hr.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email
            FROM help_requests hr 
            JOIN users u ON hr.user_id = u.id 
            WHERE hr.status = 'pending'
            ORDER BY hr.created_at ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function approveRequest($requestId, $adminId, $adminNotes = '') {
        $stmt = $this->db->prepare("
            UPDATE help_requests 
            SET status = 'approved', 
                admin_verified = TRUE,
                admin_notes = ?,
                verified_by = ?,
                verified_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$adminNotes, $adminId, $requestId]);
    }
    
    public function rejectRequest($requestId, $adminId, $adminNotes = '') {
        $stmt = $this->db->prepare("
            UPDATE help_requests 
            SET status = 'rejected', 
                admin_verified = TRUE,
                admin_notes = ?,
                verified_by = ?,
                verified_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$adminNotes, $adminId, $requestId]);
    }
    
    public function getCategories() {
        return [
            'health' => '🏥 Health',
            'education' => '📚 Education',
            'emergency' => '🚨 Emergency',
            'community' => '🤝 Community Aid',
            'other' => '📋 Other'
        ];
    }
} 