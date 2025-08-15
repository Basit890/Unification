<?php
$stats = [];

$stmt = $pdo->query("SELECT COUNT(*) FROM help_requests");
$stats['total_requests'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM help_requests WHERE status = 'pending'");
$stats['pending_requests'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM help_requests WHERE status = 'approved'");
$stats['approved_requests'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM help_requests WHERE status = 'completed'");
$stats['completed_requests'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$stats['total_users'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM donations");
$stats['total_donations'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM comments");
$stats['total_comments'] = $stmt->fetchColumn();

$tab = $_GET['tab'] ?? 'overview';
?>

<div class="admin-container">
    <!-- Admin Header -->
    <div class="admin-header">
        <h1>Admin Dashboard</h1>
        <p>Manage your crowdfunding platform</p>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-number"><?php echo $stats['total_requests']; ?></div>
            <div class="stat-label">Total Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-number"><?php echo $stats['pending_requests']; ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-number"><?php echo $stats['approved_requests']; ?></div>
            <div class="stat-label">Approved Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎉</div>
            <div class="stat-number"><?php echo $stats['completed_requests']; ?></div>
            <div class="stat-label">Completed Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-number"><?php echo $stats['total_users']; ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-number">৳<?php echo number_format($stats['total_donations'], 2); ?></div>
            <div class="stat-label">Total Donations</div>
        </div>
    </div>

    <!-- Admin Tabs -->
    <div class="admin-tabs">
        <a href="index.php?page=admin&tab=overview" class="admin-tab <?php echo $tab === 'overview' ? 'active' : ''; ?>">
            📊 Overview
        </a>
        <a href="index.php?page=admin&tab=requests" class="admin-tab <?php echo $tab === 'requests' ? 'active' : ''; ?>">
            📋 Help Requests
        </a>
        <a href="index.php?page=admin&tab=users" class="admin-tab <?php echo $tab === 'users' ? 'active' : ''; ?>">
            👥 Users
        </a>
        <a href="index.php?page=admin&tab=donations" class="admin-tab <?php echo $tab === 'donations' ? 'active' : ''; ?>">
            💰 Donations
        </a>
        <a href="index.php?page=admin&tab=comments" class="admin-tab <?php echo $tab === 'comments' ? 'active' : ''; ?>">
            💬 Comments
        </a>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <?php
        switch ($tab) {
            case 'overview':
                include 'app/views/admin/tabs/overview.php';
                break;
            case 'requests':
                include 'app/views/admin/tabs/requests.php';
                break;
            case 'users':
                include 'app/views/admin/tabs/users.php';
                break;
            case 'donations':
                include 'app/views/admin/tabs/donations.php';
                break;
            case 'comments':
                include 'app/views/admin/tabs/comments.php';
                break;
            default:
                include 'app/views/admin/tabs/overview.php';
                break;
        }
        ?>
    </div>
</div>

<style>
/* Admin Dashboard Styles */
.admin-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.admin-header {
    text-align: center;
    margin-bottom: 3rem;
    padding: 2rem;
    background: var(--card-bg);
    border-radius: 16px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
}

.admin-header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.admin-header p {
    font-size: 1.1rem;
    color: var(--text-light);
    margin: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 16px;
    text-align: center;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
}

.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-color);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--text-light);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.admin-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    background: var(--card-bg);
    padding: 1rem;
    border-radius: 16px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
    flex-wrap: wrap;
}

.admin-tab {
    padding: 0.75rem 1.5rem;
    text-decoration: none;
    color: var(--text-light);
    background: transparent;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    font-weight: 500;
}

.admin-tab:hover {
    background: rgba(102, 126, 234, 0.1);
    color: var(--accent-color);
    border-color: rgba(102, 126, 234, 0.2);
}

.admin-tab.active {
    background: var(--accent-color);
    color: white;
    border-color: var(--accent-color);
}

.tab-content {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
}

/* Dark theme overrides */
[data-theme="dark"] .admin-header,
[data-theme="dark"] .stat-card,
[data-theme="dark"] .admin-tabs,
[data-theme="dark"] .tab-content {
    background: var(--card-bg);
    border-color: var(--border-color);
    color: var(--text-dark);
}

[data-theme="dark"] .admin-header h1,
[data-theme="dark"] .admin-header p,
[data-theme="dark"] .stat-label {
    color: var(--text-dark);
}

[data-theme="dark"] .stat-number {
    color: var(--accent-color);
}

[data-theme="dark"] .admin-tab {
    color: var(--text-light);
}

[data-theme="dark"] .admin-tab.active,
[data-theme="dark"] .admin-tab:hover {
    color: var(--accent-color);
    background: rgba(102, 126, 234, 0.1);
}

/* Responsive design */
@media (max-width: 768px) {
    .admin-container {
        padding: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-tabs {
        flex-direction: column;
    }
    
    .admin-tab {
        text-align: center;
    }
    
    .tab-content {
        padding: 1.5rem;
    }
}
</style>

