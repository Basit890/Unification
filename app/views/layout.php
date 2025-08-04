<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'UNIFICATION - Crowdfunding Platform'; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <nav class="nav">
                <div class="logo">UNIFICATION</div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php?page=about">About</a></li>
                    <li><a href="index.php?page=success_stories">Success Stories</a></li>
                    <?php if (Session::isLoggedIn()): ?>
                        <?php
                        $user = $userModel->findById(Session::getUserId());
                        $userType = $user['user_type'] ?? 'fundraiser';
                        ?>
                        <li class="nav-dropdown">
                            <a href="#" class="dropdown-toggle">Actions ▼</a>
                            <ul class="dropdown-menu">
                                <?php if ($userType === 'fundraiser' || $userType === 'admin'): ?>
                                    <li><a href="index.php?page=create_request">Create Request</a></li>
                                    <li><a href="index.php?page=my_requests">My Requests</a></li>
                                <?php endif; ?>
                                <?php if ($userType === 'donor' || $userType === 'fundraiser' || $userType === 'admin'): ?>
                                    <li><a href="index.php?page=donation_history">My Donations</a></li>
                                <?php endif; ?>
                                <?php if ($userType === 'admin'): ?>
                                    <li><a href="index.php?page=admin">Admin Panel</a></li>
                                    <li><a href="index.php?page=pending_requests">Pending Requests</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="user-menu">
                            <a href="index.php?page=profile" class="user-name">Hi, <?php echo htmlspecialchars(Session::getFirstName()); ?></a>
                            <span class="user-type-badge <?php echo $userType; ?>"><?php echo $userType === 'fundraiser' ? 'Fund' : ucfirst($userType); ?></span>
                            <form method="POST" class="logout-form">
                                <input type="hidden" name="action" value="logout">
                                <button type="submit" class="btn btn-logout">Logout</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="index.php?page=login">Login</a></li>
                        <li><a href="index.php?page=register">Register</a></li>
                    <?php endif; ?>
                    <li>
                        <button id="theme-toggle" class="theme-toggle" title="Toggle theme">
                            <span class="theme-icon">🌙</span>
                        </button>
                    </li>
                </ul>
                <div class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </header>
        
        <div class="content">
            <?php 
            $flash = Session::getFlash();
            if ($flash): 
            ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>
            
            <?php echo $content; ?>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="public/js/app.js"></script>
</body>
</html> 