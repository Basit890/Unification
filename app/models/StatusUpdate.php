<?php
class StatusUpdate {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($requestId, $updateText) {
        $stmt = $this->db->prepare("
            INSERT INTO status_updates (request_id, update_text) 
            VALUES (?, ?)
        ");
        return $stmt->execute([$requestId, $updateText]);
    }
    
    public function getByRequestId($requestId) {
        $stmt = $this->db->prepare("
            SELECT * FROM status_updates 
            WHERE request_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$requestId]);
        return $stmt->fetchAll();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM status_updates WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM status_updates WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
} 