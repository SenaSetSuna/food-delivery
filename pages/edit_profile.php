<?php
if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$error = '';
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id_user = ?");
$stmt->execute([$_SESSION['customer_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profile_pic = $user['user_profile']; 

    // --- DIRECT-TO-DATABASE IMAGE LOGIC ---
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file_error = $_FILES['profile_pic']['error'];
        
        if ($file_error === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['profile_pic']['tmp_name'];
            $mime_type = mime_content_type($tmp_name); 
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
            
            if (in_array($mime_type, $allowed_mimes)) {
                $image_data = file_get_contents($tmp_name);
                $base64_string = 'data:' . $mime_type . ';base64,' . base64_encode($image_data);
                $profile_pic = $base64_string; 
            } else {
                $error = "UPLOAD FAILED: Only JPG, PNG, and WebP are allowed.";
            }
        } elseif ($file_error === UPLOAD_ERR_INI_SIZE || $file_error === UPLOAD_ERR_FORM_SIZE) {
            $error = "UPLOAD FAILED: Image is larger than 2MB! Please select a smaller file.";
        } else {
            $error = "UPLOAD ERROR CODE: " . $file_error;
        }
    }

    if (!$error) {
        try {
            $updateStmt = $pdo->prepare("UPDATE customers SET name = ?, username = ?, email = ?, phone = ?, address = ?, user_profile = ? WHERE id_user = ?");
            $updateStmt->execute([$name, $username, $email, $phone, $address, $profile_pic, $_SESSION['customer_id']]);
            
            $_SESSION['customer_name'] = $name;
            header("Location: index.php?page=profile&msg=updated");
            exit;
        } catch (PDOException $e) {
            $error = "DATABASE ERROR: " . $e->getMessage();
        }
    }
}
?>

<div class="scroll-area" style="padding: 20px; height: 100%; display: flex; flex-direction: column;">
    <div style="display:flex; align-items:center; margin-bottom:20px; padding-top:10px;">
        <a href="index.php?page=profile" style="background:#ECF0F4; width:45px; height:45px; border-radius:50%; display:grid; place-items:center; text-decoration:none; color:#000; font-weight:bold;">&lt;</a>
        <h3 style="margin:0; color:var(--dark); margin-left: 20px;">Edit Profile</h3>
    </div>

    <?php if ($error): ?>
        <div style="background: #FFE5E5; color: #D8000C; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: bold; border: 1px solid #D8000C;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px; flex-grow: 1;">
        
        <div style="text-align: center; margin-bottom: 15px; padding-top: 10px;">
            
            <input type="file" id="profile_upload" name="profile_pic" accept="image/png, image/jpeg, image/webp" style="display: none;" onchange="previewImage(event)">
            
            <div onclick="document.getElementById('profile_upload').click()" style="cursor: pointer; position: relative; width: 100px; height: 100px; margin: 0 auto; display: inline-block;">
                
                <?php if (!empty($user['user_profile'])): ?>
                    <img id="profile-preview" src="<?= htmlspecialchars($user['user_profile']) ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: 0 8px 15px rgba(0,0,0,0.1);">
                    <div id="profile-initial" style="display: none; width: 100px; height: 100px; background: #FFD27C; border-radius: 50%; font-size: 40px; font-weight: bold; color: var(--primary); line-height: 100px; box-shadow: 0 8px 15px rgba(255, 118, 34, 0.2);"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                <?php else: ?>
                    <img id="profile-preview" src="" style="display: none; width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: 0 8px 15px rgba(0,0,0,0.1);">
                    <div id="profile-initial" style="width: 100px; height: 100px; background: #FFD27C; border-radius: 50%; font-size: 40px; font-weight: bold; color: var(--primary); line-height: 100px; box-shadow: 0 8px 15px rgba(255, 118, 34, 0.2);"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                <?php endif; ?>

                <div style="position: absolute; bottom: 0; right: -5px; background: var(--primary); color: white; width: 32px; height: 32px; border-radius: 50%; display: grid; place-items: center; border: 3px solid #fff; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    📷
                </div>
            </div>
            <div style="margin-top: 10px; font-size: 13px; color: var(--gray); font-weight: 600;">Tap to change photo</div>
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">FULL NAME</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">USERNAME</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>
        
        <div>
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">EMAIL</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">PHONE NUMBER</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">DELIVERY ADDRESS</label>
            <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" required style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; margin-top: 5px; outline: none;">
        </div>

        <div style="margin-top: auto; padding-top: 20px; padding-bottom: 20px;">
            <button type="submit" class="btn-orange">Save Changes</button>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgElement = document.getElementById('profile-preview');
            const initElement = document.getElementById('profile-initial');
            
            imgElement.src = e.target.result;
            imgElement.style.display = 'block';
            initElement.style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}
</script>