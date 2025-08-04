<?php
class StatusUpdateController {
    private $statusUpdateModel;
    private $helpRequestModel;
    
    public function __construct($statusUpdateModel, $helpRequestModel) {
        $this->statusUpdateModel = $statusUpdateModel;
        $this->helpRequestModel = $helpRequestModel;
    }
    
    public function create($data) {
        if (empty($data['request_id']) || empty($data['update_text'])) {
            return ['success' => false, 'message' => 'Request ID and update text are required'];
        }
        
        $requestId = intval($data['request_id']);
        $updateText = trim($data['update_text']);
        $userId = Session::getUserId();
        
        if (strlen($updateText) < 1) {
            return ['success' => false, 'message' => 'Update text cannot be empty'];
        }
        
        if (strlen($updateText) > 2000) {
            return ['success' => false, 'message' => 'Update text is too long (max 2000 characters)'];
        }
        
        $request = $this->helpRequestModel->getById($requestId);
        if (!$request) {
            return ['success' => false, 'message' => 'Request not found'];
        }
        
        if ($request['user_id'] != $userId && !Session::isAdmin()) {
            return ['success' => false, 'message' => 'Access denied'];
        }
        
        if ($this->statusUpdateModel->create($requestId, $updateText)) {
            return ['success' => true, 'message' => 'Status update added successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to add status update.'];
        }
    }
    
    public function getByRequestId($requestId) {
        return $this->statusUpdateModel->getByRequestId($requestId);
    }
    
    public function delete($id) {
        if (!Session::isAdmin()) {
            return ['success' => false, 'message' => 'Access denied'];
        }
        
        if ($this->statusUpdateModel->delete($id)) {
            return ['success' => true, 'message' => 'Status update deleted successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete status update.'];
        }
    }
} 