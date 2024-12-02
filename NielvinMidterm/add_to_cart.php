<?php
session_start();
include('connection.php');

$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'];
$quantity = $data['quantity'];

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart or update quantity
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

echo json_encode(['success' => true]);
