<?php
$host = 'smtp.gmail.com';
$port = 587;

$connection = fsockopen($host, $port);

if (!$connection) {
    echo "Connection to SMTP server failed.";
} else {
    echo "Connection to SMTP server successful.";
    fclose($connection);
}
?>
