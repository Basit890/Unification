<?php
$categories = $helpRequestController->getCategories();
?>

<h2>Create Help Request</h2>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <?php unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="create_request">
    <div class="form-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required>
    </div>
    <div class="form-group">
        <label for="description">Description (Max 1000 characters)</label>
        <textarea id="description" name="description" rows="5" maxlength="1000" required></textarea>
        <div class="char-counter">
            <span id="char-count">0</span> / 1000 characters
        </div>
    </div>
    <div class="form-group">
        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $key => $name): ?>
                <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="goal_amount">Goal Amount (৳)</label>
        <input type="number" id="goal_amount" name="goal_amount" step="0.01" min="1" required>
    </div>
    <div class="form-group">
        <label for="image">Image (Optional)</label>
        <input type="file" id="image" name="image" accept="image/*">
        <small style="color: var(--text-light);">Maximum file size: 5MB. Supported formats: JPG, PNG, GIF</small>
    </div>
    <div class="form-group">
        <label for="document">Document (Optional)</label>
        <input type="file" id="document" name="document" accept=".pdf,.doc,.docx">
        <small style="color: var(--text-light);">Maximum file size: 5MB. Supported formats: PDF, DOC, DOCX</small>
    </div>
    <button type="submit" class="btn">Submit Request</button>
</form>

<style>
.char-counter {
    text-align: right;
    font-size: 0.9rem;
    color: var(--text-light);
    margin-top: 0.5rem;
}

.char-counter.warning {
    color: #ff6b6b;
}

.char-counter.danger {
    color: #ee5a24;
    font-weight: bold;
}
</style>

<script>
document.getElementById('description').addEventListener('input', function() {
    const maxLength = 1000;
    const currentLength = this.value.length;
    const charCount = document.getElementById('char-count');
    const charCounter = document.querySelector('.char-counter');
    
    charCount.textContent = currentLength;
    
    charCounter.classList.remove('warning', 'danger');
    if (currentLength > maxLength * 0.9) {
        charCounter.classList.add('danger');
    } else if (currentLength > maxLength * 0.8) {
        charCounter.classList.add('warning');
    }
});
</script> 