<?php
session_start();
include('connection.php');

// Check if user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->OpenConnection();

// Fetch product categories for dropdown in forms
$categoryStmt = $pdo->query("SELECT * FROM cat_tbl");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all products and apply filters if search form is submitted
$sql = "SELECT p.*, c.cat_name FROM product_tbl p INNER JOIN cat_tbl c ON p.category = c.cat_id";
$params = [];

if (isset($_POST['search'])) {
    $conditions = [];

    if (!empty($_POST['product_name'])) {
        $conditions[] = "p.product_name LIKE :product_name";
        $params[':product_name'] = "%" . $_POST['product_name'] . "%";
    }

    if (!empty($_POST['category'])) {
        $conditions[] = "p.category = :category";
        $params[':category'] = $_POST['category'];
    }

    if (!empty($_POST['product_availability'])) {
        $conditions[] = "p.product_availability = :availability";
        $params[':availability'] = $_POST['product_availability'];
    }

    if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $conditions[] = "p.date BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $_POST['start_date'];
        $params[':end_date'] = $_POST['end_date'];
    }

    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
}

$sql .= " ORDER BY p.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Order History
$orderHistoryQuery = "SELECT * FROM orders WHERE status = 'completed' ORDER BY created_at DESC";
$orderHistory = $pdo->query($orderHistoryQuery)->fetchAll(PDO::FETCH_ASSOC);

// Fetch Pending Orders
$pendingOrdersQuery = "SELECT * FROM orders WHERE status = 'pending' ORDER BY created_at DESC";
$pendingOrders = $pdo->query($pendingOrdersQuery)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($_SESSION['status'])): ?>
    <div class="alert alert-info">
        <?= $_SESSION['status']; ?>
        <?php unset($_SESSION['status']); ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Kdot Shuffling Orders</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#viewOrderHistoryModal">Order History</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#pendingOrdersModal">Pending Orders</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
                </li>
                <li class="nav-item">
                    <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Product Filter Form -->
    <form class="row g-3 mb-4" method="POST" action="index.php">
        <div class="col-md-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" class="form-control" name="product_name" id="product_name">
        </div>
        <div class="col-md-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" name="category" id="category">
                <option value="">All</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['cat_id'] ?>"><?= $cat['cat_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="product_availability" class="form-label">Availability</label>
            <select class="form-select" name="product_availability" id="product_availability">
                <option value="">All</option>
                <option value="In Stock">In Stock</option>
                <option value="Out of Stock">Out of Stock</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" id="start_date">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" id="end_date">
        </div>
        <div class="col-md-12">
            <button type="submit" name="search" class="btn btn-outline-primary">Filter</button>
        </div>
    </form>
    
    <!-- Product List Table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Availability</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id']; ?></td>
                    <td><?= $product['product_name']; ?></td>
                    <td><?= $product['cat_name']; ?></td>
                    <td><?= $product['price']; ?></td>
                    <td><?= $product['quantity']; ?></td>
                    <td><?= $product['product_availability']; ?></td>
                    <td><?= $product['date']; ?></td>
                    <td>
                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $product['id'] ?>">Edit</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $product['id'] ?>">Delete</button>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?= $product['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="crud.php" method="POST">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" class="form-control" name="product_name" value="<?= $product['product_name'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category" required>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['cat_id'] ?>" <?= $cat['cat_id'] == $product['category'] ? 'selected' : '' ?>>
                                                    <?= $cat['cat_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" class="form-control" name="price" value="<?= $product['price'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" value="<?= $product['quantity'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Availability</label>
                                        <select class="form-select" name="product_availability" required>
                                            <option value="In Stock" <?= $product['product_availability'] == 'In Stock' ? 'selected' : '' ?>>In Stock</option>
                                            <option value="Out of Stock" <?= $product['product_availability'] == 'Out of Stock' ? 'selected' : '' ?>>Out of Stock</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="edit_product" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal<?= $product['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete the product "<?= $product['product_name'] ?>"?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="crud.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>
            <?php  ?>
        </tbody>
    </table>
</div>

<!-- Order History Modal -->
<div class="modal fade" id="viewOrderHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderHistory as $order): ?>
                            <tr>
                                <td><?= $order['id']; ?></td>
                                <td><?= $order['user_id']; ?></td>
                                <td><?= $order['total_price']; ?></td>
                                <td><?= $order['status']; ?></td>
                                <td><?= $order['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pending Orders Modal -->
<div class="modal fade" id="pendingOrdersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pending Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pendingOrders)): ?>
                            <?php foreach ($pendingOrders as $order): ?>
                                <tr>
                                    <td><?= $order['id']; ?></td>
                                    <td><?= $order['user_id']; ?></td>
                                    <td><?= $order['total_price']; ?></td>
                                    <td><?= $order['status']; ?></td>
                                    <td><?= $order['created_at']; ?></td>
                                    <td>
                                        <form method="POST" action="crud.php">
                                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                            <button type="submit" name="mark_order_done" class="btn btn-success btn-sm">Complete Order</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No Pending Orders</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Add Product -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="crud.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['cat_id'] ?>"><?= $cat['cat_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" class="form-control" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Availability</label>
                        <select class="form-select" name="product_availability" required>
                            <option value="In Stock">In Stock</option>
                            <option value="Out of Stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="crud.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="category_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
