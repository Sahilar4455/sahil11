<?php
$host = "localhost";
$dbname = "villa";
$username = "root";
$password = "12345";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>