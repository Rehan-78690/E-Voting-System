<?php
session_start();
// Unset all session variables
$_SESSION = array();
session_destroy();
header("Location: ../candidate/candidate.php");
exit();
?>
