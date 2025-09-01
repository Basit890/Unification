<?php
$user = $userModel->findById(Session::getUserId());
$userTypeLabels = [
    'admin' => 'Administrator',
    'donor' => 'Donor',
    'fundraiser' => 'Fundraiser'
];
$userType = $userTypeLabels[$user['user_type']] ?? 'User';

$userEmail = $user['email'] ?? 'Not available';
$userUsername = $user['username'] ?? 'Not available';
?>

<div class="profile-container">
    
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo strtoupper(substr(Session::getFullName(), 0, 1)); ?>
        </div>
        <h1 class="profile-name"><?php echo htmlspecialchars(Session::getFullName()); ?></h1>
        <p class="profile-email"><?php echo htmlspecialchars($userEmail); ?></p>
    </div>

    <div class="profile-sections">
        
        <div class="profile-section personal-info">
            <h3>Personal Information</h3>
            <div class="profile-field">
                <span class="field-label">Full Name</span>
                <span class="field-value"><?php echo htmlspecialchars(Session::getFullName()); ?></span>
                <span class="field-icon">👤</span>
            </div>
            <div class="profile-field">
                <span class="field-label">Email Address</span>
                <span class="field-value"><?php echo htmlspecialchars($userEmail); ?></span>
                <span class="field-icon">📧</span>
            </div>
        </div>

        
        <div class="profile-section religious-info">
            <h3>Religious Information</h3>
            <div class="profile-field">
                <span class="field-label">Religion</span>
                <span class="field-value"><?php echo ucfirst(htmlspecialchars($user['religion'] ?? 'Not specified')); ?></span>
                <span class="field-icon">🕌</span>
            </div>
        </div>

        
        <div class="profile-section account-info">
            <h3>Account Information</h3>
            <div class="profile-field field-username">
                <span class="field-label">Username</span>
                <span class="field-value"><?php echo htmlspecialchars($userUsername); ?></span>
                <span class="field-icon">🆔</span>
            </div>
            <div class="profile-field field-email">
                <span class="field-label">Email</span>
                <span class="field-value"><?php echo htmlspecialchars($userEmail); ?></span>
                <span class="field-icon">📧</span>
            </div>
            <div class="profile-field field-role">
                <span class="field-label">Account Type</span>
                <span class="field-value"><?php echo htmlspecialchars($userType); ?></span>
                <span class="field-icon">👥</span>
            </div>
            <div class="profile-field field-status">
                <span class="field-label">Account Status</span>
                <span class="field-value">Active</span>
                <span class="field-icon">✅</span>
            </div>

            
            <div class="account-actions">
                <h4>Account Actions</h4>
                <div class="action-buttons">
                    <?php if ($user['user_type'] === 'fundraiser' || $user['user_type'] === 'admin'): ?>
                        <a href="index.php?page=create_request" class="action-btn">
                            <span>➕</span> Create New Request
                        </a>
                        <a href="index.php?page=my_requests" class="action-btn secondary">
                            <span>📋</span> View My Requests
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($user['user_type'] === 'donor' || $user['user_type'] === 'fundraiser' || $user['user_type'] === 'admin'): ?>
                        <a href="index.php?page=donation_history" class="action-btn secondary">
                            <span>💰</span> View My Donations
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($user['user_type'] === 'admin'): ?>
                        <a href="index.php?page=admin" class="action-btn">
                            <span>⚙️</span> Admin Panel
                        </a>
                        <a href="index.php?page=pending_requests" class="action-btn">
                            <span>⏳</span> Pending Requests
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <?php if ($user && ($user['religion'] === 'islam' || $user['religion'] === 'Islam')): ?>
        <?php include 'app/views/user/zakat_calculator.php'; ?>
    <?php endif; ?>
</div> 