<div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?> notification-<?php echo $notification['type']; ?>" 
     data-notification-id="<?php echo $notification['id']; ?>" data-aos="fade-up">
    
    <div class="notification-card">
        <div class="notification-icon">
            <?php
            $iconClass = 'fas fa-bell';
            $iconColor = 'var(--primary-color)';
            
            switch ($notification['type']) {
                case 'comment':
                    $iconClass = 'fas fa-comment';
                    $iconColor = '#3498db';
                    break;
                case 'donation':
                    $iconClass = 'fas fa-heart';
                    $iconColor = '#e74c3c';
                    break;
                case 'approval':
                    $iconClass = 'fas fa-check-circle';
                    $iconColor = '#27ae60';
                    break;
                case 'rejection':
                    $iconClass = 'fas fa-times-circle';
                    $iconColor = '#e74c3c';
                    break;
                case 'update':
                    $iconClass = 'fas fa-edit';
                    $iconColor = '#9b59b6';
                    break;
                case 'admin_request':
                    $iconClass = 'fas fa-shield-alt';
                    $iconColor = '#f39c12';
                    break;
            }
            ?>
            <i class="<?php echo $iconClass; ?>" style="color: <?php echo $iconColor; ?>;"></i>
        </div>
        
        <div class="notification-content">
            <div class="notification-header">
                <h4 class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></h4>
                <div class="notification-meta">
                    <span class="notification-time">
                        <?php echo timeAgo($notification['created_at']); ?>
                    </span>
                    <button class="delete-btn" onclick="console.log('Delete button clicked for notification:', <?php echo $notification['id']; ?>); event.stopPropagation(); console.log('About to call showDeleteModal'); showDeleteModal(<?php echo $notification['id']; ?>, this.closest('.notification-item')); console.log('showDeleteModal call completed');" title="Delete notification">
                        <span class="trash-emoji">🗑️</span>
                        <span class="delete-text">Delete</span>
                    </button>
                </div>
            </div>
            
            <p class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></p>
            
            <?php if ($notification['related_id'] && $notification['related_type']): ?>
                <div class="notification-actions">
                <?php 
                // Use the pre-processed redirect URL from the controller
                $redirectUrl = $notification['redirect_url'] ?? 'index.php?page=home';
                ?>
                
                <?php if ($notification['related_type'] === 'comment'): ?>
                    <a href="<?php echo htmlspecialchars($redirectUrl); ?>" 
                       class="btn btn-sm btn-primary">View Comment</a>
                <?php elseif ($notification['related_type'] === 'donation'): ?>
                    <a href="<?php echo htmlspecialchars($redirectUrl); ?>" 
                       class="btn btn-sm btn-primary">View Request</a>
                <?php elseif ($notification['related_type'] === 'request'): ?>
                    <a href="<?php echo htmlspecialchars($redirectUrl); ?>" 
                       class="btn btn-sm btn-primary">View Request</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div>
    </div>
    
    <?php if (!$notification['is_read']): ?>
        <div class="unread-indicator"></div>
    <?php endif; ?>
</div>