<?php
// pages/login.php
$error = '';
$msg = isset($_GET['msg']) && $_GET['msg'] == 'registered' ? 'Registration successful! Please log in.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // --- GMAIL VALIDATION ---
    if (!str_ends_with(strtolower($email), '@gmail.com')) {
        $error = "Please use a valid @gmail.com account.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['customer_id'] = $user['customer_id'];
            $_SESSION['customer_name'] = $user['name']; 
            
            header('Location: index.php?page=home');
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div style="padding: 30px; height: 100%; display: flex; flex-direction: column; justify-content: center;">
    <div style="text-align: center; margin-bottom: 50px;">
        <h1 style="color: var(--dark); margin: 0;">Log In</h1>
        <p style="color: var(--gray); margin-top: 10px;">Welcome back to Halal Lab!</p>
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

    <form method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Email</label>
            <input type="email" name="email" pattern=".+@gmail\.com$" title="Please enter a valid @gmail.com address" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Password</label>
            <input type="password" name="password" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <button type="submit" class="btn-orange" style="margin-top: 20px;">Log In</button>
    </form>

    <p style="text-align: center; margin-top: 40px; font-size: 14px; color: var(--gray);">
        Don't have an account? <a href="index.php?page=register" style="color: var(--primary); font-weight: bold; text-decoration: none;">Sign Up</a>
    </p>
</div>