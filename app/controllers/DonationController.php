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
        
        $donationId = $this->donationModel->create($userId, $requestId, $amount);
        if ($donationId) {
            $this->createDonationNotification($donationId, $requestId);
            return [
                'success' => true, 
                'message' => "Your donation to '{$request['title']}' was successful!"
            ];
        } else {
            return ['success' => false, 'message' => 'Donation failed. Please try again.'];
        }
    }
    
    private function createDonationNotification($donationId, $requestId) {
        global $pdo;
        $notificationController = new NotificationController($pdo);
        $notificationController->notifyDonation($donationId, $requestId);
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
    
    public function downloadDonationPDF($userId) {
        if (!Session::isLoggedIn() || Session::getUserId() != $userId) {
            return ['success' => false, 'message' => 'Unauthorized access'];
        }
        
        $donations = $this->donationModel->getByUserId($userId);
        if (empty($donations)) {
            return ['success' => false, 'message' => 'No donations found'];
        }
        
        // Get user info for the PDF header
        global $pdo;
        $userModel = new User($pdo);
        $user = $userModel->findById($userId);
        
        $this->generatePDF($donations, $user);
    }
    
    private function generatePDF($donations, $user) {
        // Set headers for PDF download
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="donation_history_' . date('Y-m-d') . '.html"');
        
        // Calculate totals for the view
        $totalAmount = array_sum(array_column($donations, 'amount'));
        $totalDonations = count($donations);
        
        // Include the PDF view template
        include 'app/views/user/donation_history_pdf.php';
        exit;
    }
    
} 