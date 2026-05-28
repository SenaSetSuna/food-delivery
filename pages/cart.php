<?php

if (!isset($_SESSION['customer_id'])) {
    header("Location:index.php?page=login");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// HAPUS ITEM
if (isset($_GET['remove'])) {

    $removeId = $_GET['remove'];

    foreach ($_SESSION['cart'] as $key => $item) {

        if ($item['id'] == $removeId) {

            unset($_SESSION['cart'][$key]);
        }
    }

    $_SESSION['cart'] = array_values($_SESSION['cart']);

    header("Location:index.php?page=cart");
    exit;
}

// TAMBAH QTY
if (isset($_GET['plus'])) {

    $id = $_GET['plus'];

    foreach ($_SESSION['cart'] as &$item) {

        if ($item['id'] == $id) {

            $item['qty']++;
        }
    }

    header("Location:index.php?page=cart");
    exit;
}

// KURANG QTY
if (isset($_GET['minus'])) {

    $id = $_GET['minus'];

    foreach ($_SESSION['cart'] as &$item) {

        if ($item['id'] == $id) {

            if ($item['qty'] > 1) {

                $item['qty']--;
            }
        }
    }

    header("Location:index.php?page=cart");
    exit;
}

// TOTAL
$total = 0;

foreach ($_SESSION['cart'] as $item) {

    $total += $item['price'] * $item['qty'];
}

?>

<div style="height:100%;display:flex;flex-direction:column;">

    <div
        style="
        padding:20px;
        display:flex;
        justify-content:space-between;
        align-items:center;
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
            My Cart
        </h2>

        <div style="width:45px;"></div>

    </div>

    <div
        class="scroll-area"
        style="
        padding:0 20px 180px 20px;
        "
    >

        <?php if(count($_SESSION['cart']) <= 0): ?>

            <div
                style="
                text-align:center;
                margin-top:100px;
                "
            >

                <h2>Your cart is empty</h2>

                <a
                    href="index.php?page=home"
                    style="
                    color:var(--primary);
                    text-decoration:none;
                    "
                >
                    Back to home
                </a>

            </div>

        <?php else: ?>

            <?php foreach($_SESSION['cart'] as $item): ?>

                <div
                    style="
                    background:white;
                    border-radius:25px;
                    padding:15px;
                    margin-bottom:20px;
                    display:flex;
                    gap:15px;
                    align-items:center;
                    "
                >

                    <img
                        src="<?= htmlspecialchars($item['image']) ?>"
                        style="
                        width:100px;
                        height:100px;
                        border-radius:20px;
                        object-fit:cover;
                        "
                    >

                    <div style="flex:1;">

                        <h3 style="margin:0;">
                            <?= htmlspecialchars($item['name']) ?>
                        </h3>

                        <p
                            style="
                            color:var(--primary);
                            font-weight:bold;
                            margin-top:10px;
                            "
                        >
                            Rp <?= number_format($item['price']) ?>
                        </p>

                        <div
                            style="
                            margin-top:15px;
                            display:flex;
                            align-items:center;
                            gap:10px;
                            "
                        >

                            <a
                                href="index.php?page=cart&minus=<?= $item['id'] ?>"
                                style="
                                width:35px;
                                height:35px;
                                background:#F1F5F9;
                                border-radius:10px;
                                display:grid;
                                place-items:center;
                                text-decoration:none;
                                color:black;
                                font-weight:bold;
                                "
                            >
                                -
                            </a>

                            <span
                                style="
                                font-weight:bold;
                                font-size:18px;
                                "
                            >
                                <?= $item['qty'] ?>
                            </span>

                            <a
                                href="index.php?page=cart&plus=<?= $item['id'] ?>"
                                style="
                                width:35px;
                                height:35px;
                                background:var(--primary);
                                border-radius:10px;
                                display:grid;
                                place-items:center;
                                text-decoration:none;
                                color:white;
                                font-weight:bold;
                                "
                            >
                                +
                            </a>

                        </div>

                    </div>

                    <a
                        href="index.php?page=cart&remove=<?= $item['id'] ?>"
                        style="
                        text-decoration:none;
                        color:red;
                        font-size:22px;
                        "
                    >
                        ✕
                    </a>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <?php if(count($_SESSION['cart']) > 0): ?>

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
                        TOTAL PRICE
                    </small>

                    <h2 style="margin:5px 0 0 0;">
                        Rp <?= number_format($total) ?>
                    </h2>

                </div>

            </div>

            <button
                class="btn-orange"
                onclick="window.location.href='index.php?page=transactions'"
            >
                Checkout
            </button>

        </div>

    <?php endif; ?>

</div>