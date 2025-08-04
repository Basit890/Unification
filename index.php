<?php
require_once 'app/bootstrap.php';

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
    'pending_requests' => 'Pending Requests - UNIFICATION'
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
        include 'app/views/requests/view.php';
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
        
    case 'donation_history':
        if (!Session::isLoggedIn()) {
            redirect('index.php?page=login');
        }
        include 'app/views/user/donation_history.php';
        break;
        
    case 'profile':
        if (!Session::isLoggedIn()) {
            redirect('index.php?page=login');
        }
        include 'app/views/user/profile.php';
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