<?php
session_start();
include('connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->OpenConnection();

$user_id = $_SESSION['user_id'];
$shipping_address = $_POST['shipping_address'];
$phone_number = $_POST['phone_number'];
$total_price = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $pdo->prepare("SELECT price FROM product_tbl WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_OBJ);
    $total_price += $product->price * $quantity;
}

// Insert order details
$query = "INSERT INTO orders (user_id, total_price, shipping_address, phone_number, status)
          VALUES (:user_id, :total_price, :shipping_address, :phone_number, 'pending')";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':user_id' => $user_id,
    ':total_price' => $total_price,
    ':shipping_address' => $shipping_address,
    ':phone_number' => $phone_number
]);
$order_id = $pdo->lastInsertId();

// Insert each item in cart into order_items
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                   VALUES (:order_id, :product_id, :quantity, :price)";
    $item_stmt = $pdo->prepare($item_query);
    $item_stmt->execute([
        ':order_id' => $order_id,
        ':product_id' => $product_id,
        ':quantity' => $quantity,
        ':price' => $product->price
    ]);
}

unset($_SESSION['cart']); // Clear cart after checkout
header("Location: landing.php");
exit;
