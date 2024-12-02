<?php
session_start();
include('connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->OpenConnection();

// Fetch products for display
$query = "SELECT * FROM product_tbl";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #f6d365, #fda085);
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        .container {
            margin-top: 50px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            text-align: center;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }
        .card-text {
            font-size: 1rem;
            color: #555;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid #ddd;
        }
        .btn {
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(120deg, #6a11cb, #2575fc);
            color: white;
        }
        .btn-primary:hover {
            background: linear-gradient(120deg, #2575fc, #6a11cb);
            transform: scale(1.05);
        }
        .btn-success {
            background: #28a745;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-outline-danger {
            color: #e74c3c;
            border-color: #e74c3c;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-outline-danger:hover {
            background-color: #e74c3c;
            color: white;
        }
        .logout-button {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Kdot Shuffle Ordering Shop</h2>

    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                        <p class="card-text">Price: $<?= htmlspecialchars($product['price']) ?></p>
                        <p class="card-text">Quantity: <?= htmlspecialchars($product['quantity']) ?></p>
                        <form onsubmit="addToCart(event, <?= $product['id'] ?>)">
                            <input type="number" id="quantity-<?= $product['id'] ?>" class="form-control mb-2" min="1" max="<?= $product['quantity'] ?>" required>
                            <button type="button" class="btn btn-primary" onclick="addToCart(event, <?= $product['id'] ?>)">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="cart.php" class="btn btn-success mt-3">View Cart</a>
</div>

<script>
function addToCart(event, productId) {
    event.preventDefault();
    const quantity = document.getElementById('quantity-' + productId).value;

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Item added to cart successfully.");
        } else {
            alert("Failed to add item to cart.");
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<!-- Logout Button for Users -->
<div class="logout-button">
    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
</div>

</body>
</html>
