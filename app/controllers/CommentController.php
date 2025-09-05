<?php
class CommentController {
    private $commentModel;
    
    public function __construct($commentModel) {
        $this->commentModel = $commentModel;
    }
    
    public function create($data) {
        if (empty($data['request_id']) || empty($data['comment'])) {
            return ['success' => false, 'message' => 'Request ID and comment are required'];
        }
        
        $requestId = intval($data['request_id']);
        $comment = trim($data['comment']);
        $userId = Session::getUserId();
        
        if (strlen($comment) < 1) {
            return ['success' => false, 'message' => 'Comment cannot be empty'];
        }
        
        if (strlen($comment) > 1000) {
            return ['success' => false, 'message' => 'Comment is too long (max 1000 characters)'];
        }
        
        $commentId = $this->commentModel->create($userId, $requestId, $comment);
        if ($commentId) {
            $this->createCommentNotification($commentId, $requestId);
            return ['success' => true, 'message' => 'Comment added successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to add comment.'];
        }
    }
    
    private function createCommentNotification($commentId, $requestId) {
        global $pdo;
        $notificationController = new NotificationController($pdo);
        $notificationController->notifyComment($commentId, $requestId);
    }
    
    public function getByRequestId($requestId) {
        return $this->commentModel->getByRequestId($requestId);
    }
    
    public function getAll() {
        if (!Session::isAdmin()) {
            return [];
        }
        return $this->commentModel->getAll();
    }
    
    public function delete($id) {
        if (!Session::isAdmin()) {
            return ['success' => false, 'message' => 'Access denied'];
        }
        
        if ($this->commentModel->delete($id)) {
            return ['success' => true, 'message' => 'Comment deleted successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete comment.'];
        }
    }
} 