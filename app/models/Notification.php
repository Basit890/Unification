<?php

class Notification {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // Create a new notification
    public function create($data) {
        $sql = "INSERT INTO notifications (user_id, type, title, message, related_id, related_type, is_read, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 0, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['user_id'],
            $data['type'],
            $data['title'],
            $data['message'],
            $data['related_id'] ?? null,
            $data['related_type'] ?? null
        ]);
    }
    
    // Get notifications for a specific user
    public function getForUser($userId, $limit = 50, $offset = 0) {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get unread notifications count for a user
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
    
    // Mark notification as read
    public function markAsRead($notificationId, $userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$notificationId, $userId]);
    }
    
    // Mark all notifications as read for a user
    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    // Delete old notifications (older than 30 days)
    public function cleanupOldNotifications() {
        $sql = "DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
    }
    
    // Get notification by ID
    public function getById($notificationId, $userId) {
        $sql = "SELECT * FROM notifications WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$notificationId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Delete a specific notification
    public function delete($notificationId, $userId) {
        $sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$notificationId, $userId]);
    }
}
