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
            
            // Calculate total comments for progress bars
            $totalComments = array_sum(array_column($topCommenters, 'comment_count'));
            ?>
            
            <div class="summary-card">
                <div class="summary-header">
                    <h4><i class="fas fa-comments"></i> Top Commenters</h4>
                    <span class="summary-subtitle">Most active contributors</span>
                </div>
                <?php if (empty($topCommenters)): ?>
                    <div class="no-data">
                        <i class="fas fa-comment-slash"></i>
                        <p>No comments yet</p>
                    </div>
                <?php else: ?>
                    <div class="commenter-list">
                        <?php foreach ($topCommenters as $index => $commenter): ?>
                            <div class="commenter-item">
                                <div class="commenter-rank">
                                    <span class="rank-number">#<?php echo $index + 1; ?></span>
                                    <?php if ($index === 0): ?>
                                        <i class="fas fa-fire fire-orange"></i>
                                    <?php elseif ($index === 1): ?>
                                        <i class="fas fa-star star-yellow"></i>
                                    <?php elseif ($index === 2): ?>
                                        <i class="fas fa-gem gem-blue"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="commenter-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="commenter-details">
                                    <strong><?php echo htmlspecialchars($commenter['first_name'] . ' ' . $commenter['last_name']); ?></strong>
                                    <small><?php echo round($commenter['avg_length']); ?> avg chars per comment</small>
                                    <div class="commenter-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo ($commenter['comment_count'] / $totalComments) * 100; ?>%"></div>
                                        </div>
                                        <span class="progress-text"><?php echo round(($commenter['comment_count'] / $totalComments) * 100, 1); ?>%</span>
                                    </div>
                                </div>
                                <div class="commenter-stats">
                                    <div class="stat-primary">
                                        <span class="stat-value"><?php echo $commenter['comment_count']; ?></span>
                                        <span class="stat-label">Comments</span>
                                    </div>
                                    <div class="stat-secondary">
                                        <span class="stat-value"><?php echo round($commenter['avg_length']); ?></span>
                                        <span class="stat-label">Avg Length</span>
                                    </div>
                                </div>
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
            
            // Calculate max daily count for chart scaling
            $maxDailyCount = max(array_column($recentComments, 'daily_count')) ?: 1;
            ?>
            
            <div class="summary-card">
                <div class="summary-header">
                    <h4><i class="fas fa-chart-area"></i> Last 7 Days</h4>
                    <span class="summary-subtitle">Daily comment activity</span>
                </div>
                <?php if (empty($recentComments)): ?>
                    <div class="no-data">
                        <i class="fas fa-chart-line"></i>
                        <p>No recent comments</p>
                    </div>
                <?php else: ?>
                    <div class="chart-container">
                        <div class="chart-bars">
                            <?php foreach ($recentComments as $day): ?>
                                <div class="chart-bar">
                                    <div class="bar-fill" style="height: <?php echo ($day['daily_count'] / $maxDailyCount) * 100; ?>%"></div>
                                    <div class="bar-label"><?php echo date('M j', strtotime($day['date'])); ?></div>
                                    <div class="bar-value"><?php echo $day['daily_count']; ?></div>
                                    <div class="bar-avg"><?php echo round($day['avg_length']); ?> chars</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="chart-stats">
                            <div class="stat-mini">
                                <span class="stat-label">Total Week</span>
                                <span class="stat-value"><?php echo array_sum(array_column($recentComments, 'daily_count')); ?></span>
                            </div>
                            <div class="stat-mini">
                                <span class="stat-label">Avg/Day</span>
                                <span class="stat-value"><?php echo round(array_sum(array_column($recentComments, 'daily_count')) / count($recentComments), 1); ?></span>
                            </div>
                            <div class="stat-mini">
                                <span class="stat-label">Avg Length</span>
                                <span class="stat-value"><?php echo round(array_sum(array_column($recentComments, 'avg_length')) / count($recentComments), 0); ?></span>
                            </div>
                        </div>
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
        
/* Comment Summary Styles */
.comment-summary {
    margin-top: 3rem;
}

.comment-summary h3 {
    margin-bottom: 2rem;
    color: var(--text-primary);
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.summary-card {
    background: linear-gradient(135deg, var(--card-bg) 0%, rgba(255, 255, 255, 0.02) 100%);
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.summary-header {
    margin-bottom: 1.5rem;
    text-align: center;
}

.summary-header h4 {
    margin: 0 0 0.5rem 0;
    color: var(--text-primary);
    font-size: 1.3rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.summary-subtitle {
    color: var(--text-secondary);
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Top Commenters Styles */
.commenter-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.commenter-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.commenter-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateX(5px);
}

.commenter-rank {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    min-width: 50px;
}

.rank-number {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
}

.fire-orange {
    color: #ff6b35;
    font-size: 1.1rem;
}

.star-yellow {
    color: #ffd700;
    font-size: 1rem;
}

.gem-blue {
    color: #4ecdc4;
    font-size: 1rem;
}

.commenter-avatar {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.commenter-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.commenter-details strong {
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 600;
}

.commenter-details small {
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.commenter-progress {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.progress-bar {
    flex: 1;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    border-radius: 3px;
    transition: width 0.3s ease;
}

.progress-text {
    color: var(--text-light);
    font-size: 0.8rem;
    font-weight: 600;
    min-width: 40px;
}

.commenter-stats {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    min-width: 100px;
}

.stat-primary, .stat-secondary {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    text-align: center;
}

.stat-primary .stat-value {
    color: var(--primary-color);
    font-size: 1.1rem;
    font-weight: 700;
}

.stat-secondary .stat-value {
    color: var(--secondary-color);
    font-size: 1rem;
    font-weight: 600;
}

.stat-primary .stat-label, .stat-secondary .stat-label {
    color: var(--text-secondary);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

/* Chart Styles */
.chart-container {
    padding: 1rem 0;
}

.chart-bars {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 0.5rem;
    height: 200px;
    margin-bottom: 1.5rem;
    padding: 0 1rem;
}

.chart-bar {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
}

.bar-fill {
    width: 100%;
    background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
    border-radius: 4px 4px 0 0;
    transition: height 0.3s ease;
    min-height: 10px;
}

.bar-label {
    color: var(--text-secondary);
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
}

.bar-value {
    color: var(--text-primary);
    font-size: 0.9rem;
    font-weight: 600;
    text-align: center;
}

.bar-avg {
    color: var(--text-light);
    font-size: 0.75rem;
    opacity: 0.8;
    text-align: center;
}

.chart-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.stat-mini {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    text-align: center;
}

.stat-mini .stat-label {
    color: var(--text-secondary);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.stat-mini .stat-value {
    color: var(--text-primary);
    font-size: 1.1rem;
    font-weight: 700;
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

/* Filter Count Display */
.comment-count-display {
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    text-align: center;
}

.count-info {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.count-info strong {
    color: var(--text-primary);
}

/* Clear Filters Button */
.clear-filters {
    background: var(--text-secondary);
    color: var(--card-bg);
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.clear-filters:hover {
    background: var(--text-primary);
    transform: translateY(-1px);
}
</style>

<script>
function filterComments() {
    const commentLengthFilter = document.getElementById('commentLengthFilter').value;
    const commentDateFilter = document.getElementById('commentDateFilter').value;
    const commentSearchFilter = document.getElementById('commentSearchFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('.comment-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const commentLength = parseInt(row.dataset.length);
        const commentDate = row.dataset.date;
        const searchText = row.querySelector('.comment-text').textContent.toLowerCase();
        
        let matchesLength = true;
        if (commentLengthFilter === 'short') {
            matchesLength = commentLength < 50;
        } else if (commentLengthFilter === 'medium') {
            matchesLength = commentLength >= 50 && commentLength <= 200;
        } else if (commentLengthFilter === 'long') {
            matchesLength = commentLength > 200;
        }
        
        const matchesDate = !commentDateFilter || commentDate === commentDateFilter;
        const matchesSearch = !commentSearchFilter || searchText.includes(commentSearchFilter);
        
        if (matchesLength && matchesDate && matchesSearch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update visible count display
    updateCommentCount(visibleCount);
    
    // Show/hide no results message
    const noResultsMsg = document.getElementById('noResultsMessage');
    if (visibleCount === 0) {
        if (!noResultsMsg) {
            const tbody = document.querySelector('#commentsTable tbody');
            const noResultsRow = document.createElement('tr');
            noResultsRow.id = 'noResultsMessage';
            noResultsRow.innerHTML = `
                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    <p>No comments match your filters</p>
                    <small>Try adjusting your search criteria</small>
                </td>
            `;
            tbody.appendChild(noResultsRow);
        }
    } else {
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }
}

function updateCommentCount(visibleCount) {
    const totalCount = document.querySelectorAll('.comment-row').length;
    const countDisplay = document.getElementById('commentCountDisplay');
    
    if (!countDisplay) {
        const filterSection = document.querySelector('.filter-section');
        const countDiv = document.createElement('div');
        countDiv.id = 'commentCountDisplay';
        countDiv.className = 'comment-count-display';
        countDiv.innerHTML = `
            <span class="count-info">
                Showing <strong>${visibleCount}</strong> of <strong>${totalCount}</strong> comments
            </span>
        `;
        filterSection.appendChild(countDiv);
    } else {
        countDisplay.innerHTML = `
            <span class="count-info">
                Showing <strong>${visibleCount}</strong> of <strong>${totalCount}</strong> comments
            </span>
        `;
    }
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('commentDateFilter').value = today;
    
    // Add clear filters button
    const filterControls = document.querySelector('.filter-controls');
    const clearButton = document.createElement('button');
    clearButton.type = 'button';
    clearButton.className = 'btn btn-secondary clear-filters';
    clearButton.innerHTML = '<i class="fas fa-times"></i> Clear Filters';
    clearButton.onclick = clearCommentFilters;
    filterControls.appendChild(clearButton);
    
    // Initial count display
    updateCommentCount(document.querySelectorAll('.comment-row').length);
});

function clearCommentFilters() {
    document.getElementById('commentLengthFilter').value = '';
    document.getElementById('commentDateFilter').value = '';
    document.getElementById('commentSearchFilter').value = '';
    filterComments();
}
</script>
