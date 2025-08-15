<div class="content-section">
    <h2>💬 Comments Management</h2>
    
    <!-- Comment Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-number"><?php echo $stats['total_comments']; ?></div>
            <div class="stat-label">Total Comments</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM comments");
        $uniqueCommenters = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">👤</div>
            <div class="stat-number"><?php echo $uniqueCommenters; ?></div>
            <div class="stat-label">Unique Commenters</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT COUNT(DISTINCT request_id) FROM comments");
        $requestsWithComments = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-number"><?php echo $requestsWithComments; ?></div>
            <div class="stat-label">Requests with Comments</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT AVG(LENGTH(comment)) FROM comments");
        $avgCommentLength = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">📏</div>
            <div class="stat-number"><?php echo round($avgCommentLength); ?></div>
            <div class="stat-label">Avg Comment Length</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE LENGTH(comment) > 200");
        $longComments = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-number"><?php echo $longComments; ?></div>
            <div class="stat-label">Long Comments (>200)</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE LENGTH(comment) < 50");
        $shortComments = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">💭</div>
            <div class="stat-number"><?php echo $shortComments; ?></div>
            <div class="stat-label">Short Comments (<50)</div>
        </div>
    </div>
    
    <!-- Comment Filters -->
    <div class="filter-section">
        <div class="filter-controls">
            <select id="commentLengthFilter" onchange="filterComments()">
                <option value="">All Lengths</option>
                <option value="short">Short (< 50 chars)</option>
                <option value="medium">Medium (50-200 chars)</option>
                <option value="long">Long (> 200 chars)</option>
            </select>
            
            <input type="date" id="commentDateFilter" onchange="filterComments()">
            
            <input type="text" id="commentSearchFilter" placeholder="Search comments..." onkeyup="filterComments()">
        </div>
    </div>
    
    <!-- Comments Table -->
    <div class="table-responsive">
        <table class="table" id="commentsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Request</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("
                    SELECT c.*, 
                           CONCAT(u.first_name, ' ', u.last_name) as user_name,
                           u.email as user_email,
                           hr.title as request_title,
                           hr.status as request_status
                    FROM comments c 
                    JOIN users u ON c.user_id = u.id 
                    JOIN help_requests hr ON c.request_id = hr.id 
                    ORDER BY c.created_at DESC
                ");
                $stmt->execute();
                $comments = $stmt->fetchAll();
                ?>
                
                <?php foreach ($comments as $comment): ?>
                    <tr class="comment-row" data-length="<?php echo strlen($comment['comment']); ?>" data-date="<?php echo date('Y-m-d', strtotime($comment['created_at'])); ?>">
                        <td><?php echo $comment['id']; ?></td>
                        <td>
                            <div class="user-info">
                                <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                                <small><?php echo htmlspecialchars($comment['user_email']); ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="request-info">
                                <strong><?php echo htmlspecialchars($comment['request_title']); ?></strong>
                                <span class="badge badge-<?php echo $comment['request_status']; ?>">
                                    <?php echo ucfirst($comment['request_status']); ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="comment-content">
                                <div class="comment-text">
                                    <?php echo htmlspecialchars($comment['comment']); ?>
                                </div>
                                <div class="comment-meta">
                                    <span class="comment-length"><?php echo strlen($comment['comment']); ?> chars</span>
                                    <?php if (strlen($comment['comment']) > 200): ?>
                                        <span class="long-comment">📝</span>
                                    <?php elseif (strlen($comment['comment']) > 50): ?>
                                        <span class="medium-comment">💬</span>
                                    <?php else: ?>
                                        <span class="short-comment">💭</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="date-main"><?php echo date('M j, Y', strtotime($comment['created_at'])); ?></div>
                                <small><?php echo date('g:i A', strtotime($comment['created_at'])); ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?page=view_request&id=<?php echo $comment['request_id']; ?>" class="btn btn-secondary" title="View Request">
                                    👁️
                                </a>
                                
                                <a href="index.php?page=profile&user_id=<?php echo $comment['user_id']; ?>" class="btn btn-secondary" title="View User">
                                    👤
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_comment">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" class="btn btn-danger" title="Delete Comment" onclick="return confirm('Are you sure you want to delete this comment? This action cannot be undone.')">
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
    
    <!-- Comment Summary -->
    <div class="comment-summary">
        <h3>📊 Comment Summary</h3>
        <div class="summary-grid">
            <?php
            $stmt = $pdo->query("
                SELECT u.first_name, u.last_name, COUNT(c.id) as comment_count, AVG(LENGTH(c.comment)) as avg_length
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                GROUP BY c.user_id 
                ORDER BY comment_count DESC 
                LIMIT 5
            ");
            $topCommenters = $stmt->fetchAll();
            ?>
            
            <div class="summary-card">
                <h4>🏆 Top Commenters</h4>
                <?php if (empty($topCommenters)): ?>
                    <p class="no-data">No comments yet</p>
                <?php else: ?>
                    <div class="commenter-list">
                        <?php foreach ($topCommenters as $index => $commenter): ?>
                            <div class="commenter-item">
                                <div class="commenter-rank">#<?php echo $index + 1; ?></div>
                                <div class="commenter-details">
                                    <strong><?php echo htmlspecialchars($commenter['first_name'] . ' ' . $commenter['last_name']); ?></strong>
                                    <small><?php echo round($commenter['avg_length']); ?> avg chars</small>
                                </div>
                                <div class="commenter-count"><?php echo $commenter['comment_count']; ?> comments</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php
            $stmt = $pdo->query("
                SELECT DATE(created_at) as date, COUNT(*) as daily_count, AVG(LENGTH(comment)) as avg_length
                FROM comments 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            ");
            $recentComments = $stmt->fetchAll();
            ?>
            
            <div class="summary-card">
                <h4>📈 Last 7 Days</h4>
                <?php if (empty($recentComments)): ?>
                    <p class="no-data">No recent comments</p>
                <?php else: ?>
                    <div class="daily-comments">
                        <?php foreach ($recentComments as $day): ?>
                            <div class="daily-comment-item">
                                <div class="daily-date"><?php echo date('M j', strtotime($day['date'])); ?></div>
                                <div class="daily-count"><?php echo $day['daily_count']; ?> comments</div>
                                <div class="daily-length"><?php echo round($day['avg_length']); ?> avg chars</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
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

/* Table Styles */
.comment-stats {
    margin-bottom: 2rem;
}

.stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        
