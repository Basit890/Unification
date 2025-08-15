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
                    <tr class="user-row" data-type="<?php echo $user['user_type']; ?>" data-religion="<?php echo $user['religion']; ?>" data-search="<?php echo htmlspecialchars(strtolower($user['first_name'] . ' ' . $user['last_name'] . ' ' . $user['email'])); ?>">
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
                                        <button type="submit" class="btn btn-success" title="Make Admin" onclick="return showConfirmModal('make_admin', <?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">
                                            👑
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($user['user_type'] === 'admin' && $user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="remove_admin">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger" title="Remove Admin" onclick="return showConfirmModal('remove_admin', <?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">
                                            🚫
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger" title="Delete User" onclick="return showConfirmModal('delete_user', <?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">
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

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Confirm Action</h3>
            <button class="modal-close" onclick="closeConfirmModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="modalMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeConfirmModal()">Cancel</button>
            <button id="modalConfirmBtn" class="btn btn-danger">Confirm</button>
        </div>
    </div>
</div>

<script>
function filterUsers() {
    const userTypeFilter = document.getElementById('userTypeFilter').value;
    const religionFilter = document.getElementById('religionFilter').value;
    const searchFilter = document.getElementById('userSearchFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('.user-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const userType = row.dataset.type;
        const religion = row.dataset.religion;
        const searchText = row.dataset.search;
        
        const matchesType = !userTypeFilter || userType === userTypeFilter;
        const matchesReligion = !religionFilter || religion === religionFilter;
        const matchesSearch = !searchFilter || searchText.includes(searchFilter);
        
        if (matchesType && matchesReligion && matchesSearch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update visible count if you have a counter element
    const counterElement = document.querySelector('.user-count');
    if (counterElement) {
        counterElement.textContent = visibleCount;
    }
}

function showConfirmModal(action, userId, userName) {
    const modal = document.getElementById('confirmModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    
    let title, message, btnClass, btnText;
    
    switch(action) {
        case 'make_admin':
            title = 'Make User Admin';
            message = `Are you sure you want to make <strong>${userName}</strong> an admin? This will give them full access to the admin panel.`;
            btnClass = 'btn-success';
            btnText = 'Make Admin';
            break;
        case 'remove_admin':
            title = 'Remove Admin Privileges';
            message = `Are you sure you want to remove admin privileges from <strong>${userName}</strong>? They will lose access to the admin panel.`;
            btnClass = 'btn-warning';
            btnText = 'Remove Admin';
            break;
        case 'delete_user':
            title = 'Delete User';
            message = `Are you sure you want to delete <strong>${userName}</strong>? This action cannot be undone and will remove all their data.`;
            btnClass = 'btn-danger';
            btnText = 'Delete User';
            break;
    }
    
    modalTitle.textContent = title;
    modalMessage.innerHTML = message;
    confirmBtn.className = `btn ${btnClass}`;
    confirmBtn.textContent = btnText;
    
    // Position modal near the clicked button
    const clickedButton = event.target;
    const buttonRect = clickedButton.getBoundingClientRect();
    const modalRect = modal.querySelector('.modal-content').getBoundingClientRect();
    
    // Calculate position to show modal near the button
    let left = buttonRect.left + (buttonRect.width / 2) - (modalRect.width / 2);
    let top = buttonRect.bottom + 10; // 10px below the button
    
    // Ensure modal stays within viewport
    if (left < 20) left = 20;
    if (left + modalRect.width > window.innerWidth - 20) {
        left = window.innerWidth - modalRect.width - 20;
    }
    if (top + modalRect.height > window.innerHeight - 20) {
        top = buttonRect.top - modalRect.height - 10; // Show above button if not enough space below
    }
    
    modal.style.position = 'fixed';
    modal.style.top = top + 'px';
    modal.style.left = left + 'px';
    modal.style.transform = 'none';
    modal.style.display = 'block';
    
    // Set up the confirm button action
    confirmBtn.onclick = function() {
        // Find and submit the corresponding form
        const form = document.querySelector(`form[data-action="${action}"][data-user-id="${userId}"]`);
        if (form) {
            form.submit();
        } else {
            // Fallback: find form by action and user_id
            const forms = document.querySelectorAll('form');
            forms.forEach(f => {
                const actionInput = f.querySelector('input[name="action"]');
                const userIdInput = f.querySelector('input[name="user_id"]');
                if (actionInput && userIdInput && 
                    actionInput.value === action && 
                    userIdInput.value == userId) {
                    f.submit();
                }
            });
        }
        closeConfirmModal();
    };
    
    return false; // Prevent form submission
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConfirmModal();
    }
});
</script>

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
    color: var(--accent-color);
    margin-bottom: 0.5rem;
}

.stat-item .stat-label {
    color: var(--text-light);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

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

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    backdrop-filter: blur(5px);
}

.modal-content {
    background: var(--card-bg);
    border-radius: 16px;
    box-shadow: var(--shadow-hover);
    border: 1px solid var(--border-color);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    animation: modalSlideIn 0.3s ease-out;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
    margin: 0;
    color: var(--text-dark);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-light);
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: var(--hover-bg);
    color: var(--text-dark);
}

.modal-body {
    padding: 1.5rem;
}

.modal-body p {
    margin: 0;
    color: var(--text-dark);
    line-height: 1.6;
}

.modal-footer {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    padding: 1rem 1.5rem 1.5rem;
    border-top: 1px solid var(--border-color);
}

/* Responsive design */
@media (max-width: 768px) {
    .filter-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-controls select,
    .filter-controls input {
        min-width: auto;
        width: 100%;
    }
    
    .modal-content {
        width: 95%;
        margin: 1rem;
    }
    
    .modal-footer {
        flex-direction: column;
    }
}
</style>
        
