<?php
header('Content-Type: application/json');
include '../db.php';

$sql = "SELECT p.*, c.slug as category FROM products p JOIN categories c ON p.category_id = c.id";
$result = $conn->query($sql);

$menuItems = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $menuItems[] = [
            "id" => (int)$row['id'],
            "name" => $row['name'],
            "category" => $row['category'],
            "price" => (float)$row['price'],
            "rating" => (float)$row['rating'],
            "image" => $row['image'],
            "desc" => $row['description']
        ];
    }
}

echo json_encode($menuItems);
$conn->close();
?>