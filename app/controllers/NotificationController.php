<?php

require_once 'app/models/Notification.php';

class NotificationController {
    private $notificationModel;
    private $userModel;
    private $helpRequestModel;
    private $commentModel;
    private $donationModel;
    
    public function __construct($db) {
        $this->notificationModel = new Notification($db);
        $this->userModel = new User($db);
        $this->helpRequestModel = new HelpRequest($db);
        $this->commentModel = new Comment($db);
        $this->donationModel = new Donation($db);
    }
    
    public function index() {
        if (!Session::isLoggedIn()) {
            $this->redirect('index.php?page=login');
        }
        
        $userId = Session::getUserId();
        $user = $this->userModel->findById($userId);
        $notifications = $this->notificationModel->getForUser($userId);
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        
        // Group notifications by type
        $groupedNotifications = $this->groupNotificationsByType($notifications);
        
        include 'app/views/notifications/index.php';
    }
    
    public function markAsRead() {
        if (!Session::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        $notificationId = $_POST['notification_id'] ?? null;
        $userId = Session::getUserId();
        
        if ($notificationId) {
            $success = $this->notificationModel->markAsRead($notificationId, $userId);
            if ($success) {
                $unreadCount = $this->notificationModel->getUnreadCount($userId);
                echo json_encode(['success' => true, 'unread_count' => $unreadCount]);
            } else {
                echo json_encode(['error' => 'Failed to mark as read']);
            }
        } else {
            echo json_encode(['error' => 'Notification ID required']);
        }
    }
    
    public function markAllAsRead() {
        if (!Session::isLoggedIn()) {
            $this->redirect('index.php?page=login');
        }
        
        $userId = Session::getUserId();
        $this->notificationModel->markAllAsRead($userId);
        
        $this->redirect('index.php?page=notifications');
    }
    
    public function delete() {
        if (!Session::isLoggedIn()) {
            $this->redirect('index.php?page=login');
        }
        
        $notificationId = $_POST['notification_id'] ?? null;
        $userId = Session::getUserId();
        
        if ($notificationId) {
            $this->notificationModel->delete($notificationId, $userId);
        }
        
        $this->redirect('index.php?page=notifications');
    }
    
    private function redirect($url) {
        header("Location: $url");
        exit();
    }
    
    public function notifyComment($commentId, $requestId) {
        try {
            $comment = $this->commentModel->getById($commentId);
            $request = $this->helpRequestModel->getById($requestId);
            
            if ($comment && $request && $comment['user_id'] != $request['user_id']) {
                $this->notificationModel->create([
                    'user_id' => $request['user_id'],
                    'type' => 'comment',
                    'title' => 'New Comment on Your Request',
                    'message' => htmlspecialchars($comment['user_name']) . ' commented on your request: "' . htmlspecialchars($request['title']) . '"',
                    'related_id' => $commentId,
                    'related_type' => 'comment'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error creating comment notification: " . $e->getMessage());
        }
    }
    
    public function notifyDonation($donationId, $requestId) {
        try {
            $donation = $this->donationModel->getById($donationId);
            $request = $this->helpRequestModel->getById($requestId);
            
            if ($donation && $request) {
                $this->notificationModel->create([
                    'user_id' => $request['user_id'],
                    'type' => 'donation',
                    'title' => 'New Donation Received',
                    'message' => 'You received a donation of ৳' . number_format($donation['amount'], 2) . ' for your request: "' . htmlspecialchars($request['title']) . '"',
                    'related_id' => $donationId,
                    'related_type' => 'donation'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error creating donation notification: " . $e->getMessage());
        }
    }
    
    public function notifyRequestStatus($requestId, $status, $adminId = null) {
        try {
            $request = $this->helpRequestModel->getById($requestId);
            
            if ($request) {
                $adminName = $adminId ? $this->userModel->findById($adminId)['first_name'] : 'Admin';
                
                if ($status === 'approved') {
                    $this->notificationModel->create([
                        'user_id' => $request['user_id'],
                        'type' => 'approval',
                        'title' => 'Request Approved',
                        'message' => 'Your request "' . htmlspecialchars($request['title']) . '" has been approved by ' . $adminName,
                        'related_id' => $requestId,
                        'related_type' => 'request'
                    ]);
                } elseif ($status === 'rejected') {
                    $this->notificationModel->create([
                        'user_id' => $request['user_id'],
                        'type' => 'rejection',
                        'title' => 'Request Rejected',
                        'message' => 'Your request "' . htmlspecialchars($request['title']) . '" has been rejected by ' . $adminName,
                        'related_id' => $requestId,
                        'related_type' => 'request'
                    ]);
                }
            }
        } catch (Exception $e) {
            error_log("Error creating request status notification: " . $e->getMessage());
        }
    }
    
    public function notifyAdminNewRequest($requestId) {
        try {
            $admins = $this->userModel->getAdmins();
            $request = $this->helpRequestModel->getById($requestId);
            
            if ($request && $admins) {
                foreach ($admins as $admin) {
                    $this->notificationModel->create([
                        'user_id' => $admin['id'],
                        'type' => 'admin_request',
                        'title' => 'New Pending Request',
                        'message' => 'New request "' . htmlspecialchars($request['title']) . '" from ' . htmlspecialchars($request['user_name']) . ' needs approval',
                        'related_id' => $requestId,
                        'related_type' => 'request'
                    ]);
                }
            }
        } catch (Exception $e) {
            error_log("Error creating admin notification: " . $e->getMessage());
        }
    }
    
    private function groupNotificationsByType($notifications) {
        $grouped = [
            'recent' => [],
            'comments' => [],
            'donations' => [],
            'approvals' => [],
            'admin' => []
        ];
        
        foreach ($notifications as $notification) {
            switch ($notification['type']) {
                case 'comment':
                    $grouped['comments'][] = $notification;
                    break;
                case 'donation':
                    $grouped['donations'][] = $notification;
                    break;
                case 'approval':
                case 'rejection':
                    $grouped['approvals'][] = $notification;
                    break;
                case 'admin_request':
                    $grouped['admin'][] = $notification;
                    break;
                default:
                    $grouped['recent'][] = $notification;
                    break;
            }
        }
        
        return $grouped;
    }
    
    public function getHeaderNotifications($limit = 5) {
        if (!Session::isLoggedIn()) {
            return [];
        }
        
        $userId = Session::getUserId();
        return $this->notificationModel->getForUser($userId, $limit);
    }
}
