<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'UNIFICATION - Crowdfunding Platform'; ?></title>
    <link rel="stylesheet" href="public/css/style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <nav class="nav">
                <div class="nav-left">
                    <div class="logo">UNIFICATION</div>
                    <ul class="nav-links">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">
                                <span class="nav-emoji">🏠</span>
                                <span class="nav-label">Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=about" class="nav-link">
                                <span class="nav-emoji">ℹ️</span>
                                <span class="nav-label">About</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=success_stories" class="nav-link">
                                <span class="nav-emoji">🌟</span>
                                <span class="nav-label">Success Stories</span>
                            </a>
                        </li>
                        <?php if (Session::isLoggedIn()): ?>
                            <?php
                            $user = $userModel->findById(Session::getUserId());
                            $userType = $user['user_type'] ?? 'fundraiser';
                            ?>
                            <li class="nav-item nav-dropdown">
                                <a href="#" class="nav-link dropdown-toggle">
                                    <span class="nav-emoji">⚡</span>
                                    <span class="nav-label">Actions</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if ($userType === 'fundraiser' || $userType === 'admin'): ?>
                                        <li><a href="index.php?page=create_request">📝 Create Request</a></li>
                                        <li><a href="index.php?page=my_requests">📋 My Requests</a></li>
                                    <?php endif; ?>
                                    <?php if ($userType === 'donor' || $userType === 'fundraiser' || $userType === 'admin'): ?>
                                        <li><a href="index.php?page=donation_history">💝 My Donations</a></li>
                                    <?php endif; ?>
                                    <?php if (strtolower($user['religion']) === 'islam'): ?>
                                        <li><a href="index.php?page=zakat_calculator">🕌 Zakat Calculator</a></li>
                                    <?php endif; ?>
                                    <?php if ($userType === 'admin'): ?>
                                        <li><a href="index.php?page=admin">🛡️ Admin Panel</a></li>
                                        <li><a href="index.php?page=pending_requests">⏳ Pending Requests</a></li>
                                    <?php endif; ?>
    
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a href="index.php?page=login" class="nav-link">
                                    <span class="nav-emoji">🔑</span>
                                    <span class="nav-label">Login</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=register" class="nav-link">
                                    <span class="nav-emoji">📝</span>
                                    <span class="nav-label">Register</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="nav-right">
                    <?php if (Session::isLoggedIn()): ?>
                        <li class="user-menu">
                            <div class="user-info-container">
                                <a href="index.php?page=profile" class="user-info-box" data-user-type="<?php echo $userType; ?>">
                                    <span class="user-greeting">👋 Hi, <?php echo htmlspecialchars(Session::getFirstName()); ?></span>
                                    <span class="user-category"><?php echo ucfirst($userType); ?></span>
                                </a>
                            </div>
                            
                            <div class="header-notifications">
                                <a href="index.php?page=notifications" class="notification-link" title="Notifications">
                                    🔔
                                </a>
                            </div>
                            
                            <button id="theme-toggle" class="nav-link theme-toggle circular-toggle" title="Toggle theme">
                                <span class="nav-emoji theme-icon">🌙</span>
                            </button>
                            
                            <form method="POST" class="logout-form">
                                <input type="hidden" name="action" value="logout">
                                <button type="submit" class="btn btn-logout">Logout</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <button id="theme-toggle" class="nav-link theme-toggle circular-toggle" title="Toggle theme">
                                <span class="nav-emoji theme-icon">🌙</span>
                            </button>
                        </li>
                    <?php endif; ?>
                </div>
                
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
    
    
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Action</h3>
                <button class="modal-close" id="modalClose">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modalMessage">Are you sure you want to perform this action?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="modalCancel">Cancel</button>
                <button class="btn btn-primary" id="modalConfirm">Confirm</button>
            </div>
        </div>
    </div>

    
    <script src="public/js/app.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    </script>
</body>
</html> 