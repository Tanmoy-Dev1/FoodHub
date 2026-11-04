<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $rating = (int)$_POST['rating'];
    $comment = $conn->real_escape_string(trim($_POST['comment']));

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $conn->query("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES ($user_id, $product_id, $rating, '$comment')");
    }
    
    // Redirect back to home or previous page
    header("Location: index.php?reviewed=success");
    exit;
}
?>