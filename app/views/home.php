<?php
$category_filter = $_GET['category'] ?? '';
$sort_by = $_GET['sort'] ?? 'created_at';

$filters = [
    'category' => $category_filter,
    'sort' => $sort_by
];

// Get all approved requests first
$allRequests = $helpRequestController->getApprovedRequests([]);
$requests = $helpRequestController->getApprovedRequests($filters);
$categories = $helpRequestController->getCategories();

$topDonors = $donationController->getTopDonors(5);

// Check if current category has any posts
$categoryHasPosts = true;
if ($category_filter && !empty($category_filter)) {
    $categoryRequests = array_filter($allRequests, function($req) use ($category_filter) {
        return $req['category'] === $category_filter;
    });
    $categoryHasPosts = !empty($categoryRequests);
}
?>

<div style="text-align: center; margin-bottom: 3rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem; background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Welcome to Community Help</h1>
    <p style="font-size: 1.1rem; color: var(--text-light); max-width: 600px; margin: 0 auto;">Help others achieve their goals or get support for your own cause. Together, we can make a difference!</p>
    
    <!-- Quick Stats -->
    <div style="display: flex; justify-content: center; gap: 2rem; margin-top: 2rem; flex-wrap: wrap;">
        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?php echo count($allRequests); ?></div>
            <div style="color: var(--text-light);">Active Campaigns</div>
        </div>
        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--accent-color);"><?php echo count($categories); ?></div>
            <div style="color: var(--text-light);">Categories</div>
        </div>
        <div style="text-align: center; padding: 1rem;">
            <div style="font-size: 2rem; font-weight: bold; color: var(--secondary-color);">100%</div>
            <div style="color: var(--text-light);">Community Driven</div>
        </div>
    </div>
</div>

<!-- Top Donors Chart -->
<?php if (!empty($topDonors)): ?>
<div class="top-donors-chart" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 2rem; margin: 2rem 0; color: white; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2 style="margin: 0; font-size: 1.8rem; font-weight: 700;">🏆 Top Generous Donors</h2>
        <p style="margin: 0.5rem 0 0 0; opacity: 0.9; font-size: 1rem;">Celebrating our community's most generous supporters</p>
    </div>
    
    <div class="top-donors-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; max-width: 1000px; margin: 0 auto;">
        <?php foreach ($topDonors as $index => $donor): ?>
            <div class="donor-card" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border-radius: 12px; padding: 1.5rem; text-align: center; border: 1px solid rgba(255, 255, 255, 0.2); transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <div class="medal" style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                    <?php 
                    $medals = ['🥇', '🥈', '🥉', '🏅', '🏅'];
                    echo $medals[$index] ?? '🏅';
                    ?>
                </div>
                <div class="donor-name" style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    <?php echo htmlspecialchars($donor['donor_name']); ?>
                </div>
                <div class="donor-amount" style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                    ৳<?php echo number_format($donor['total_donated'], 2); ?>
                </div>
                <div style="font-size: 0.9rem; opacity: 0.8;">
                    <?php echo $donor['donation_count']; ?> donation<?php echo $donor['donation_count'] > 1 ? 's' : ''; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <p style="margin: 0; font-size: 0.95rem; opacity: 0.8;">
            💝 <strong>Be inspired!</strong> Join our community of generous donors and make a difference today.
        </p>
    </div>
</div>
<?php endif; ?>

<!-- Enhanced Filter Bar -->
<div class="filter-bar">
    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; width: 100%;">
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label for="search" style="margin-bottom: 0.5rem; font-size: 0.9rem;">Search Campaigns</label>
            <input type="search" id="search" placeholder="Search by title, description, or creator..." style="width: 100%;">
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label for="category" style="margin-bottom: 0.5rem; font-size: 0.9rem;">Category</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $key => $name): ?>
                    <option value="<?php echo $key; ?>" <?php echo $category_filter === $key ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label for="sort" style="margin-bottom: 0.5rem; font-size: 0.9rem;">Sort By</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="created_at" <?php echo $sort_by === 'created_at' ? 'selected' : ''; ?>>Newest First</option>
                <option value="progress" <?php echo $sort_by === 'progress' ? 'selected' : ''; ?>>Most Progress</option>
                <option value="goal" <?php echo $sort_by === 'goal' ? 'selected' : ''; ?>>Highest Goal</option>
            </select>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: end;">
            <button type="button" class="btn btn-secondary" onclick="clearFilters()" style="padding: 12px 16px;">
                Clear
            </button>
            <button type="button" class="btn" onclick="toggleView()" style="padding: 12px 16px;" data-tooltip="Toggle between grid and list view">
                <span id="view-icon">📋</span>
            </button>
        </div>
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
            <div class="card campaign-card" data-category="<?php echo htmlspecialchars($request['category']); ?>" data-search="<?php echo htmlspecialchars(strtolower($request['title'] . ' ' . $request['description'] . ' ' . $request['user_name'])); ?>">
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
                        <div style="width: 30px; height: 30px; background: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 0.5rem;"><?php echo strtoupper(substr($request['user_name'], 0, 1)); ?></div>
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
    
    <!-- Campaign count -->
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

function toggleView() {
    const grid = document.getElementById('campaigns-grid');
    const icon = document.getElementById('view-icon');
    
    if (grid.classList.contains('list-view')) {
        grid.classList.remove('list-view');
        icon.textContent = '📋';
    } else {
        grid.classList.add('list-view');
        icon.textContent = '🔲';
    }
}

function filterCampaigns() {
    const searchTerm = document.getElementById('search').value.toLowerCase();
    const categoryFilter = document.getElementById('category').value;
    const cards = document.querySelectorAll('.campaign-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const searchText = card.dataset.search;
        const category = card.dataset.category;
        const matchesSearch = searchText.includes(searchTerm);
        const matchesCategory = !categoryFilter || category === categoryFilter;
        
        if (matchesSearch && matchesCategory) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    document.getElementById('campaign-count').textContent = visibleCount;
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
    .list-view {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
    
    .list-view .campaign-card {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .list-view .campaign-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .list-view .request-image,
    .list-view div[style*="height: 200px"] {
        width: 180px;
        height: 120px !important;
        margin-bottom: 0 !important;
        flex-shrink: 0;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .list-view .campaign-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .list-view h3 {
        margin-bottom: 0.25rem !important;
        font-size: 1.4rem;
    }
    
    .list-view p {
        margin-bottom: 0.5rem !important;
        line-height: 1.6;
    }
    
    .list-view .campaign-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .list-view .campaign-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: auto;
    }
    
    .list-view .progress-section {
        flex: 1;
        max-width: 300px;
    }
    
    @media (max-width: 768px) {
        .list-view .campaign-card {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
        
        .list-view .request-image,
        .list-view div[style*="height: 200px"] {
            width: 100%;
            height: 200px !important;
        }
        
        .list-view .campaign-meta {
            flex-wrap: wrap;
        }
        
        .list-view .campaign-actions {
            justify-content: center;
        }
    }
`;
document.head.appendChild(style);
</script>
        
