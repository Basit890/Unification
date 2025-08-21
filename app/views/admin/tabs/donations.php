<div class="content-section">
    <h2>💰 Donations Management</h2>
    
    <!-- Donation Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-number">৳<?php echo number_format($stats['total_donations'], 2); ?></div>
            <div class="stat-label">Total Donations</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT COUNT(*) FROM donations");
        $totalDonationCount = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">🔄</div>
            <div class="stat-number"><?php echo $totalDonationCount; ?></div>
            <div class="stat-label">Total Transactions</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM donations");
        $uniqueDonors = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-number"><?php echo $uniqueDonors; ?></div>
            <div class="stat-label">Unique Donors</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT AVG(amount) FROM donations");
        $avgDonation = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-number">৳<?php echo number_format($avgDonation, 2); ?></div>
            <div class="stat-label">Average Donation</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT MAX(amount) FROM donations");
        $maxDonation = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-number">৳<?php echo number_format($maxDonation, 2); ?></div>
            <div class="stat-label">Largest Donation</div>
        </div>
        <?php
        $stmt = $pdo->query("SELECT COUNT(*) FROM donations WHERE amount >= 10000");
        $largeDonations = $stmt->fetchColumn();
        ?>
        <div class="stat-card">
            <div class="stat-icon">💎</div>
            <div class="stat-number"><?php echo $largeDonations; ?></div>
            <div class="stat-label">Large Donations (10k+)</div>
        </div>
    </div>
    
    <!-- Donation Filters -->
    <div class="filter-section">
        <div class="filter-controls">
            <select id="amountFilter" onchange="filterDonations()">
                <option value="">All Amounts</option>
                <option value="0-1000">৳0 - ৳1,000</option>
                <option value="1000-5000">৳1,000 - ৳5,000</option>
                <option value="5000-10000">৳5,000 - ৳10,000</option>
                <option value="10000+">৳10,000+</option>
            </select>
            
            <input type="date" id="dateFilter" onchange="filterDonations()">
            
            <input type="text" id="donationSearchFilter" placeholder="Search donations..." onkeyup="filterDonations()">
        </div>
    </div>
    
    <!-- Donations Table -->
    <div class="table-responsive">
        <table class="table" id="donationsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donor</th>
                    <th>Request</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("
                    SELECT d.*, 
                           CONCAT(u.first_name, ' ', u.last_name) as donor_name,
                           u.email as donor_email,
                           hr.title as request_title,
                           hr.status as request_status
                    FROM donations d 
                    JOIN users u ON d.user_id = u.id 
                    JOIN help_requests hr ON d.request_id = hr.id 
                    ORDER BY d.created_at DESC
                ");
                $stmt->execute();
                $donations = $stmt->fetchAll();
                ?>
                
                <?php foreach ($donations as $donation): ?>
                    <tr class="donation-row" data-amount="<?php echo $donation['amount']; ?>" data-date="<?php echo date('Y-m-d', strtotime($donation['created_at'])); ?>">
                        <td><?php echo $donation['id']; ?></td>
                        <td>
                            <div class="donor-info">
                                <strong><?php echo htmlspecialchars($donation['donor_name']); ?></strong>
                                <small><?php echo htmlspecialchars($donation['donor_email']); ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="request-info">
                                <strong><?php echo htmlspecialchars($donation['request_title']); ?></strong>
                                <span class="badge badge-<?php echo $donation['request_status']; ?>">
                                    <?php echo ucfirst($donation['request_status']); ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="amount-display">
                                <strong class="amount-value">৳<?php echo number_format($donation['amount'], 2); ?></strong>
                                <?php if ($donation['amount'] >= 10000): ?>
                                    <span class="large-donation">💎</span>
                                <?php elseif ($donation['amount'] >= 5000): ?>
                                    <span class="medium-donation">⭐</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="date-info">
                                <div class="date-main"><?php echo date('M j, Y', strtotime($donation['created_at'])); ?></div>
                                <small><?php echo date('g:i A', strtotime($donation['created_at'])); ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="index.php?page=view_request&id=<?php echo $donation['request_id']; ?>" class="btn btn-secondary" title="View Request">
                                    👁️
                                </a>
                                
                                <a href="index.php?page=profile&user_id=<?php echo $donation['user_id']; ?>" class="btn btn-secondary" title="View Donor">
                                    👤
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_donation">
                                    <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
                                    <button type="submit" class="btn btn-danger" title="Delete Donation" onclick="return confirm('Are you sure you want to delete this donation? This action cannot be undone.')">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Donation Summary -->
    <div class="donation-summary">
        <h3>📊 Donation Summary</h3>
        <div class="summary-grid">
            <?php
            $stmt = $pdo->query("
                SELECT u.first_name, u.last_name, SUM(d.amount) as total_donated, COUNT(d.id) as donation_count
                FROM donations d 
                JOIN users u ON d.user_id = u.id 
                GROUP BY d.user_id 
                ORDER BY total_donated DESC 
                LIMIT 5
            ");
            $topDonors = $stmt->fetchAll();
            
            // Calculate total for progress bars
            $totalDonated = array_sum(array_column($topDonors, 'total_donated'));
            ?>
            
            <div class="summary-card">
                <div class="summary-header">
                    <h4><i class="fas fa-trophy"></i> Top Donors</h4>
                    <span class="summary-subtitle">Most generous contributors</span>
                </div>
                <?php if (empty($topDonors)): ?>
                    <div class="no-data">
                        <i class="fas fa-gift"></i>
                        <p>No donations yet</p>
                    </div>
                <?php else: ?>
                    <div class="donor-list">
                        <?php foreach ($topDonors as $index => $donor): ?>
                            <div class="donor-item">
                                <div class="donor-rank">
                                    <span class="rank-number">#<?php echo $index + 1; ?></span>
                                    <?php if ($index === 0): ?>
                                        <i class="fas fa-crown crown-gold"></i>
                                    <?php elseif ($index === 1): ?>
                                        <i class="fas fa-medal medal-silver"></i>
                                    <?php elseif ($index === 2): ?>
                                        <i class="fas fa-medal medal-bronze"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="donor-details">
                                    <strong><?php echo htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']); ?></strong>
                                    <small><?php echo $donor['donation_count']; ?> donations</small>
                                    <div class="donor-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo ($donor['total_donated'] / $totalDonated) * 100; ?>%"></div>
                                        </div>
                                        <span class="progress-text"><?php echo round(($donor['total_donated'] / $totalDonated) * 100, 1); ?>%</span>
                                    </div>
                                </div>
                                <div class="donor-amount">
                                    <span class="amount-value">৳<?php echo number_format($donor['total_donated'], 2); ?></span>
                                    <span class="amount-label">Total</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php
            $stmt = $pdo->query("
                SELECT DATE(created_at) as date, SUM(amount) as daily_total, COUNT(*) as daily_count
                FROM donations 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date DESC
            ");
            $recentDonations = $stmt->fetchAll();
            
            // Calculate max daily total for chart scaling
            $maxDailyTotal = max(array_column($recentDonations, 'daily_total')) ?: 1;
            ?>
            
            <div class="summary-card">
                <div class="summary-header">
                    <h4><i class="fas fa-chart-line"></i> Last 7 Days</h4>
                    <span class="summary-subtitle">Daily donation trends</span>
                </div>
                <?php if (empty($recentDonations)): ?>
                    <div class="no-data">
                        <i class="fas fa-chart-bar"></i>
                        <p>No recent donations</p>
                    </div>
                <?php else: ?>
                    <div class="chart-container">
                        <div class="chart-bars">
                            <?php foreach ($recentDonations as $day): ?>
                                <div class="chart-bar">
                                    <div class="bar-fill" style="height: <?php echo ($day['daily_total'] / $maxDailyTotal) * 100; ?>%"></div>
                                    <div class="bar-label"><?php echo date('M j', strtotime($day['date'])); ?></div>
                                    <div class="bar-value">৳<?php echo number_format($day['daily_total'], 0); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="chart-stats">
                            <div class="stat-mini">
                                <span class="stat-label">Total Week</span>
                                <span class="stat-value">৳<?php echo number_format(array_sum(array_column($recentDonations, 'daily_total')), 2); ?></span>
                            </div>
                            <div class="stat-mini">
                                <span class="stat-label">Avg/Day</span>
                                <span class="stat-value">৳<?php echo number_format(array_sum(array_column($recentDonations, 'daily_total')) / count($recentDonations), 2); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.donation-stats {
    margin-bottom: 2rem;
}

.stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-item {
    background: var(--bg-secondary);
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
}

        .stat-item .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-item .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
/* Filter Controls Styling */
.filter-section {
    margin-bottom: 2rem;
}

.filter-controls {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: end;
}

.filter-controls select,
.filter-controls input {
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--card-bg);
    color: var(--text-dark);
    min-width: 150px;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
}

.filter-controls select:focus,
.filter-controls input:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-controls select:hover,
.filter-controls input:hover {
    border-color: var(--accent-color);
    transform: translateY(-1px);
    box-shadow: var(--shadow-hover);
}

.filter-controls input {
    flex: 1;
    min-width: 200px;
}

/* Donation Summary Styles */
.donation-summary {
    margin-top: 3rem;
}

.donation-summary h3 {
    margin-bottom: 2rem;
    color: var(--text-primary);
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.summary-card {
    background: linear-gradient(135deg, var(--card-bg) 0%, rgba(255, 255, 255, 0.02) 100%);
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.summary-header {
    margin-bottom: 1.5rem;
    text-align: center;
}

.summary-header h4 {
    margin: 0 0 0.5rem 0;
    color: var(--text-primary);
    font-size: 1.3rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.summary-subtitle {
    color: var(--text-secondary);
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Top Donors Styles */
.donor-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.donor-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.donor-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateX(5px);
}

.donor-rank {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    min-width: 50px;
}

.rank-number {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
}

.crown-gold {
    color: #ffd700;
    font-size: 1.1rem;
}

.medal-silver {
    color: #c0c0c0;
    font-size: 1rem;
}

.medal-bronze {
    color: #cd7f32;
    font-size: 1rem;
}

.donor-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.donor-details strong {
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 600;
}

.donor-details small {
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.donor-progress {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.progress-bar {
    flex: 1;
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    border-radius: 3px;
    transition: width 0.3s ease;
}

.progress-text {
    color: var(--text-light);
    font-size: 0.8rem;
    font-weight: 600;
    min-width: 40px;
}

.donor-amount {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
    min-width: 100px;
}

.amount-value {
    color: var(--primary-color);
    font-size: 1.1rem;
    font-weight: 700;
}

.amount-label {
    color: var(--text-secondary);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Chart Styles */
.chart-container {
    padding: 1rem 0;
}

.chart-bars {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 0.5rem;
    height: 200px;
    margin-bottom: 1.5rem;
    padding: 0 1rem;
}

.chart-bar {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
}

.bar-fill {
    width: 100%;
    background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
    border-radius: 4px 4px 0 0;
    transition: height 0.3s ease;
    min-height: 10px;
}

.bar-label {
    color: var(--text-secondary);
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
}

.bar-value {
    color: var(--text-primary);
    font-size: 0.9rem;
    font-weight: 600;
    text-align: center;
}

.chart-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.stat-mini {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
    text-align: center;
}

.stat-mini .stat-label {
    color: var(--text-secondary);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.stat-mini .stat-value {
    color: var(--text-primary);
    font-size: 1.1rem;
    font-weight: 700;
}

.no-data {
    text-align: center;
    padding: 2rem;
    color: var(--text-secondary);
}

.no-data i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-data p {
    margin: 0;
    font-style: italic;
}

/* Table Styles */

/* Filter Count Display */
.donation-count-display {
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    text-align: center;
}

.count-info {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.count-info strong {
    color: var(--text-primary);
}

/* Clear Filters Button */
.clear-filters {
    background: var(--text-secondary);
    color: var(--card-bg);
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.clear-filters:hover {
    background: var(--text-primary);
    transform: translateY(-1px);
}
</style>

<script>
function filterDonations() {
    const amountFilter = document.getElementById('amountFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const searchFilter = document.getElementById('donationSearchFilter').value.toLowerCase();
    
    const rows = document.querySelectorAll('.donation-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const amount = parseFloat(row.dataset.amount);
        const date = row.dataset.date;
        const searchText = row.textContent.toLowerCase();
        
        let matchesAmount = true;
        if (amountFilter === '0-1000') {
            matchesAmount = amount >= 0 && amount <= 1000;
        } else if (amountFilter === '1000-5000') {
            matchesAmount = amount > 1000 && amount <= 5000;
        } else if (amountFilter === '5000-10000') {
            matchesAmount = amount > 5000 && amount <= 10000;
        } else if (amountFilter === '10000+') {
            matchesAmount = amount > 10000;
        }
        
        const matchesDate = !dateFilter || date === dateFilter;
        const matchesSearch = !searchFilter || searchText.includes(searchFilter);
        
        if (matchesAmount && matchesDate && matchesSearch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update visible count display
    updateDonationCount(visibleCount);
    
    // Show/hide no results message
    const noResultsMsg = document.getElementById('noResultsMessage');
    if (visibleCount === 0) {
        if (!noResultsMsg) {
            const tbody = document.querySelector('#donationsTable tbody');
            const noResultsRow = document.createElement('tr');
            noResultsRow.id = 'noResultsMessage';
            noResultsRow.innerHTML = `
                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    <p>No donations match your filters</p>
                    <small>Try adjusting your search criteria</small>
                </td>
            `;
            tbody.appendChild(noResultsRow);
        }
    } else {
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }
}

function updateDonationCount(visibleCount) {
    const totalCount = document.querySelectorAll('.donation-row').length;
    const countDisplay = document.getElementById('donationCountDisplay');
    
    if (!countDisplay) {
        const filterSection = document.querySelector('.filter-section');
        const countDiv = document.createElement('div');
        countDiv.id = 'donationCountDisplay';
        countDiv.className = 'donation-count-display';
        countDiv.innerHTML = `
            <span class="count-info">
                Showing <strong>${visibleCount}</strong> of <strong>${totalCount}</strong> donations
            </span>
        `;
        filterSection.appendChild(countDiv);
    } else {
        countDisplay.innerHTML = `
            <span class="count-info">
                Showing <strong>${visibleCount}</strong> of <strong>${totalCount}</strong> donations
            </span>
        `;
    }
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateFilter').value = today;
    
    // Add clear filters button
    const filterControls = document.querySelector('.filter-controls');
    const clearButton = document.createElement('button');
    clearButton.type = 'button';
    clearButton.className = 'btn btn-secondary clear-filters';
    clearButton.innerHTML = '<i class="fas fa-times"></i> Clear Filters';
    clearButton.onclick = clearDonationFilters;
    filterControls.appendChild(clearButton);
    
    // Initial count display
    updateDonationCount(document.querySelectorAll('.donation-row').length);
});

function clearDonationFilters() {
    document.getElementById('amountFilter').value = '';
    document.getElementById('dateFilter').value = '';
    document.getElementById('donationSearchFilter').value = '';
    filterDonations();
}
</script>
