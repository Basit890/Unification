<h2>Login</h2>
<form method="POST">
    <input type="hidden" name="action" value="login">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn">Login</button>
</form>
<p style="margin-top: 1rem;">
    Don't have an account? <a href="index.php?page=register">Register here</a>
</p> 