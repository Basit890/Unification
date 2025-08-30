<div class="notifications-container">
    <div class="notifications-header">
        <div class="header-content">
            <div class="header-icon">
                <div class="notification-icon-wrapper">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
            <div class="header-text">
                <h1>Notifications</h1>
                <p class="header-subtitle">Stay updated with your latest activities and updates</p>
            </div>
        </div>
        
        <div class="header-actions">
            <button class="btn btn-secondary btn-modern" data-action="mark-all-read">
                <i class="fas fa-check-double"></i>
                <span>Mark All as Read</span>
            </button>
            <div class="unread-status <?php echo $unreadCount > 0 ? 'has-unread' : 'all-caught-up' ?>">
                <span class="status-icon">
                    <?php if ($unreadCount > 0): ?>
                        <i class="fas fa-circle"></i>
                    <?php else: ?>
                        <i class="fas fa-check-circle"></i>
                    <?php endif; ?>
                </span>
                <span class="status-text">
                    <?php echo $unreadCount > 0 ? $unreadCount . ' unread' : 'All caught up!' ?>
                </span>
            </div>
        </div>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="empty-notifications">
            <div class="empty-icon">
                <div class="empty-icon-wrapper">
                    <i class="fas fa-bell-slash"></i>
                </div>
            </div>
            <div class="empty-content">
                <h3>All Caught Up!</h3>
                <p>You have no new notifications at the moment. New updates will appear here when you receive comments, donations, or other important updates.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="notifications-content">
            <!-- Recent Notifications -->
            <?php if (!empty($groupedNotifications['recent'])): ?>
                <div class="notification-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h2 class="section-title">Recent</h2>
                        <span class="section-count"><?php echo count($groupedNotifications['recent']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['recent'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Comments Notifications -->
            <?php if (!empty($groupedNotifications['comments'])): ?>
                <div class="notification-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h2 class="section-title">Comments</h2>
                        <span class="section-count"><?php echo count($groupedNotifications['comments']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['comments'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Donations Notifications -->
            <?php if (!empty($groupedNotifications['donations'])): ?>
                <div class="notification-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h2 class="section-title">Donations</h2>
                        <span class="section-count"><?php echo count($groupedNotifications['donations']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['donations'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Approval/Rejection Notifications -->
            <?php if (!empty($groupedNotifications['approvals'])): ?>
                <div class="notification-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h2 class="section-title">Request Updates</h2>
                        <span class="section-count"><?php echo count($groupedNotifications['approvals']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['approvals'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Admin Notifications -->
            <?php if (!empty($groupedNotifications['admin']) && $user['user_type'] === 'admin'): ?>
                <div class="notification-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h2 class="section-title">Admin Notifications</h2>
                        <span class="section-count"><?php echo count($groupedNotifications['admin']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['admin'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function markNotificationAsRead(notificationId, element) {
    fetch('index.php?page=notifications&action=mark_read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            element.classList.add('read');
            updateUnreadCount(data.unread_count);
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteNotification(notificationId) {
    showConfirmationModal(
        'Are you sure you want to delete this notification?',
        () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'index.php?page=notifications&action=delete';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'notification_id';
            input.value = notificationId;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    );
}

function updateUnreadCount(count) {
    const badge = document.querySelector('.unread-badge');
    if (badge) {
        badge.textContent = count + ' unread';
        if (count === 0) {
            badge.classList.remove('has-unread');
        }
    }
}

// Auto-mark as read when notification is clicked
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            const isUnread = this.classList.contains('unread');
            
            if (isUnread) {
                markNotificationAsRead(notificationId, this);
            }
        });
    });
});
</script>
