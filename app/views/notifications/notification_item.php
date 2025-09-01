<div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" 
     data-notification-id="<?php echo $notification['id']; ?>">
    
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
            <span class="notification-time">
                <?php echo timeAgo($notification['created_at']); ?>
            </span>
        </div>
        
        <p class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></p>
        
        <?php if ($notification['related_id'] && $notification['related_type']): ?>
            <div class="notification-actions">
                <?php if ($notification['related_type'] === 'comment'): ?>
                    <a href="index.php?page=view_request&id=<?php echo $notification['related_id']; ?>" 
                       class="btn btn-sm btn-primary">View Comment</a>
                <?php elseif ($notification['related_type'] === 'donation'): ?>
                    <a href="index.php?page=view_request&id=<?php echo $notification['related_id']; ?>" 
                       class="btn btn-sm btn-primary">View Request</a>
                <?php elseif ($notification['related_type'] === 'request'): ?>
                    <a href="index.php?page=view_request&id=<?php echo $notification['related_id']; ?>" 
                       class="btn btn-sm btn-primary">View Request</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="notification-actions-menu">
        <button class="action-btn" onclick="event.stopPropagation(); deleteNotification(<?php echo $notification['id']; ?>)">
            <i class="fas fa-trash"></i>
        </button>
        
        <?php if (!$notification['is_read']): ?>
            <button class="action-btn" onclick="event.stopPropagation(); markNotificationAsRead(<?php echo $notification['id']; ?>, this.parentElement.parentElement)">
                <i class="fas fa-check"></i>
            </button>
        <?php endif; ?>
    </div>
    
    <?php if (!$notification['is_read']): ?>
        <div class="unread-indicator"></div>
    <?php endif; ?>
</div>

<?php
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $time);
    }
}
?>
