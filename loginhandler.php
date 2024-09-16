<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT id, name FROM voters WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->bind_result($voter_id, $voter_name);
    $stmt->fetch();
    $stmt->close();
    
    if ($voter_id) {
        $_SESSION['voter_id'] = $voter_id;
        $_SESSION['voter_name'] = $voter_name;
        header("Location: voting.php");
        exit();
    } else {
        echo "Invalid email or password.";
    }
    
    $conn->close();
}
?>
