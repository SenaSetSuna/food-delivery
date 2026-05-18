<?php
// pages/edit_profile.php

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$error = '';

// 1. Fetch current data to pre-fill the form
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$_SESSION['customer_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Handle the Save button click
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Enforce the @gmail.com rule
    if (!str_ends_with(strtolower($email), '@gmail.com')) {
        $error = "Please use a valid @gmail.com account.";
    } else {
        try {
            // Update the database
            $updateStmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, address = ? WHERE customer_id = ?");
            $updateStmt->execute([$name, $email, $phone, $address, $_SESSION['customer_id']]);
            
            // Update the session variable so home.php greets them with their new name
            $_SESSION['customer_name'] = $name;
            
            // Send them back to the profile page with a success message
            header("Location: index.php?page=profile&msg=updated");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                $error = "That Gmail address is already registered to someone else!";
            } else {
                $error = "Update failed. Please try again.";
            }
        }
    }
}
?>

<div class="scroll-area" style="padding: 20px; height: 100%; display: flex; flex-direction: column;">
    
    <div style="display:flex; align-items:center; margin-bottom:30px; padding-top:10px;">
        <a href="index.php?page=profile" style="background:#ECF0F4; width:45px; height:45px; border-radius:50%; display:grid; place-items:center; text-decoration:none; color:#000; font-weight:bold;">&lt;</a>
        <h3 style="margin:0; color:var(--dark); margin-left: 20px;">Edit Profile</h3>
    </div>

    <?php if ($error): ?>
        <div style="background: #FFE5E5; color: #D8000C; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="display: flex; flex-direction: column; gap: 15px; flex-grow: 1;">
        
        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none; font-size: 15px; color: var(--dark); font-weight: 600;">
        </div>
        
        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" pattern=".+@gmail\.com$" title="Please enter a valid @gmail.com address" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none; font-size: 15px; color: var(--dark); font-weight: 600;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Phone Number</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none; font-size: 15px; color: var(--dark); font-weight: 600;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; text-transform: uppercase; font-weight: bold;">Delivery Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none; font-size: 15px; color: var(--dark); font-weight: 600;">
        </div>

        <div style="margin-top: auto; padding-top: 20px; padding-bottom: 20px;">
            <button type="submit" class="btn-orange">Save Changes</button>
        </div>
    </form>
</div>