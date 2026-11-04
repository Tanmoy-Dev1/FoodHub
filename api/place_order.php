<?php
session_start();
header('Content-Type: application/json');
include '../db.php';

// Get POST data JSON format
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name'], $data['phone'], $data['address'], $data['payment_method'], $data['cart'])) {
    echo json_encode(["success" => false, "message" => "Invalid input data!"]);
    exit;
}

$name = $conn->real_escape_string($data['name']);
$phone = $conn->real_escape_string($data['phone']);
$address = $conn->real_escape_string($data['address']);
$payment = $conn->real_escape_string($data['payment_method']);
$cart = $data['cart'];

// User login thakle tar email-ti session theke nilam, na thakলে blank rakha hobe
$email = isset($_SESSION['email']) ? $conn->real_escape_string($_SESSION['email']) : '';

// Calculate total price + delivery fee (৳60)
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['qty'];
}
$total_price = $subtotal + 60;

// 1. Insert into orders table (Including user email)
$sql_order = "INSERT INTO orders (customer_name, email, phone, address, payment_method, total_price, status) 
              VALUES ('$name', '$email', '$phone', '$address', '$payment', '$total_price', 'Pending')";

if ($conn->query($sql_order) === TRUE) {
    $order_id = $conn->insert_id;

    // 2. Insert into order_items table
    foreach ($cart as $item) {
        $product_id = (int)$item['id'];
        $qty = (int)$item['qty'];
        $price = (float)$item['price'];

        $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                     VALUES ($order_id, $product_id, $qty, $price)";
        $conn->query($sql_item);
    }

    echo json_encode(["success" => true, "message" => "Order placed successfully!", "order_id" => $order_id]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>