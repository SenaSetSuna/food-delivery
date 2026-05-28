<?php
// Secure the page
if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$msg = '';
$error = '';

// Grab success message from the PRG redirect
if (isset($_SESSION['cart_msg'])) {
    $msg = $_SESSION['cart_msg'];
    unset($_SESSION['cart_msg']);
}

// ==========================================
// 1. HANDLE CHECKOUT SUBMISSION
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // --- CLEAR CART ACTION ---
    if ($_POST['action'] === 'clear_cart') {
        unset($_SESSION['cart']);
        header("Location: index.php?page=cart");
        exit;
    }
    
    // --- PROCESS CHECKOUT ACTION ---
    if ($_POST['action'] === 'checkout') {
        if (empty($_SESSION['cart'])) {
            $error = "Your cart is empty!";
        } else {
            try {
                // Start a safe database transaction
                $pdo->beginTransaction();

                $total_amount = 0;
                $items_to_insert = [];

                // 1. Calculate total amount AND gather current prices
                foreach ($_SESSION['cart'] as $id_prod => $qty) {
                    $priceStmt = $pdo->prepare("SELECT price FROM product WHERE id_product = ?");
                    $priceStmt->execute([$id_prod]);
                    $prodData = $priceStmt->fetch(PDO::FETCH_ASSOC);

                    if ($prodData) {
                        $price = $prodData['price'];
                        $total_amount += ($price * $qty);
                        
                        // Save it temporarily so we don't have to query the database twice
                        $items_to_insert[] = [
                            'id_product' => $id_prod,
                            'qty' => $qty,
                            'price' => $price
                        ];
                    }
                }

                // 2. Create the Master Transaction Record (WITH BACKTICKS AND TOTAL_AMOUNT)
                $stmt = $pdo->prepare("INSERT INTO `Transaction` (id_user, total_amount, status) VALUES (?, ?, ?)");
                $stmt->execute([$customer_id, $total_amount, 'Pending']);
                $transaction_id = $pdo->lastInsertId();

                // 3. Prepare the order_item insert statement
                $stmtItem = $pdo->prepare("INSERT INTO order_item (id_transaksi, id_product, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");

                // 4. Loop through our saved items and insert them
                foreach ($items_to_insert as $item) {
                    $stmtItem->execute([$transaction_id, $item['id_product'], $item['qty'], $item['price']]);
                }

                // 5. If everything worked perfectly, COMMIT the data to the database!
                $pdo->commit();
                
                // 6. Clear the cart and celebrate
                unset($_SESSION['cart']);
                $_SESSION['cart_msg'] = "Order placed successfully! 🍔";
                header("Location: index.php?page=cart");
                exit;

            } catch (Exception $e) {
                // If anything failed, ROLLBACK the changes so we don't have corrupted data
                $pdo->rollBack();
                $error = "Checkout failed: " . $e->getMessage();
            }
        }
    }
}

// ==========================================
// 2. FETCH CART DATA TO DISPLAY
// ==========================================
$cart_items = [];
$cart_total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    $stmt = $pdo->prepare("SELECT p.*, s.shopname FROM product p JOIN User_Seller s ON p.id_seller = s.id_seller WHERE p.id_product IN ($placeholders)");
    $stmt->execute($product_ids);
    $fetched_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($fetched_products as $prod) {
        $qty = $_SESSION['cart'][$prod['id_product']];
        $subtotal = $qty * $prod['price'];
        $cart_total += $subtotal;
        
        $prod['quantity'] = $qty;
        $prod['subtotal'] = $subtotal;
        $cart_items[] = $prod;
    }
}
?>

<div style="height: 100%; display: flex; flex-direction: column; background: #F8F9FA; position: relative;">
    
    <div style="padding: 20px 20px 10px 20px; display: flex; justify-content: space-between; align-items: center; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.03); z-index: 10;">
        <a href="index.php?page=home" style="width: 45px; height: 45px; border-radius: 15px; background: #F6F6F6; display: grid; place-items: center; text-decoration: none; color: var(--dark); font-weight: bold; border: 1px solid #eee;">&lt;</a>
        <h2 style="margin: 0; color: var(--dark); font-size: 18px;">Your Cart</h2>
        <div style="width: 45px;"></div> </div>

    <div class="scroll-area" style="padding: 20px; padding-bottom: 120px;">
        
        <?php if(empty($cart_items)): ?>
            
            <div style="text-align: center; padding: 50px 20px;">
                <div style="font-size: 60px; margin-bottom: 15px;">🛒</div>
                <h3 style="color: var(--dark); margin: 0 0 10px 0;">Cart is empty</h3>
                <p style="color: var(--gray); font-size: 14px;">Looks like you haven't added any delicious food yet.</p>
                <a href="index.php?page=home" style="display: inline-block; margin-top: 20px; background: var(--primary); color: white; text-decoration: none; padding: 12px 25px; border-radius: 12px; font-weight: bold;">Browse Menu</a>
            </div>

        <?php else: ?>
            
            <div style="text-align: right; margin-bottom: 15px;">
                <form method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to empty your cart?');">
                    <input type="hidden" name="action" value="clear_cart">
                    <button type="submit" style="background: none; border: none; color: #FF4B4B; font-weight: bold; font-size: 13px; cursor: pointer;">
                        🗑️ Empty Cart
                    </button>
                </form>
            </div>

            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach($cart_items as $item): ?>
                    <div style="background: #fff; padding: 15px; border-radius: 15px; border: 1px solid #eee; display: flex; gap: 15px; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                        <img src="<?= htmlspecialchars($item['product_img']) ?>" style="width: 70px; height: 70px; border-radius: 12px; object-fit: cover; background: #eee;">
                        <div style="flex: 1;">
                            <h4 style="margin: 0; color: var(--dark); font-size: 15px;"><?= htmlspecialchars($item['name']) ?></h4>
                            <div style="color: var(--gray); font-size: 11px; margin-top: 2px;">From <?= htmlspecialchars($item['shopname']) ?></div>
                            <div style="color: var(--primary); font-weight: bold; font-size: 14px; margin-top: 5px;">Rp <?= number_format($item['price'], 0, ',', '.') ?></div>
                        </div>
                        <div style="background: #974141; padding: 8px 12px; border-radius: 10px; font-weight: bold; color: var(--dark); font-size: 14px;">
                            x<?= $item['quantity'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>

    <?php if(!empty($cart_items)): ?>
        <div style="position: absolute; bottom: 0; width: 100%; background: #fff; padding: 20px; box-sizing: border-box; border-top: 1px solid #eee; border-radius: 25px 25px 0 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <span style="color: var(--gray); font-weight: bold; font-size: 14px;">Total Price</span>
                <span style="color: var(--dark); font-weight: 900; font-size: 22px;">Rp <?= number_format($cart_total, 0, ',', '.') ?></span>
            </div>

            <form method="POST" style="margin: 0;">
                <input type="hidden" name="action" value="checkout">
                <button type="submit" class="btn-orange" style="padding: 18px 0; font-size: 16px; box-shadow: 0 5px 15px rgba(255, 118, 34, 0.3);">
                    Place Order Now
                </button>
            </form>
            
        </div>
    <?php endif; ?>

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