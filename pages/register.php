<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role']; // 'customer' or 'seller'
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $profile_pic = ''; 
    
    // --- BASE64 DIRECT-TO-DATABASE (Works for both Customers & Sellers) ---
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file_error = $_FILES['profile_pic']['error'];
        
        if ($file_error === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['profile_pic']['tmp_name'];
            $mime_type = mime_content_type($tmp_name);
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
            
            if (in_array($mime_type, $allowed_mimes)) {
                $image_data = file_get_contents($tmp_name);
                $profile_pic = 'data:' . $mime_type . ';base64,' . base64_encode($image_data);
            } else {
                $error = "UPLOAD FAILED: Only JPG, PNG, and WebP are allowed.";
            }
        } elseif ($file_error === UPLOAD_ERR_INI_SIZE || $file_error === UPLOAD_ERR_FORM_SIZE) {
            $error = "UPLOAD FAILED: Image is larger than 2MB!";
        } else {
            $error = "UPLOAD ERROR CODE: " . $file_error;
        }
    }

    if (!$error) {
        if (!str_ends_with(strtolower($email), '@gmail.com')) {
            $error = "You need a valid @gmail.com account to register.";
        } else {
            try {
                // Check which table we need to insert data into!
                if ($role === 'seller') {
                    $shopname = $_POST['shopname'];
                    $stmt = $pdo->prepare("INSERT INTO User_Seller (shopname, email, password, phone, seller_profile) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$shopname, $email, $password, $phone, $profile_pic]);
                } else {
                    $name = $_POST['name'];
                    $username = $_POST['username'];
                    $address = $_POST['address'];
                    $stmt = $pdo->prepare("INSERT INTO customers (name, username, email, password, address, phone, user_profile) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $username, $email, $password, $address, $phone, $profile_pic]);
                }
                
                header('Location: index.php?page=login&msg=registered');
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { 
                    $error = "That Email, Username, or Shop Name is already taken!";
                } else {
                    $error = "Registration failed. " . $e->getMessage();
                }
            }
        }
    }
}
?>

<div class="scroll-area" style="width: 100%; height: 100%; padding: 30px 20px; display: flex; justify-content: center; align-items: center; box-sizing: border-box;">
    <div style="width: min(100%, 420px); margin: 0 auto; padding: 0 12px; box-sizing: border-box;">
        <div style="display:flex; align-items:center; margin-bottom:20px;">
            <a href="index.php?page=login" style="background:#ECF0F4; min-width:45px; height:45px; border-radius:50%; display:grid; place-items:center; text-decoration:none; color:#000; font-weight:bold; font-size:18px;">&lt;</a>
        <div style="margin-left: 20px;">
            <h1 style="color: var(--dark); margin: 0; font-size: 24px;">Sign Up</h1>
            <p style="color: var(--gray); margin-top: 5px; font-size: 13px;">Create an account to start ordering.</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div style="background: #FFE5E5; color: #D8000C; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px; width: 100%;">
        
        <input type="hidden" name="role" id="role_input" value="customer">

        <div style="display: flex; gap: 10px; margin-bottom: 5px;">
            <div id="btn_customer" onclick="selectRole('customer')" style="flex: 1; text-align: center; padding: 12px; border-radius: 12px; background: var(--primary); color: white; font-weight: bold; font-size: 14px; cursor: pointer; transition: 0.3s;">
                👤 Customer
            </div>
            <div id="btn_seller" onclick="selectRole('seller')" style="flex: 1; text-align: center; padding: 12px; border-radius: 12px; background: #eee; color: var(--gray); font-weight: bold; font-size: 14px; cursor: pointer; transition: 0.3s;">
                🏪 Seller
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 10px; padding-top: 5px;">
            <input type="file" id="profile_upload" name="profile_pic" accept="image/png, image/jpeg, image/webp" style="display: none;" onchange="previewImage(event)">
            
            <div onclick="document.getElementById('profile_upload').click()" style="cursor: pointer; position: relative; width: 100px; height: 100px; margin: 0 auto; display: inline-block;">
                <img id="profile-preview" src="" style="display: none; width: 100px; height: 100px; border-radius: 50%; object-fit: cover; box-shadow: 0 8px 15px rgba(0,0,0,0.1);">
                <div id="profile-initial" style="width: 100px; height: 100px; background: #ECF0F4; border-radius: 50%; font-size: 45px; color: #98A8B8; display: grid; place-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">👤</div>
                <div style="position: absolute; bottom: 0; right: -5px; background: var(--primary); color: white; width: 32px; height: 32px; border-radius: 50%; display: grid; place-items: center; border: 3px solid #fff; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">📷</div>
            </div>
            <div style="margin-top: 10px; font-size: 13px; color: var(--gray); font-weight: 600;">Tap to upload photo</div>
        </div>

        <div id="customer_fields" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="color: var(--gray); font-size: 12px; font-weight: bold;">FULL NAME</label>
                <input type="text" id="inp_name" name="name" required style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
            </div>
            <div>
                <label style="color: var(--gray); font-size: 12px; font-weight: bold;">USERNAME</label>
                <input type="text" id="inp_username" name="username" required style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
            </div>
            <div>
                <label style="color: var(--gray); font-size: 12px; font-weight: bold;">DELIVERY ADDRESS</label>
                <input type="text" id="inp_address" name="address" required style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
            </div>
        </div>

        <div id="seller_fields" style="display: none; flex-direction: column; gap: 15px;">
            <div>
                <label style="color: var(--gray); font-size: 12px; font-weight: bold;">RESTAURANT / SHOP NAME</label>
                <input type="text" id="inp_shopname" name="shopname" style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
            </div>
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">EMAIL</label>
            <input type="email" name="email" pattern=".+@gmail\.com$" title="Please enter a valid @gmail.com address" required style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">PHONE NUMBER</label>
            <input type="text" name="phone" required style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
        </div>

        <div>
            <label style="color: var(--gray); font-size: 12px; font-weight: bold;">PASSWORD</label>
            <input type="password" name="password" required style="display: block; width: 100%; margin: 5px 0 0; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; box-sizing: border-box;">
        </div>

        <button type="submit" class="btn-orange" style="margin-top: 10px;">Register Account</button>
    </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-preview').src = e.target.result;
            document.getElementById('profile-preview').style.display = 'block';
            document.getElementById('profile-initial').style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}

function selectRole(role) {
    // 1. Set the hidden input value for PHP
    document.getElementById('role_input').value = role;
    
    // 2. Change Button Colors
    document.getElementById('btn_customer').style.background = role === 'customer' ? 'var(--primary)' : '#eee';
    document.getElementById('btn_customer').style.color = role === 'customer' ? '#fff' : 'var(--gray)';
    
    document.getElementById('btn_seller').style.background = role === 'seller' ? 'var(--primary)' : '#eee';
    document.getElementById('btn_seller').style.color = role === 'seller' ? '#fff' : 'var(--gray)';

    // 3. Show/Hide specific fields and toggle 'required' attributes so HTML doesn't block submission
    if (role === 'seller') {
        document.getElementById('customer_fields').style.display = 'none';
        document.getElementById('seller_fields').style.display = 'flex';
        
        // Remove customer requirements
        document.getElementById('inp_name').required = false;
        document.getElementById('inp_username').required = false;
        document.getElementById('inp_address').required = false;
        
        // Add seller requirements
        document.getElementById('inp_shopname').required = true;
    } else {
        document.getElementById('customer_fields').style.display = 'flex';
        document.getElementById('seller_fields').style.display = 'none';
        
        // Add customer requirements
        document.getElementById('inp_name').required = true;
        document.getElementById('inp_username').required = true;
        document.getElementById('inp_address').required = true;
        
        // Remove seller requirements
        document.getElementById('inp_shopname').required = false;
    }
}
</script>