<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'kasir') {
    exit;
}

$id = $_GET['id'];
$status = $_GET['status'];

$stmt = $pdo->prepare("
    UPDATE orders
    SET status = ?
    WHERE order_id = ?
");

$stmt->execute([$status, $id]);

header("Location:index.php?page=kasir_dashboard");
exit;