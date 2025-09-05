<div class="notifications-container notifications-<?php echo $user['user_type']; ?>">
    <div class="notifications-header">
        <div class="header-content">
            <div class="header-icon">
                <div class="notification-icon-wrapper">
                    <i class="fas fa-bell"></i>
                </div>
            </div>
            <div class="header-text">
                <h1><?php echo ucfirst($user['user_type']); ?> Notifications</h1>
                <p class="header-subtitle">
                    <?php if ($user['user_type'] === 'admin'): ?>
                        Monitor platform activities, manage requests, and oversee user interactions
                    <?php elseif ($user['user_type'] === 'fundraiser'): ?>
                        Track your help requests, donor interactions, and request status updates
                    <?php else: ?>
                        Stay updated with your donation activities and platform interactions
                    <?php endif; ?>
                </p>
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
            <div class="empty-icon-wrapper">
                <i class="fas fa-bell-slash"></i>
            </div>
            <div class="empty-content">
                <h3>All Caught Up!</h3>
                <p>
                    <?php if ($user['user_type'] === 'admin'): ?>
                        You have no new notifications at the moment. New updates will appear here when there are pending requests, user activities, or platform events that need your attention.
                    <?php elseif ($user['user_type'] === 'fundraiser'): ?>
                        You have no new notifications at the moment. New updates will appear here when you receive comments, donations, or status updates on your help requests.
                    <?php else: ?>
                        You have no new notifications at the moment. New updates will appear here when you receive updates about your donations or other platform activities.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="notifications-content">
            
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

            
            <?php if (!empty($groupedNotifications['comments'])): ?>
                <div class="notification-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h2 class="section-title">
                            <?php if ($user['user_type'] === 'admin'): ?>
                                Platform Comments
                            <?php elseif ($user['user_type'] === 'fundraiser'): ?>
                                Comments on Your Requests
                            <?php else: ?>
                                Comments on Your Donations
                            <?php endif; ?>
                        </h2>
                        <span class="section-count"><?php echo count($groupedNotifications['comments']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['comments'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if (!empty($groupedNotifications['donations'])): ?>
                <div class="notification-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h2 class="section-title">
                            <?php if ($user['user_type'] === 'admin'): ?>
                                Platform Donations
                            <?php elseif ($user['user_type'] === 'fundraiser'): ?>
                                Donations to Your Requests
                            <?php else: ?>
                                Your Donations
                            <?php endif; ?>
                        </h2>
                        <span class="section-count"><?php echo count($groupedNotifications['donations']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['donations'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if (!empty($groupedNotifications['updates'])): ?>
                <div class="notification-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h2 class="section-title">
                            <?php if ($user['user_type'] === 'admin'): ?>
                                Post Updates
                            <?php elseif ($user['user_type'] === 'fundraiser'): ?>
                                Your Post Updates
                            <?php else: ?>
                                Updates on Your Donated Posts
                            <?php endif; ?>
                        </h2>
                        <span class="section-count"><?php echo count($groupedNotifications['updates']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['updates'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($groupedNotifications['approvals'])): ?>
                <div class="notification-section <?php echo $user['user_type'] === 'fundraiser' ? 'fundraiser-section' : ''; ?>">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h2 class="section-title">
                            <?php if ($user['user_type'] === 'admin'): ?>
                                Request Status Updates
                            <?php elseif ($user['user_type'] === 'fundraiser'): ?>
                                Your Request Updates
                            <?php else: ?>
                                Request Updates
                            <?php endif; ?>
                        </h2>
                        <span class="section-count"><?php echo count($groupedNotifications['approvals']); ?></span>
                    </div>
                    <div class="notification-list">
                        <?php foreach ($groupedNotifications['approvals'] as $notification): ?>
                            <?php include 'notification_item.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($groupedNotifications['admin']) && $user['user_type'] === 'admin'): ?>
                <div class="notification-section admin-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h2 class="section-title">Admin Alerts</h2>
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
            'Content-Type': 'application/x/www-form-urlencoded',
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

function markAllNotificationsAsRead() {
    fetch('index.php?page=notifications&action=mark_all_read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x/www-form-urlencoded',
        }
    })
    .then(response => {
        if (response.ok) {
            // Reload the page to show updated notifications
            window.location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
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
}

function updateUnreadCount(count) {
    const statusText = document.querySelector('.status-text');
    if (statusText) {
        if (count === 0) {
            statusText.textContent = 'All caught up!';
            document.querySelector('.unread-status').className = 'unread-status all-caught-up';
        } else {
            statusText.textContent = count + ' unread';
            document.querySelector('.unread-status').className = 'unread-status has-unread';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for Mark All as Read button
    const markAllReadBtn = document.querySelector('[data-action="mark-all-read"]');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', markAllNotificationsAsRead);
    }
    
    // Add event listeners for individual notifications
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
