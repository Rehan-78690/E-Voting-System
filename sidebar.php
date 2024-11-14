 <!-- Sidebar -->
    <div class="sidebar closed" id="sidebar">
    <h5>Dashboard Menu</h5>
    <a href="welcome.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'welcome.php' ? 'active-link' : ''; ?>">Dashboard</a>
    <a href="manage%20users/approval_requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'approval_requests.php' ? 'active-link' : ''; ?>">Approval Requests</a>
    <a href="manage%20users/manage%20candidates/manage_candidates.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_candidates.php' ? 'active-link' : ''; ?>">Candidate Management</a>
    <a href="admin_profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_profile.php' ? 'active-link' : ''; ?>">Profile Management</a>
    <a href="document_verification.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'document_verification.php' ? 'active-link' : ''; ?>">Document Verification</a>
    <a href="manage%20users/symbol_allocation.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'symbol_allocation.php' ? 'active-link' : ''; ?>">Symbol Allocation</a>
    <a href="manage_feedback.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_feedback.php' ? 'active-link' : ''; ?>">Feedback Management</a>
    <a href="elections/election_settings/election_settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'election_settings.php' ? 'active-link' : ''; ?>">Settings</a>
    <a href="logout.php">Sign Out</a>
</div>