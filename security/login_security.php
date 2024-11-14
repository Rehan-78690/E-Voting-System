<?php
// Start a secure session
function start_secure_session() {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => 'localhost', // Replace with your domain
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

// Initialize login attempts
function init_login_attempts() {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
}

// Increase login attempts
function increase_login_attempts() {
    if (isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
    }
}

function reset_login_attempts() {
    $_SESSION['login_attempts'] = 0;
    unset($_SESSION['last_attempt_time']);
}


function check_login_attempts($max_attempts = 5, $lockout_duration = 500) {
    init_login_attempts();

    if (isset($_SESSION['last_attempt_time'])) {
        $time_diff = time() - $_SESSION['last_attempt_time'];

        if ($time_diff > $lockout_duration) {
            reset_login_attempts();
        }
    }

    if ($_SESSION['login_attempts'] >= $max_attempts) {
        $remaining_time = $lockout_duration - (time() - $_SESSION['last_attempt_time']);
        die("Too many login attempts. Try again in " . $remaining_time . " seconds.");
    }
}

function secure_login($email, $password, $table, $email_field, $id_field, $conn) {
    $email = sanitize_input($email);
    check_login_attempts(); 


    $sql = "SELECT $id_field, $email_field, password FROM $table WHERE $email_field = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                regenerate_session_id();
                $_SESSION['logged_in'] = true;
                $_SESSION['email'] = $user[$email_field];
                $_SESSION['user_id'] = $user[$id_field]; 

                reset_login_attempts();
                return true;
            } else {
                increase_login_attempts();
                return false;
            }
        } else {
            increase_login_attempts();
            return false;
        }

        $stmt->close();
    } else {
        die("Failed to prepare SQL statement: " . $conn->error);
    }
}
?>
