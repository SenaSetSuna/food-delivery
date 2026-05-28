<?php
session_start();

// Include your database connection at the very beginning of the app
require_once 'db.php'; 

// Look at the URL (e.g., index.php?page=home). If it's empty, default to 'login'.
$page = isset($_GET['page']) ? $_GET['page'] : 'login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Foodel Delivery</title>
    
    <style>
        :root {
            --primary: #FF7622;
            --dark: #323643;
            --gray: #98A8B8;
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e9ecef;
            height: 100vh;
            display: flex;
            justify-content: center;
        }

        #app-container {
            width: 100%;
            max-width: 480px; /* Mobile App dimensions */
            background: #ffffff;
            height: 100%;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }

        .scroll-area {
            height: 100%;
            overflow-y: auto;
        }
        
        /* Hide scrollbar for clean app-like look */
        .scroll-area::-webkit-scrollbar, .scroll-hide::-webkit-scrollbar { 
            display: none; 
        }

        .btn-orange {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
        }
        
        .btn-orange:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

    <div id="app-container">
        <?php
        // ==========================================
        // THE ROUTER (Controls which page is loaded)
        // ==========================================
        switch ($page) {
            
            // Core Pages
            case 'home':
                include 'pages/home.php';
                break;
            case 'login':
                include 'pages/login.php';
                break;
            case 'register':
                include 'pages/register.php';
                break;
                
            // User Pages
            case 'profile':
                include 'pages/profile.php';
                break;
            case 'edit_profile':
                include 'pages/edit_profile.php';
                break;
            case 'cart':
                include 'pages/cart.php';
                break;
                
            // Application Details
            case 'info':
                include 'pages/info.php';
                break;
            case 'app_info':
                include 'pages/app_info.php';
                break;
                
            // Seller Pages
            case 'seller_dashboard':
                include 'pages/seller_dashboard.php';
                break;
            case 'admin_dashboard':
                include 'pages/admin_dashboard.php';
                break;
                
            // Fallback (If someone types a wrong URL)
            default:
                include 'pages/login.php';
                break;
        }
        ?>
    </div>

</body>
</html>