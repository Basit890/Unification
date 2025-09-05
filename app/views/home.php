<?php
if (!isset($fontAwesomeAdded)) {
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
    $fontAwesomeAdded = true;
}

$category_filter = $_GET['category'] ?? '';
$sort_by = $_GET['sort'] ?? 'created_at';

$filters = [
    'category' => $category_filter,
    'sort' => $sort_by
];


$allRequests = $helpRequestController->getApprovedRequests([]);
$requests = $helpRequestController->getApprovedRequests($filters);
$categories = $helpRequestController->getCategories();

$topDonors = $donationController->getTopDonors(5);

$categoryHasPosts = true;
if ($category_filter && !empty($category_filter)) {
    $categoryRequests = array_filter($allRequests, function($req) use ($category_filter) {
        return $req['category'] === $category_filter;
    });
    $categoryHasPosts = !empty($categoryRequests);
}
?>

<div class="home-hero">
    <div class="hero-background">
        <div class="hero-pattern"></div>
    </div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <span>Making a Difference Since 2021</span>
            </div>
            <h1 class="hero-title">
                <span class="title-line">Welcome to</span>
                <span class="title-line highlight">Community Help</span>
            </h1>
            <p class="hero-subtitle">Help others achieve their goals or get support for your own cause. Together, we can make a difference through transparent, direct giving!</p>
            
            <div class="hero-stats">
                <div class="hero-stat" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number" data-count="<?php echo count($allRequests); ?>">0+</span>
                        <span class="stat-label">Active Campaigns</span>
                    </div>
                </div>
                <div class="hero-stat" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number" data-count="<?php echo count($categories); ?>">0+</span>
                        <span class="stat-label">Categories</span>
                    </div>
                </div>
                <div class="hero-stat" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">Community Driven</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php if (!empty($topDonors)): ?>
<div class="top-donors-section" data-aos="fade-up">
    <div class="section-header">
        <h2>Top Generous Donors</h2>
        <p class="section-subtitle">Celebrating our community's most generous supporters</p>
    </div>
    
    <div class="top-donors-grid">
        <?php foreach ($topDonors as $index => $donor): ?>
            <div class="donor-card" data-aos="zoom-in" data-aos-delay="<?php echo ($index + 1) * 100; ?>">
                <div class="donor-card-inner">
                    <div class="donor-rank">
                        <?php if ($index === 0): ?>
                            <div class="rank-badge gold">🥇</div>
                        <?php elseif ($index === 1): ?>
                            <div class="rank-badge silver">🥈</div>
                        <?php elseif ($index === 2): ?>
                            <div class="rank-badge bronze">🥉</div>
                        <?php else: ?>
                            <div class="rank-badge">#<?php echo $index + 1; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="donor-avatar">
                        <?php echo AvatarHelper::render($donor['profile_picture'], $donor['donor_name'], 'lg'); ?>
                    </div>
                    
                    <div class="donor-info">
                        <h3 class="donor-name"><?php echo htmlspecialchars($donor['donor_name']); ?></h3>
                        <div class="donor-stats">
                            <span class="donation-count">
                                <i class="fas fa-heart"></i>
                                <?php echo $donor['donation_count']; ?> donation<?php echo $donor['donation_count'] > 1 ? 's' : ''; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="donor-amount">
                        <span class="amount-value">৳<?php echo number_format($donor['total_donated'], 2); ?></span>
                        <span class="amount-label">Total Donated</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="donors-inspiration" data-aos="fade-up" data-aos-delay="600">
        <i class="fas fa-sparkles"></i>
        <p><strong>Be inspired!</strong> Join our community of generous donors and make a difference today.</p>
    </div>
</div>
<?php endif; ?>




<div class="filter-bar" data-aos="fade-up">
    <div class="filter-container">
        <div class="filter-controls">
            <div class="filter-header">
                <h3>Find Your Perfect Campaign</h3>
                <p>Discover campaigns that matter to you</p>
            </div>
            <div class="filter-form-row">
                <div class="form-group search-group">
                    <label for="search">
                        <i class="fas fa-search"></i>
                        Search Campaigns
                    </label>
                    <input type="search" id="search" placeholder="Search by title, description, or creator..." oninput="filterCampaigns()">
                </div>
                
                <div class="form-group category-group">
                    <label for="category">
                        <i class="fas fa-tags"></i>
                        Category
                    </label>
                    <select name="category" id="category" onchange="filterCampaigns()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $key => $name): ?>
                            <option value="<?php echo $key; ?>" <?php echo $category_filter === $key ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group sort-group">
                    <label for="sort">
                        <i class="fas fa-sort"></i>
                        Sort By
                    </label>
                    <select name="sort" id="sort" onchange="sortCampaigns()">
                        <option value="created_at" <?php echo $sort_by === 'created_at' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="progress" <?php echo $sort_by === 'progress' ? 'selected' : ''; ?>>Most Progress</option>
                        <option value="goal" <?php echo $sort_by === 'goal' ? 'selected' : ''; ?>>Highest Goal</option>
                        <option value="title" <?php echo $sort_by === 'title' ? 'selected' : ''; ?>>Alphabetical</option>
                    </select>
                </div>
            </div>
            
        </div>
    </div>
    
    <div class="filter-results">
        <span class="results-count">
            Showing <strong id="campaign-count"><?php echo count($requests); ?></strong> of <?php echo count($allRequests); ?> campaigns
            <?php if ($category_filter): ?>
                in <strong><?php echo htmlspecialchars($categories[$category_filter] ?? $category_filter); ?></strong> category
            <?php endif; ?>
        </span>
    </div>
</div>

<?php if (empty($requests) || (!$categoryHasPosts && $category_filter)): ?>
    <div style="text-align: center; padding: 3rem;">
        <div style="font-size: 4rem; margin-bottom: 1rem;">
            <?php if (!$categoryHasPosts && $category_filter): ?>
                🔍
            <?php else: ?>
                🎯
            <?php endif; ?>
        </div>
        <h3 style="margin-bottom: 1rem;">
            <?php if (!$categoryHasPosts && $category_filter): ?>
                No campaigns found in this category
            <?php else: ?>
                No campaigns found
            <?php endif; ?>
        </h3>
        <p style="color: var(--text-light); margin-bottom: 2rem;">
            <?php if (!$categoryHasPosts && $category_filter): ?>
                There are currently no fundraising campaigns in the <strong><?php echo htmlspecialchars($categories[$category_filter] ?? $category_filter); ?></strong> category.
                <br>Try selecting a different category or browse all campaigns.
            <?php elseif (Session::isLoggedIn()): ?>
                <?php 
                $user = $userModel->findById(Session::getUserId());
                if ($user['user_type'] === 'fundraiser' || $user['user_type'] === 'admin'): ?>
                    Be the first to create a campaign and make a difference!
                <?php else: ?>
                    No campaigns are currently available. Check back later!
                <?php endif; ?>
            <?php else: ?>
                Be the first to create a campaign and make a difference!
            <?php endif; ?>
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <?php if (!$categoryHasPosts && $category_filter): ?>
                <a href="index.php" class="btn btn-secondary">View All Categories</a>
            <?php endif; ?>
            <?php if (Session::isLoggedIn()): ?>
                <?php if ($user['user_type'] === 'fundraiser' || $user['user_type'] === 'admin'): ?>
                    <a href="index.php?page=create_request" class="btn">Create Your First Campaign</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="index.php?page=register" class="btn">Join Our Community</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="grid" id="campaigns-grid">
        <?php foreach ($requests as $request): ?>
            <div class="card campaign-card" 
                 data-category="<?php echo htmlspecialchars($request['category']); ?>" 
                 data-search="<?php echo htmlspecialchars(strtolower($request['title'] . ' ' . $request['description'] . ' ' . $request['user_name'])); ?>"
                 data-created-at="<?php echo $request['created_at']; ?>"
                 data-progress="<?php echo ($request['current_amount'] / $request['goal_amount']) * 100; ?>"
                 data-goal="<?php echo $request['goal_amount']; ?>"
                 data-title="<?php echo htmlspecialchars(strtolower($request['title'])); ?>">
                <?php if ($request['image_path'] && file_exists($request['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($request['image_path']); ?>" alt="Request Image" class="request-image" loading="lazy">
                <?php else: ?>
                    <div style="height: 200px; background: linear-gradient(45deg, var(--primary-color), var(--accent-color)); border-radius: var(--border-radius); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; margin-bottom: 1rem;">🎯</div>
                <?php endif; ?>
                
                <div class="campaign-content">
                    <div class="campaign-meta">
                        <span class="badge badge-approved"><?php echo htmlspecialchars(ucfirst($request['category'])); ?></span>
                        <small style="color: var(--text-light);"><?php echo date('M j, Y', strtotime($request['created_at'])); ?></small>
                    </div>
                    
                    <h3 style="margin-bottom: 0.5rem; font-size: 1.3rem;"><?php echo htmlspecialchars($request['title']); ?></h3>
                    <p style="color: var(--text-light); margin-bottom: 1rem; line-height: 1.5;"><?php echo htmlspecialchars(substr($request['description'], 0, 120)) . '...'; ?></p>
                    
                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                        <div style="margin-right: 0.5rem;">
                            <?php echo AvatarHelper::render($request['profile_picture'], $request['user_name'], 'sm'); ?>
                        </div>
                        <span style="font-weight: 500;"><?php echo htmlspecialchars($request['user_name']); ?></span>
                    </div>
                    
                    <div class="progress-section">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-weight: 600; color: var(--primary-color);">৳<?php echo number_format($request['current_amount'], 2); ?></span>
                            <span style="color: var(--text-light);">of ৳<?php echo number_format($request['goal_amount'], 2); ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min(($request['current_amount'] / $request['goal_amount']) * 100, 100); ?>%"></div>
                        </div>
                        <div style="text-align: center; margin-top: 0.5rem; font-weight: 600; color: var(--primary-color);">
                            <?php echo number_format(($request['current_amount'] / $request['goal_amount']) * 100, 1); ?>% funded
                        </div>
                    </div>
                    
                    <div class="campaign-actions">
                        <a href="index.php?page=view_request&id=<?php echo $request['id']; ?>" class="btn" style="flex: 1; text-align: center;">View Details</a>
                        <button class="btn btn-secondary" onclick="shareCampaign(<?php echo $request['id']; ?>)" style="padding: 12px; margin-left: 0.5rem;" data-tooltip="Share this campaign">
                            📤
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    
    <div style="text-align: center; margin-top: 2rem; color: var(--text-light);">
        Showing <span id="campaign-count"><?php echo count($requests); ?></span> of <?php echo count($allRequests); ?> campaigns
        <?php if ($category_filter): ?>
            in <strong><?php echo htmlspecialchars($categories[$category_filter] ?? $category_filter); ?></strong> category
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('category').value = '';
    document.getElementById('sort').value = 'created_at';
    filterCampaigns();
}

// Toggle functionality removed - using list view as default

function filterCampaigns() {
    const searchTerm = document.getElementById('search').value.toLowerCase();
    const categoryFilter = document.getElementById('category').value;
    const cards = document.querySelectorAll('.campaign-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const searchText = card.dataset.search;
        const category = card.dataset.category;
        const matchesSearch = searchTerm === '' || searchText.includes(searchTerm);
        const matchesCategory = !categoryFilter || category === categoryFilter;
        
        if (matchesSearch && matchesCategory) {
            card.style.display = 'flex';
            card.style.flexDirection = 'row';
            card.style.alignItems = 'center';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    document.getElementById('campaign-count').textContent = visibleCount;
    
    // Show/hide no results message
    const noResultsMsg = document.getElementById('noResultsMessage');
    if (visibleCount === 0) {
        if (!noResultsMsg) {
            const grid = document.getElementById('campaigns-grid');
            const noResultsDiv = document.createElement('div');
            noResultsDiv.id = 'noResultsMessage';
            noResultsDiv.className = 'no-results';
            noResultsDiv.innerHTML = `
                <div class="no-results-content">
                    <i class="fas fa-search"></i>
                    <h3>No campaigns found</h3>
                    <p>Try adjusting your search criteria or browse all categories.</p>
                </div>
            `;
            // Insert at the beginning of the grid
            grid.insertBefore(noResultsDiv, grid.firstChild);
        }
    } else {
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }
}

function sortCampaigns() {
    const sortBy = document.getElementById('sort').value;
    const grid = document.getElementById('campaigns-grid');
    const cards = Array.from(grid.querySelectorAll('.campaign-card'));
    
    cards.sort((a, b) => {
        switch (sortBy) {
            case 'created_at':
                const dateA = new Date(a.dataset.createdAt || '1970-01-01');
                const dateB = new Date(b.dataset.createdAt || '1970-01-01');
                return dateB - dateA;
            case 'progress':
                const progressA = parseFloat(a.dataset.progress || '0');
                const progressB = parseFloat(b.dataset.progress || '0');
                return progressB - progressA;
            case 'goal':
                const goalA = parseFloat(a.dataset.goal || '0');
                const goalB = parseFloat(b.dataset.goal || '0');
                return goalB - goalA;
            case 'title':
                const titleA = a.dataset.title || '';
                const titleB = b.dataset.title || '';
                return titleA.localeCompare(titleB);
            default:
                return 0;
        }
    });
    
    // Re-append sorted cards
    cards.forEach(card => grid.appendChild(card));
}

function shareCampaign(id) {
    const url = `${window.location.origin}${window.location.pathname}?page=view_request&id=${id}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Check out this campaign!',
            url: url
        });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            showNotification('Campaign link copied to clipboard!', 'success');
        });
    }
}

document.getElementById('search').addEventListener('input', filterCampaigns);
document.getElementById('category').addEventListener('change', filterCampaigns);

const style = document.createElement('style');
style.textContent = `
    /* Modern Homepage Styles */
    
    /* Section Headers */
    .section-header {
        text-align: center;
        margin-bottom: 3rem;
        padding: 2rem 0;
    }
    
    .section-header h2 {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    
    .section-header p {
        font-size: 1.1rem;
        color: var(--text-secondary);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }
    
    /* Top Donors Section */
    .top-donors-section {
        background: linear-gradient(135deg, var(--card-bg) 0%, rgba(255, 255, 255, 0.02) 100%);
        border-radius: 24px;
        padding: 3rem 2rem;
        margin: 3rem 0;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }
    
    .top-donors-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }
    
    .top-donors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .donor-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .donor-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .donor-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .donor-card:hover::before {
        transform: scaleX(1);
    }
    
    .donor-rank {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .rank-badge {
        display: inline-block;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    
    .rank-badge.gold {
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        border-color: #ffd700;
        color: #b8860b;
    }
    
    .rank-badge.silver {
        background: linear-gradient(135deg, #c0c0c0, #e5e5e5);
        border-color: #c0c0c0;
        color: #696969;
    }
    
    .rank-badge.bronze {
        background: linear-gradient(135deg, #cd7f32, #daa520);
        border-color: #cd7f32;
        color: #8b4513;
    }
    
    .donor-avatar {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .donor-avatar i {
        font-size: 3rem;
        color: var(--primary-color);
        opacity: 0.8;
    }
    
    .donor-info {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .donor-name {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: var(--text-primary);
    }
    
    .donor-stats {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    .donor-stats i {
        color: var(--primary-color);
    }
    
    .donor-amount {
        text-align: center;
    }
    
    .amount-value {
        display: block;
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }
    
    .amount-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    
    .donors-inspiration {
        text-align: center;
        margin-top: 2rem;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .donors-inspiration i {
        font-size: 2rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
        display: block;
    }
    
    .donors-inspiration p {
        margin: 0;
        font-size: 1rem;
        color: var(--text-primary);
    }
    
    
    
    
    
    
    
    /* Enhanced Filter Bar */
    .filter-bar {
        background: linear-gradient(135deg, var(--card-bg) 0%, rgba(255, 255, 255, 0.02) 100%);
        border-radius: 20px;
        padding: 2rem;
        margin: 3rem 0;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }
    
    .filter-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }
    
    .filter-container {
        display: grid;
        grid-template-columns: 1fr auto auto auto;
        gap: 1.5rem;
        align-items: end;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .form-group label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }
    
    .form-group label i {
        color: var(--accent-color);
    }
    
    .form-group input,
    .form-group select {
        height: 48px;
        padding: 0 1rem;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-group input:hover,
    .form-group select:hover {
        border-color: var(--accent-color);
        transform: translateY(-1px);
    }
    
    .search-group {
        grid-column: 1;
    }
    
    .category-group,
    .sort-group {
        min-width: 180px;
    }
    
    .filter-actions {
        display: flex;
        gap: 0.75rem;
        align-items: end;
        padding-top: 2.25rem;
    }
    
    .clear-btn,
    .toggle-btn {
        height: 48px;
        padding: 0 1rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        min-width: 80px;
        box-sizing: border-box;
    }
    
    .clear-btn:hover,
    .toggle-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
        border-color: var(--accent-color);
    }
    
    .filter-results {
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .results-count {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    .results-count strong {
        color: var(--text-primary);
    }
    
    /* No Results Message */
    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .no-results-content i {
        font-size: 3rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .no-results-content h3 {
        margin-bottom: 0.75rem;
        color: var(--text-primary);
    }
    
    .no-results-content p {
        color: var(--text-secondary);
        margin: 0;
    }
    
    /* Default List View Styles */
    .grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .campaign-card {
        display: flex !important;
        align-items: center !important;
        gap: 1.5rem !important;
        padding: 1.5rem !important;
        border-radius: 12px;
        transition: all 0.3s ease;
        flex-direction: row !important;
    }
    
    .campaign-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .request-image,
    div[style*="height: 200px"] {
        width: 180px !important;
        height: 120px !important;
        margin-bottom: 0 !important;
        flex-shrink: 0 !important;
        border-radius: 8px;
        object-fit: cover;
        display: block !important;
    }
    
    .campaign-content {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 0.5rem !important;
        width: calc(100% - 180px - 1.5rem) !important;
    }
    
    .campaign-card h3 {
        margin-bottom: 0.25rem !important;
        font-size: 1.4rem;
    }
    
    .campaign-card p {
        margin-bottom: 0.5rem !important;
        line-height: 1.6;
    }
    
    .campaign-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .campaign-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: auto;
    }
    
    .progress-section {
        flex: 1;
        max-width: 300px;
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
        .filter-container {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .filter-actions {
            justify-content: center;
        }
        
        .top-donors-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
    }
    
    @media (max-width: 768px) {
        .section-header h2 {
            font-size: 1.8rem;
        }
        
        .top-donors-section {
            padding: 2rem 1rem;
            margin: 2rem 0;
        }
        
        .top-donors-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .filter-bar {
            padding: 1.5rem;
        }
        
        .campaign-card {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .request-image,
        div[style*="height: 200px"] {
            width: 100%;
            height: 200px !important;
        }
        
        .campaign-meta {
            flex-wrap: wrap;
        }
        
        .campaign-actions {
            justify-content: center;
        }
    }
`;
document.head.appendChild(style);

// AOS (Animate On Scroll) initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS if available
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
    }

    // Counter animation for hero stats
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number[data-count]');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000; // 2 seconds
            const increment = target / (duration / 16); // 60fps
            let current = 0;
            
            // Get the suffix from the current text content
            const currentText = counter.textContent;
            const suffix = currentText.replace(/^\d+/, '');
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current) + suffix;
            }, 16);
        });
    }

    // Start counter animation immediately when page loads
    setTimeout(() => {
        animateCounters();
    }, 300);

    // Add smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add parallax effect to hero section
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.home-hero');
        if (hero) {
            const rate = scrolled * -0.3;
            hero.style.transform = `translateY(${rate}px)`;
        }
    });
});
</script>
        
