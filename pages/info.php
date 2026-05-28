<?php

if (!isset($_SESSION['customer_id'])) {
    header("Location:index.php?page=login");
    exit;
}

// =======================
// GET PRODUCT ID
// =======================
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// =======================
// GET PRODUCT
// =======================
$stmt = $pdo->prepare("
    SELECT *
    FROM products
    WHERE product_id = ?
");

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

// =======================
// PRODUCT NOT FOUND
// =======================
if (!$product) {

    echo "
    <div style='padding:30px;text-align:center;'>
        <h2>Product Not Found</h2>
    </div>
    ";

    exit;
}

// =======================
// ADD TO CART
// =======================
if (isset($_POST['add_to_cart'])) {

    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

    if ($qty < 1) {
        $qty = 1;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $productId = $product['product_id'];

    // kalau produk sudah ada di cart
    if (isset($_SESSION['cart'][$productId])) {

        $_SESSION['cart'][$productId]['qty'] += $qty;

    } else {

        $_SESSION['cart'][$productId] = [

            'id' => $product['product_id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image_url'],
            'qty' => $qty
        ];
    }

    header("Location:index.php?page=cart");

    exit;
}

?>

<div style="height:100%;display:flex;flex-direction:column;">

    <!-- IMAGE -->
    <div
        style="
        height:42vh;
        min-height:320px;
        position:relative;
        "
    >

        <img
            src="<?= htmlspecialchars($product['image_url']) ?>"
            style="
            width:100%;
            height:100%;
            object-fit:cover;
            "
        >

        <!-- BACK -->
        <a
            href="index.php?page=home"
            style="
            position:absolute;
            top:20px;
            left:20px;
            width:45px;
            height:45px;
            border-radius:50%;
            background:white;
            display:grid;
            place-items:center;
            text-decoration:none;
            color:black;
            font-size:20px;
            font-weight:bold;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
            "
        >
            ←
        </a>

    </div>

    <!-- CONTENT -->
    <div
        class="scroll-area"
        style="
        padding:25px 20px 160px 20px;
        "
    >

        <!-- TITLE -->
        <div
            style="
            display:flex;
            justify-content:space-between;
            align-items:start;
            gap:15px;
            "
        >

            <div>

                <h1
                    style="
                    margin:0;
                    color:var(--dark);
                    font-size:30px;
                    "
                >
                    <?= htmlspecialchars($product['name']) ?>
                </h1>

                <p
                    style="
                    color:var(--gray);
                    margin-top:10px;
                    "
                >
                    Delicious premium food
                </p>

            </div>

            <!-- RATING -->
            <div
                style="
                background:#FFF3E9;
                color:var(--primary);
                padding:10px 15px;
                border-radius:15px;
                font-weight:bold;
                "
            >
                ⭐ 4.8
            </div>

        </div>

        <!-- INFO -->
        <div
            style="
            display:flex;
            gap:15px;
            margin-top:25px;
            flex-wrap:wrap;
            "
        >

            <div
                style="
                background:#F1F5F9;
                padding:12px 18px;
                border-radius:15px;
                font-weight:bold;
                "
            >
                🚚 Free Delivery
            </div>

            <div
                style="
                background:#F1F5F9;
                padding:12px 18px;
                border-radius:15px;
                font-weight:bold;
                "
            >
                ⏱️ 20-30 Min
            </div>

        </div>

        <!-- DESCRIPTION -->
        <div style="margin-top:35px;">

            <h3 style="margin-bottom:15px;">
                Description
            </h3>

            <p
                style="
                color:var(--gray);
                line-height:1.8;
                font-size:15px;
                "
            >
                <?= !empty($product['description'])
                    ? htmlspecialchars($product['description'])
                    : 'Fresh delicious food with premium ingredients and authentic taste.' ?>
            </p>

        </div>

        <!-- PRICE CARD -->
        <div
            style="
            background:white;
            border-radius:25px;
            padding:25px;
            margin-top:35px;
            box-shadow:0 5px 20px rgba(0,0,0,0.05);
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

                    <small
                        style="
                        color:var(--gray);
                        font-weight:bold;
                        "
                    >
                        PRICE
                    </small>

                    <h1
                        style="
                        margin:8px 0 0 0;
                        color:var(--primary);
                        "
                    >
                        Rp <?= number_format($product['price']) ?>
                    </h1>

                </div>

                <!-- QTY -->
                <div
                    style="
                    background:var(--dark);
                    color:white;
                    padding:12px 20px;
                    border-radius:20px;
                    display:flex;
                    align-items:center;
                    gap:20px;
                    "
                >

                    <!-- MINUS -->
                    <button
                        type="button"
                        onclick="decreaseQty()"
                        style="
                        background:none;
                        border:none;
                        color:white;
                        font-size:24px;
                        cursor:pointer;
                        "
                    >
                        −
                    </button>

                    <!-- QTY -->
                    <span
                        id="qtyText"
                        style="
                        font-weight:bold;
                        font-size:18px;
                        min-width:20px;
                        text-align:center;
                        "
                    >
                        1
                    </span>

                    <!-- PLUS -->
                    <button
                        type="button"
                        onclick="increaseQty()"
                        style="
                        background:none;
                        border:none;
                        color:white;
                        font-size:24px;
                        cursor:pointer;
                        "
                    >
                        +
                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- FOOTER -->
    <div
        style="
        position:absolute;
        bottom:0;
        width:100%;
        background:white;
        padding:25px;
        border-radius:30px 30px 0 0;
        box-shadow:0 -10px 30px rgba(0,0,0,0.08);
        "
    >

        <form method="POST">

            <input
                type="hidden"
                name="qty"
                id="qtyInput"
                value="1"
            >

            <button
                type="submit"
                name="add_to_cart"
                class="btn-orange"
            >
                Add To Cart
            </button>

        </form>

    </div>

</div>

<script>

let qty = 1;

function increaseQty() {

    qty++;

    document.getElementById('qtyText').innerText = qty;

    document.getElementById('qtyInput').value = qty;
}

function decreaseQty() {

    if (qty > 1) {

        qty--;

        document.getElementById('qtyText').innerText = qty;

        document.getElementById('qtyInput').value = qty;
    }
}

</script>