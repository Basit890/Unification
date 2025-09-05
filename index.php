<?php
require_once 'app/bootstrap.php';

$action = $_GET['action'] ?? null;

if ($action === 'download_donation_pdf') {
    if (!Session::isLoggedIn()) {
        redirect('index.php?page=login');
    }
    
    $donationController = new DonationController($donationModel, $helpRequestModel);
    $donationController->downloadDonationPDF(Session::getUserId());
    exit; // PDF download will handle the response
}

$page = $_GET['page'] ?? 'home';

$pageTitles = [
    'home' => 'UNIFICATION - Crowdfunding Platform',
    'about' => 'About Us - UNIFICATION',
    'login' => 'Login - UNIFICATION',
    'register' => 'Register - UNIFICATION',
    'create_request' => 'Create Request - UNIFICATION',
    'view_request' => 'View Request - UNIFICATION',
    'my_requests' => 'My Requests - UNIFICATION',
    'success_stories' => 'Success Stories - UNIFICATION',
    'donation_history' => 'My Donations - UNIFICATION',
    'profile' => 'My Profile - UNIFICATION',
    'admin' => 'Admin Panel - UNIFICATION',
    'pending_requests' => 'Pending Requests - UNIFICATION',
    'notifications' => 'Notifications - UNIFICATION',
    'zakat_calculator' => 'Zakat Calculator - UNIFICATION'
];

$pageTitle = $pageTitles[$page] ?? 'UNIFICATION - Crowdfunding Platform';

ob_start();

switch ($page) {
    case 'register':
        if (Session::isLoggedIn()) {
            redirect('index.php');
        }
        include 'app/views/auth/register.php';
        break;
        
    case 'login':
        if (Session::isLoggedIn()) {
            redirect('index.php');
        }
        include 'app/views/auth/login.php';
        break;
        
    case 'create_request':
        if (!Session::isLoggedIn()) {
            redirect('index.php?page=login');
        }
        $user = $userModel->findById(Session::getUserId());
        if ($user['user_type'] !== 'fundraiser' && $user['user_type'] !== 'admin') {
            $content = '<div class="alert alert-error">Access denied. Only fundraisers and admins can create requests.</div>';
        } else {
            include 'app/views/requests/create.php';
        }
        break;
        
    case 'view_request':
        $requestId = $_GET['id'] ?? 0;
        $viewData = $helpRequestController->viewRequest($requestId);
        
        if (!$viewData['success']) {
            $content = '<div class="alert alert-error">' . htmlspecialchars($viewData['message']) . '</div>';
        } else {
            // Extract data for the view
            $request = $viewData['request'];
            $comments = $viewData['comments'];
            $status_updates = $viewData['status_updates'];
            $donations = $viewData['donations'];
            $progress_percentage = $viewData['progress_percentage'];
            include 'app/views/requests/view.php';
        }
        break;
        
    case 'my_requests':
        if (!Session::isLoggedIn()) {
            redirect('index.php?page=login');
        }
        $user = $userModel->findById(Session::getUserId());
        if ($user['user_type'] !== 'fundraiser' && $user['user_type'] !== 'admin') {
            $content = '<div class="alert alert-error">Access denied. Only fundraisers and admins can view their requests.</div>';
        } else {
            include 'app/views/requests/my_requests.php';
        }
        break;
        
    case 'success_stories':
        include 'app/views/requests/success_stories.php';
        break;
        
    case 'about':
        include 'app/views/about.php';
        break;
        
    case 'notifications':
        if (!Session::isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $notificationController = new NotificationController($pdo);
        
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'mark_read':
                $notificationController->markAsRead();
                break;
            case 'mark_all_read':
                $notificationController->markAllAsRead();
                break;
            case 'delete':
                $notificationController->delete();
                break;
            default:
                $data = $notificationController->index();
                extract($data);
                include 'app/views/notifications/notification_item.php';
                break;
        }
        break;
        
    case 'zakat_calculator':
        if (!Session::isLoggedIn()) {
            redirect('index.php?page=login');
        }
        
        $zakatController = new ZakatController($userModel);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $zakatController->calculate($_POST);
            if ($result['success']) {
                $calculation = $result['calculation'];
                include 'app/views/zakat/calculator.php';
            } else {
                $error = $result['message'];
                $data = $zakatController->index();
                extract($data);
                include 'app/views/zakat/calculator.php';
            }
        } else {
            $data = $zakatController->index();
            extract($data);
            include 'app/views/zakat/calculator.php';
        }
        break;
        
    case 'donation_history':
        if (!Session::isLoggedIn()) {
            redirect('index.php?page=login');
        }
        include 'app/views/user/donation_history.php';
        break;
        
    case 'profile':
        $profileController = new ProfileController($userModel, $fileUpload);
        switch ($action) {
            case 'update':
                $profileController->updateProfile();
                break;
            case 'delete_picture':
                $profileController->deleteProfilePicture();
                break;
            default:
                $data = $profileController->index();
                extract($data);
                include 'app/views/user/profile.php';
                break;
        }
        break;
        
    case 'admin':
        if (!Session::isAdmin()) {
            redirect('index.php');
        }
        include 'app/views/admin/dashboard.php';
        break;
        
    case 'pending_requests':
        if (!Session::isAdmin()) {
            redirect('index.php');
        }
        include 'app/views/admin/pending_requests.php';
        break;
        
    default:
        include 'app/views/home.php';
        break;
}

$content = ob_get_clean();

include 'app/views/layout.php';
?>