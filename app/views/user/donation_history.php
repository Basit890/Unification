<?php
$donations = $donationController->getByUserId(Session::getUserId());
?>

<h2>My Donation History</h2>

<?php if (empty($donations)): ?>
    <p>You haven't made any donations yet.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Request</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donations as $donation): ?>
            <tr>
                <td><?php echo date('M j, Y', strtotime($donation['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($donation['title']); ?></td>
                <td>৳<?php echo number_format($donation['amount'], 2); ?></td>
                <td>
                    <a href="index.php?page=view_request&id=<?php echo $donation['request_id']; ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;">View Request</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?> 