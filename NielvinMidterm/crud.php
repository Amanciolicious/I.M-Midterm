<?php
session_start();
include("connection.php");

$connection = new Connection();
$con = $connection->OpenConnection();

// Add Product Logic
if (isset($_POST['add_product'])) {
    // Check if required fields are provided
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
    
    // Set default values for availability and date if not provided
    $availability = isset($_POST['availability']) ? $_POST['availability'] : 'In Stock'; // Default value
    $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');  // Default to today's date

    // Ensure all required fields are provided
    if (empty($product_name) || empty($category) || empty($price) || empty($quantity) || empty($availability) || empty($date)) {
        $_SESSION['status'] = "All fields are required!";
        header("Location: index.php");
        exit(0);
    }

    // Insert query to add the product
    $query = "INSERT INTO product_tbl (product_name, category, price, quantity, product_availability, date) 
              VALUES (:product_name, :category, :price, :quantity, :availability, :date)";
    $query_run = $con->prepare($query);

    $data = [
        ':product_name' => $product_name,
        ':category' => $category,
        ':price' => $price,
        ':quantity' => $quantity,
        ':availability' => $availability,
        ':date' => $date,
    ];

    $query_execute = $query_run->execute($data);

    if ($query_execute) {
        $_SESSION['status'] = "Product Added Successfully";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Product Not Added";
        header("Location: index.php");
        exit(0);
    }
}

// Update Product Logic
if (isset($_POST['update_product'])) {
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
    
    // Ensure availability and date are set or use defaults
    $availability = isset($_POST['availability']) ? $_POST['availability'] : 'In Stock';
    $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');

    // Ensure product ID is provided
    if (empty($product_id) || empty($product_name) || empty($category) || empty($price) || empty($quantity) || empty($availability) || empty($date)) {
        $_SESSION['status'] = "All fields are required!";
        header("Location: index.php");
        exit(0);
    }

    // Update query to modify the product details
    $query = "UPDATE product_tbl 
              SET product_name = :product_name, category = :category, price = :price, quantity = :quantity, 
                  product_availability = :availability, date = :date 
              WHERE id = :product_id";
    $query_run = $con->prepare($query);

    $data = [
        ':product_name' => $product_name,
        ':category' => $category,
        ':price' => $price,
        ':quantity' => $quantity,
        ':availability' => $availability,
        ':date' => $date,
        ':product_id' => $product_id,
    ];

    $query_execute = $query_run->execute($data);

    if ($query_execute) {
        $_SESSION['status'] = "Product Updated Successfully";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Product Not Updated";
        header("Location: index.php");
        exit(0);
    }
}

// Delete Product Logic
if (isset($_POST['delete_product'])) {
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';

    // Ensure product ID is provided
    if (empty($product_id)) {
        $_SESSION['status'] = "Product ID is required!";
        header("Location: index.php");
        exit(0);
    }

    // Delete query to remove the product
    $query = "DELETE FROM product_tbl WHERE id = :product_id";
    $query_run = $con->prepare($query);

    $data = [':product_id' => $product_id];

    $query_execute = $query_run->execute($data);

    if ($query_execute) {
        $_SESSION['status'] = "Product Deleted Successfully";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Product Not Deleted";
        header("Location: index.php");
        exit(0);
    }
}

// Add Category Logic
if (isset($_POST['add_category'])) {
    $cat_name = isset($_POST['cat_name']) ? $_POST['cat_name'] : '';

    // Ensure category name is provided
    if (empty($cat_name)) {
        $_SESSION['status'] = "Category Name is required!";
        header("Location: index.php");
        exit(0);
    }

    // Insert query to add a new category
    $query = "INSERT INTO cat_tbl (cat_name) VALUES (:cat_name)";
    $query_run = $con->prepare($query);

    $data = [':cat_name' => $cat_name];

    $query_execute = $query_run->execute($data);

    if ($query_execute) {
        $_SESSION['status'] = "Category Added Successfully";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Category Not Added";
        header("Location: index.php");
        exit(0);
    }
}

// Mark Pending Order as Done Logic
if (isset($_POST['mark_order_done'])) {
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';

    // Ensure order ID is provided
    if (empty($order_id)) {
        $_SESSION['status'] = "Order ID is required!";
        header("Location: index.php");
        exit(0);
    }

    // Update query to mark the order as completed
    $query = "UPDATE orders SET status = 'Completed' WHERE id = :order_id";
    $query_run = $con->prepare($query);

    $data = [':order_id' => $order_id];

    $query_execute = $query_run->execute($data);

    if ($query_execute) {
        $_SESSION['status'] = "Order Marked as Done";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Order Not Updated";
        header("Location: index.php");
        exit(0);
    }
}

// Delete Pending Order Logic
if (isset($_POST['delete_order'])) {
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';

    // Ensure order ID is provided
    if (empty($order_id)) {
        $_SESSION['status'] = "Order ID is required!";
        header("Location: index.php");
        exit(0);
    }

    // Delete query to remove the order
    $query = "DELETE FROM orders WHERE id = :order_id";
    $query_run = $con->prepare($query);

    $data = [':order_id' => $order_id];

    $query_execute = $query_run->execute($data);

    if ($query_execute) {
        $_SESSION['status'] = "Order Deleted Successfully";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['status'] = "Order Not Deleted";
        header("Location: index.php");
        exit(0);
    }
}
?>