<div class="content-section">
    <h2>📊 Dashboard Overview</h2>
    
    
    <div class="quick-actions">
        <h3>🚀 Quick Actions</h3>
        <div class="action-grid">
            <a href="index.php?page=pending_requests" class="action-card">
                <div class="action-icon pending">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="action-content">
                    <h4>Review Pending Requests</h4>
                    <p><?php echo $stats['pending_requests']; ?> requests waiting for approval</p>
                    <div class="action-badge pending"><?php echo $stats['pending_requests']; ?> pending</div>
                </div>
            </a>
            
            <a href="index.php?page=admin&tab=users" class="action-card">
                <div class="action-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-content">
                    <h4>Manage Users</h4>
                    <p><?php echo $stats['total_users']; ?> registered users</p>
                    <div class="action-badge users"><?php echo $stats['total_users']; ?> users</div>
                </div>
            </a>
            
            <a href="index.php?page=admin&tab=requests" class="action-card">
                <div class="action-icon requests">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="action-content">
                    <h4>View All Requests</h4>
                    <p><?php echo $stats['total_requests']; ?> total requests</p>
                    <div class="action-badge requests"><?php echo $stats['total_requests']; ?> total</div>
                </div>
            </a>
            
            <a href="index.php?page=admin&tab=donations" class="action-card">
                <div class="action-icon donations">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="action-content">
                    <h4>Donation Reports</h4>
                    <p>৳<?php echo number_format($stats['total_donations'], 2); ?> total raised</p>
                    <div class="action-badge donations">৳<?php echo number_format($stats['total_donations'], 2); ?></div>
                </div>
            </a>
        </div>
    </div>
    
    
    <div class="recent-activity">
        <h3>📈 Recent Activity</h3>
        
        <div class="activity-grid">
            
            <div class="activity-card">
                <div class="activity-header">
                    <h4><i class="fas fa-plus-circle"></i> Recent Requests</h4>
                    <span class="activity-count"><?php echo count($recentRequests ?? []); ?> new</span>
                </div>
                <?php
                $stmt = $pdo->query("
                    SELECT hr.*, CONCAT(u.first_name, ' ', u.last_name) as user_name 
                    FROM help_requests hr 
                    JOIN users u ON hr.user_id = u.id 
                    ORDER BY hr.created_at DESC 
                    LIMIT 5
                ");
                $recentRequests = $stmt->fetchAll();
                ?>
                
                <?php if (empty($recentRequests)): ?>
                    <div class="no-data">
                        <i class="fas fa-inbox"></i>
                        <p>No recent requests</p>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recentRequests as $request): ?>
                            <div class="activity-item">
                                <div class="activity-icon status-<?php echo $request['status']; ?>">
                                    <i class="fas fa-<?php echo $request['status'] === 'pending' ? 'clock' : ($request['status'] === 'approved' ? 'check' : 'star'); ?>"></i>
                                </div>
                                <div class="activity-info">
                                    <strong><?php echo htmlspecialchars($request['title']); ?></strong>
                                    <span class="activity-meta">by <?php echo htmlspecialchars($request['user_name']); ?></span>
                                    <span class="activity-time"><?php echo date('M j, g:i A', strtotime($request['created_at'])); ?></span>
                                </div>
                                <div class="activity-status">
                                    <span class="badge badge-<?php echo $request['status']; ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            
            <div class="activity-card">
                <div class="activity-header">
                    <h4><i class="fas fa-heart"></i> Recent Donations</h4>
                    <span class="activity-count"><?php echo count($recentDonations ?? []); ?> new</span>
                </div>
                <?php
                $stmt = $pdo->query("
                    SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as donor_name, hr.title as request_title
                    FROM donations d 
                    JOIN users u ON d.user_id = u.id 
                    JOIN help_requests hr ON d.request_id = hr.id 
                    ORDER BY d.created_at DESC 
                    LIMIT 5
                ");
                $recentDonations = $stmt->fetchAll();
                ?>
                
                <?php if (empty($recentDonations)): ?>
                    <div class="no-data">
                        <i class="fas fa-gift"></i>
                        <p>No recent donations</p>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recentDonations as $donation): ?>
                            <div class="activity-item">
                                <div class="activity-icon donation">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </div>
                                <div class="activity-info">
                                    <strong class="donation-amount">৳<?php echo number_format($donation['amount'], 2); ?></strong>
                                    <span class="activity-meta">by <?php echo htmlspecialchars($donation['donor_name']); ?></span>
                                    <span class="activity-time"><?php echo date('M j, g:i A', strtotime($donation['created_at'])); ?></span>
                                </div>
                                <div class="activity-detail">
                                    <small><?php echo htmlspecialchars(substr($donation['request_title'], 0, 30)) . '...'; ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.quick-actions {
    margin-bottom: 2.5rem;
}

.quick-actions h3 {
    margin-bottom: 1.5rem;
    color: var(--text-primary);
    font-size: 1.4rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.action-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 2rem;
    background: linear-gradient(135deg, var(--card-bg) 0%, rgba(255, 255, 255, 0.05) 100%);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.action-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    text-decoration: none;
    color: var(--text-primary);
}

.action-card:hover::before {
    transform: scaleX(1);
}

.action-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    font-size: 1.8rem;
    color: white;
    position: relative;
    flex-shrink: 0;
}

.action-icon.pending {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
}

.action-icon.users {
    background: linear-gradient(135deg, #4ecdc4, #44a08d);
}

.action-icon.requests {
    background: linear-gradient(135deg, #45b7d1, #96c93d);
}

.action-icon.donations {
    background: linear-gradient(135deg, #f093fb, #f5576c);
}

.action-content {
    flex: 1;
}

.action-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-primary);
}

.action-content p {
    margin: 0 0 0.75rem 0;
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.4;
}

.action-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.action-badge.pending {
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 1px solid rgba(255, 107, 107, 0.3);
}

.action-badge.users {
    background: rgba(78, 205, 196, 0.2);
    color: #4ecdc4;
    border: 1px solid rgba(78, 205, 196, 0.3);
}

.action-badge.requests {
    background: rgba(69, 183, 209, 0.2);
    color: #45b7d1;
    border: 1px solid rgba(69, 183, 209, 0.3);
}

.action-badge.donations {
    background: rgba(240, 147, 251, 0.2);
    color: #f093fb;
    border: 1px solid rgba(240, 147, 251, 0.3);
}

.recent-activity h3 {
    margin-bottom: 1.5rem;
    color: var(--text-primary);
    font-size: 1.4rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.activity-card {
    background: linear-gradient(135deg, var(--card-bg) 0%, rgba(255, 255, 255, 0.02) 100%);
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.activity-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.activity-header h4 {
    margin: 0;
    color: var(--text-primary);
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.activity-count {
    background: var(--accent-gradient);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateX(5px);
}

.activity-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.1rem;
    color: white;
    flex-shrink: 0;
}

.activity-icon.status-pending {
    background: linear-gradient(135deg, #ffa726, #ff9800);
}

.activity-icon.status-approved {
    background: linear-gradient(135deg, #66bb6a, #4caf50);
}

.activity-icon.status-completed {
    background: linear-gradient(135deg, #42a5f5, #2196f3);
}

.activity-icon.donation {
    background: linear-gradient(135deg, #ec407a, #e91e63);
}

.activity-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.activity-info strong {
    color: var(--text-primary);
    font-size: 0.95rem;
    font-weight: 600;
}

.donation-amount {
    color: var(--primary-color);
    font-size: 1.1rem;
}

.activity-meta {
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.activity-time {
    color: var(--text-light);
    font-size: 0.8rem;
    opacity: 0.8;
}

.activity-status {
    display: flex;
    align-items: center;
}

.activity-detail small {
    color: var(--text-secondary);
    font-size: 0.8rem;
    opacity: 0.8;
}

.no-data {
    text-align: center;
    padding: 2rem;
    color: var(--text-secondary);
}

.no-data i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-data p {
    margin: 0;
    font-style: italic;
}

/* Responsive design */
@media (max-width: 768px) {
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .activity-grid {
        grid-template-columns: 1fr;
    }
    
    .action-card {
        padding: 1.5rem;
        flex-direction: column;
        text-align: center;
    }
    
    .action-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .activity-card {
        padding: 1.5rem;
    }
    
    .activity-item {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .activity-icon {
        width: 50px;
        height: 50px;
    }
}
</style> 