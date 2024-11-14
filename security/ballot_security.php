<?php
// Start a secure session
function start_secure_session() {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => 'localhost',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// Regenerate session ID
function regenerate_session_id() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

// Generate CSRF token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
}

// Sanitize input
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Prevent double voting using a lock mechanism
function prevent_double_voting() {
    if (isset($_SESSION['has_voted']) && $_SESSION['has_voted'] === true) {
        die("You have already voted. Voting again is not allowed.");
    }
}

// Lock the voting mechanism after a successful vote
function lock_voting() {
    $_SESSION['has_voted'] = true;
}

// Log user IP for tracking
function log_user_ip() {
    if (!isset($_SESSION['user_ip'])) {
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    }
    if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
        die("Session hijacking attempt detected.");
    }
}

// Implement rate limiting to prevent rapid requests
function rate_limiting() {
    $max_requests = 5;
    $time_window = 60; // In seconds

    if (!isset($_SESSION['request_count'])) {
        $_SESSION['request_count'] = 1;
        $_SESSION['first_request_time'] = time();
    } else {
        $_SESSION['request_count']++;
        if ($_SESSION['request_count'] > $max_requests && (time() - $_SESSION['first_request_time']) < $time_window) {
            die("Too many requests. Please try again later.");
        }
    }

    // Reset request count after time window
    if ((time() - $_SESSION['first_request_time']) > $time_window) {
        $_SESSION['request_count'] = 1;
        $_SESSION['first_request_time'] = time();
    }
}

// Secure the ballot page
function secure_ballot_page() {
    start_secure_session();
    regenerate_session_id();
    log_user_ip();
    rate_limiting();
    prevent_double_voting();
}
?>
