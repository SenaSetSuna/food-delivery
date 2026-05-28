<?php
// Double-check they are logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $stmtDelete = $pdo->prepare("DELETE FROM customers WHERE id_user = ?");
    $stmtDelete->execute([$_SESSION['customer_id']]);

    session_unset();
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

// Fetch the user's data from the database
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id_user = ?");
$stmt->execute([$_SESSION['customer_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fallback in case user is deleted but session remains
if (!$user) {
    header("Location: logout.php");
    exit;
}

$msg = isset($_GET['msg']) && $_GET['msg'] == 'updated' ? 'Profile updated successfully!' : '';
?>

<div style="height: 100%; display: flex; flex-direction: column;">
    
    <div class="scroll-area" style="padding: 30px 20px 100px 20px;">
        
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
            <h2 style="margin: 0; color: var(--dark); font-size: 20px;">Personal Profile</h2>
        </div>

        <?php if ($msg): ?>
            <div style="background: #E5F6DF; color: #1E4620; padding: 12px; border-radius: 12px; margin-bottom: 25px; text-align: center; font-size: 14px; font-weight: bold;">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 30px;">
            
            <?php if (!empty($user['user_profile'])): ?>
                <img src="<?= htmlspecialchars($user['user_profile']) ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; box-shadow: 0 10px 20px rgba(0,0,0, 0.1);">
            <?php else: ?>
                <div style="width: 100px; height: 100px; background: #FFD27C; border-radius: 50%; display: grid; place-items: center; font-size: 40px; font-weight: bold; color: var(--primary); margin-bottom: 15px; box-shadow: 0 10px 20px rgba(255, 118, 34, 0.2);">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            
            <h3 style="margin: 0; color: var(--dark); font-size: 22px;"><?= htmlspecialchars($user['name']) ?></h3>
            <p style="margin: 5px 0 0 0; color: var(--gray); font-size: 14px;">@<?= htmlspecialchars($user['username']) ?></p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 35px;">
            <div style="background: #F6F6F6; padding: 18px; border-radius: 15px; border: 1px solid #eee;">
                <small style="color: var(--gray); font-size: 11px; font-weight: bold;">EMAIL ADDRESS</small>
                <div style="color: var(--dark); font-weight: 600; margin-top: 5px; font-size: 15px;">
                    <?= htmlspecialchars($user['email']) ?>
                </div>
            </div>

            <div style="background: #F6F6F6; padding: 18px; border-radius: 15px; border: 1px solid #eee;">
                <small style="color: var(--gray); font-size: 11px; font-weight: bold;">PHONE NUMBER</small>
                <div style="color: var(--dark); font-weight: 600; margin-top: 5px; font-size: 15px;">
                    <?= htmlspecialchars($user['phone']) ?>
                </div>
            </div>

            <div style="background: #F6F6F6; padding: 18px; border-radius: 15px; border: 1px solid #eee;">
                <small style="color: var(--gray); font-size: 11px; font-weight: bold;">DELIVERY ADDRESS</small>
                <div style="color: var(--dark); font-weight: 600; margin-top: 5px; font-size: 15px; line-height: 1.4;">
                    <?= htmlspecialchars($user['address']) ?>
                </div>
            </div>
        </div>

        <a href="index.php?page=edit_profile" style="text-decoration: none;">
            <button class="btn-orange" style="margin-bottom: 15px;">
                Edit Profile
            </button>
        </a>

        <form method="post" style="margin-bottom: 15px;">
            <button type="submit" name="delete_account" class="btn-orange" style="background: #FFF1F1; color: #FF4B4B; border: 1px solid #FFE0E0;" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                Delete Account
            </button>
        </form>

        <a href="logout.php" style="text-decoration: none;">
            <button class="btn-orange" style="background: #FFF1F1; color: #FF4B4B; border: 1px solid #FFE0E0; margin-bottom: 20px;">
                Log Out
            </button>
        </a>
    </div>

    <nav style="height: 85px; background: #fff; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #f0f0f0; border-radius: 25px 25px 0 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.03); position: absolute; bottom: 0; width: 100%; max-width: 480px;">
        <div onclick="window.location.href='index.php?page=home'" style="color: #98A8B8; font-size: 26px; cursor: pointer;">🏠</div>
        <div onclick="window.location.href='index.php?page=app_info'" style="color: #98A8B8; font-size: 26px; cursor: pointer;">ℹ️</div>
        <div style="color: var(--primary); font-size: 26px; cursor: pointer;">👤</div>
    </nav>
</div>