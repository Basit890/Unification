<h2>My Profile</h2>
<div class="card">
    <h3>Account Information</h3>
    <?php
    $user = $userModel->findById(Session::getUserId());
    $userTypeLabels = [
        'admin' => 'Administrator',
        'donor' => 'Donor',
        'fundraiser' => 'Fundraiser'
    ];
    $userType = $userTypeLabels[$user['user_type']] ?? 'User';
    ?>
    <p><strong>Name:</strong> <?php echo htmlspecialchars(Session::getFullName()); ?></p>
    <p><strong>Account Type:</strong> <?php echo htmlspecialchars($userType); ?></p>
    <p><strong>Religion:</strong> <?php echo ucfirst(htmlspecialchars($user['religion'])); ?></p>
</div>

<div class="card">
    <h3>Quick Actions</h3>
    <?php if ($user['user_type'] === 'fundraiser' || $user['user_type'] === 'admin'): ?>
        <a href="index.php?page=create_request" class="btn">Create New Request</a>
        <a href="index.php?page=my_requests" class="btn btn-secondary">View My Requests</a>
    <?php endif; ?>
    <?php if ($user['user_type'] === 'donor' || $user['user_type'] === 'fundraiser' || $user['user_type'] === 'admin'): ?>
        <a href="index.php?page=donation_history" class="btn btn-secondary">View My Donations</a>
    <?php endif; ?>
    <?php if ($user['user_type'] === 'admin'): ?>
        <a href="index.php?page=admin" class="btn btn-primary">Admin Panel</a>
        <a href="index.php?page=pending_requests" class="btn btn-primary">Pending Requests</a>
    <?php endif; ?>
</div>

<?php
$user = $userModel->findById(Session::getUserId());
if ($user && $user['religion'] === 'islam'): ?>
    <?php include 'app/views/user/zakat_calculator.php'; ?>
<?php endif; ?> 