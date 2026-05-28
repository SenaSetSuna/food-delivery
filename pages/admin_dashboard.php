
<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php?page=login");
    exit;
}

$stmt = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalProducts = count($products);

?>

<div style="height:100%;display:flex;flex-direction:column;">

    <div class="scroll-area" style="padding:20px 20px 120px 20px;">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;">

            <div>
                <h2 style="margin:0;color:var(--dark);">
                    Admin Dashboard
                </h2>

                <p style="margin-top:5px;color:var(--gray);">
                    Welcome,
                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                </p>
            </div>

            <a href="logout.php" style="text-decoration:none;">

                <button
                    style="
                    border:none;
                    background:#FFE5E5;
                    color:#D8000C;
                    padding:12px 18px;
                    border-radius:12px;
                    font-weight:bold;
                    cursor:pointer;
                    "
                >
                    Logout
                </button>

            </a>

        </div>

        <div
            style="
            background:linear-gradient(135deg,#FF7622,#FF9A3D);
            color:white;
            padding:25px;
            border-radius:25px;
            margin-bottom:25px;
            "
        >

            <h3 style="margin:0;font-size:18px;">
                Total Products
            </h3>

            <div style="font-size:42px;font-weight:bold;margin-top:10px;">
                <?= $totalProducts ?>
            </div>

        </div>

        <a
            href="index.php?page=add_product"
            style="text-decoration:none;"
        >

            <button class="btn-orange" style="margin-bottom:25px;">
                + Add New Product
            </button>

        </a>

        <h3 style="margin-bottom:20px;">
            Product List
        </h3>

        <?php if(count($products) > 0): ?>

            <?php foreach($products as $product): ?>

                <div
                    style="
                    background:#fff;
                    border-radius:20px;
                    padding:15px;
                    margin-bottom:20px;
                    box-shadow:0 5px 20px rgba(0,0,0,0.05);
                    "
                >

                    <img
                        src="<?= htmlspecialchars($product['image_url']) ?>"
                        onerror="this.src='https://via.placeholder.com/400x300?text=Food+Image'"
                        style="
                        width:100%;
                        height:180px;
                        object-fit:cover;
                        border-radius:15px;
                        "
                    >

                    <h3 style="margin:15px 0 5px 0;">
                        <?= htmlspecialchars($product['name']) ?>
                    </h3>

                    <p style="color:var(--gray);margin:0;">
                        <?= htmlspecialchars($product['description']) ?>
                    </p>

                    <div
                        style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        margin-top:15px;
                        "
                    >

                        <b style="font-size:20px;">
                            Rp <?= number_format($product['price']) ?>
                        </b>

                        <span
                            style="
                            background:#FFF1E9;
                            color:var(--primary);
                            padding:8px 15px;
                            border-radius:20px;
                            font-size:13px;
                            font-weight:bold;
                            "
                        >
                            <?= htmlspecialchars($product['type']) ?>
                        </span>

                    </div>

                    <!-- BUTTONS -->
                    <div
                        style="
                        display:flex;
                        gap:10px;
                        margin-top:20px;
                        "
                    >

                        <a
                            href="index.php?page=edit_product&id=<?= $product['product_id'] ?>"
                            style="flex:1;text-decoration:none;"
                        >

                            <button
                                style="
                                width:100%;
                                padding:14px;
                                border:none;
                                border-radius:12px;
                                background:#181C2E;
                                color:white;
                                font-weight:bold;
                                cursor:pointer;
                                "
                            >
                                Edit
                            </button>

                        </a>

                        <a
                            href="index.php?page=delete_product&id=<?= $product['product_id'] ?>"
                            style="flex:1;text-decoration:none;"
                            onclick="return confirm('Delete this product?')"
                        >

                            <button
                                style="
                                width:100%;
                                padding:14px;
                                border:none;
                                border-radius:12px;
                                background:#FFE5E5;
                                color:#D8000C;
                                font-weight:bold;
                                cursor:pointer;
                                "
                            >
                                Delete
                            </button>

                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div
                style="
                background:#fff;
                padding:40px 20px;
                border-radius:20px;
                text-align:center;
                color:var(--gray);
                "
            >

                <div style="font-size:50px;margin-bottom:10px;">
                    🍔
                </div>

                <h3>No Products Yet</h3>

                <p>
                    Start adding food or drinks for customers.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

