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
            ?>
            
            <div class="summary-card">
                <h4>🏆 Top Donors</h4>
                <?php if (empty($topDonors)): ?>
                    <p class="no-data">No donations yet</p>
                <?php else: ?>
                    <div class="donor-list">
                        <?php foreach ($topDonors as $index => $donor): ?>
                            <div class="donor-item">
                                <div class="donor-rank">#<?php echo $index + 1; ?></div>
                                <div class="donor-details">
                                    <strong><?php echo htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']); ?></strong>
                                    <small><?php echo $donor['donation_count']; ?> donations</small>
                                </div>
                                <div class="donor-amount">৳<?php echo number_format($donor['total_donated'], 2); ?></div>
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
            ?>
            
            <div class="summary-card">
                <h4>📈 Last 7 Days</h4>
                <?php if (empty($recentDonations)): ?>
                    <p class="no-data">No recent donations</p>
                <?php else: ?>
                    <div class="daily-stats">
                        <?php foreach ($recentDonations as $day): ?>
                            <div class="daily-item">
                                <div class="daily-date"><?php echo date('M j', strtotime($day['date'])); ?></div>
                                <div class="daily-amount">৳<?php echo number_format($day['daily_total'], 2); ?></div>
                                <div class="daily-count"><?php echo $day['daily_count']; ?> donations</div>
                            </div>
                        <?php endforeach; ?>
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

/* Table Styles */
