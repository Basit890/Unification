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
            $this->createDonationNotification($requestId, $amount);
            return [
                'success' => true, 
                'message' => "Your donation to '{$request['title']}' was successful!"
            ];
        } else {
            return ['success' => false, 'message' => 'Donation failed. Please try again.'];
        }
    }
    
    private function createDonationNotification($requestId, $amount) {
        $donations = $this->donationModel->getByRequestId($requestId);
        if (!empty($donations)) {
            $donationId = $donations[0]['id'];
            
            global $pdo;
            $notificationController = new NotificationController($pdo);
            $notificationController->notifyDonation($donationId, $requestId);
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
        // For now, we'll serve HTML that can be printed to PDF
        // This avoids the PDF loading error while maintaining functionality
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="donation_history_' . date('Y-m-d') . '.html"');
        
        // Create PDF content using basic HTML that can be converted to PDF
        $html = $this->generatePDFHTML($donations, $user);
        
        // Output HTML that can be printed to PDF
        echo $html;
        exit;
    }
    
    private function generatePDFHTML($donations, $user) {
        $totalAmount = array_sum(array_column($donations, 'amount'));
        $totalDonations = count($donations);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Donation History</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .user-info { margin-bottom: 20px; }
                .summary { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .total { font-weight: bold; font-size: 18px; margin-top: 20px; text-align: right; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Donation History Report</h1>
                <h2>Unification</h2>
                <p>Generated on: ' . date('F j, Y \a\t g:i A') . '</p>
            </div>
            
            <div class="user-info">
                <h3>User Information</h3>
                <p><strong>Name:</strong> ' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</p>
                <p><strong>User Type:</strong> ' . ucfirst(htmlspecialchars($user['user_type'])) . '</p>
            </div>
            
            <div class="summary">
                <h3>Summary</h3>
                <p><strong>Total Donations:</strong> ' . $totalDonations . '</p>
                <p><strong>Total Amount:</strong> ৳' . number_format($totalAmount, 2) . '</p>
            </div>
            
            <h3>Donation Details</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Request Title</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($donations as $donation) {
            $html .= '
                    <tr>
                        <td>' . date('M j, Y', strtotime($donation['created_at'])) . '</td>
                        <td>' . htmlspecialchars($donation['title']) . '</td>
                        <td>৳' . number_format($donation['amount'], 2) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="total">
                <p><strong>Total Amount: ৳' . number_format($totalAmount, 2) . '</strong></p>
            </div>
            
            <div class="footer">
                <p>This report was generated by the Unification platform</p>
                <p>Thank you for your generosity and support!</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
} 