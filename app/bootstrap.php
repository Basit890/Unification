<?php

require_once __DIR__ . '/helpers/Session.php';
require_once __DIR__ . '/helpers/FileUpload.php';

Session::start();

require_once __DIR__ . '/config/Database.php';

require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/HelpRequest.php';
require_once __DIR__ . '/models/Donation.php';
require_once __DIR__ . '/models/Comment.php';
require_once __DIR__ . '/models/StatusUpdate.php';

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/HelpRequestController.php';
require_once __DIR__ . '/controllers/DonationController.php';
require_once __DIR__ . '/controllers/CommentController.php';
require_once __DIR__ . '/controllers/StatusUpdateController.php';

$database = Database::getInstance();
$pdo = $database->getConnection();

$userModel = new User($pdo);
$helpRequestModel = new HelpRequest($pdo);
$donationModel = new Donation($pdo);
$commentModel = new Comment($pdo);
$statusUpdateModel = new StatusUpdate($pdo);

try {
    $fileUpload = new FileUpload(__DIR__ . '/uploads/');
} catch (Exception $e) {
    error_log("FileUpload initialization failed: " . $e->getMessage());
    $fileUpload = null;
}

$authController = new AuthController($userModel);
$helpRequestController = new HelpRequestController($helpRequestModel, $fileUpload);
$donationController = new DonationController($donationModel, $helpRequestModel);
$commentController = new CommentController($commentModel);
$statusUpdateController = new StatusUpdateController($statusUpdateModel, $helpRequestModel);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $result = null;
    
    switch ($action) {
        case 'register':
            $result = $authController->register($_POST);
            break;
            
        case 'login':
            $result = $authController->login($_POST);
            break;
            
        case 'logout':
            $result = $authController->logout();
            break;
            
        case 'create_request':
            if (Session::isLoggedIn()) {
                $user = $userModel->findById(Session::getUserId());
                if ($user['user_type'] !== 'fundraiser' && $user['user_type'] !== 'admin') {
                    $result = ['success' => false, 'message' => 'Access denied. Only fundraisers and admins can create requests.'];
                } else {
                    $result = $helpRequestController->create($_POST, $_FILES);
                }
            } else {
                $result = ['success' => false, 'message' => 'Please login to create a request'];
            }
            break;
            
        case 'donate':
            if (Session::isLoggedIn()) {
                $result = $donationController->create($_POST);
            } else {
                $result = ['success' => false, 'message' => 'Please login to make a donation'];
            }
            break;
            
        case 'add_comment':
            if (Session::isLoggedIn()) {
                $result = $commentController->create($_POST);
            } else {
                $result = ['success' => false, 'message' => 'Please login to add a comment'];
            }
            break;
            
        case 'add_status_update':
            if (Session::isLoggedIn()) {
                $result = $statusUpdateController->create($_POST);
            } else {
                $result = ['success' => false, 'message' => 'Please login to add a status update'];
            }
            break;
            
        case 'approve_request':
            if (Session::isAdmin()) {
                $result = $helpRequestController->approveRequest($_POST['request_id'], $_POST['admin_notes'] ?? '');
            } else {
                $result = ['success' => false, 'message' => 'Access denied'];
            }
            break;
            
        case 'reject_request':
            if (Session::isAdmin()) {
                $result = $helpRequestController->rejectRequest($_POST['request_id'], $_POST['admin_notes'] ?? '');
            } else {
                $result = ['success' => false, 'message' => 'Access denied'];
            }
            break;
            
        case 'delete_comment':
            if (Session::isAdmin()) {
                $result = $commentController->delete($_POST['comment_id']);
            } else {
                $result = ['success' => false, 'message' => 'Access denied'];
            }
            break;
            
        case 'update_request_status':
            if (Session::isLoggedIn()) {
                $result = $helpRequestController->updateStatus($_POST['request_id'], $_POST['status']);
            } else {
                $result = ['success' => false, 'message' => 'Please login to update request status'];
            }
            break;
            
        case 'delete_request':
            if (Session::isAdmin()) {
                $result = $helpRequestController->delete($_POST['request_id']);
            } else {
                $result = ['success' => false, 'message' => 'Access denied'];
            }
            break;
    }
    
    if ($result) {
        $type = $result['success'] ? 'success' : 'error';
        Session::setFlash($type, $result['message']);
        
        switch ($action) {
            case 'register':
            case 'login':
                redirect('index.php?page=login');
                break;
            case 'create_request':
                redirect('index.php?page=my_requests');
                break;
            case 'donate':
            case 'add_comment':
            case 'add_status_update':
            case 'update_request_status':
                redirect('index.php?page=view_request&id=' . $_POST['request_id']);
                break;
            case 'approve_request':
            case 'reject_request':
            case 'delete_request':
                redirect('index.php?page=admin');
                break;
            case 'delete_comment':
                redirect('index.php?page=view_request&id=' . $_POST['request_id']);
                break;
            default:
                redirect('index.php');
        }
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
} 