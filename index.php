<?php
session_start();
require_once 'db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'login';
$publicPages = ['login', 'register'];

$isAdminPage = in_array($page, ['admin_dashboard', 'add_product', 'edit_product', 'delete_product']);
$isKasirPage = in_array($page, ['kasir_dashboard', 'update_order']);

// Proteksi Halaman
if (!in_array($page, $publicPages)) {
    if ($isAdminPage && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
        header('Location: index.php?page=login');
        exit;
    }
    if ($isKasirPage && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kasir')) {
        header('Location: index.php?page=login');
        exit;
    }
    if (!$isAdminPage && !$isKasirPage && !isset($_SESSION['customer_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
}

$isStaf = ($isAdminPage || $isKasirPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodel Platform</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="background: #F8FAFC; margin: 0; padding: 0; font-family: 'Inter', sans-serif;">

<?php if ($isStaf): ?>
    <div style="display: block !important; position: static !important; max-width: 1200px; margin: 0 auto; padding: 40px 20px; box-sizing: border-box;">
        <?php include "pages/{$page}.php"; ?>
    </div>
<?php else: ?>
    <div class="app-viewport" style="margin: 0 auto; position: relative;">
        <?php include "pages/{$page}.php"; ?>
    </div>
<?php endif; ?>

</body>
</html>