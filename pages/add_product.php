<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location:index.php?page=login");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image_url']);

    if (
        empty($name) ||
        empty($type) ||
        empty($price) ||
        empty($description) ||
        empty($image)
    ) {

        $error = "All fields are required.";

    } else {

        // Bersihkan harga biar hanya angka
        $cleanPrice = preg_replace('/[^0-9]/', '', $price);

        try {

            $stmt = $pdo->prepare("
                INSERT INTO products
                (
                    name,
                    type,
                    price,
                    description,
                    image_url
                )
                VALUES
                (
                    ?, ?, ?, ?, ?
                )
            ");

            $stmt->execute([
                $name,
                $type,
                $cleanPrice,
                $description,
                $image
            ]);

            $success = "Product added successfully!";

        } catch (PDOException $e) {

            $error = "Failed to add product.";

        }
    }
}

?>

<div style="height:100%;display:flex;flex-direction:column;">

    <div class="scroll-area" style="padding:20px;">

        <!-- HEADER -->
        <div
            style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:30px;
            "
        >

            <div>

                <h2 style="margin:0;color:var(--dark);">
                    Add Product
                </h2>

                <p style="margin-top:5px;color:var(--gray);">
                    Add food or drink menu
                </p>

            </div>

            <a
                href="index.php?page=admin_dashboard"
                style="
                text-decoration:none;
                background:#F1F5F9;
                width:45px;
                height:45px;
                border-radius:50%;
                display:grid;
                place-items:center;
                color:black;
                font-weight:bold;
                "
            >
                ✕
            </a>

        </div>

        <!-- ERROR -->
        <?php if($error): ?>

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

        <!-- SUCCESS -->
        <?php if($success): ?>

            <div
                style="
                background:#E7F9ED;
                color:#157347;
                padding:15px;
                border-radius:15px;
                margin-bottom:20px;
                "
            >
                <?= $success ?>
            </div>

        <?php endif; ?>

        <!-- FORM -->
        <form method="POST">

            <div
                style="
                background:white;
                border-radius:25px;
                padding:25px;
                box-shadow:0 10px 30px rgba(0,0,0,0.05);
                "
            >

                <!-- PRODUCT NAME -->
                <div style="margin-bottom:20px;">

                    <label
                        style="
                        font-size:13px;
                        font-weight:bold;
                        color:var(--gray);
                        text-transform:uppercase;
                        "
                    >
                        Product Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        placeholder="Burger Crispy"
                        required
                        style="
                        width:100%;
                        margin-top:8px;
                        padding:16px;
                        border-radius:15px;
                        border:1px solid #eee;
                        background:#F8FAFC;
                        outline:none;
                        font-size:15px;
                        "
                    >

                </div>

                <!-- CATEGORY -->
                <div style="margin-bottom:20px;">

                    <label
                        style="
                        font-size:13px;
                        font-weight:bold;
                        color:var(--gray);
                        text-transform:uppercase;
                        "
                    >
                        Category
                    </label>

                    <select
                        name="type"
                        required
                        style="
                        width:100%;
                        margin-top:8px;
                        padding:16px;
                        border-radius:15px;
                        border:1px solid #eee;
                        background:#F8FAFC;
                        outline:none;
                        font-size:15px;
                        "
                    >

                        <option value="">
                            Select category
                        </option>

                        <option value="Food">
                            Food
                        </option>

                        <option value="Drink">
                            Drink
                        </option>

                    </select>

                </div>

                <!-- PRICE -->
                <div style="margin-bottom:20px;">

                    <label
                        style="
                        font-size:13px;
                        font-weight:bold;
                        color:var(--gray);
                        text-transform:uppercase;
                        "
                    >
                        Price
                    </label>

                    <div
                        style="
                        display:flex;
                        align-items:center;
                        background:#F8FAFC;
                        border:1px solid #eee;
                        border-radius:15px;
                        padding-left:15px;
                        margin-top:8px;
                        "
                    >

                        <span
                            style="
                            font-weight:bold;
                            color:var(--primary);
                            font-size:18px;
                            "
                        >
                            Rp
                        </span>

                        <input
                            type="text"
                            name="price"
                            placeholder="25000"
                            required
                            style="
                            width:100%;
                            padding:16px;
                            border:none;
                            background:transparent;
                            outline:none;
                            font-size:15px;
                            "
                        >

                    </div>

                    <small
                        style="
                        color:var(--gray);
                        display:block;
                        margin-top:8px;
                        "
                    >
                        Numbers only.
                    </small>

                </div>

                <!-- DESCRIPTION -->
                <div style="margin-bottom:20px;">

                    <label
                        style="
                        font-size:13px;
                        font-weight:bold;
                        color:var(--gray);
                        text-transform:uppercase;
                        "
                    >
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        placeholder="Delicious spicy burger..."
                        required
                        style="
                        width:100%;
                        margin-top:8px;
                        padding:16px;
                        border-radius:15px;
                        border:1px solid #eee;
                        background:#F8FAFC;
                        outline:none;
                        font-size:15px;
                        resize:none;
                        "
                    ></textarea>

                </div>

                <!-- IMAGE URL -->
                <div style="margin-bottom:30px;">

                    <label
                        style="
                        font-size:13px;
                        font-weight:bold;
                        color:var(--gray);
                        text-transform:uppercase;
                        "
                    >
                        Image URL
                    </label>

                    <input
                        type="text"
                        name="image_url"
                        placeholder="https://images.unsplash.com/..."
                        required
                        style="
                        width:100%;
                        margin-top:8px;
                        padding:16px;
                        border-radius:15px;
                        border:1px solid #eee;
                        background:#F8FAFC;
                        outline:none;
                        font-size:15px;
                        "
                    >

                </div>

                <!-- BUTTON -->
                <button
                    type="submit"
                    class="btn-orange"
                >
                    Add Product
                </button>

            </div>

        </form>

    </div>

</div>