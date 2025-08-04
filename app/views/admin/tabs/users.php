<div class="content-section">
    <h2>👥 Users Management</h2>
    
    <!-- User Statistics -->
    <div class="user-stats">
        <div class="stat-row">
            <div class="stat-item">
                <span class="stat-number"><?php echo $stats['total_users']; ?></span>
                <span class="stat-label">Total Users</span>
            </div>
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'admin'");
            $adminCount = $stmt->fetchColumn();
            ?>
            <div class="stat-item">
                <span class="stat-number"><?php echo $adminCount; ?></span>
                <span class="stat-label">Admins</span>
            </div>
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'fundraiser'");
            $fundraiserCount = $stmt->fetchColumn();
            ?>
            <div class="stat-item">
                <span class="stat-number"><?php echo $fundraiserCount; ?></span>
                <span class="stat-label">Fundraisers</span>
            </div>
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'donor'");
            $donorCount = $stmt->fetchColumn();
            ?>
            <div class="stat-item">
                <span class="stat-number"><?php echo $donorCount; ?></span>
                <span class="stat-label">Donors</span>
            </div>
        </div>
    </div>
    
    <!-- User Filters -->
    <div class="filter-section">
        <div class="filter-controls">
            <select id="userTypeFilter" onchange="filterUsers()">
                <option value="">All User Types</option>
                <option value="admin">Admin</option>
                <option value="fundraiser">Fundraiser</option>
                <option value="donor">Donor</option>
            </select>
            
            <select id="religionFilter" onchange="filterUsers()">
                <option value="">All Religions</option>
                <option value="islam">Islam</option>
                <option value="hindu">Hindu</option>
                <option value="christian">Christian</option>
                <option value="buddhist">Buddhist</option>
                <option value="other">Other</option>
            </select>
            
            <input type="text" id="userSearchFilter" placeholder="Search users..." onkeyup="filterUsers()">
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table" id="usersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Religion</th>
                    <th>Joined</th>
                    <th>Activity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("
                    SELECT u.*, 
                           COUNT(DISTINCT hr.id) as request_count,
                           COUNT(DISTINCT d.id) as donation_count,
                           SUM(d.amount) as total_donated
                    FROM users u 
                    LEFT JOIN help_requests hr ON u.id = hr.user_id
                    LEFT JOIN donations d ON u.id = d.user_id
                    GROUP BY u.id
                    ORDER BY u.created_at DESC
                ");
                $users = $stmt->fetchAll();
                ?>
                
                <?php foreach ($users as $user): ?>
                    <tr class="user-row" data-type="<?php echo $user['user_type']; ?>" data-religion="<?php echo $user['religion']; ?>">
                        <td><?php echo $user['id']; ?></td>
                        <td>
                            <div class="user-info">
                                <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                <?php if ($user['is_admin']): ?>
                                    <span class="admin-badge">👑 Admin</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $user['user_type'] === 'admin' ? 'completed' : ($user['user_type'] === 'fundraiser' ? 'approved' : 'pending'); ?>">
                                <?php echo ucfirst($user['user_type']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="religion-badge"><?php echo ucfirst($user['religion']); ?></span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <div class="activity-info">
                                <div class="activity-item">
                                    <span class="activity-label">Requests:</span>
                                    <span class="activity-value"><?php echo $user['request_count']; ?></span>
                                </div>
                                <div class="activity-item">
                                    <span class="activity-label">Donations:</span>
                                    <span class="activity-value"><?php echo $user['donation_count']; ?></span>
                                </div>
                                <?php if ($user['total_donated'] > 0): ?>
                                    <div class="activity-item">
                                        <span class="activity-label">Total:</span>
                                        <span class="activity-value">৳<?php echo number_format($user['total_donated'], 2); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?page=profile&user_id=<?php echo $user['id']; ?>" class="btn btn-secondary" title="View Profile">
                                    👁️
                                </a>
                                
                                <?php if ($user['user_type'] !== 'admin'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="make_admin">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-success" title="Make Admin" onclick="return confirm('Are you sure you want to make this user an admin?')">
                                            👑
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($user['user_type'] === 'admin' && $user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="remove_admin">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger" title="Remove Admin" onclick="return confirm('Are you sure you want to remove admin privileges from this user?')">
                                            🚫
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger" title="Delete User" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            🗑️
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.user-stats {
    margin-bottom: 2rem;
}

.stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.stat-item {
    background: var(--bg-secondary);
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
}

        .stat-item .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-item .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
