<?php
session_start();
include('connection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$connection = new Connection();
$pdo = $connection->OpenConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_completed'])) {
    $order_id = $_POST['order_id'];
    $query = "UPDATE orders SET status = 'completed' WHERE id = :order_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':order_id' => $order_id]);

    header("Location: index.php");
    exit;
}
?>
