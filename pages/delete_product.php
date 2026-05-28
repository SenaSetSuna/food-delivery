<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    
    // Eksekusi penghapusan berbasis primary key produk
    $stmtDelete = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmtDelete->execute([$productId]);
}

header("Location: index.php?page=admin_dashboard");
exit;