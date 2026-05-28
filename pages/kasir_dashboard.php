<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'kasir') {

    header("Location:index.php?page=login");
    exit;
}

// =======================
// ACCEPT ORDER
// =======================
if (isset($_GET['accept'])) {

    $id = $_GET['accept'];

    $update = $pdo->prepare("
        UPDATE orders
        SET status='Accepted'
        WHERE order_id=?
    ");

    $update->execute([$id]);

    header("Location:index.php?page=kasir_dashboard");
    exit;
}

// =======================
// CANCEL ORDER
// =======================
if (isset($_GET['cancel'])) {

    $id = $_GET['cancel'];

    $update = $pdo->prepare("
        UPDATE orders
        SET status='Cancelled'
        WHERE order_id=?
    ");

    $update->execute([$id]);

    header("Location:index.php?page=kasir_dashboard");
    exit;
}

// =======================
// TOTAL ORDER
// =======================
$totalOrder = $pdo->query("
    SELECT COUNT(*) FROM orders
")->fetchColumn();

// =======================
// PENDING
// =======================
$pendingOrder = $pdo->query("
    SELECT COUNT(*) FROM orders
    WHERE status='Pending'
")->fetchColumn();

// =======================
// ACCEPTED
// =======================
$acceptedOrder = $pdo->query("
    SELECT COUNT(*) FROM orders
    WHERE status='Accepted'
")->fetchColumn();

// =======================
// CANCELLED
// =======================
$cancelledOrder = $pdo->query("
    SELECT COUNT(*) FROM orders
    WHERE status='Cancelled'
")->fetchColumn();

// =======================
// TOTAL INCOME
// =======================
$totalIncome = $pdo->query("
    SELECT SUM(total_price)
    FROM orders
    WHERE status='Accepted'
")->fetchColumn();

if (!$totalIncome) {
    $totalIncome = 0;
}

// =======================
// AMBIL SEMUA ORDER
// =======================
$stmt = $pdo->prepare("
    SELECT
        orders.*,
        customers.name as customer_name
    FROM orders
    LEFT JOIN customers
    ON orders.customer_id = customers.customer_id
    ORDER BY orders.order_id DESC
");

$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div style="height:100%;display:flex;flex-direction:column;">

    <!-- HEADER -->
    <div
        style="
        padding:25px 20px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        "
    >

        <div>

            <h2 style="margin:0;">
                Cashier Dashboard
            </h2>

            <p
                style="
                margin-top:8px;
                color:gray;
                "
            >
                Daily Transaction Report
            </p>

        </div>

        <a
            href="logout.php"
            style="
            text-decoration:none;
            background:#FFEEEE;
            color:#FF4B4B;
            padding:12px 18px;
            border-radius:15px;
            font-weight:bold;
            "
        >
            Logout
        </a>

    </div>

    <!-- CONTENT -->
    <div
        class="scroll-area"
        style="
        padding:0 20px 120px 20px;
        "
    >

        <!-- STATS -->
        <div
            style="
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:15px;
            margin-bottom:20px;
            "
        >

            <div
                style="
                background:white;
                padding:20px;
                border-radius:25px;
                "
            >

                <small style="color:gray;">
                    TOTAL ORDER
                </small>

                <h2 style="margin:10px 0 0 0;">
                    <?= $totalOrder ?>
                </h2>

            </div>

            <div
                style="
                background:white;
                padding:20px;
                border-radius:25px;
                "
            >

                <small style="color:gray;">
                    PENDING
                </small>

                <h2 style="margin:10px 0 0 0;">
                    <?= $pendingOrder ?>
                </h2>

            </div>

        </div>

        <!-- INCOME -->
        <div
            style="
            background:linear-gradient(135deg,#FF7622,#FF9B54);
            color:white;
            padding:25px;
            border-radius:30px;
            margin-bottom:25px;
            "
        >

            <small>
                TOTAL INCOME
            </small>

            <h1 style="margin:10px 0 0 0;">
                Rp <?= number_format($totalIncome) ?>
            </h1>

        </div>

        <!-- BOOKKEEPING -->
        <div
            style="
            background:white;
            border-radius:30px;
            padding:25px;
            margin-bottom:25px;
            "
        >

            <h3 style="margin-top:0;">
                Financial Summary
            </h3>

            <div
                style="
                display:flex;
                justify-content:space-between;
                margin-top:20px;
                "
            >

                <span style="color:gray;">
                    Accepted Orders
                </span>

                <b style="color:#22C55E;">
                    <?= $acceptedOrder ?>
                </b>

            </div>

            <div
                style="
                display:flex;
                justify-content:space-between;
                margin-top:18px;
                "
            >

                <span style="color:gray;">
                    Cancelled Orders
                </span>

                <b style="color:#EF4444;">
                    <?= $cancelledOrder ?>
                </b>

            </div>

            <div
                style="
                display:flex;
                justify-content:space-between;
                margin-top:18px;
                "
            >

                <span style="color:gray;">
                    Estimated Revenue
                </span>

                <b style="color:var(--primary);">
                    Rp <?= number_format($totalIncome) ?>
                </b>

            </div>

        </div>

        <!-- HISTORY -->
        <h3 style="margin-bottom:20px;">
            Transaction History
        </h3>

        <?php foreach($orders as $order): ?>

            <div
                style="
                background:white;
                border-radius:25px;
                padding:20px;
                margin-bottom:20px;
                "
            >

                <!-- TOP -->
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
                            "
                        >
                            <?= htmlspecialchars($order['customer_name']) ?>
                        </p>

                    </div>

                    <div
                        style="
                        padding:10px 15px;
                        border-radius:15px;
                        font-weight:bold;

                        <?php

                        if($order['status'] == 'Accepted') {
                            echo 'background:#DCFCE7;color:#166534;';
                        }
                        elseif($order['status'] == 'Cancelled') {
                            echo 'background:#FEE2E2;color:#991B1B;';
                        }
                        else {
                            echo 'background:#FEF3C7;color:#92400E;';
                        }

                        ?>
                        "
                    >

                        <?= $order['status'] ?>

                    </div>

                </div>

                <hr
                    style="
                    border:none;
                    border-top:1px solid #eee;
                    margin:20px 0;
                    "
                >

                <!-- DETAIL -->
                <div
                    style="
                    display:flex;
                    justify-content:space-between;
                    margin-bottom:12px;
                    "
                >

                    <span style="color:gray;">
                        Payment
                    </span>

                    <b>
                        <?= htmlspecialchars($order['payment_method']) ?>
                    </b>

                </div>

                <div
                    style="
                    display:flex;
                    justify-content:space-between;
                    margin-bottom:15px;
                    "
                >

                    <span style="color:gray;">
                        Total
                    </span>

                    <b style="color:var(--primary);">
                        Rp <?= number_format($order['total_price']) ?>
                    </b>

                </div>

                <!-- ADDRESS -->
                <div
                    style="
                    background:#F8FAFC;
                    padding:15px;
                    border-radius:18px;
                    margin-bottom:20px;
                    "
                >

                    <small
                        style="
                        color:gray;
                        text-transform:uppercase;
                        font-weight:bold;
                        "
                    >
                        Address
                    </small>

                    <p
                        style="
                        margin:10px 0 0 0;
                        line-height:1.5;
                        "
                    >
                        <?= htmlspecialchars($order['address']) ?>
                    </p>

                </div>

                <!-- BUTTON -->
                <?php if($order['status'] == 'Pending'): ?>

                    <div
                        style="
                        display:flex;
                        gap:10px;
                        "
                    >

                        <a
                            href="index.php?page=kasir_dashboard&accept=<?= $order['order_id'] ?>"
                            style="flex:1;"
                        >

                            <button
                                style="
                                width:100%;
                                background:#22C55E;
                                color:white;
                                border:none;
                                padding:15px;
                                border-radius:15px;
                                font-weight:bold;
                                cursor:pointer;
                                "
                            >
                                Accept
                            </button>

                        </a>

                        <a
                            href="index.php?page=kasir_dashboard&cancel=<?= $order['order_id'] ?>"
                            style="flex:1;"
                        >

                            <button
                                style="
                                width:100%;
                                background:#EF4444;
                                color:white;
                                border:none;
                                padding:15px;
                                border-radius:15px;
                                font-weight:bold;
                                cursor:pointer;
                                "
                            >
                                Cancel
                            </button>

                        </a>

                    </div>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

</div>