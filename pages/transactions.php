<?php

if (!isset($_SESSION['customer_id'])) {
    header("Location:index.php?page=login");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (count($_SESSION['cart']) <= 0) {

    header("Location:index.php?page=cart");
    exit;
}

// =======================
// TOTAL
// =======================
$total = 0;

foreach ($_SESSION['cart'] as $item) {

    $total += $item['price'] * $item['qty'];
}

// =======================
// PAY NOW
// =======================
if (isset($_POST['pay_now'])) {

    $paymentMethod = $_POST['payment_method'];
    $address = trim($_POST['address']);

    if ($address != "") {

        // INSERT ORDER
        $stmt = $pdo->prepare("
            INSERT INTO orders
            (
                customer_id,
                total_price,
                payment_method,
                address
            )
            VALUES
            (
                ?, ?, ?, ?
            )
        ");

        $stmt->execute([
            $_SESSION['customer_id'],
            $total,
            $paymentMethod,
            $address
        ]);

        $orderId = $pdo->lastInsertId();

        // INSERT ORDER ITEMS
        foreach ($_SESSION['cart'] as $item) {

            $insert = $pdo->prepare("
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    quantity,
                    price
                )
                VALUES
                (
                    ?, ?, ?, ?
                )
            ");

            $insert->execute([
                $orderId,
                $item['id'],
                $item['qty'],
                $item['price']
            ]);
        }

        // CLEAR CART
        $_SESSION['cart'] = [];

        // REDIRECT HISTORY
        header("Location:index.php?page=history");
        exit;

    } else {

        $error = "Address must be filled.";
    }
}

?>

<div style="height:100%;display:flex;flex-direction:column;">

    <!-- HEADER -->
    <div
        style="
        padding:20px;
        display:flex;
        align-items:center;
        gap:15px;
        "
    >

        <a
            href="index.php?page=cart"
            style="
            text-decoration:none;
            background:#ECF0F4;
            width:45px;
            height:45px;
            border-radius:50%;
            display:grid;
            place-items:center;
            color:black;
            font-weight:bold;
            "
        >
            ←
        </a>

        <h2 style="margin:0;">
            Checkout
        </h2>

    </div>

    <!-- CONTENT -->
    <div
        class="scroll-area"
        style="
        padding:0 20px 220px 20px;
        "
    >

        <!-- ERROR -->
        <?php if(isset($error)): ?>

            <div
                style="
                background:#FFE5E5;
                color:#D8000C;
                padding:15px;
                border-radius:15px;
                margin-bottom:20px;
                "
            >
                <?= $error ?>
            </div>

        <?php endif; ?>

        <!-- ORDER SUMMARY -->
        <div
            style="
            background:white;
            border-radius:25px;
            padding:20px;
            margin-bottom:20px;
            "
        >

            <h3 style="margin-top:0;">
                Your Order
            </h3>

            <?php foreach($_SESSION['cart'] as $item): ?>

                <div
                    style="
                    display:flex;
                    align-items:center;
                    gap:15px;
                    margin-top:20px;
                    "
                >

                    <img
                        src="<?= htmlspecialchars($item['image']) ?>"
                        style="
                        width:80px;
                        height:80px;
                        border-radius:15px;
                        object-fit:cover;
                        "
                    >

                    <div style="flex:1;">

                        <h4 style="margin:0;">
                            <?= htmlspecialchars($item['name']) ?>
                        </h4>

                        <p
                            style="
                            color:var(--gray);
                            margin-top:8px;
                            "
                        >
                            Qty: <?= $item['qty'] ?>
                        </p>

                    </div>

                    <div
                        style="
                        font-weight:bold;
                        color:var(--primary);
                        "
                    >
                        Rp <?= number_format($item['price'] * $item['qty']) ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <!-- FORM -->
        <form method="POST">

            <!-- PAYMENT -->
            <div
                style="
                background:white;
                border-radius:25px;
                padding:20px;
                margin-bottom:20px;
                "
            >

                <h3 style="margin-top:0;">
                    Payment Method
                </h3>

                <div
                    style="
                    margin-top:20px;
                    display:flex;
                    flex-direction:column;
                    gap:15px;
                    "
                >

                    <label
                        style="
                        background:#FFF3E9;
                        padding:18px;
                        border-radius:18px;
                        cursor:pointer;
                        "
                    >

                        <input
                            type="radio"
                            name="payment_method"
                            value="Cash On Delivery"
                            checked
                        >

                        Cash On Delivery

                    </label>

                    <label
                        style="
                        background:#F1F5F9;
                        padding:18px;
                        border-radius:18px;
                        cursor:pointer;
                        "
                    >

                        <input
                            type="radio"
                            name="payment_method"
                            value="Bank Transfer"
                        >

                        Bank Transfer

                    </label>

                    <label
                        style="
                        background:#F1F5F9;
                        padding:18px;
                        border-radius:18px;
                        cursor:pointer;
                        "
                    >

                        <input
                            type="radio"
                            name="payment_method"
                            value="E-Wallet"
                        >

                        E-Wallet

                    </label>

                </div>

            </div>

            <!-- ADDRESS -->
            <div
                style="
                background:white;
                border-radius:25px;
                padding:20px;
                margin-bottom:20px;
                "
            >

                <h3 style="margin-top:0;">
                    Delivery Address
                </h3>

                <textarea
                    name="address"
                    required
                    placeholder="Input your complete address..."
                    style="
                    width:100%;
                    height:120px;
                    margin-top:15px;
                    border:none;
                    background:#F6F6F6;
                    border-radius:18px;
                    padding:15px;
                    resize:none;
                    outline:none;
                    font-size:14px;
                    "
                ></textarea>

            </div>

            <!-- FOOTER -->
            <div
                style="
                position:absolute;
                bottom:0;
                left:0;
                width:100%;
                background:white;
                padding:25px;
                border-radius:30px 30px 0 0;
                box-shadow:0 -10px 30px rgba(0,0,0,0.08);
                "
            >

                <div
                    style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:20px;
                    "
                >

                    <div>

                        <small style="color:var(--gray);">
                            TOTAL PAYMENT
                        </small>

                        <h2 style="margin:5px 0 0 0;">
                            Rp <?= number_format($total) ?>
                        </h2>

                    </div>

                </div>

                <button
                    type="submit"
                    name="pay_now"
                    class="btn-orange"
                >
                    Pay Now
                </button>

            </div>

        </form>

    </div>

</div>