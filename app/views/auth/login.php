<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <!-- Replace with your unification image -->
                    <img src="public/images/unification-logo.png" alt="Unification Logo" class="auth-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <i class="fas fa-heart auth-logo-fallback" style="display: none;"></i>
                </div>
                <h1>Welcome Back</h1>
                <p>Sign in to your account to continue</p>
            </div>
            
            <form method="POST" class="auth-form" id="loginForm">
                <input type="hidden" name="action" value="login">
                
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
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    <div class="form-error" id="password-error"></div>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary auth-btn">
                    <span class="btn-text">Sign In</span>
                    <div class="btn-loader" style="display: none;">
                        <div class="spinner"></div>
                    </div>
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="index.php?page=register" class="auth-link">Create one here</a></p>
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

// Simple form validation (client-side only)
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    // Clear previous errors
    document.querySelectorAll('.form-error').forEach(error => error.textContent = '');
    
    // Basic validation
    let hasErrors = false;
    
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
    } else if (password.length < 6) {
        document.getElementById('password-error').textContent = 'Password must be at least 6 characters';
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