<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'foodhub_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

$conn->set_charset("utf8");
?>