<?php
$error = '';
$msg = isset($_GET['msg']) && $_GET['msg'] == 'registered'
    ? 'Registration successful! Please log in.'
    : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!str_ends_with(strtolower($email), '@gmail.com')) {
        $error = "Please use a valid @gmail.com account.";
    } else {

        // ================= ADMIN LOGIN =================
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {

            $_SESSION['user_id'] = $admin['id_admin'];
            $_SESSION['user_name'] = $admin['name'];
            $_SESSION['role'] = 'admin';

            header("Location: index.php?page=admin_dashboard");
            exit;
        }

        // ================= KASIR LOGIN =================
        $stmt = $pdo->prepare("SELECT * FROM kasir WHERE email = ?");
        $stmt->execute([$email]);
        $kasir = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kasir && password_verify($password, $kasir['password'])) {

            $_SESSION['user_id'] = $kasir['kasir_id'];
            $_SESSION['user_name'] = $kasir['name'];
            $_SESSION['role'] = 'kasir';

            header("Location: index.php?page=kasir_dashboard");
            exit;
        }

        // ================= CUSTOMER LOGIN =================
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($password, $customer['password'])) {

            $_SESSION['customer_id'] = $customer['customer_id'];
            $_SESSION['customer_name'] = $customer['name'];
            $_SESSION['role'] = 'customer';

            header("Location: index.php?page=home");
            exit;
        }

        $error = "Invalid email or password.";
    }
}
?>

<div style="padding: 30px; height: 100%; display: flex; flex-direction: column; justify-content: center;">

    <div style="text-align: center; margin-bottom: 50px;">
        <h1 style="color: var(--dark); margin: 0;">Log In</h1>
        <p style="color: var(--gray); margin-top: 10px;">
            Welcome back to Foodel!
        </p>
    </div>

    <?php if ($msg): ?>
        <div style="background:#E5F6DF;color:#1E4620;padding:10px;border-radius:8px;margin-bottom:20px;text-align:center;">
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div style="background:#FFE5E5;color:#D8000C;padding:10px;border-radius:8px;margin-bottom:20px;text-align:center;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="display:flex;flex-direction:column;gap:20px;">

        <input type="email"
               name="email"
               placeholder="Email"
               required
               style="width:100%;padding:15px;border-radius:12px;border:1px solid #eee;background:#F6F6F6;">

        <input type="password"
               name="password"
               placeholder="Password"
               required
               style="width:100%;padding:15px;border-radius:12px;border:1px solid #eee;background:#F6F6F6;">

        <button type="submit" class="btn-orange">
            LOGIN
        </button>
    </form>

    <p style="text-align:center;margin-top:30px;">
        Don't have an account?
        <a href="index.php?page=register"
           style="color:var(--primary);font-weight:bold;text-decoration:none;">
            Register
        </a>
    </p>

</div>