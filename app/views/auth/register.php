<h2>Register</h2>
<form method="POST">
    <input type="hidden" name="action" value="register">
    <div class="form-group">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" required>
    </div>
    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="religion">Religion</label>
        <select id="religion" name="religion" required>
            <option value="">Select Religion</option>
            <option value="islam">Islam</option>
            <option value="hindu">Hindu</option>
            <option value="christian">Christian</option>
            <option value="buddhist">Buddhist</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div class="form-group">
        <label for="user_type">Account Type</label>
        <select id="user_type" name="user_type" required>
            <option value="">Select Account Type</option>
            <option value="fundraiser">Fundraiser (Create Requests)</option>
            <option value="donor">Donor (Make Donations)</option>
            <option value="admin">Admin (Manage Platform)</option>
        </select>
    </div>
    <button type="submit" class="btn">Register</button>
</form>
<p style="margin-top: 1rem;">
    Already have an account? <a href="index.php?page=login">Login here</a>
</p> 