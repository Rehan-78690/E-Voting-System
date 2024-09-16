<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT candidate_id, name FROM candidates WHERE candidate_email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->bind_result($candidate_id, $candidate_name);
    $stmt->fetch();
    $stmt->close();
    
    if ($voter_id) {
        $_SESSION['candidate_id'] = $candidate_id;
        $_SESSION['voter_name'] = $candidate_name;
        header("Location: voting.php");
        exit();
    } else {
        echo "Invalid email or password.";
    }
    
    $conn->close();
}
?>
