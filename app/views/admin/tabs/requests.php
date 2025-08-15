<div class="content-section">
    <h2>📋 Help Requests Management</h2>
    
    <!-- Request Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-number"><?php echo $stats['total_requests']; ?></div>
            <div class="stat-label">Total Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-number"><?php echo $stats['pending_requests']; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-number"><?php echo $stats['approved_requests']; ?></div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🎉</div>
            <div class="stat-number"><?php echo $stats['completed_requests']; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">❌</div>
            <div class="stat-number">
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM help_requests WHERE status = 'rejected'");
                echo $stmt->fetchColumn();
                ?>
            </div>
            <div class="stat-label">Rejected</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🔒</div>
            <div class="stat-number">
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM help_requests WHERE status = 'closed'");
                echo $stmt->fetchColumn();
                ?>
            </div>
            <div class="stat-label">Closed</div>
        </div>
    </div>
    
    <!-- Request Filters -->
    <div class="filter-section">
        <div class="filter-controls">
            <select id="statusFilter" onchange="filterRequests()">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="completed">Completed</option>
                <option value="closed">Closed</option>
            </select>
            
            <select id="categoryFilter" onchange="filterRequests()">
                <option value="">All Categories</option>
                <option value="health">Health</option>
                <option value="education">Education</option>
                <option value="emergency">Emergency</option>
                <option value="community">Community Aid</option>
                <option value="other">Other</option>
            </select>
            
            <input type="text" id="searchFilter" placeholder="Search requests..." onkeyup="filterRequests()">
        </div>
    </div>
    
    <!-- Requests Table -->
    <div class="table-responsive">
        <table class="table" id="requestsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Creator</th>
                    <th>Category</th>
                    <th>Goal Amount</th>
                    <th>Raised</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("
                    SELECT hr.*, CONCAT(u.first_name, ' ', u.last_name) as user_name 
                    FROM help_requests hr 
                    JOIN users u ON hr.user_id = u.id 
                    ORDER BY hr.created_at DESC
                ");
                $stmt->execute();
                $requests = $stmt->fetchAll();
                ?>
                
                <?php foreach ($requests as $request): ?>
                    <tr class="request-row" data-status="<?php echo $request['status']; ?>" data-category="<?php echo $request['category']; ?>">
                        <td><?php echo $request['id']; ?></td>
                        <td>
                            <div class="request-title">
                                <strong><?php echo htmlspecialchars($request['title']); ?></strong>
                                <small><?php echo htmlspecialchars(substr($request['description'], 0, 50)) . '...'; ?></small>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                        <td>
                            <span class="category-badge"><?php echo ucfirst($request['category']); ?></span>
                        </td>
                        <td>৳<?php echo number_format($request['goal_amount'], 2); ?></td>
                        <td>
                            <div class="amount-info">
                                <strong>৳<?php echo number_format($request['current_amount'], 2); ?></strong>
                                <div class="progress-mini">
                                    <div class="progress-fill-mini" style="width: <?php echo min(($request['current_amount'] / $request['goal_amount']) * 100, 100); ?>%"></div>
                                </div>
                                <small><?php echo number_format(($request['current_amount'] / $request['goal_amount']) * 100, 1); ?>%</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $request['status']; ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?page=view_request&id=<?php echo $request['id']; ?>" class="btn btn-secondary" title="View Details">
                                    👁️
                                </a>
                                
                                <?php if ($request['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="approve_request">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" class="btn btn-success" title="Approve Request">
                                            ✅
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="reject_request">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" class="btn btn-danger" title="Reject Request" onclick="return confirm('Are you sure you want to reject this request?')">
                                            ❌
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($request['status'] === 'approved'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="update_request_status">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-success" title="Mark as Completed">
                                            🎉
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_request">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" class="btn btn-danger" title="Delete Request" onclick="return confirm('Are you sure you want to delete this request? This action cannot be undone.')">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.request-stats {
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
        
        /* Filter Controls Styling */
        .filter-section {
            margin-bottom: 2rem;
        }
        
        .filter-controls {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: end;
        }
        
        .filter-controls select,
        .filter-controls input {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-dark);
            min-width: 150px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        
        .filter-controls select:focus,
        .filter-controls input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .filter-controls select:hover,
        .filter-controls input:hover {
            border-color: var(--accent-color);
            transform: translateY(-1px);
            box-shadow: var(--shadow-hover);
        }
        
        .filter-controls input {
            flex: 1;
            min-width: 200px;
        }
        
