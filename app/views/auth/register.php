<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <!-- Replace with your unification image -->
                    <img src="public/images/unification-logo.png" alt="Unification Logo" class="auth-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <i class="fas fa-heart auth-logo-fallback" style="display: none;"></i>
                </div>
                <h1>Join Our Community</h1>
                <p>Create your account to start making a difference</p>
            </div>
            
            <form method="POST" class="auth-form" id="registerForm">
                <input type="hidden" name="action" value="register">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">
                            <i class="fas fa-user"></i>
                            First Name
                        </label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                        <div class="form-error" id="first-name-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">
                            <i class="fas fa-user"></i>
                            Last Name
                        </label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                        <div class="form-error" id="last-name-error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <div class="form-error" id="email-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <span class="strength-text" id="strength-text">Password strength</span>
                    </div>
                    <div class="form-error" id="password-error"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="religion">
                            <i class="fas fa-pray"></i>
                            Religion
                        </label>
                        <select id="religion" name="religion" required>
                            <option value="">Select Religion</option>
                            <option value="islam">Islam</option>
                            <option value="christianity">Christianity</option>
                            <option value="hinduism">Hinduism</option>
                            <option value="buddhism">Buddhism</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="form-error" id="religion-error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_type">
                            <i class="fas fa-user-tag"></i>
                            Account Type
                        </label>
                        <select id="user_type" name="user_type" required>
                            <option value="">Select Account Type</option>
                            <option value="fundraiser">Fundraiser (Create Requests)</option>
                            <option value="donor">Donor (Make Donations)</option>
                        </select>
                        <div class="form-error" id="user-type-error"></div>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="terms-agreement">
                        <input type="checkbox" name="terms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary auth-btn">
                    <span class="btn-text">Create Account</span>
                    <div class="btn-loader" style="display: none;">
                        <div class="spinner"></div>
                    </div>
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="index.php?page=login" class="auth-link">Sign in here</a></p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');
    
    let strength = 0;
    let strengthLabel = '';
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    switch(strength) {
        case 0:
        case 1:
            strengthLabel = 'Very Weak';
            strengthFill.style.width = '20%';
            strengthFill.style.backgroundColor = '#e74c3c';
            break;
        case 2:
            strengthLabel = 'Weak';
            strengthFill.style.width = '40%';
            strengthFill.style.backgroundColor = '#f39c12';
            break;
        case 3:
            strengthLabel = 'Fair';
            strengthFill.style.width = '60%';
            strengthFill.style.backgroundColor = '#f1c40f';
            break;
        case 4:
            strengthLabel = 'Good';
            strengthFill.style.width = '80%';
            strengthFill.style.backgroundColor = '#2ecc71';
            break;
        case 5:
            strengthLabel = 'Strong';
            strengthFill.style.width = '100%';
            strengthFill.style.backgroundColor = '#27ae60';
            break;
    }
    
    strengthText.textContent = strengthLabel;
});

// Simple form validation (client-side only)
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const firstName = document.getElementById('first_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const religion = document.getElementById('religion').value;
    const userType = document.getElementById('user_type').value;
    const terms = document.querySelector('input[name="terms"]').checked;
    
    // Clear previous errors
    document.querySelectorAll('.form-error').forEach(error => error.textContent = '');
    
    // Validation
    let hasErrors = false;
    
    if (!firstName || firstName.length < 2) {
        document.getElementById('first-name-error').textContent = 'First name must be at least 2 characters';
        hasErrors = true;
    }
    
    if (!lastName || lastName.length < 2) {
        document.getElementById('last-name-error').textContent = 'Last name must be at least 2 characters';
        hasErrors = true;
    }
    
    if (!email) {
        document.getElementById('email-error').textContent = 'Email is required';
        hasErrors = true;
    } else if (!isValidEmail(email)) {
        document.getElementById('email-error').textContent = 'Please enter a valid email';
        hasErrors = true;
    }
    
    if (!password) {
        document.getElementById('password-error').textContent = 'Password is required';
        hasErrors = true;
    } else if (password.length < 8) {
        document.getElementById('password-error').textContent = 'Password must be at least 8 characters';
        hasErrors = true;
    }
    
    if (!religion) {
        document.getElementById('religion-error').textContent = 'Please select your religion';
        hasErrors = true;
    }
    
    if (!userType) {
        document.getElementById('user-type-error').textContent = 'Please select an account type';
        hasErrors = true;
    }
    
    if (!terms) {
        alert('Please agree to the Terms of Service and Privacy Policy');
        hasErrors = true;
    }
    
    if (hasErrors) {
        e.preventDefault();
        return false;
    }
    
    // Show loading state
    const btn = document.querySelector('.auth-btn');
    const btnText = btn.querySelector('.btn-text');
    const btnLoader = btn.querySelector('.btn-loader');
    
    btn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'flex';
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
</script> 