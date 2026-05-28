<?php

if (!isset($_SESSION['customer_id'])) {
    header("Location:index.php?page=login");
    exit;
}

// AMBIL SEMUA ORDER USER
$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE customer_id = ?
    ORDER BY order_id DESC
");

$stmt->execute([
    $_SESSION['customer_id']
]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            href="index.php?page=home"
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
            Transaction History
        </h2>

    </div>

    <!-- CONTENT -->
    <div
        class="scroll-area"
        style="
        padding:0 20px 120px 20px;
        "
    >

        <?php if(count($orders) <= 0): ?>

            <div
                style="
                text-align:center;
                margin-top:120px;
                "
            >

                <h2>No Transactions Yet</h2>

                <p style="color:gray;">
                    Start ordering your favorite foods
                </p>

            </div>

        <?php else: ?>

            <?php foreach($orders as $order): ?>

                <div
                    style="
                    background:white;
                    border-radius:25px;
                    padding:20px;
                    margin-bottom:20px;
                    "
                >

                    <div
                        style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        "
                    >

                        <div>

                            <h3 style="margin:0;">
                                Order #<?= $order['order_id'] ?>
                            </h3>

                            <p
                                style="
                                color:gray;
                                margin-top:8px;
                                font-size:14px;
                                "
                            >
                                <?= htmlspecialchars($order['payment_method']) ?>
                            </p>

                        </div>

                        <div
                            style="
                            background:#FFF3E9;
                            color:var(--primary);
                            padding:10px 15px;
                            border-radius:15px;
                            font-weight:bold;
                            "
                        >
                            Paid
                        </div>

                    </div>

                    <hr
                        style="
                        border:none;
                        border-top:1px solid #eee;
                        margin:20px 0;
                        "
                    >

                    <div
                        style="
                        display:flex;
                        justify-content:space-between;
                        "
                    >

                        <span style="color:gray;">
                            Total Payment
                        </span>

                        <b>
                            Rp <?= number_format($order['total_price']) ?>
                        </b>

                    </div>

                    <div
                        style="
                        margin-top:15px;
                        color:gray;
                        font-size:14px;
                        line-height:1.5;
                        "
                    >
                        <?= htmlspecialchars($order['address']) ?>
                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>