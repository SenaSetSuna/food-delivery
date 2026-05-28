
<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location:index.php?page=login");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location:index.php?page=admin_dashboard");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT * FROM products
    WHERE product_id = ?
");

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location:index.php?page=admin_dashboard");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = preg_replace('/[^0-9]/', '', $_POST['price']);
    $description = $_POST['description'];
    $image = $_POST['image_url'];

    $update = $pdo->prepare("
        UPDATE products
        SET
            name = ?,
            type = ?,
            price = ?,
            description = ?,
            image_url = ?
        WHERE product_id = ?
    ");

    $update->execute([
        $name,
        $type,
        $price,
        $description,
        $image,
        $id
    ]);

    header("Location:index.php?page=admin_dashboard");
    exit;
}

?>

<div style="padding:20px;">

    <h2 style="margin-bottom:25px;">
        Edit Product
    </h2>

    <form method="POST">

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($product['name']) ?>"
            required
            style="width:100%;padding:15px;margin-bottom:15px;border-radius:12px;border:1px solid #ddd;"
        >

        <select
            name="type"
            required
            style="width:100%;padding:15px;margin-bottom:15px;border-radius:12px;border:1px solid #ddd;"
        >

            <option value="Food"
                <?= $product['type'] == 'Food' ? 'selected' : '' ?>>
                Food
            </option>

            <option value="Drink"
                <?= $product['type'] == 'Drink' ? 'selected' : '' ?>>
                Drink
            </option>

        </select>

        <input
            type="text"
            name="price"
            value="<?= htmlspecialchars($product['price']) ?>"
            required
            style="width:100%;padding:15px;margin-bottom:15px;border-radius:12px;border:1px solid #ddd;"
        >

        <textarea
            name="description"
            required
            style="width:100%;padding:15px;margin-bottom:15px;border-radius:12px;border:1px solid #ddd;height:120px;"
        ><?= htmlspecialchars($product['description']) ?></textarea>

        <input
            type="text"
            name="image_url"
            value="<?= htmlspecialchars($product['image_url']) ?>"
            required
            style="width:100%;padding:15px;margin-bottom:20px;border-radius:12px;border:1px solid #ddd;"
        >

        <button class="btn-orange">
            Save Changes
        </button>

    </form>

</div>

