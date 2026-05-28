<?php

// =======================
// AMBIL SEMUA PRODUCT
// =======================
$stmt = $pdo->prepare("
    SELECT *
    FROM products
    ORDER BY product_id DESC
");

$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =======================
// HITUNG CART
// =======================
$cartCount = 0;

if (isset($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $item) {

        $cartCount += $item['qty'];
    }
}

?>

<div style="height:100%;display:flex;flex-direction:column;">

    <!-- CONTENT -->
    <div
        class="scroll-area"
        style="
        padding:25px 20px 120px 20px;
        "
    >

        <!-- TOP HEADER -->
        <div
            style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:30px;
            "
        >

            <!-- MENU -->
            <div
                style="
                width:45px;
                height:45px;
                border-radius:15px;
                background:#F1F5F9;
                display:grid;
                place-items:center;
                font-size:22px;
                "
            >
                ☰
            </div>

            <!-- LOCATION -->
            <div style="text-align:center;">

                <small
                    style="
                    color:var(--primary);
                    font-weight:bold;
                    "
                >
                    DELIVER TO
                </small>

                <div
                    style="
                    margin-top:5px;
                    color:var(--gray);
                    font-size:14px;
                    "
                >
                    Surakarta, Indonesia
                </div>

            </div>

            <!-- CART -->
            <a
                href="index.php?page=cart"
                style="text-decoration:none;"
            >

                <div
                    style="
                    width:50px;
                    height:50px;
                    background:var(--dark);
                    border-radius:18px;
                    display:grid;
                    place-items:center;
                    position:relative;
                    "
                >

                    <span style="font-size:22px;">
                        🛒
                    </span>

                    <?php if($cartCount > 0): ?>

                        <div
                            style="
                            position:absolute;
                            top:-5px;
                            right:-5px;
                            width:22px;
                            height:22px;
                            border-radius:50%;
                            background:var(--primary);
                            color:white;
                            font-size:12px;
                            display:grid;
                            place-items:center;
                            font-weight:bold;
                            "
                        >
                            <?= $cartCount ?>
                        </div>

                    <?php endif; ?>

                </div>

            </a>

        </div>

        <!-- GREETING -->
        <div style="margin-bottom:30px;">

            <h1
                style="
                margin:0;
                font-size:30px;
                color:var(--dark);
                "
            >
                Hello,
                <?= htmlspecialchars($_SESSION['customer_name']) ?>
                👋
            </h1>

            <p
                style="
                color:var(--gray);
                margin-top:10px;
                "
            >
                What do you want to eat today?
            </p>

        </div>

        <!-- SEARCH -->
        <div
            style="
            background:#F6F6F6;
            border-radius:18px;
            padding:15px 20px;
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:30px;
            "
        >

            <span style="font-size:20px;">
                🔍
            </span>

            <input
                type="text"
                placeholder="Search food..."
                style="
                border:none;
                background:none;
                outline:none;
                width:100%;
                font-size:15px;
                "
            >

        </div>

        <!-- CATEGORY -->
        <div
            style="
            display:flex;
            gap:15px;
            overflow-x:auto;
            margin-bottom:30px;
            "
            class="scroll-hide"
        >

            <div
                style="
                min-width:120px;
                background:#FFD27C;
                padding:15px;
                border-radius:20px;
                font-weight:bold;
                display:flex;
                align-items:center;
                gap:10px;
                "
            >
                🍽️ All
            </div>

            <div
                style="
                min-width:120px;
                background:white;
                padding:15px;
                border-radius:20px;
                font-weight:bold;
                display:flex;
                align-items:center;
                gap:10px;
                "
            >
                🍔 Burger
            </div>

            <div
                style="
                min-width:120px;
                background:white;
                padding:15px;
                border-radius:20px;
                font-weight:bold;
                display:flex;
                align-items:center;
                gap:10px;
                "
            >
                🍕 Pizza
            </div>

            <div
                style="
                min-width:120px;
                background:white;
                padding:15px;
                border-radius:20px;
                font-weight:bold;
                display:flex;
                align-items:center;
                gap:10px;
                "
            >
                🍜 Noodles
            </div>

        </div>

        <!-- PRODUCT LIST -->
        <h2
            style="
            margin-bottom:20px;
            color:var(--dark);
            "
        >
            Popular Foods
        </h2>

        <?php if(count($products) > 0): ?>

            <?php foreach($products as $product): ?>

                <a
                    href="index.php?page=info&id=<?= $product['product_id'] ?>"
                    style="
                    text-decoration:none;
                    color:inherit;
                    "
                >

                    <div
                        style="
                        background:white;
                        border-radius:25px;
                        overflow:hidden;
                        margin-bottom:25px;
                        box-shadow:0 5px 20px rgba(0,0,0,0.05);
                        "
                    >

                        <!-- IMAGE -->
                        <img
                            src="<?= htmlspecialchars($product['image_url']) ?>"
                            style="
                            width:100%;
                            height:220px;
                            object-fit:cover;
                            "
                        >

                        <!-- CONTENT -->
                        <div style="padding:20px;">

                            <div
                                style="
                                display:flex;
                                justify-content:space-between;
                                align-items:start;
                                "
                            >

                                <div>

                                    <h3
                                        style="
                                        margin:0;
                                        color:var(--dark);
                                        "
                                    >
                                        <?= htmlspecialchars($product['name']) ?>
                                    </h3>

                                    <p
                                        style="
                                        color:var(--gray);
                                        margin-top:10px;
                                        font-size:14px;
                                        "
                                    >
                                        Delicious premium food
                                    </p>

                                </div>

                                <div
                                    style="
                                    background:#FFF3E9;
                                    color:var(--primary);
                                    padding:8px 12px;
                                    border-radius:12px;
                                    font-weight:bold;
                                    "
                                >
                                    ⭐ 4.8
                                </div>

                            </div>

                            <!-- PRICE -->
                            <div
                                style="
                                display:flex;
                                justify-content:space-between;
                                align-items:center;
                                margin-top:20px;
                                "
                            >

                                <h2
                                    style="
                                    margin:0;
                                    color:var(--primary);
                                    "
                                >
                                    Rp <?= number_format($product['price']) ?>
                                </h2>

                                <div
                                    style="
                                    background:var(--dark);
                                    color:white;
                                    width:45px;
                                    height:45px;
                                    border-radius:15px;
                                    display:grid;
                                    place-items:center;
                                    font-size:22px;
                                    "
                                >
                                    →
                                </div>

                            </div>

                        </div>

                    </div>

                </a>

            <?php endforeach; ?>

        <?php else: ?>

            <div
                style="
                background:white;
                border-radius:25px;
                padding:50px 20px;
                text-align:center;
                "
            >

                <div style="font-size:70px;">
                    🍽️
                </div>

                <h2>No Products Yet</h2>

                <p style="color:var(--gray);">
                    Admin has not added foods yet.
                </p>

            </div>

        <?php endif; ?>

    </div>

    <!-- BOTTOM NAVBAR -->
    <nav
        style="
        position:absolute;
        bottom:0;
        width:100%;
        height:90px;
        background:white;
        border-radius:30px 30px 0 0;
        display:flex;
        justify-content:space-around;
        align-items:center;
        box-shadow:0 -5px 20px rgba(0,0,0,0.05);
        "
    >

        <!-- HOME -->
        <a
            href="index.php?page=home"
            style="
            text-decoration:none;
            color:var(--primary);
            font-size:28px;
            "
        >
            🏠
        </a>

        <!-- HISTORY -->
        <a
            href="index.php?page=history"
            style="
            text-decoration:none;
            color:#98A8B8;
            font-size:28px;
            "
        >
            📦
        </a>

        <!-- PROFILE -->
        <a
            href="index.php?page=profile"
            style="
            text-decoration:none;
            color:#98A8B8;
            font-size:28px;
            "
        >
            👤
        </a>

    </nav>

</div>