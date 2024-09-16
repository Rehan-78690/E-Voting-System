<?php
$hostname='localhost';
$username='root';
$password='';
$dbname='voting';
$conn=mysqli_connect($hostname,$username,$password,$dbname);
if(!$conn){
    echo'check your connection';
}

?>