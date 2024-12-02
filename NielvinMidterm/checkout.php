<?php
session_start();
include('connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->OpenConnection();

$cart_items = $_SESSION['cart'] ?? [];
$total_price = 0;

foreach ($cart_items as $product_id => $quantity) {
    $stmt = $pdo->prepare("SELECT * FROM product_tbl WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $total_price += $product['price'] * $quantity;
    }
}

// Process the order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = $_POST['shipping_address'];
    $phone_number = $_POST['phone_number'];

    // Insert the order into the orders table
    $user_id = $_SESSION['user_id']; // Assumes user_id is stored in the session
    $order_status = 'pending';
    $created_at = date('Y-m-d H:i:s'); // Current timestamp for the order

    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_price, status, created_at, shipping_address, phone_number) 
        VALUES (:user_id, :total_price, :status, :created_at, :shipping_address, :phone_number)
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':total_price' => $total_price,
        ':status' => $order_status,
        ':created_at' => $created_at,
        ':shipping_address' => $shipping_address,
        ':phone_number' => $phone_number
    ]);

    // Fetch the last inserted order ID
    $order_id = $pdo->lastInsertId();

    // Insert order items into the order_items table
    foreach ($cart_items as $product_id => $quantity) {
        $stmt = $pdo->prepare("SELECT * FROM product_tbl WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES (:order_id, :product_id, :quantity, :price)
            ");
            $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':quantity' => $quantity,
                ':price' => $product['price']
            ]);
        }
    }

    // Clear cart after successful order
    $_SESSION['cart'] = [];
    header("Location: landing.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Checkout</h2>

    <h4>Order Summary</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $product_id => $quantity): ?>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM product_tbl WHERE id = :id");
                $stmt->execute([':id' => $product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td><?= $quantity ?></td>
                        <td>$<?= number_format($product['price'] * $quantity, 2) ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Grand Total</th>
                <th>$<?= number_format($total_price, 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <h4>Shipping Information</h4>
    <form method="POST">
        <div class="mb-3">
            <label for="shipping_address" class="form-label">Shipping Address</label>
            <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
        </div>
        <button type="submit" class="btn btn-success">Place Order</button>
    </form>
</div>
</body>
</html>
