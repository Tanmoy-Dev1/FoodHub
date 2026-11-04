<?php
include '../db.php';
session_start();
// Jodi user login na thake ba tar role admin na hoy, tahole take login page-e ferot pathiye dibe
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['order_id'], $data['status'])) {
        $order_id = (int)$data['order_id'];
        $status = $conn->real_escape_string($data['status']);

        $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Order status updated successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid parameters!"]);
    }
}
$conn->close();
?>