<?php
$success_stories = $helpRequestController->getSuccessStories();
?>

<h2>Success Stories</h2>
<p>These are the amazing stories of people who reached their goals with the help of our community.</p>

<?php if (empty($success_stories)): ?>
    <div class="empty-state">
        <div class="empty-icon">📈</div>
        <h3>No Success Stories Yet</h3>
        <p>Be the first to complete a campaign and inspire others!</p>
        <a href="index.php?page=create_request" class="btn btn-primary">Create Your First Request</a>
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($success_stories as $story): ?>
            <div class="card success-story-card">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($story['title']); ?></h3>
                    <span class="badge badge-success">Success</span>
                </div>
                
                <?php if ($story['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($story['image_path']); ?>" alt="Success Story Image" class="request-image">
                <?php endif; ?>
                
                <div class="story-preview">
                    <p><?php echo htmlspecialchars(substr($story['description'], 0, 150)) . '...'; ?></p>
                </div>
                
                <div class="story-meta">
                    <div class="meta-item">
                        <span class="label">👤 Creator:</span>
                        <span class="value"><?php echo htmlspecialchars($story['user_name']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="label">📂 Category:</span>
                        <span class="value"><?php echo ucfirst(htmlspecialchars($story['category'])); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="label">🎯 Goal:</span>
                        <span class="value">৳<?php echo number_format($story['goal_amount'], 2); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="label">💰 Raised:</span>
                        <span class="value">৳<?php echo number_format($story['current_amount'], 2); ?></span>
                    </div>
                </div>
                
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 100%"></div>
                </div>
                
                <div class="card-actions">
                    <a href="index.php?page=view_request&id=<?php echo $story['id']; ?>" class="btn btn-primary">Read Full Story</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
.success-story-card {
    position: relative;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.card-header h3 {
    margin: 0;
    flex: 1;
    margin-right: 1rem;
}

.badge-success {
    background: linear-gradient(45deg, #00a651, #008f45);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

.story-preview {
    margin-bottom: 1rem;
}

.story-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 1.5rem;
    background: rgba(0, 166, 81, 0.05);
    border-radius: var(--border-radius);
    border: 1px solid rgba(0, 166, 81, 0.1);
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.meta-item .label {
    font-weight: 600;
    color: var(--text-light);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.meta-item .value {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1.1rem;
}

.card-actions {
    margin-top: 1rem;
    text-align: center;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: var(--card-bg);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-state h3 {
    margin-bottom: 1rem;
    color: var(--text-dark);
}

.empty-state p {
    color: var(--text-light);
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .story-meta {
        grid-template-columns: 1fr;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .card-header h3 {
        margin-right: 0;
        margin-bottom: 0.5rem;
    }
}
</style> 