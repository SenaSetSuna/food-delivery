<?php
// Make sure a product ID was passed in the URL
if (!isset($_GET['id'])) {
    header("Location: index.php?page=home");
    exit;
}

$product_id = $_GET['id'];
$msg = '';
$error = '';

// ==========================================
// 1. HANDLE FORM SUBMISSIONS (CART & REVIEW)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Force user to login if they try to interact while logged out
    if (!isset($_SESSION['customer_id'])) {
        header("Location: index.php?page=login");
        exit;
    }

    $customer_id = $_SESSION['customer_id'];

    // --- ACTION A: ADD TO CART ---
    if ($_POST['action'] === 'add_to_cart') {
        $qty = (int)$_POST['quantity'];
        
        if ($qty > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $qty;
            } else {
                $_SESSION['cart'][$product_id] = $qty;
            }
            
            $_SESSION['detail_msg'] = "Added to your cart! 🛒";
            header("Location: index.php?page=info&id=" . $product_id);
            exit;
        }
    }
    
    // --- ACTION B: SUBMIT REVIEW ---
    elseif ($_POST['action'] === 'submit_review') {
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        
        if ($rating >= 1 && $rating <= 5) {
            try {
                // Insert the new review
                $stmt = $pdo->prepare("INSERT INTO reviews (id_product, id_user, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt->execute([$product_id, $customer_id, $rating, $comment]);
                
                // Automatically recalculate and update the product's average rating
                $updateRating = $pdo->prepare("
                    UPDATE product 
                    SET rating = (SELECT AVG(rating) FROM reviews WHERE id_product = ?) 
                    WHERE id_product = ?
                ");
                $updateRating->execute([$product_id, $product_id]);
                
                $_SESSION['detail_msg'] = "Thank you for your feedback! ⭐";
                header("Location: index.php?page=info&id=" . $product_id);
                exit;
            } catch (PDOException $e) {
                $_SESSION['detail_err'] = "Failed to submit review.";
                header("Location: index.php?page=info&id=" . $product_id);
                exit;
            }
        }
    }
}

// Grab success message after redirect
if (isset($_SESSION['detail_msg'])) {
    $msg = $_SESSION['detail_msg'];
    unset($_SESSION['detail_msg']);
}
if (isset($_SESSION['detail_err'])) {
    $error = $_SESSION['detail_err'];
    unset($_SESSION['detail_err']);
}

// ==========================================
// 2. FETCH PRODUCT & SELLER DATA
// ==========================================
$stmt = $pdo->prepare("
    SELECT p.*, s.shopname, s.seller_profile, c.categoryname 
    FROM product p
    JOIN User_Seller s ON p.id_seller = s.id_seller
    JOIN Category c ON p.category_id = c.category_id
    WHERE p.id_product = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php?page=home");
    exit;
}

// ==========================================
// 3. CHECK IF CUSTOMER HAS PURCHASED THIS
// ==========================================
$has_purchased = false;
if (isset($_SESSION['customer_id'])) {
    // Note: Transaction keyword is wrapped in backticks to prevent SQL syntax errors
    $checkStmt = $pdo->prepare("
        SELECT 1 FROM order_item oi 
        JOIN `Transaction` t ON oi.id_transaksi = t.id_transaksi 
        WHERE t.id_user = ? AND oi.id_product = ? 
        LIMIT 1
    ");
    $checkStmt->execute([$_SESSION['customer_id'], $product_id]);
    if ($checkStmt->fetchColumn()) {
        $has_purchased = true;
    }
}

// ==========================================
// 4. FETCH CUSTOMER REVIEWS
// ==========================================
$revStmt = $pdo->prepare("
    SELECT r.*, c.name as customer_name, c.user_profile 
    FROM reviews r 
    JOIN customers c ON r.id_user = c.id_user 
    WHERE r.id_product = ? 
    ORDER BY r.created_at DESC
");
$revStmt->execute([$product_id]);
$reviews = $revStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="height: 100%; display: flex; flex-direction: column; background: #F8F9FA; position: relative;">
    
    <div style="position: absolute; top: 0; width: 100%; padding: 20px; display: flex; justify-content: space-between; align-items: center; z-index: 10; box-sizing: border-box;">
        <a href="index.php?page=home" style="background: rgba(255,255,255,0.9); width: 45px; height: 45px; border-radius: 50%; display: grid; place-items: center; text-decoration: none; color: var(--dark); font-weight: bold; box-shadow: 0 4px 10px rgba(0,0,0,0.1); backdrop-filter: blur(5px);">&lt;</a>
        
        <a href="index.php?page=cart" style="background: rgba(255,255,255,0.9); width: 45px; height: 45px; border-radius: 50%; display: grid; place-items: center; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1); backdrop-filter: blur(5px); position: relative;">
            <span style="font-size: 20px;">👜</span>
            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <div style="position: absolute; top: 0; right: -5px; background: #FF4B4B; color: white; font-size: 10px; font-weight: bold; width: 18px; height: 18px; border-radius: 50%; display: grid; place-items: center; border: 2px solid white;">
                    <?= count($_SESSION['cart']) ?>
                </div>
            <?php endif; ?>
        </a>
    </div>

    <div class="scroll-area" style="padding-bottom: 100px;">
        
        <div style="width: 100%; height: 300px; background: #eee;">
            <img src="<?= htmlspecialchars($product['product_img']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>

        <div style="background: #fff; padding: 25px 20px; border-radius: 30px 30px 0 0; margin-top: -30px; position: relative; z-index: 5; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <div style="flex: 1; padding-right: 15px;">
                    <div style="background: #FFF1E5; color: var(--primary); padding: 4px 10px; border-radius: 8px; font-weight: bold; font-size: 11px; display: inline-block; margin-bottom: 8px;">
                        <?= htmlspecialchars($product['categoryname']) ?>
                    </div>
                    <h1 style="margin: 0; color: var(--dark); font-size: 24px; line-height: 1.2;"><?= htmlspecialchars($product['name']) ?></h1>
                </div>
                <div style="text-align: right;">
                    <div style="color: var(--primary); font-size: 22px; font-weight: 800;">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
                    <div style="font-size: 14px; color: var(--gray); margin-top: 5px;">⭐ <?= number_format($product['rating'], 1) ?></div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 15px; margin: 20px 0; padding: 15px; background: #F6F6F6; border-radius: 15px; border: 1px solid #eee;">
                <?php if (!empty($product['seller_profile'])): ?>
                    <img src="<?= htmlspecialchars($product['seller_profile']) ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 40px; height: 40px; background: #e0e0e0; border-radius: 50%; display: grid; place-items: center; font-weight: bold; color: var(--gray);">
                        <?= strtoupper(substr($product['shopname'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <div style="font-size: 11px; color: var(--gray); font-weight: bold; letter-spacing: 0.5px;">PREPARED BY</div>
                    <div style="font-size: 15px; color: var(--dark); font-weight: bold;"><?= htmlspecialchars($product['shopname']) ?></div>
                </div>
            </div>

            <h4 style="margin: 0 0 10px 0; color: var(--dark);">Description</h4>
            <p style="color: var(--gray); font-size: 14px; line-height: 1.6; margin-bottom: 30px;">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </p>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h4 style="margin: 0; color: var(--dark);">Reviews (<?= count($reviews) ?>)</h4>
                <div style="font-size: 14px; font-weight: bold; color: var(--primary);">⭐ <?= number_format($product['rating'], 1) ?> Average</div>
            </div>

            <?php if ($has_purchased): ?>
                <div style="background: #FFF9F5; padding: 15px; border-radius: 15px; border: 1px dashed var(--primary); margin-bottom: 20px;">
                    <h5 style="margin: 0 0 10px 0; color: var(--dark); font-size: 14px;">Leave a Review</h5>
                    <form method="POST" style="display: flex; flex-direction: column; gap: 10px; margin: 0;">
                        <input type="hidden" name="action" value="submit_review">
                        
                        <select name="rating" required style="padding: 12px; border-radius: 10px; border: 1px solid #eee; background: #fff; outline: none; color: var(--dark); font-weight: bold; font-size: 13px;">
                            <option value="">Select Rating...</option>
                            <option value="5">⭐⭐⭐⭐⭐ (5/5) - Excellent!</option>
                            <option value="4">⭐⭐⭐⭐ (4/5) - Very Good</option>
                            <option value="3">⭐⭐⭐ (3/5) - Good</option>
                            <option value="2">⭐⭐ (2/5) - Fair</option>
                            <option value="1">⭐ (1/5) - Poor</option>
                        </select>
                        
                        <textarea name="comment" placeholder="What did you think about this food?" required rows="2" style="padding: 12px; border-radius: 10px; border: 1px solid #eee; background: #fff; outline: none; resize: none; font-size: 13px; font-family: inherit;"></textarea>
                        
                        <button type="submit" class="btn-orange" style="padding: 10px; border-radius: 10px; font-size: 13px;">Post Review</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if(empty($reviews)): ?>
                <p style="color: var(--gray); font-size: 14px; text-align: center; padding: 20px; background: #F6F6F6; border-radius: 15px; border: 1px dashed #ccc;">No reviews yet. Be the first to try it!</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach($reviews as $r): ?>
                        <div style="background: #fff; padding: 15px; border-radius: 15px; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.01);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <?php if (!empty($r['user_profile'])): ?>
                                        <img src="<?= htmlspecialchars($r['user_profile']) ?>" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                                    <?php else: ?>
                                        <div style="width: 30px; height: 30px; background: #FFD27C; color: var(--primary); border-radius: 50%; display: grid; place-items: center; font-weight: bold; font-size: 12px;">
                                            <?= strtoupper(substr($r['customer_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <strong style="color: var(--dark); font-size: 14px;"><?= htmlspecialchars($r['customer_name']) ?></strong>
                                </div>
                                <span style="background: #FFF1E5; color: var(--primary); padding: 3px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">⭐ <?= $r['rating'] ?></span>
                            </div>
                            <p style="color: var(--gray); font-size: 13px; margin: 0; line-height: 1.5; font-style: italic;">
                                "<?= htmlspecialchars($r['comment']) ?>"
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div style="position: absolute; bottom: 0; width: 100%; background: #fff; padding: 15px 20px; box-sizing: border-box; border-top: 1px solid #eee; border-radius: 25px 25px 0 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.05); display: flex; gap: 15px; align-items: center;">
        
        <form method="POST" style="display: flex; width: 100%; gap: 15px; align-items: center; margin: 0;">
            <input type="hidden" name="action" value="add_to_cart">
            
            <div style="display: flex; align-items: center; background: #F6F6F6; border-radius: 12px; padding: 5px; border: 1px solid #eee;">
                <button type="button" onclick="changeQty(-1)" style="background: #fff; border: 1px solid #ddd; width: 35px; height: 35px; border-radius: 8px; font-size: 18px; font-weight: bold; color: var(--dark); cursor: pointer; display: grid; place-items: center;">-</button>
                <input type="number" id="qty_input" name="quantity" value="1" min="1" max="50" style="width: 40px; text-align: center; background: transparent; border: none; font-size: 16px; font-weight: bold; color: var(--dark); outline: none; -moz-appearance: textfield;" readonly>
                <button type="button" onclick="changeQty(1)" style="background: var(--primary); border: none; width: 35px; height: 35px; border-radius: 8px; font-size: 18px; font-weight: bold; color: white; cursor: pointer; display: grid; place-items: center;">+</button>
            </div>

            <button type="submit" class="btn-orange" style="flex: 1; margin: 0; padding: 15px 0; font-size: 15px; box-shadow: 0 5px 15px rgba(255, 118, 34, 0.3);">
                Add to Cart
            </button>
        </form>

    </div>

    <?php if ($msg || $error): ?>
        <style>
            .modern-toast {
                position: fixed; top: 80px; left: 50%; transform: translateX(-50%) translateY(-20px);
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

<script>
function changeQty(change) {
    const input = document.getElementById('qty_input');
    let currentVal = parseInt(input.value);
    if (isNaN(currentVal)) currentVal = 1;
    let newVal = currentVal + change;
    if (newVal < 1) newVal = 1;
    if (newVal > 50) newVal = 50; 
    input.value = newVal;
}
</script>