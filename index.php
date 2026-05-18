<?php
// index.php
session_start();
require_once 'db.php'; // Load the database

// Get the requested page, default to 'login' if not logged in
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// If user is not logged in, only allow them to see login or register pages
if (!isset($_SESSION['customer_id']) && !in_array($page, ['login', 'register'])) {
    header('Location: index.php?page=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Delivery</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-viewport">
        <?php
        // Load the requested page
        $file = "pages/{$page}.php";
        if (file_exists($file)) {
            include $file;
        } else {
            echo "<h2 style='text-align:center; margin-top:50px;'>404 - Page Not Found</h2>";
        }
        ?>
    </div>
</body>
</html>