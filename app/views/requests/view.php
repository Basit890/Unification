<?php
$request_id = $_GET['id'] ?? 0;
$request = $helpRequestController->getById($request_id);

if (!$request) {
    echo "<p>Request not found.</p>";
    return;
}

$comments = $commentController->getByRequestId($request_id);
$status_updates = $statusUpdateController->getByRequestId($request_id);
$donations = $donationController->getByRequestId($request_id);

$progress_percentage = $request['goal_amount'] > 0 ? ($request['current_amount'] / $request['goal_amount']) * 100 : 0;

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return $bytes . ' byte';
    } else {
        return '0 bytes';
    }
}
?>

<div class="two-column">
    <div class="request-main">
        <div class="request-header">
            <h2><?php echo htmlspecialchars($request['title']); ?></h2>
            <div class="request-meta">
                <div class="meta-grid">
                    <div class="meta-item">
                        <span class="meta-label">📂 Category:</span>
                        <span class="meta-value"><?php echo ucfirst(htmlspecialchars($request['category'])); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">👤 Created by:</span>
                        <span class="meta-value"><?php echo htmlspecialchars($request['user_name']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">📊 Status:</span>
                        <span class="meta-value">
                            <span class="badge badge-<?php echo $request['status']; ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">📅 Created:</span>
                        <span class="meta-value"><?php echo date('M j, Y', strtotime($request['created_at'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($request['image_path'] && file_exists($request['image_path'])): ?>
            <div class="request-image-container">
                <img src="<?php echo htmlspecialchars($request['image_path']); ?>" alt="Request Image" class="request-image">
            </div>
        <?php endif; ?>
        
        <div class="request-description">
            <h3>Description</h3>
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($request['description'])); ?>
            </div>
        </div>
        
        <?php if ($request['document_path'] && file_exists($request['document_path'])): ?>
            <div class="card">
                <h3>📄 Supporting Documents</h3>
                <div class="document-info">
                    <?php
                    $fileExtension = strtolower(pathinfo($request['document_path'], PATHINFO_EXTENSION));
                    $fileName = basename($request['document_path']);
                    $fileSize = file_exists($request['document_path']) ? filesize($request['document_path']) : 0;
                    $fileSizeFormatted = $fileSize > 0 ? formatFileSize($fileSize) : 'Unknown size';
                    ?>
                    <div class="document-preview">
                        <div class="document-icon">
                            <?php
                            switch($fileExtension) {
                                case 'pdf':
                                    echo '📄';
                                    break;
                                case 'doc':
                                case 'docx':
                                    echo '📝';
                                    break;
                                case 'txt':
                                    echo '📄';
                                    break;
                                case 'jpg':
                                case 'jpeg':
                                case 'png':
                                case 'gif':
                                    echo '🖼️';
                                    break;
                                default:
                                    echo '📎';
                            }
                            ?>
                        </div>
                        <div class="document-details">
                            <div class="document-name"><?php echo htmlspecialchars($fileName); ?></div>
                            <div class="document-meta">
                                <span class="file-type"><?php echo strtoupper($fileExtension); ?></span>
                                <span class="file-size"><?php echo $fileSizeFormatted; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="document-actions">
                        <a href="<?php echo htmlspecialchars($request['document_path']); ?>" target="_blank" class="btn btn-secondary">
                            <span>👁️</span> View Document
                        </a>
                        <a href="<?php echo htmlspecialchars($request['document_path']); ?>" download="<?php echo htmlspecialchars($fileName); ?>" class="btn btn-outline">
                            <span>⬇️</span> Download
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        
        <?php if (!empty($status_updates)): ?>
            <div class="card">
                <h3>Status Updates</h3>
                <?php foreach ($status_updates as $update): ?>
                    <div class="status-update">
                        <p><?php echo nl2br(htmlspecialchars($update['update_text'])); ?></p>
                        <small><?php echo date('M j, Y g:i A', strtotime($update['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        
        <?php if (Session::isLoggedIn() && (Session::getUserId() == $request['user_id'] || Session::isAdmin())): ?>
            <div class="card">
                <h3>Add Status Update</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_status_update">
                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                    <div class="form-group">
                        <textarea name="update_text" rows="3" placeholder="Share an update with your supporters..." required></textarea>
                    </div>
                    <button type="submit" class="btn">Add Update</button>
                </form>
            </div>
        <?php endif; ?>
        
        
        <?php if (Session::isLoggedIn() && (Session::getUserId() == $request['user_id'] || Session::isAdmin())): ?>
            <div class="card">
                <h3>Update Request Status</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update_request_status">
                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                    <div class="form-group">
                        <select name="status" required>
                            <option value="">Select Status</option>
                            <option value="completed" <?php echo $request['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="closed" <?php echo $request['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Update Status</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <div>
        
        <div class="card">
            <h3>Funding Progress</h3>
            <p><strong>৳<?php echo number_format($request['current_amount'], 2); ?></strong> raised of ৳<?php echo number_format($request['goal_amount'], 2); ?> goal</p>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo min($progress_percentage, 100); ?>%"></div>
            </div>
            <p><?php echo number_format($progress_percentage, 1); ?>% funded</p>
        </div>
        
        
        <?php if (Session::isLoggedIn() && $request['status'] === 'approved'): ?>
            <div class="card">
                <h3>Make a Donation</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="donate">
                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                    <div class="form-group">
                        <label for="amount">Amount (৳)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="1" required>
                    </div>
                    <button type="submit" class="btn">Donate Now</button>
                </form>
            </div>
        <?php elseif (!Session::isLoggedIn()): ?>
            <div class="card">
                <h3>Make a Donation</h3>
                <p>Please <a href="index.php?page=login">login</a> to make a donation.</p>
            </div>
        <?php endif; ?>
        
        
        <?php if (!empty($donations)): ?>
            <div class="card">
                <h3>Recent Donations</h3>
                <?php foreach (array_slice($donations, 0, 10) as $donation): ?>
                    <div style="padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                        <strong><?php echo htmlspecialchars($donation['donor_name']); ?></strong> donated 
                        <strong>৳<?php echo number_format($donation['amount'], 2); ?></strong>
                        <br>
                        <small><?php echo date('M j, Y g:i A', strtotime($donation['created_at'])); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="card" style="margin-top: 2rem;">
    <h3>Comments</h3>
    
    <?php if (Session::isLoggedIn()): ?>
        <form method="POST" style="margin-bottom: 2rem;">
            <input type="hidden" name="action" value="add_comment">
            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
            <div class="form-group">
                <textarea name="comment" rows="3" placeholder="Add a comment..." required></textarea>
            </div>
            <button type="submit" class="btn">Add Comment</button>
        </form>
    <?php endif; ?>
    
    <?php if (empty($comments)): ?>
        <p>No comments yet. Be the first to comment!</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <div class="comment-meta">
                    <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                    <span><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></span>
                    <?php if (Session::isAdmin()): ?>
                        <form method="POST" style="display: inline; float: right;">
                            <input type="hidden" name="action" value="delete_comment">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" class="btn btn-danger" style="padding: 2px 6px; font-size: 0.7rem;" onclick="return confirm('Delete this comment?')">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
                <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.request-main {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.request-header {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.request-header h2 {
    margin: 0 0 1.5rem 0;
    color: var(--text-dark);
    font-size: 2rem;
}

.request-meta {
    margin-top: 1rem;
}

.meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem;
    background: rgba(0, 166, 81, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid rgba(0, 166, 81, 0.1);
}

.meta-label {
    font-weight: 600;
    color: var(--text-light);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.meta-value {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1.1rem;
}

.request-image-container {
    background: var(--card-bg);
    padding: 1rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.request-image {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius);
    max-height: 400px;
    object-fit: cover;
}

.request-description {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.request-description h3 {
    margin: 0 0 1rem 0;
    color: var(--text-dark);
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

.description-content {
    line-height: 1.7;
    color: var(--text-dark);
    font-size: 1.1rem;
}

.document-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.document-preview {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    background: rgba(0, 166, 81, 0.08);
    padding: 0.8rem 1rem;
    border-radius: var(--border-radius);
    border: 1px solid rgba(0, 166, 81, 0.2);
}

.document-icon {
    font-size: 1.8rem;
    color: var(--primary-color);
}

.document-details {
    display: flex;
    flex-direction: column;
}

.document-name {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 200px;
}

.document-meta {
    font-size: 0.8rem;
    color: var(--text-light);
    display: flex;
    gap: 0.5rem;
}

.document-actions {
    display: flex;
    gap: 0.8rem;
    margin-top: 0.8rem;
}

.document-actions .btn {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.document-actions .btn-secondary {
    background-color: var(--secondary-color);
    color: var(--text-light);
    border: 1px solid var(--secondary-color);
}

.document-actions .btn-secondary:hover {
    background-color: var(--secondary-color-hover);
    border-color: var(--secondary-color-hover);
}

.document-actions .btn-outline {
    background-color: transparent;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.document-actions .btn-outline:hover {
    background-color: var(--primary-color-light);
    border-color: var(--primary-color-light);
}

@media (max-width: 768px) {
    .meta-grid {
        grid-template-columns: 1fr;
    }
    
    .request-header {
        padding: 1.5rem;
    }
    
    .request-description {
        padding: 1.5rem;
    }
}
</style> 