<h2>Pending Requests for Approval</h2>

<?php
$pendingRequests = $helpRequestController->getPendingRequests();

if (empty($pendingRequests)): ?>
    <div class="alert alert-success">
        <p>No pending requests to review. All requests have been processed!</p>
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($pendingRequests as $request): ?>
            <div class="card">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($request['title']); ?></h3>
                    <span class="badge badge-pending">Pending Approval</span>
                </div>
                
                <div class="card-body">
                    <p><strong>Requested by:</strong> <?php echo htmlspecialchars($request['user_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($request['user_email']); ?></p>
                    <p><strong>Category:</strong> <?php echo ucfirst(htmlspecialchars($request['category'])); ?></p>
                    <p><strong>Goal Amount:</strong> ৳<?php echo number_format($request['goal_amount'], 2); ?></p>
                    <p><strong>Created:</strong> <?php echo date('M j, Y', strtotime($request['created_at'])); ?></p>
                    
                    <div class="description">
                        <strong>Description:</strong>
                        <p><?php echo nl2br(htmlspecialchars($request['description'])); ?></p>
                    </div>
                    
                    <?php if ($request['image_path']): ?>
                        <div class="request-image">
                            <img src="<?php echo htmlspecialchars($request['image_path']); ?>" alt="Request Image">
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($request['document_path']): ?>
                        <div class="document-link">
                            <a href="<?php echo htmlspecialchars($request['document_path']); ?>" target="_blank" class="btn btn-secondary">
                                📄 View Document
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <form method="POST" class="approval-form">
                        <input type="hidden" name="action" value="approve_request">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        
                        <div class="form-group">
                            <label for="admin_notes_<?php echo $request['id']; ?>">Admin Notes (Optional):</label>
                            <textarea name="admin_notes" id="admin_notes_<?php echo $request['id']; ?>" rows="3" placeholder="Add any notes about this approval..."></textarea>
                        </div>
                        
                        <div class="approval-buttons">
                            <button type="submit" class="btn btn-success">✅ Approve Request</button>
                            
                            <button type="button" class="btn btn-danger" onclick="rejectRequest(<?php echo $request['id']; ?>)">
                                ❌ Reject Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    
    <form id="rejectForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="reject_request">
        <input type="hidden" name="request_id" id="reject_request_id">
        <input type="hidden" name="admin_notes" id="reject_admin_notes">
    </form>
    
    <script>
    function rejectRequest(requestId) {
        const notes = prompt('Please provide a reason for rejection (optional):');
        document.getElementById('reject_request_id').value = requestId;
        document.getElementById('reject_admin_notes').value = notes || '';
        document.getElementById('rejectForm').submit();
    }
    </script>
<?php endif; ?>

<style>
.approval-form {
    margin-top: 1rem;
}

.approval-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.btn-success {
    background: linear-gradient(45deg, #00a651, #008f45);
}

.btn-danger {
    background: linear-gradient(45deg, #ff6b6b, #ee5a24);
}

.document-link {
    margin-top: 1rem;
}

.request-image img {
    max-width: 100%;
    height: auto;
    border-radius: var(--border-radius);
    margin-top: 1rem;
}
</style> 