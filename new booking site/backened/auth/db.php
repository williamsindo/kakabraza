<?php
$servername = "127.0.0.1";
$username = "root";
$password = "root";
$dbname = "new booking site";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname,$port );

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>  