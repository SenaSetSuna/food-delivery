<?php
// pages/register.php
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // --- GMAIL VALIDATION ---
    // Convert email to lowercase and check if it ends with @gmail.com
    if (!str_ends_with(strtolower($email), '@gmail.com')) {
        $error = "You need a valid @gmail.com account to register.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO customers (name, email, password, address, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $address, $phone]);
            
            // Redirect to login after successful registration
            header('Location: index.php?page=login&msg=registered');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                $error = "That Gmail address is already registered!";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<div class="scroll-area" style="padding: 30px; display: flex; flex-direction: column; justify-content: center;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: var(--dark); margin: 0;">Sign Up</h1>
        <p style="color: var(--gray); margin-top: 10px;">Please create an account to start ordering food.</p>
    </div>

    <?php if ($error): ?>
        <div style="background: #FFE5E5; color: #D8000C; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Full Name</label>
            <input type="text" name="name" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>
        
        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Email</label>
            <input type="email" name="email" pattern=".+@gmail\.com$" title="Please enter a valid @gmail.com address" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Password</label>
            <input type="password" name="password" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Phone Number</label>
            <input type="text" name="phone" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Delivery Address</label>
            <input type="text" name="address" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <button type="submit" class="btn-orange" style="margin-top: 20px;">Register</button>
    </form>

    <p style="text-align: center; margin-top: 30px; font-size: 14px; color: var(--gray);">
        Already have an account? <a href="index.php?page=login" style="color: var(--primary); font-weight: bold; text-decoration: none;">Log In</a>
    </p>
</div>