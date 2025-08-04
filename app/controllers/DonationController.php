<?php
class DonationController {
    private $donationModel;
    private $helpRequestModel;
    
    public function __construct($donationModel, $helpRequestModel) {
        $this->donationModel = $donationModel;
        $this->helpRequestModel = $helpRequestModel;
    }
    
    public function create($data) {
        if (empty($data['request_id']) || empty($data['amount'])) {
            return ['success' => false, 'message' => 'Request ID and amount are required'];
        }
        
        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            return ['success' => false, 'message' => 'Amount must be a positive number'];
        }
        
        $requestId = intval($data['request_id']);
        $amount = floatval($data['amount']);
        $userId = Session::getUserId();
        
        $request = $this->helpRequestModel->getById($requestId);
        if (!$request) {
            return ['success' => false, 'message' => 'Request not found'];
        }
        
        if ($request['status'] !== 'approved') {
            return ['success' => false, 'message' => 'Cannot donate to this request'];
        }
        
        if ($this->donationModel->create($userId, $requestId, $amount)) {
            return [
                'success' => true, 
                'message' => "Your donation to '{$request['title']}' was successful!"
            ];
        } else {
            return ['success' => false, 'message' => 'Donation failed. Please try again.'];
        }
    }
    
    public function getByUserId($userId) {
        return $this->donationModel->getByUserId($userId);
    }
    
    public function getByRequestId($requestId) {
        return $this->donationModel->getByRequestId($requestId);
    }
    
    public function getAll() {
        if (!Session::isAdmin()) {
            return [];
        }
        return $this->donationModel->getAll();
    }
    
    public function getStats() {
        return $this->donationModel->getStats();
    }
    
    public function getRecent($limit = 10) {
        return $this->donationModel->getRecent($limit);
    }
    
    public function getTopDonors($limit = 5) {
        return $this->donationModel->getTopDonors($limit);
    }
} 