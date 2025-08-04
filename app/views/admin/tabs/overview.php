<div class="content-section">
    <h2>📊 Dashboard Overview</h2>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3>🚀 Quick Actions</h3>
        <div class="action-grid">
            <a href="index.php?page=pending_requests" class="action-card">
                <div class="action-icon">🔔</div>
                <div class="action-content">
                    <h4>Review Pending Requests</h4>
                    <p><?php echo $stats['pending_requests']; ?> requests waiting for approval</p>
                </div>
            </a>
            
            <a href="index.php?page=admin&tab=users" class="action-card">
                <div class="action-icon">👥</div>
                <div class="action-content">
                    <h4>Manage Users</h4>
                    <p><?php echo $stats['total_users']; ?> registered users</p>
                </div>
            </a>
            
            <a href="index.php?page=admin&tab=requests" class="action-card">
                <div class="action-icon">📋</div>
                <div class="action-content">
                    <h4>View All Requests</h4>
                    <p><?php echo $stats['total_requests']; ?> total requests</p>
                </div>
            </a>
            
            <a href="index.php?page=admin&tab=donations" class="action-card">
                <div class="action-icon">💰</div>
                <div class="action-content">
                    <h4>Donation Reports</h4>
                    <p>৳<?php echo number_format($stats['total_donations'], 2); ?> total raised</p>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="recent-activity">
        <h3>📈 Recent Activity</h3>
        
        <div class="activity-grid">
            <!-- Recent Requests -->
            <div class="activity-card">
                <h4>🆕 Recent Requests</h4>
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
                    <p class="no-data">No recent requests</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recentRequests as $request): ?>
                            <div class="activity-item">
                                <div class="activity-info">
                                    <strong><?php echo htmlspecialchars($request['title']); ?></strong>
                                    <span class="activity-meta">by <?php echo htmlspecialchars($request['user_name']); ?></span>
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
            
            <!-- Recent Donations -->
            <div class="activity-card">
                <h4>💝 Recent Donations</h4>
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
                    <p class="no-data">No recent donations</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recentDonations as $donation): ?>
                            <div class="activity-item">
                                <div class="activity-info">
                                    <strong>৳<?php echo number_format($donation['amount'], 2); ?></strong>
                                    <span class="activity-meta">by <?php echo htmlspecialchars($donation['donor_name']); ?></span>
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
    margin-bottom: 2rem;
}

.quick-actions h3 {
    margin-bottom: 1rem;
    color: var(--text-primary);
    font-size: 1.3rem;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.action-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--bg-secondary);
    border-radius: 12px;
    box-shadow: var(--shadow);
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.action-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
    text-decoration: none;
    color: var(--text-primary);
}

.action-icon {
    font-size: 2rem;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--accent-gradient);
    border-radius: 12px;
    color: white;
}

.action-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.action-content p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.recent-activity h3 {
    margin-bottom: 1rem;
    color: var(--text-primary);
    font-size: 1.3rem;
}

.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.activity-card {
    background: var(--bg-secondary);
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
}

.activity-card h4 {
    margin: 0 0 1rem 0;
    color: var(--text-primary);
    font-size: 1.1rem;
    font-weight: 600;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.activity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: rgba(102, 126, 234, 0.05);
    border-radius: 8px;
    border-left: 3px solid var(--primary-color);
}

.activity-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.activity-info strong {
    color: var(--text-primary);
    font-size: 0.9rem;
}

.activity-meta {
    color: var(--text-secondary);
    font-size: 0.8rem;
}

.activity-status {
    display: flex;
    align-items: center;
}

.activity-detail small {
    color: var(--text-secondary);
    font-size: 0.8rem;
}

.no-data {
    color: var(--text-secondary);
    font-style: italic;
    text-align: center;
    padding: 1rem;
}

@media (max-width: 768px) {
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .activity-grid {
        grid-template-columns: 1fr;
    }
    
    .action-card {
        padding: 1rem;
    }
    
    .action-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
}
</style> 