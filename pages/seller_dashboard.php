<?php
// Secure the page
if (!isset($_SESSION['seller_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$seller_id = $_SESSION['seller_id'];
$shopname = $_SESSION['shopname'];

// Grab success messages from the session (PRG Pattern)
$msg = '';
if (isset($_SESSION['dashboard_msg'])) {
    $msg = $_SESSION['dashboard_msg'];
    unset($_SESSION['dashboard_msg']);
}

$error = '';

// FETCH SELLER PROFILE PICTURE
$stmtSeller = $pdo->prepare("SELECT seller_profile FROM User_Seller WHERE id_seller = ?");
$stmtSeller->execute([$seller_id]);
$sellerData = $stmtSeller->fetch(PDO::FETCH_ASSOC);
$sellerProfilePic = $sellerData ? $sellerData['seller_profile'] : '';

// ==========================================
// 1. HANDLE FORM SUBMISSIONS
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // --- ACTION A: ADD NEW CATEGORY ---
    if ($_POST['action'] === 'add_category') {
        $category_name = trim($_POST['category_name']);
        if (!empty($category_name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO Category (categoryname, imgurl) VALUES (?, ?)");
                $stmt->execute([$category_name, '']);
                $_SESSION['dashboard_msg'] = "Category '{$category_name}' created successfully!";
                header("Location: index.php?page=seller_dashboard"); exit;
            } catch (PDOException $e) { $error = "Failed to add category: " . $e->getMessage(); }
        }
    }
    
    // --- ACTION B: SINGLE DELETE PRODUCT ---
    elseif ($_POST['action'] === 'delete_product') {
        $product_id = $_POST['product_ids']; 
        try {
            $stmt = $pdo->prepare("DELETE FROM product WHERE id_product = ? AND id_seller = ?");
            $stmt->execute([$product_id, $seller_id]);
            $_SESSION['dashboard_msg'] = "Product deleted successfully!";
            header("Location: index.php?page=seller_dashboard"); exit;
        } catch (PDOException $e) { $error = "Failed to delete: " . $e->getMessage(); }
    }

    // --- ACTION C: BULK DELETE PRODUCTS ---
    elseif ($_POST['action'] === 'bulk_delete') {
        if (!empty($_POST['product_ids'])) {
            $ids = explode(',', $_POST['product_ids']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $params = $ids;
            $params[] = $seller_id;
            
            try {
                $stmt = $pdo->prepare("DELETE FROM product WHERE id_product IN ($placeholders) AND id_seller = ?");
                $stmt->execute($params);
                $_SESSION['dashboard_msg'] = count($ids) . " products deleted successfully!";
                header("Location: index.php?page=seller_dashboard"); exit;
            } catch (PDOException $e) {
                $error = "Failed to bulk delete: " . $e->getMessage();
            }
        }
    }

    // --- ACTION D: ADD OR EDIT PRODUCT ---
    elseif ($_POST['action'] === 'add_product' || $_POST['action'] === 'edit_product') {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $category_id = $_POST['category_id'];
        $product_img = '';

        if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['product_img']['tmp_name'];
            $mime_type = mime_content_type($tmp_name);
            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            
            if (in_array($mime_type, $allowed)) {
                $image_data = file_get_contents($tmp_name);
                $product_img = 'data:' . $mime_type . ';base64,' . base64_encode($image_data);
            } else {
                $error = "Only JPG, PNG, and WebP images are allowed.";
            }
        }

        if (!$error) {
            try {
                if ($_POST['action'] === 'add_product') {
                    $stmt = $pdo->prepare("INSERT INTO product (id_seller, category_id, name, description, price, product_img) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$seller_id, $category_id, $name, $description, $price, $product_img]);
                    $_SESSION['dashboard_msg'] = "Product '{$name}' published successfully!";
                } else {
                    $product_id = $_POST['product_id'];
                    if ($product_img !== '') {
                        $stmt = $pdo->prepare("UPDATE product SET category_id = ?, name = ?, description = ?, price = ?, product_img = ? WHERE id_product = ? AND id_seller = ?");
                        $stmt->execute([$category_id, $name, $description, $price, $product_img, $product_id, $seller_id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE product SET category_id = ?, name = ?, description = ?, price = ? WHERE id_product = ? AND id_seller = ?");
                        $stmt->execute([$category_id, $name, $description, $price, $product_id, $seller_id]);
                    }
                    $_SESSION['dashboard_msg'] = "Product '{$name}' updated successfully!";
                }
                header("Location: index.php?page=seller_dashboard"); exit;
            } catch (PDOException $e) { $error = "Database Error: " . $e->getMessage(); }
        }
    }
}

// ==========================================
// 2. FETCH ALL DATA FOR THE DASHBOARD TABS
// ==========================================
$catStmt = $pdo->query("SELECT * FROM Category");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$prodStmt = $pdo->prepare("SELECT p.*, c.categoryname FROM product p LEFT JOIN Category c ON p.category_id = c.category_id WHERE p.id_seller = ? ORDER BY p.id_product DESC");
$prodStmt->execute([$seller_id]);
$products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);

$revStmt = $pdo->prepare("
    SELECT r.*, p.name as product_name, c.name as customer_name 
    FROM reviews r JOIN product p ON r.id_product = p.id_product JOIN customers c ON r.id_user = c.id_user 
    WHERE p.id_seller = ? ORDER BY r.created_at DESC
");
$revStmt->execute([$seller_id]);
$reviews = $revStmt->fetchAll(PDO::FETCH_ASSOC);

$statsStmt = $pdo->prepare("
    SELECT COALESCE(SUM(oi.quantity * oi.price_at_purchase), 0) as total_revenue, COUNT(DISTINCT oi.id_transaksi) as total_orders, SUM(oi.quantity) as items_sold
    FROM order_item oi JOIN product p ON oi.id_product = p.id_product WHERE p.id_seller = ?
");
$statsStmt->execute([$seller_id]);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
?>

<div style="height: 100%; display: flex; flex-direction: column; background: #F8F9FA; position: relative;">
    
    <div style="padding: 20px 20px 0 20px; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.03); z-index: 10;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <?php if (!empty($sellerProfilePic)): ?>
                    <img src="<?= htmlspecialchars($sellerProfilePic) ?>" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <?php else: ?>
                    <div style="width: 45px; height: 45px; background: #FFD27C; border-radius: 50%; display: grid; place-items: center; font-size: 20px; font-weight: bold; color: var(--primary); box-shadow: 0 4px 10px rgba(255, 118, 34, 0.2);"><?= strtoupper(substr($shopname, 0, 1)) ?></div>
                <?php endif; ?>
                <div>
                    <small style="color: var(--primary); font-weight: 700; font-size: 11px; letter-spacing: 0.5px;">SELLER PORTAL</small>
                    <h2 style="margin: 2px 0 0 0; color: var(--dark); font-size: 20px;"><?= htmlspecialchars($shopname) ?></h2>
                </div>
            </div>
            <a href="logout.php" style="text-decoration:none;">
                <div style="background: #FFF1F1; color: #FF4B4B; width: 45px; height: 45px; border-radius: 15px; display: grid; place-items: center; font-size: 20px; border: 1px solid #FFE0E0;">🚪</div>
            </a>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div id="btn-analysis" onclick="switchTab('analysis')" style="flex: 1; text-align: center; padding: 12px; border-radius: 12px; background: #eee; color: var(--gray); font-weight: bold; font-size: 13px; cursor: pointer; transition: 0.3s;">📊 Reports</div>
            <div id="btn-products" onclick="switchTab('products')" style="flex: 1; text-align: center; padding: 12px; border-radius: 12px; background: var(--primary); color: white; font-weight: bold; font-size: 13px; cursor: pointer; transition: 0.3s;">🍔 Products</div>
            <div id="btn-reviews" onclick="switchTab('reviews')" style="flex: 1; text-align: center; padding: 12px; border-radius: 12px; background: #eee; color: var(--gray); font-weight: bold; font-size: 13px; cursor: pointer; transition: 0.3s;">⭐ Reviews</div>
        </div>
    </div>

    <div class="scroll-area" style="padding: 20px;">

        <div id="tab-analysis" style="display: none;">
            <h3 style="color: var(--dark); margin: 0 0 15px 0;">Performance Overview</h3>
            <div style="background: var(--dark); color: white; padding: 25px; border-radius: 20px; margin-bottom: 15px; text-align: center; box-shadow: 0 10px 20px rgba(50, 54, 67, 0.2);">
                <div style="font-size: 12px; font-weight: bold; color: #98A8B8; letter-spacing: 1px;">TOTAL REVENUE</div>
                <div style="font-size: 32px; font-weight: bold; margin-top: 5px; color: var(--primary);">Rp <?= number_format($stats['total_revenue'], 0, ',', '.') ?></div>
            </div>
            <div style="display: flex; gap: 15px; margin-bottom: 30px;">
                <div style="flex: 1; background: #fff; padding: 20px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #eee; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: var(--dark);"><?= $stats['total_orders'] ?></div>
                    <div style="font-size: 12px; color: var(--gray); margin-top: 5px; font-weight: bold;">TOTAL ORDERS</div>
                </div>
                <div style="flex: 1; background: #fff; padding: 20px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #eee; text-align: center;">
                    <div style="font-size: 24px; font-weight: bold; color: var(--dark);"><?= $stats['items_sold'] ?: 0 ?></div>
                    <div style="font-size: 12px; color: var(--gray); margin-top: 5px; font-weight: bold;">ITEMS SOLD</div>
                </div>
            </div>
        </div>

        <div id="tab-products" style="display: block; padding-bottom: 80px;">
            
            <div style="background: #fff; padding: 15px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #eee; margin-bottom: 15px;">
                <h4 style="margin: 0 0 10px 0; color: var(--dark); font-size: 14px;">+ Create New Category</h4>
                <form method="POST" style="display: flex; gap: 10px;">
                    <input type="hidden" name="action" value="add_category">
                    <input type="text" name="category_name" placeholder="E.g., Beverages, Fast Food" required style="flex: 1; padding: 12px; border-radius: 10px; border: 1px solid #eee; background: #F6F6F6; outline: none; font-size: 13px;">
                    <button type="submit" class="btn-orange" style="width: auto; padding: 0 20px; font-size: 13px; border-radius: 10px;">Add</button>
                </form>
            </div>

            <div id="product_form_card" style="background: #fff; padding: 20px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #eee; margin-bottom: 30px;">
                <h3 id="form_title" style="margin: 0 0 15px 0; color: var(--dark);">+ Add New Product</h3>
                <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px;">
                    <input type="hidden" name="action" id="form_action" value="add_product">
                    <input type="hidden" name="product_id" id="form_product_id" value="">
                    
                    <input type="text" id="inp_name" name="name" placeholder="Food Name (e.g. Nasi Goreng)" required style="padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none;">
                    
                    <div style="display: flex; gap: 10px;">
                        <input type="number" id="inp_price" name="price" placeholder="Price (Rp)" required style="flex: 1; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none;">
                        <select id="inp_category" name="category_id" required style="flex: 1; padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; color: var(--dark);">
                            <option value="">Category...</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['categoryname']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <textarea id="inp_desc" name="description" placeholder="Short tasty description..." rows="2" style="padding: 15px; border-radius: 12px; border: 1px solid #eee; background: #F6F6F6; outline: none; resize: none;"></textarea>
                    
                    <div style="background: #F6F6F6; padding: 15px; border-radius: 12px; border: 1px solid #eee; text-align: center;">
                        <label style="color: var(--gray); font-size: 12px; font-weight: bold; display: block; margin-bottom: 10px;">UPLOAD FOOD PHOTO</label>
                        <img id="product-preview" src="" style="display: none; width: 100%; height: 180px; object-fit: cover; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        <input type="file" id="inp_img" name="product_img" accept="image/png, image/jpeg, image/webp" required style="font-size: 13px; width: 100%;" onchange="previewProductImage(event)">
                        <small id="img_hint" style="display: none; color: var(--gray); margin-top: 5px;">Leave empty to keep current photo</small>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 5px;">
                        <button type="submit" id="submit_btn" class="btn-orange" style="flex: 1;">Publish Product</button>
                        <button type="button" id="cancel_btn" onclick="cancelEdit()" style="display: none; flex: 1; background: #eee; color: var(--dark); border: none; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer;">Cancel Edit</button>
                    </div>
                </form>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="color: var(--dark); margin: 0;">Your Active Menu</h3>
                
                <button id="enterBulkModeBtn" onclick="toggleBulkMode(true)" style="background: #FFF1F1; color: #FF4B4B; border: none; padding: 8px 15px; border-radius: 8px; font-size: 12px; font-weight: bold; cursor: pointer;">
                    🗑️ Bulk Delete
                </button>

                <div id="bulkModeControls" style="display: none; align-items: center; gap: 15px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--gray); font-size: 13px; font-weight: bold; user-select: none;">
                        <input type="checkbox" id="selectAll" onclick="toggleAll(this)" style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;"> Select All
                    </label>
                    <button onclick="toggleBulkMode(false)" style="background: #eee; color: var(--dark); border: none; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: bold; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </div>

            <?php if(empty($products)): ?>
                <p style="color: var(--gray); text-align: center;">You haven't uploaded any food yet.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach($products as $p): ?>
                        
                        <div class="product-card" data-id="<?= $p['id_product'] ?>" onclick="toggleCardSelection(this, <?= $p['id_product'] ?>)" style="background: #fff; padding: 15px; border-radius: 15px; display: flex; flex-direction: column; gap: 10px; border: 2px solid #eee; box-shadow: 0 4px 10px rgba(0,0,0,0.02); transition: all 0.2s ease;">
                            
                            <div style="display: flex; gap: 15px; align-items: center; pointer-events: none;">
                                <img src="<?= htmlspecialchars($p['product_img']) ?>" style="width: 70px; height: 70px; border-radius: 12px; object-fit: cover; background: #eee;">
                                <div style="flex: 1;">
                                    <h4 style="margin: 0; color: var(--dark); font-size: 16px;"><?= htmlspecialchars($p['name']) ?></h4>
                                    <div style="color: var(--gray); font-size: 12px; margin-top: 2px;"><?= htmlspecialchars($p['categoryname']) ?></div>
                                    <div style="color: var(--primary); font-weight: bold; font-size: 14px; margin-top: 5px;">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                                </div>
                            </div>

                            <div class="single-action-btns" style="display: flex; gap: 10px; margin-top: 5px; border-top: 1px solid #f0f0f0; padding-top: 10px; pointer-events: auto;">
                                <button onclick="startEdit(this, event)" 
                                        data-id="<?= $p['id_product'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-price="<?= $p['price'] ?>" 
                                        data-cat="<?= $p['category_id'] ?>" data-desc="<?= htmlspecialchars($p['description']) ?>" data-img="<?= htmlspecialchars($p['product_img']) ?>" 
                                        style="flex: 1; background: #ECF0F4; color: var(--dark); border: none; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer;">
                                    ✏️ Edit
                                </button>
                                
                                <button type="button" onclick="deleteSingle(<?= $p['id_product'] ?>, '<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>', event)" style="flex: 1; background: #FFF1F1; color: #FF4B4B; border: none; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer;">
                                    🗑️ Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div id="tab-reviews" style="display: none;">
            <h3 style="color: var(--dark); margin: 0 0 15px 0;">Customer Feedback</h3>
            <?php if(empty($reviews)): ?>
                <p style="color: var(--gray); text-align: center; margin-top: 30px;">No reviews yet. Keep selling!</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach($reviews as $r): ?>
                        <div style="background: #fff; padding: 20px; border-radius: 15px; border: 1px solid #eee; box-shadow: 0 5px 10px rgba(0,0,0,0.02);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <strong style="color: var(--dark);"><?= htmlspecialchars($r['customer_name']) ?></strong>
                                <span style="background: #FFF1E5; color: var(--primary); padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 12px;">⭐ <?= $r['rating'] ?>/5</span>
                            </div>
                            <div style="font-size: 12px; color: var(--gray); font-weight: bold; margin-bottom: 8px;">ORDER: <?= htmlspecialchars($r['product_name']) ?></div>
                            <p style="color: var(--dark); font-size: 14px; margin: 0; line-height: 1.5; font-style: italic;">"<?= htmlspecialchars($r['comment']) ?>"</p>
                            <div style="font-size: 11px; color: var(--gray); margin-top: 10px; text-align: right;"><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
    
    <div id="bulkActionBar" style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(100px); opacity: 0; pointer-events: none; background: #323643; color: white; padding: 15px 25px; border-radius: 30px; display: flex; align-items: center; gap: 20px; box-shadow: 0 10px 25px rgba(50, 54, 67, 0.3); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 100; width: max-content;">
        <span style="font-size: 14px; font-weight: bold;"><span id="selCount" style="color: var(--primary);">0</span> Selected</span>
        <div style="width: 1px; height: 20px; background: rgba(255,255,255,0.2);"></div>
        <button onclick="submitBulkDelete()" style="background: transparent; color: #FF4B4B; border: none; padding: 0; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 6px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            Delete
        </button>
    </div>

    <?php if ($msg || $error): ?>
        <style>
            .modern-toast {
                position: fixed; top: 110px; left: 50%; transform: translateX(-50%) translateY(-20px);
                opacity: 0; background: <?= $error ? '#FF4B4B' : '#323643' ?>; color: white;
                padding: 14px 28px; border-radius: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                font-size: 14px; font-weight: bold; z-index: 9999; pointer-events: none;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                white-space: nowrap;
            }
            .modern-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        </style>
        <div id="modernToast" class="modern-toast">
            <?= $error ? $error : $msg ?>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toast = document.getElementById('modernToast');
                setTimeout(() => toast.classList.add('show'), 100);
                setTimeout(() => toast.classList.remove('show'), 3500);
            });
        </script>
    <?php endif; ?>

</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="action" id="del_action" value="">
    <input type="hidden" name="product_ids" id="del_ids" value="">
</form>

<script>
// UI TABS & IMAGES
function switchTab(tabName) {
    document.getElementById('tab-analysis').style.display = 'none';
    document.getElementById('tab-products').style.display = 'none';
    document.getElementById('tab-reviews').style.display = 'none';
    
    document.getElementById('btn-analysis').style.background = '#eee'; document.getElementById('btn-analysis').style.color = 'var(--gray)';
    document.getElementById('btn-products').style.background = '#eee'; document.getElementById('btn-products').style.color = 'var(--gray)';
    document.getElementById('btn-reviews').style.background = '#eee'; document.getElementById('btn-reviews').style.color = 'var(--gray)';
    
    document.getElementById('tab-' + tabName).style.display = 'block';
    document.getElementById('btn-' + tabName).style.background = 'var(--primary)'; document.getElementById('btn-' + tabName).style.color = 'white';
}

function previewProductImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('product-preview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}

// EDIT CONTROLS
function startEdit(btn, event) {
    event.stopPropagation(); 
    document.getElementById('form_action').value = 'edit_product';
    document.getElementById('form_product_id').value = btn.getAttribute('data-id');
    document.getElementById('inp_name').value = btn.getAttribute('data-name');
    document.getElementById('inp_price').value = btn.getAttribute('data-price');
    document.getElementById('inp_category').value = btn.getAttribute('data-cat');
    document.getElementById('inp_desc').value = btn.getAttribute('data-desc');
    
    document.getElementById('product-preview').src = btn.getAttribute('data-img');
    document.getElementById('product-preview').style.display = 'block';
    
    document.getElementById('inp_img').required = false;
    document.getElementById('img_hint').style.display = 'block';

    document.getElementById('form_title').innerText = '✏️ Edit Product';
    document.getElementById('submit_btn').innerText = 'Save Changes';
    document.getElementById('cancel_btn').style.display = 'inline-block';
    document.getElementById('product_form_card').style.border = '2px solid var(--primary)';
    
    document.querySelector('.scroll-area').scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelEdit() {
    document.getElementById('form_action').value = 'add_product';
    document.getElementById('form_product_id').value = '';
    document.getElementById('inp_name').value = '';
    document.getElementById('inp_price').value = '';
    document.getElementById('inp_category').value = '';
    document.getElementById('inp_desc').value = '';
    
    document.getElementById('product-preview').src = '';
    document.getElementById('product-preview').style.display = 'none';
    
    document.getElementById('inp_img').value = '';
    document.getElementById('inp_img').required = true;
    document.getElementById('img_hint').style.display = 'none';

    document.getElementById('form_title').innerText = '+ Add New Product';
    document.getElementById('submit_btn').innerText = 'Publish Product';
    document.getElementById('cancel_btn').style.display = 'none';
    document.getElementById('product_form_card').style.border = '1px solid #eee';
}

// BULK DELETE MODE AND CARD SELECTION
let bulkModeActive = false;
let selectedItems = new Set();

function toggleBulkMode(isActive) {
    bulkModeActive = isActive;
    const enterBtn = document.getElementById('enterBulkModeBtn');
    const controls = document.getElementById('bulkModeControls');
    const actionBtns = document.querySelectorAll('.single-action-btns');
    const cards = document.querySelectorAll('.product-card');
    
    if (isActive) {
        enterBtn.style.display = 'none';
        controls.style.display = 'flex';
        actionBtns.forEach(btn => btn.style.display = 'none');
        cards.forEach(card => card.style.cursor = 'pointer');
    } else {
        enterBtn.style.display = 'block';
        controls.style.display = 'none';
        actionBtns.forEach(btn => btn.style.display = 'flex');
        
        selectedItems.clear();
        cards.forEach(card => {
            card.style.cursor = 'default';
            card.style.borderColor = '#eee';
            card.style.backgroundColor = '#fff';
        });
        document.getElementById('selectAll').checked = false;
        updateBulkBtn();
    }
}

function toggleCardSelection(cardElem, id) {
    if (!bulkModeActive) return;

    if (selectedItems.has(id)) {
        selectedItems.delete(id);
        cardElem.style.borderColor = '#eee';
        cardElem.style.backgroundColor = '#fff';
    } else {
        selectedItems.add(id);
        cardElem.style.borderColor = 'var(--primary)';
        cardElem.style.backgroundColor = '#FFF9F5'; 
    }
    updateBulkBtn();
}

function toggleAll(source) {
    const cards = document.querySelectorAll('.product-card');
    if (source.checked) {
        cards.forEach(card => {
            const id = parseInt(card.getAttribute('data-id'));
            selectedItems.add(id);
            card.style.borderColor = 'var(--primary)';
            card.style.backgroundColor = '#FFF9F5';
        });
    } else {
        selectedItems.clear();
        cards.forEach(card => {
            card.style.borderColor = '#eee';
            card.style.backgroundColor = '#fff';
        });
    }
    updateBulkBtn();
}

function updateBulkBtn() {
    const actionBar = document.getElementById('bulkActionBar');
    
    if (selectedItems.size > 0) {
        actionBar.style.transform = 'translateX(-50%) translateY(0)';
        actionBar.style.opacity = '1';
        actionBar.style.pointerEvents = 'auto';
        document.getElementById('selCount').innerText = selectedItems.size;
    } else {
        actionBar.style.transform = 'translateX(-50%) translateY(100px)';
        actionBar.style.opacity = '0';
        actionBar.style.pointerEvents = 'none';
    }
    
    const totalCards = document.querySelectorAll('.product-card').length;
    if (totalCards > 0) {
        document.getElementById('selectAll').checked = (selectedItems.size === totalCards);
    }
}

function deleteSingle(id, name, event) {
    event.stopPropagation();
    if (confirm('Are you sure you want to delete ' + name + '?')) {
        document.getElementById('del_action').value = 'delete_product';
        document.getElementById('del_ids').value = id;
        document.getElementById('deleteForm').submit();
    }
}

function submitBulkDelete() {
    if (selectedItems.size === 0) return;
    
    if (confirm('Are you sure you want to delete ' + selectedItems.size + ' selected products? This cannot be undone!')) {
        const ids = Array.from(selectedItems).join(',');
        document.getElementById('del_action').value = 'bulk_delete';
        document.getElementById('del_ids').value = ids;
        document.getElementById('deleteForm').submit();
    }
}
</script>