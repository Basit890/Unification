<?php
$requests = $helpRequestController->getByUserId(Session::getUserId());
?>

<h2>My Requests</h2>

<?php if (empty($requests)): ?>
    <p>You haven't created any requests yet. <a href="index.php?page=create_request">Create one now</a>.</p>
<?php else: ?>
    <div class="grid">
        <?php foreach ($requests as $request): ?>
            <div class="card">
                <?php if ($request['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($request['image_path']); ?>" alt="Request Image" class="request-image">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($request['title']); ?></h3>
                <p><?php echo htmlspecialchars(substr($request['description'], 0, 150)) . '...'; ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($request['category']); ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge badge-<?php echo $request['status']; ?>">
                        <?php echo ucfirst($request['status']); ?>
                    </span>
                </p>
                <p><strong>Goal:</strong> ৳<?php echo number_format($request['goal_amount'], 2); ?></p>
                <p><strong>Raised:</strong> ৳<?php echo number_format($request['current_amount'], 2); ?></p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo min(($request['current_amount'] / $request['goal_amount']) * 100, 100); ?>%"></div>
                </div>
                <a href="index.php?page=view_request&id=<?php echo $request['id']; ?>" class="btn">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?> 