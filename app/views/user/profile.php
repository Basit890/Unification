<?php
$userTypeLabels = [
    'admin' => 'Administrator',
    'donor' => 'Donor',
    'fundraiser' => 'Fundraiser'
];
$userType = $userTypeLabels[$user['user_type']] ?? 'User';

$userEmail = $user['email'] ?? 'Not available';
$userUsername = $user['username'] ?? 'Not available';
$profilePicture = $user['profile_picture'] ?? null;
?>

<div class="profile-page">
    <!-- Profile Hero Section -->
    <div class="profile-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="profile-avatar-section">
                    <div class="profile-avatar-container" 
                         onclick="document.getElementById('profile-picture-input').click()"
                         ondrop="handleDrop(event)" 
                         ondragover="handleDragOver(event)" 
                         ondragenter="handleDragEnter(event)" 
                         ondragleave="handleDragLeave(event)"
                         title="Click or drag to upload profile picture">
                        <?php 
                        $profilePicturePath = $profilePicture;
                        if ($profilePicture && !str_starts_with($profilePicture, 'http')) {
                            $profilePicturePath = (str_starts_with($profilePicture, 'app/')) ? $profilePicture : 'app/uploads/' . basename($profilePicture);
                        }
                        ?>
                        <?php if ($profilePicture && file_exists($profilePicturePath)): ?>
                            <img src="<?php echo htmlspecialchars($profilePicturePath); ?>" alt="Profile Picture" class="profile-avatar-img">
                        <?php else: ?>
                            <div class="profile-avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        <div class="avatar-overlay">
                            <button class="avatar-upload-btn" type="button">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <div class="upload-hint">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Upload Photo</span>
                        </div>
                    </div>
                    <input type="file" id="profile-picture-input" accept="image/*" style="display: none;" onchange="uploadProfilePicture(this)">
                </div>
                
                <div class="profile-info">
                    <h1 class="profile-name" id="profile-name-display"><?php echo htmlspecialchars(Session::getFullName()); ?></h1>
                    <p class="profile-email"><?php echo htmlspecialchars($userEmail); ?></p>
                    <div class="profile-badges">
                        <span class="profile-badge profile-type"><?php echo htmlspecialchars($userType); ?></span>
                        <span class="profile-badge profile-status">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <div class="container">
            <div class="profile-sections">
                
                <!-- Account Information - Modern Card Layout -->
                <div class="account-info-modern" data-aos="fade-up">
                    <div class="account-header">
                        <div class="account-title">
                            <i class="fas fa-user-circle"></i>
                            <h3>Account Information</h3>
                        </div>
                        <button class="edit-btn-modern" onclick="toggleEditMode()">
                            <i class="fas fa-edit"></i>
                            <span>Edit Profile</span>
                        </button>
                    </div>
                    
                    <div class="account-grid">
                        <!-- Personal Information Card -->
                        <div class="info-card personal-info">
                            <div class="card-header">
                                <i class="fas fa-user"></i>
                                <h4>Personal Information</h4>
                            </div>
                            <div class="card-content">
                                <div class="info-field">
                                    <div class="field-icon">
                                        <i class="fas fa-signature"></i>
                                    </div>
                                    <div class="field-content">
                                        <label class="field-label">First Name</label>
                                        <div class="field-value-container">
                                            <span class="field-value" id="first-name-display"><?php echo htmlspecialchars($user['first_name'] ?? ''); ?></span>
                                            <input type="text" class="field-input" id="first-name-input" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-field">
                                    <div class="field-icon">
                                        <i class="fas fa-signature"></i>
                                    </div>
                                    <div class="field-content">
                                        <label class="field-label">Last Name</label>
                                        <div class="field-value-container">
                                            <span class="field-value" id="last-name-display"><?php echo htmlspecialchars($user['last_name'] ?? ''); ?></span>
                                            <input type="text" class="field-input" id="last-name-input" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-field">
                                    <div class="field-icon">
                                        <i class="fas fa-at"></i>
                                    </div>
                                    <div class="field-content">
                                        <label class="field-label">Username</label>
                                        <div class="field-value-container">
                                            <span class="field-value" id="username-display"><?php echo htmlspecialchars($userUsername); ?></span>
                                            <input type="text" class="field-input" id="username-input" value="<?php echo htmlspecialchars($userUsername); ?>" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-field">
                                    <div class="field-icon">
                                        <i class="fas fa-pray"></i>
                                    </div>
                                    <div class="field-content">
                                        <label class="field-label">Religion</label>
                                        <div class="field-value-container">
                                            <span class="field-value" id="religion-display"><?php echo ucfirst(htmlspecialchars($user['religion'] ?? 'Not specified')); ?></span>
                                            <select class="field-input" id="religion-input" style="display: none;">
                                                <option value="islam" <?php echo (strtolower($user['religion'] ?? '') === 'islam') ? 'selected' : ''; ?>>Islam</option>
                                                <option value="christianity" <?php echo (strtolower($user['religion'] ?? '') === 'christianity') ? 'selected' : ''; ?>>Christianity</option>
                                                <option value="hinduism" <?php echo (strtolower($user['religion'] ?? '') === 'hinduism') ? 'selected' : ''; ?>>Hinduism</option>
                                                <option value="buddhism" <?php echo (strtolower($user['religion'] ?? '') === 'buddhism') ? 'selected' : ''; ?>>Buddhism</option>
                                                <option value="other" <?php echo (strtolower($user['religion'] ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Details Card -->
                        <div class="info-card account-details">
                            <div class="card-header">
                                <i class="fas fa-id-card"></i>
                                <h4>Account Details</h4>
                            </div>
                            <div class="card-content">
                                <div class="info-field">
                                    <div class="field-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="field-content">
                                        <label class="field-label">Email Address</label>
                                        <div class="field-value-container">
                                            <span class="field-value email-value"><?php echo htmlspecialchars($userEmail); ?></span>
                                            <span class="field-note">Email cannot be changed</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-field">
                                    <div class="field-icon">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                    <div class="field-content">
                                        <label class="field-label">Account Type</label>
                                        <div class="field-value-container">
                                            <span class="field-value account-type-value"><?php echo htmlspecialchars($userType); ?></span>
                                            <span class="field-note">Account type cannot be changed</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-field">
                                    <div class="field-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="field-content">
                                        <label class="field-label">Account Status</label>
                                        <div class="field-value-container">
                                            <span class="field-value status-badge active">
                                                <i class="fas fa-check-circle"></i>
                                                Active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="profile-actions" data-aos="fade-up" data-aos-delay="300">
                    <button class="btn btn-primary" id="save-changes-btn" onclick="saveProfileChanges()" style="display: none;">
                        <i class="fas fa-save"></i>
                        <span>Save Changes</span>
                    </button>
                    <button class="btn btn-secondary" id="cancel-changes-btn" onclick="cancelEditMode()" style="display: none;">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </button>
                    <?php if ($profilePicture): ?>
                        <button class="btn btn-danger" onclick="deleteProfilePicture()">
                            <i class="fas fa-trash"></i>
                            <span>Delete Picture</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p>Updating profile...</p>
    </div>
</div>

<script>
let editMode = false;
let originalData = {};

function toggleEditMode() {
    if (editMode) return;
    
    editMode = true;
    originalData = {
        firstName: document.getElementById('first-name-input').value,
        lastName: document.getElementById('last-name-input').value,
        username: document.getElementById('username-input').value,
        religion: document.getElementById('religion-input').value
    };
    
    // Show input fields
    document.querySelectorAll('.field-input').forEach(input => {
        input.style.display = 'block';
    });
    
    // Hide display values
    document.querySelectorAll('.field-value').forEach(value => {
        value.style.display = 'none';
    });
    
    // Show action buttons
    document.getElementById('save-changes-btn').style.display = 'inline-flex';
    document.getElementById('cancel-changes-btn').style.display = 'inline-flex';
    
    // Hide edit buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.style.display = 'none';
    });
}

function cancelEditMode() {
    editMode = false;
    
    // Restore original values
    document.getElementById('first-name-input').value = originalData.firstName;
    document.getElementById('last-name-input').value = originalData.lastName;
    document.getElementById('username-input').value = originalData.username;
    document.getElementById('religion-input').value = originalData.religion;
    
    // Show display values
    document.querySelectorAll('.field-value').forEach(value => {
        value.style.display = 'block';
    });
    
    // Hide input fields
    document.querySelectorAll('.field-input').forEach(input => {
        input.style.display = 'none';
    });
    
    // Hide action buttons
    document.getElementById('save-changes-btn').style.display = 'none';
    document.getElementById('cancel-changes-btn').style.display = 'none';
    
    // Show edit buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.style.display = 'inline-flex';
    });
}

function saveProfileChanges() {
    const firstName = document.getElementById('first-name-input').value.trim();
    const lastName = document.getElementById('last-name-input').value.trim();
    const username = document.getElementById('username-input').value.trim();
    const religion = document.getElementById('religion-input').value;
    
    // Validation
    if (!firstName || firstName.length < 2) {
        alert('First name must be at least 2 characters long');
        return;
    }
    
    if (!lastName || lastName.length < 2) {
        alert('Last name must be at least 2 characters long');
        return;
    }
    
    if (!username || username.length < 3) {
        alert('Username must be at least 3 characters long');
        return;
    }
    
    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        alert('Username can only contain letters, numbers, and underscores');
        return;
    }
    
    // Show loading
    showLoading();
    
    const formData = new FormData();
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('username', username);
    formData.append('religion', religion);
    
    fetch('index.php?page=profile&action=update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            // Update display values
            document.getElementById('first-name-display').textContent = firstName;
            document.getElementById('last-name-display').textContent = lastName;
            document.getElementById('username-display').textContent = username;
            document.getElementById('religion-display').textContent = religion.charAt(0).toUpperCase() + religion.slice(1);
            document.getElementById('profile-name-display').textContent = firstName + ' ' + lastName;
            
            // Exit edit mode
            cancelEditMode();
            
            // Show success message
            showNotification('Profile updated successfully!', 'success');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        alert('An error occurred while updating profile');
    });
}

function uploadProfilePicture(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        processFileUpload(file);
    }
}

function processFileUpload(file) {
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showNotification('Please select an image file', 'error');
        return;
    }
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('Image size must be less than 5MB', 'error');
        return;
    }
    
    showLoading();
    
    const formData = new FormData();
    formData.append('profile_picture', file);
    
    fetch('index.php?page=profile&action=update', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            // Update profile picture
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarContainer = document.querySelector('.profile-avatar-container');
                avatarContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Profile Picture" class="profile-avatar-img">
                    <div class="avatar-overlay">
                        <button class="avatar-upload-btn" type="button">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <div class="upload-hint">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Upload Photo</span>
                    </div>
                `;
                // Re-attach event listeners
                attachAvatarEventListeners();
            };
            reader.readAsDataURL(file);
            
            showNotification('Profile picture updated successfully!', 'success');
        } else {
            console.error('Upload failed:', data.message); // Debug log
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('An error occurred while uploading profile picture', 'error');
    });
}

// Drag and drop functions
function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function handleDragEnter(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        processFileUpload(files[0]);
    }
}

function attachAvatarEventListeners() {
    const avatarContainer = document.querySelector('.profile-avatar-container');
    if (avatarContainer) {
        avatarContainer.onclick = () => document.getElementById('profile-picture-input').click();
        avatarContainer.ondrop = handleDrop;
        avatarContainer.ondragover = handleDragOver;
        avatarContainer.ondragenter = handleDragEnter;
        avatarContainer.ondragleave = handleDragLeave;
    }
}

function deleteProfilePicture() {
    if (!confirm('Are you sure you want to delete your profile picture?')) {
        return;
    }
    
    showLoading();
    
    const formData = new FormData();
    
    fetch('index.php?page=profile&action=delete_picture', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            // Reset to placeholder
            const avatarContainer = document.querySelector('.profile-avatar-container');
            avatarContainer.innerHTML = `
                <div class="profile-avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
                <div class="avatar-overlay">
                    <button class="avatar-upload-btn" onclick="document.getElementById('profile-picture-input').click()">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
            `;
            
            showNotification('Profile picture deleted successfully!', 'success');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        alert('An error occurred while deleting profile picture');
    });
}

function showLoading() {
    document.getElementById('loading-overlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loading-overlay').style.display = 'none';
}

function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Remove notification after 4 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Initialize AOS and event listeners
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    }
    
    // Attach avatar event listeners
    attachAvatarEventListeners();
});
</script>