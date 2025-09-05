<?php
$success_stories = $helpRequestController->getSuccessStories();
?>

<div class="success-stories-page">
    <div class="success-stories-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-trophy"></i>
                    <span>Inspiring Stories</span>
                </div>
                <h1 class="hero-title">
                    <span class="title-line">Success</span>
                    <span class="title-line highlight">Stories</span>
                </h1>
                <p class="hero-subtitle">
                    Discover the amazing journeys of people who reached their goals with the help of our community. 
                    These inspiring stories show the power of unity and compassion.
                </p>
                
                <div class="success-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-number"><?php echo count($success_stories); ?></span>
                            <span class="stat-label">Success Stories</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="success-stories-container">
        <?php if (empty($success_stories)): ?>
            <div class="empty-success-stories">
                <div class="empty-content">
                    <div class="empty-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>No Success Stories Yet</h3>
                    <p>Be the first to complete a campaign and inspire others with your success story!</p>
                    <a href="index.php?page=create_request" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Create Your First Request
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="success-stories-grid">
                <?php foreach ($success_stories as $index => $story): ?>
                    <div class="success-story-card" id="post-<?php echo $story['id']; ?>" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                        <div class="story-image-section">
                            <?php if ($story['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($story['image_path']); ?>" alt="Success Story Image" class="story-image">
                            <?php else: ?>
                                <div class="story-image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="success-badge">
                                <i class="fas fa-check-circle"></i>
                                <span>Success</span>
                            </div>
                        </div>
                        
                        <div class="story-content">
                            <div class="story-header">
                                <h3 class="story-title"><?php echo htmlspecialchars($story['title']); ?></h3>
                                <div class="story-category">
                                    <i class="fas fa-tag"></i>
                                    <span><?php echo ucfirst(htmlspecialchars($story['category'])); ?></span>
                                </div>
                            </div>
                            
                            <p class="story-description"><?php echo htmlspecialchars(substr($story['description'], 0, 150)) . '...'; ?></p>
                            
                            <div class="story-meta">
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <?php echo AvatarHelper::render($story['profile_picture'], $story['user_name'], 'sm'); ?>
                                    </div>
                                    <div class="meta-content">
                                        <span class="meta-label">Creator</span>
                                        <span class="meta-value"><?php echo htmlspecialchars($story['user_name']); ?></span>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-target"></i>
                                    </div>
                                    <div class="meta-content">
                                        <span class="meta-label">Goal</span>
                                        <span class="meta-value">৳<?php echo number_format($story['goal_amount'], 0); ?></span>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="meta-content">
                                        <span class="meta-label">Raised</span>
                                        <span class="meta-value">৳<?php echo number_format($story['current_amount'], 0); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="story-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 100%"></div>
                                </div>
                                <div class="progress-label">100% Complete</div>
                            </div>
                            
                            <div class="story-actions">
                                <a href="index.php?page=view_request&id=<?php echo $story['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-book-open"></i>
                                    Read Full Story
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Handle scrolling to specific posts
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const postId = urlParams.get('post_id');
    
    if (postId) {
        const targetElement = document.querySelector('#post-' + postId);
        if (targetElement) {
            // Scroll to the element with some offset
            setTimeout(() => {
                targetElement.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Add a highlight effect
                targetElement.style.boxShadow = '0 0 20px rgba(0, 166, 81, 0.5)';
                targetElement.style.border = '2px solid #00a651';
                setTimeout(() => {
                    targetElement.style.boxShadow = '';
                    targetElement.style.border = '';
                }, 3000);
            }, 1000);
        }
    }
    
    // Also handle hash-based scrolling for backward compatibility
    const hash = window.location.hash;
    if (hash && !postId) {
        const targetElement = document.querySelector(hash);
        if (targetElement) {
            setTimeout(() => {
                targetElement.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                targetElement.style.boxShadow = '0 0 20px rgba(0, 166, 81, 0.5)';
                setTimeout(() => {
                    targetElement.style.boxShadow = '';
                }, 3000);
            }, 500);
        }
    }
});
</script>
