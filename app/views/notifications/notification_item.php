<div class="notifications-page">
    <div class="notifications-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </div>
                <h1 class="hero-title">
                    <span class="title-line"><?php echo ucfirst($user['user_type']); ?></span>
                    <span class="title-line highlight">Updates</span>
                </h1>
                <p class="hero-subtitle">
                    Stay updated with your platform activities
                </p>
            </div>
        </div>
    </div>
    
    <div class="notifications-container">
        
        <?php if (empty($notifications)): ?>
            <div class="empty-notifications">
                <div class="empty-content">
                    <div class="empty-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h3>All Caught Up!</h3>
                    <p>You have no new notifications at the moment.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="notifications-content">
                <div class="notification-list">
                    <?php foreach ($notifications as $notification): ?>
                        <?php include 'notification_item_template.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Delete Notification</h3>
            <button class="modal-close" onclick="hideDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this notification? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="hideDeleteModal()">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<script>


let currentDeleteId = null;
let currentDeleteElement = null;

function showDeleteModal(notificationId, element) {
    console.log('=== DELETE MODAL DEBUG START ===');
    console.log('showDeleteModal called with notificationId:', notificationId);
    console.log('Element passed:', element);
    
    currentDeleteId = notificationId;
    currentDeleteElement = element;
    
    const modal = document.getElementById('deleteModal');
    console.log('Modal element found:', modal);
    
    if (!modal) {
        console.error('CRITICAL: Delete modal element not found in DOM!');
        console.log('Available elements with "modal" in ID:', document.querySelectorAll('[id*="modal"]'));
        return;
    }
    
    console.log('Modal found, setting display properties...');
    modal.style.display = 'flex';
    modal.style.visibility = 'visible';
    modal.style.opacity = '1';
    modal.style.zIndex = '9999';
    modal.style.backgroundColor = 'rgba(255, 0, 0, 0.8)';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    
    document.body.style.overflow = 'hidden';
    
    console.log('Modal display set to:', modal.style.display);
    console.log('Modal visibility set to:', modal.style.visibility);
    console.log('Modal opacity set to:', modal.style.opacity);
    console.log('Modal z-index set to:', modal.style.zIndex);
    
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        console.log('Modal content found:', modalContent);
        modalContent.style.position = 'relative';
        modalContent.style.left = 'auto';
        modalContent.style.top = 'auto';
        modalContent.style.margin = 'auto';
        modalContent.style.transform = 'scale(1) translateY(0)';
        modalContent.style.backgroundColor = 'white';
        modalContent.style.padding = '20px';
        modalContent.style.borderRadius = '10px';
    } else {
        console.error('Modal content not found!');
    }
    
    console.log('=== DELETE MODAL DEBUG END ===');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentDeleteId = null;
    currentDeleteElement = null;
}


function deleteNotification(notificationId) {
    console.log('Deleting notification:', notificationId);
    if (notificationId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?page=notifications&action=delete';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'notification_id';
        input.value = notificationId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        console.log('Submitting delete form for notification:', notificationId);
        form.submit();
    } else {
        console.error('No notification ID provided for deletion');
    }
}


document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up notification event listeners');
    
    // Test if showDeleteModal function exists
    console.log('showDeleteModal function exists:', typeof showDeleteModal);
    if (typeof showDeleteModal === 'function') {
        console.log('showDeleteModal is a function');
    } else {
        console.error('showDeleteModal is not a function!');
    }
    
    // Test delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    console.log('Found delete buttons:', deleteButtons.length);
    deleteButtons.forEach((btn, index) => {
        console.log(`Delete button ${index}:`, btn);
    });
    
    // Test modal element
    const modal = document.getElementById('deleteModal');
    console.log('Modal element in DOM:', modal);
    if (modal) {
        console.log('Modal is in DOM and ready');
    } else {
        console.error('Modal element not found in DOM!');
    }
    
    // Add event listener for confirm delete button
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            console.log('Confirm delete button clicked, currentDeleteId:', currentDeleteId);
            if (currentDeleteId) {
                deleteNotification(currentDeleteId);
                hideDeleteModal();
            } else {
                console.error('No notification ID to delete');
            }
        });
    } else {
        console.error('Confirm delete button not found');
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideDeleteModal();
            }
        });
    }
    
    // No additional event listeners needed
});
</script>

<?php
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    // Fix for timezone issues
    if ($diff < 0) {
        return 'Just now';
    }
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . 'm ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . 'h ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . 'd ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . 'w ago';
    } else {
        return date('M j', $time);
    }
}
?>