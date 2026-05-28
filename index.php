<?php
session_start();
require_once 'db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'login';

$publicPages = ['login', 'register'];

if (
    !isset($_SESSION['customer_id']) &&
    !isset($_SESSION['role']) &&
    !in_array($page, $publicPages)
) {
    header('Location: index.php?page=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Food Delivery</title>

    <link rel="stylesheet" href="css/style.css">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap"
        rel="stylesheet"
    >
</head>

<body>

<div class="app-viewport">

    <?php

    $file = "pages/{$page}.php";

    if (file_exists($file)) {

        include $file;

    } else {

        echo "
        <div style='padding:40px;text-align:center;'>
            <h2>404 - Page Not Found</h2>
        </div>
        ";
    }

    ?>

</div>

</body>
</html>

