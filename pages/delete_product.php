
<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location:index.php?page=login");
    exit;
}

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $stmt = $pdo->prepare("
        DELETE FROM products
        WHERE product_id = ?
    ");

    $stmt->execute([$id]);
}

header("Location:index.php?page=admin_dashboard");
exit;

