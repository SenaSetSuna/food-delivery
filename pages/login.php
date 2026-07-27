<?php
$error = '';
$msg = isset($_GET['msg']) && $_GET['msg'] == 'registered' ? 'Registration successful! Please log in.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!str_ends_with(strtolower($email), '@gmail.com')) {
        $error = "Please use a valid @gmail.com account.";
    } else {
        
        // 1. CHECK CUSTOMER TABLE FIRST
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($password, $customer['password'])) {
            // It's a Customer!
            $_SESSION['customer_id'] = $customer['id_user'];
            $_SESSION['customer_name'] = $customer['name']; 
            $_SESSION['role'] = 'customer';
            
            header('Location: index.php?page=home');
            exit;
        } 
        
        // 2. IF NOT A CUSTOMER, CHECK SELLER TABLE
        $stmt2 = $pdo->prepare("SELECT * FROM User_Seller WHERE email = ?");
        $stmt2->execute([$email]);
        $seller = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($seller && password_verify($password, $seller['password'])) {
            // It's a Seller!
            $_SESSION['seller_id'] = $seller['id_seller'];
            $_SESSION['shopname'] = $seller['shopname'];
            $_SESSION['role'] = 'seller';
            
            // Send them to the Seller Dashboard instead of the customer home
            header('Location: index.php?page=seller_dashboard');
            exit;
        }

        // 3. IF NOT A SELLER, CHECK FOR KASIR
        $stmt3 = $pdo->prepare("SELECT * FROM User_kasir WHERE email = ?");
        $stmt3->execute([$email]);
        $kasir = $stmt3->fetch(PDO::FETCH_ASSOC);

        if ($kasir && password_verify($password, $kasir['password'])) {
            $_SESSION['kasir_id'] = $kasir['id_kasir'];
            $_SESSION['username'] = $kasir['name'];
            $_SESSION['role'] = 'kasir';
            header('Location: index.php?page=kasir_dashboard');
            exit;
        }

        // 4. IF NOT A KASIR, CHECK FOR ADMIN
        $stmt4 = $pdo->prepare("SELECT * FROM User_admin WHERE email = ?");
        $stmt3->execute([$email]);
        $admin = $stmt3->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['role'] = 'admin';
            header('Location: index.php?page=admin_dashboard');
            exit;
        }

        // 5. IF NEITHER MATCHES
        $error = "Invalid email or password.";
    }
}
?>

<div class="scroll-area" style="width: 100%; height: 100%; padding: 30px 20px; display: flex; justify-content: center; align-items: center; box-sizing: border-box;">
    <div style="width: min(100%, 420px); margin: 0 auto; padding: 0 12px; box-sizing: border-box;">
        <div style="text-align: center; margin-bottom: 50px;">
            <div style="font-size: 50px; margin-bottom: 10px;">🍔</div>
        <h1 style="color: var(--dark); margin: 0;">Log In</h1>
        <p style="color: var(--gray); margin-top: 10px;">Welcome back to Foodel!</p>
    </div>

    <?php if ($msg): ?>
        <div style="background: #E5F6DF; color: #1E4620; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px;">
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div style="background: #FFE5E5; color: #D8000C; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
        <div style="width: 100%;">
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">EMAIL</label>
            <input type="email" name="email" pattern=".+@gmail\.com$" required style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
        </div>

        <div style="width: 100%;">
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">PASSWORD</label>
            <input type="password" name="password" required style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
        </div>

        <button type="submit" class="btn-orange" style="margin-top: 20px;">Log In</button>
    </form>

    <p style="text-align: center; margin-top: 30px; font-size: 14px; color: var(--gray);">
        Don't have an account? <a href="index.php?page=register" style="color: var(--primary); font-weight: bold; text-decoration: none;">Sign Up</a>
    </p>
    </div>
</div>