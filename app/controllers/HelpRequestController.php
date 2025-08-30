<?php
class HelpRequestController {
    private $helpRequestModel;
    private $fileUpload;
    
    public function __construct($helpRequestModel, $fileUpload) {
        $this->helpRequestModel = $helpRequestModel;
        $this->fileUpload = $fileUpload;
    }
    
    public function create($data, $files) {
        if (empty($data['title']) || empty($data['description']) || 
            empty($data['category']) || empty($data['goal_amount'])) {
            return ['success' => false, 'message' => 'All required fields must be filled'];
        }
        
        if (!is_numeric($data['goal_amount']) || $data['goal_amount'] <= 0) {
            return ['success' => false, 'message' => 'Goal amount must be a positive number'];
        }
        
        if (strlen($data['description']) > 1000) {
            return ['success' => false, 'message' => 'Description must be 1000 characters or less'];
        }
        
        $imagePath = null;
        $documentPath = null;
        
        if (!empty($files['image']['name'])) {
            $imageResult = $this->fileUpload->uploadImage($files['image']);
            if (!$imageResult['success']) {
                return ['success' => false, 'message' => 'Image upload failed: ' . $imageResult['error']];
            }
            $imagePath = $imageResult['filepath'];
        }
        
        if (!empty($files['document']['name'])) {
            $documentResult = $this->fileUpload->uploadDocument($files['document']);
            if (!$documentResult['success']) {
                return ['success' => false, 'message' => 'Document upload failed: ' . $documentResult['error']];
            }
            $documentPath = $documentResult['filepath'];
        }
        
        $requestData = [
            'user_id' => Session::getUserId(),
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'category' => $data['category'],
            'goal_amount' => floatval($data['goal_amount']),
            'image_path' => $imagePath,
            'document_path' => $documentPath
        ];
        
        if ($this->helpRequestModel->create($requestData)) {
            // Notify admins about new pending request
            $this->notifyAdminsNewRequest($requestData);
            return ['success' => true, 'message' => 'Help request submitted successfully! It will be reviewed by our team.'];
        } else {
            return ['success' => false, 'message' => 'Failed to create help request.'];
        }
    }
    
    public function getApprovedRequests($filters = []) {
        return $this->helpRequestModel->getApprovedRequests($filters);
    }
    
    public function getPendingRequests() {
        return $this->helpRequestModel->getPendingRequests();
    }
    
    public function approveRequest($requestId, $adminNotes = '') {
        if (!Session::isAdmin()) {
            return ['success' => false, 'message' => 'Access denied. Admin privileges required.'];
        }
        
        $result = $this->helpRequestModel->approveRequest($requestId, Session::getUserId(), $adminNotes);
        
        if ($result) {
            $this->notifyRequestStatus($requestId, 'approved', $adminNotes);
            return ['success' => true, 'message' => 'Request approved successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to approve request.'];
        }
    }
    
    public function rejectRequest($requestId, $adminNotes = '') {
        if (!Session::isAdmin()) {
            return ['success' => false, 'message' => 'Access denied. Admin privileges required.'];
        }
        
        $result = $this->helpRequestModel->rejectRequest($requestId, Session::getUserId(), $adminNotes);
        
        if ($result) {
            $this->notifyRequestStatus($requestId, 'rejected', $adminNotes);
            return ['success' => true, 'message' => 'Request rejected successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to reject request.'];
        }
    }
    
    public function getById($id) {
        return $this->helpRequestModel->getById($id);
    }
    
    private function notifyAdminsNewRequest($requestData) {
        global $pdo;
        $notificationController = new NotificationController($pdo);
        $notificationController->notifyAdminNewRequest($requestData['id']);
    }
    
    private function notifyRequestStatus($requestId, $status, $adminNotes = '') {
        global $pdo;
        $notificationController = new NotificationController($pdo);
        $notificationController->notifyRequestStatus($requestId, $status, Session::getUserId());
    }
    
    public function getByUserId($userId) {
        return $this->helpRequestModel->getByUserId($userId);
    }
    
    public function getSuccessStories() {
        return $this->helpRequestModel->getSuccessStories();
    }
    
    public function updateStatus($id, $status) {
        if (!Session::isAdmin()) {
            return ['success' => false, 'message' => 'Access denied'];
        }
        
        $validStatuses = ['pending', 'approved', 'rejected', 'completed', 'closed'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        if ($this->helpRequestModel->updateStatus($id, $status)) {
            return ['success' => true, 'message' => 'Request status updated successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to update request status.'];
        }
    }
    
    public function delete($id) {
        if (!Session::isAdmin()) {
            return ['success' => false, 'message' => 'Access denied'];
        }
        
        if ($this->helpRequestModel->delete($id)) {
            return ['success' => true, 'message' => 'Request deleted successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete request.'];
        }
    }
    
    public function getCategories() {
        return $this->helpRequestModel->getCategories();
    }
    
    public function getStats() {
        return $this->helpRequestModel->getStats();
    }
} 