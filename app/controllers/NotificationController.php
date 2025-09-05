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
        
        return [
            'user' => $user,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'groupedNotifications' => $groupedNotifications
        ];
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
            
            if ($comment && $request) {
                // Notify the fundraiser (if not the commenter)
                if ($comment['user_id'] != $request['user_id']) {
                    $this->notificationModel->create([
                        'user_id' => $request['user_id'],
                        'type' => 'comment',
                        'title' => 'New Comment on Your Request',
                        'message' => htmlspecialchars($comment['user_name']) . ' commented on your request: "' . htmlspecialchars($request['title']) . '"',
                        'related_id' => $commentId,
                        'related_type' => 'comment'
                    ]);
                }
                
                // Notify all donors who donated to this request (except the commenter)
                $this->notifyDonorsAboutComment($commentId, $requestId, $comment['user_id']);
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
                // Notify the fundraiser
                $this->notificationModel->create([
                    'user_id' => $request['user_id'],
                    'type' => 'donation',
                    'title' => 'New Donation Received',
                    'message' => 'You received a donation of ৳' . number_format($donation['amount'], 2) . ' for your request: "' . htmlspecialchars($request['title']) . '"',
                    'related_id' => $donationId,
                    'related_type' => 'donation'
                ]);
                
                // Notify all other donors who donated to this request (except the current donor)
                $this->notifyDonorsAboutDonation($donationId, $requestId, $donation['user_id']);
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
                    // Notify the fundraiser
                    $this->notificationModel->create([
                        'user_id' => $request['user_id'],
                        'type' => 'approval',
                        'title' => 'Request Approved',
                        'message' => 'Your request "' . htmlspecialchars($request['title']) . '" has been approved by ' . $adminName,
                        'related_id' => $requestId,
                        'related_type' => 'request'
                    ]);
                    
                    // Notify all donors who donated to this request
                    $this->notifyDonorsAboutStatusUpdate($requestId, 'approved', $adminName);
                } elseif ($status === 'rejected') {
                    // Notify the fundraiser
                    $this->notificationModel->create([
                        'user_id' => $request['user_id'],
                        'type' => 'rejection',
                        'title' => 'Request Rejected',
                        'message' => 'Your request "' . htmlspecialchars($request['title']) . '" has been rejected by ' . $adminName,
                        'related_id' => $requestId,
                        'related_type' => 'request'
                    ]);
                    
                    // Notify all donors who donated to this request
                    $this->notifyDonorsAboutStatusUpdate($requestId, 'rejected', $adminName);
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
            'updates' => [],
            'admin' => []
        ];
        
        // Sort notifications by created_at (newest first)
        usort($notifications, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        foreach ($notifications as $notification) {
            $isRecent = (time() - strtotime($notification['created_at'])) < 3600; // Less than 1 hour old
            
            switch ($notification['type']) {
                case 'comment':
                    $grouped['comments'][] = $notification;
                    if ($isRecent) {
                        $grouped['recent'][] = $notification;
                    }
                    break;
                case 'donation':
                    $grouped['donations'][] = $notification;
                    if ($isRecent) {
                        $grouped['recent'][] = $notification;
                    }
                    break;
                case 'approval':
                case 'rejection':
                    $grouped['approvals'][] = $notification;
                    if ($isRecent) {
                        $grouped['recent'][] = $notification;
                    }
                    break;
                case 'update':
                    $grouped['updates'][] = $notification;
                    if ($isRecent) {
                        $grouped['recent'][] = $notification;
                    }
                    break;
                case 'admin_request':
                    $grouped['admin'][] = $notification;
                    if ($isRecent) {
                        $grouped['recent'][] = $notification;
                    }
                    break;
                default:
                    $grouped['recent'][] = $notification;
                    break;
            }
        }
        
        // Remove duplicates from recent (in case a notification appears in both recent and its category)
        $grouped['recent'] = array_unique($grouped['recent'], SORT_REGULAR);
        
        return $grouped;
    }
    
    public function getHeaderNotifications($limit = 5) {
        if (!Session::isLoggedIn()) {
            return [];
        }
        
        $userId = Session::getUserId();
        return $this->notificationModel->getForUser($userId, $limit);
    }
    
    private function notifyDonorsAboutComment($commentId, $requestId, $excludeUserId) {
        try {
            $comment = $this->commentModel->getById($commentId);
            $request = $this->helpRequestModel->getById($requestId);
            
            if ($comment && $request) {
                $donorIds = $this->donationModel->getDonorIdsByRequestId($requestId);
                
                foreach ($donorIds as $donorId) {
                    // Skip the commenter and the fundraiser
                    if ($donorId != $excludeUserId && $donorId != $request['user_id']) {
                        $this->notificationModel->create([
                            'user_id' => $donorId,
                            'type' => 'comment',
                            'title' => 'New Comment on Donated Post',
                            'message' => htmlspecialchars($comment['user_name']) . ' commented on "' . htmlspecialchars($request['title']) . '" that you donated to',
                            'related_id' => $commentId,
                            'related_type' => 'comment'
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error creating donor comment notification: " . $e->getMessage());
        }
    }
    
    private function notifyDonorsAboutDonation($donationId, $requestId, $excludeUserId) {
        try {
            $donation = $this->donationModel->getById($donationId);
            $request = $this->helpRequestModel->getById($requestId);
            
            if ($donation && $request) {
                $donorIds = $this->donationModel->getDonorIdsByRequestId($requestId);
                
                foreach ($donorIds as $donorId) {
                    // Skip the current donor and the fundraiser
                    if ($donorId != $excludeUserId && $donorId != $request['user_id']) {
                        $this->notificationModel->create([
                            'user_id' => $donorId,
                            'type' => 'donation',
                            'title' => 'New Donation on Your Post',
                            'message' => 'Someone donated ৳' . number_format($donation['amount'], 2) . ' to "' . htmlspecialchars($request['title']) . '" that you also donated to',
                            'related_id' => $donationId,
                            'related_type' => 'donation'
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error creating donor donation notification: " . $e->getMessage());
        }
    }
    
    private function notifyDonorsAboutStatusUpdate($requestId, $status, $adminName) {
        try {
            $request = $this->helpRequestModel->getById($requestId);
            
            if ($request) {
                $donorIds = $this->donationModel->getDonorIdsByRequestId($requestId);
                
                foreach ($donorIds as $donorId) {
                    // Skip the fundraiser
                    if ($donorId != $request['user_id']) {
                        $title = $status === 'approved' ? 'Post You Donated To Was Approved' : 'Post You Donated To Was Rejected';
                        $message = 'The post "' . htmlspecialchars($request['title']) . '" that you donated to has been ' . $status . ' by ' . $adminName;
                        
                        $this->notificationModel->create([
                            'user_id' => $donorId,
                            'type' => $status === 'approved' ? 'approval' : 'rejection',
                            'title' => $title,
                            'message' => $message,
                            'related_id' => $requestId,
                            'related_type' => 'request'
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error creating donor status notification: " . $e->getMessage());
        }
    }
    
    public function notifyDonorsAboutPostUpdate($requestId, $updateText) {
        try {
            $request = $this->helpRequestModel->getById($requestId);
            
            if ($request) {
                $donorIds = $this->donationModel->getDonorIdsByRequestId($requestId);
                
                foreach ($donorIds as $donorId) {
                    // Skip the fundraiser
                    if ($donorId != $request['user_id']) {
                        $this->notificationModel->create([
                            'user_id' => $donorId,
                            'type' => 'update',
                            'title' => 'Post Update',
                            'message' => 'The fundraiser added an update to "' . htmlspecialchars($request['title']) . '" that you donated to',
                            'related_id' => $requestId,
                            'related_type' => 'request'
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error creating donor post update notification: " . $e->getMessage());
        }
    }
}
