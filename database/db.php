<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datvexekhach";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Kết nối thất bại: " . mysqli_connect_errno());
}
?>
