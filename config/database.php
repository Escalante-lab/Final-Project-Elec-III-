<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'meat_inventory';

$conn = new mysqli($host, $user, $pass, $name);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');