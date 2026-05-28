<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

if (isset($_POST['save_product'])) {
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $price = intval($_POST['price']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);

    if ($name != "" && $price > 0 && $description != "" && $image_url != "") {
        $stmt = $pdo->prepare("INSERT INTO products (name, type, price, description, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $type, $price, $description, $image_url]);
        header("Location: index.php?page=admin_dashboard");
        exit;
    } else {
        $error = "Semua bidang formulir wajib diisi dengan benar!";
    }
}
?>

<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 25px;">
        <a href="index.php?page=admin_dashboard" style="text-decoration:none; color:#4F46E5; font-weight:600; font-size:14px;">← Kembali ke Dashboard</a>
        <h1 style="margin: 15px 0 5px 0; font-size: 24px; color:#1E293B;">Tambah Menu Kuliner</h1>
        <p style="margin:0; color:#64748B; font-size:14px;">Masukkan rincian informasi produk makanan/minuman baru</p>
    </div>

    <?php if (isset($error)): ?>
        <div style="background: #FEE2E2; color: #991B1B; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <div style="background: white; padding: 30px; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display:block; font-size:14px; font-weight:600; color:#334155; margin-bottom:8px;">Nama Produk</label>
                <input type="text" name="name" required placeholder="Misal: Nasi Goreng Spesial" style="width:100%; padding:10px 14px; border:1px solid #CBD5E1; border-radius:8px; outline:none; font-size:14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; font-size:14px; font-weight:600; color:#334155; margin-bottom:8px;">Kategori</label>
                <select name="type" style="width:100%; padding:10px 14px; border:1px solid #CBD5E1; border-radius:8px; outline:none; font-size:14px; background:white;">
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                    <option value="Cemilan">Cemilan</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; font-size:14px; font-weight:600; color:#334155; margin-bottom:8px;">Harga Jual (Rp)</label>
                <input type="number" name="price" required placeholder="Contoh: 25000" style="width:100%; padding:10px 14px; border:1px solid #CBD5E1; border-radius:8px; outline:none; font-size:14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; font-size:14px; font-weight:600; color:#334155; margin-bottom:8px;">URL Tautan Gambar</label>
                <input type="url" name="image_url" required placeholder="https://images.unsplash.com/... atau images/nama.jpg" style="width:100%; padding:10px 14px; border:1px solid #CBD5E1; border-radius:8px; outline:none; font-size:14px;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display:block; font-size:14px; font-weight:600; color:#334155; margin-bottom:8px;">Deskripsi Hidangan</label>
                <textarea name="description" required placeholder="Tulis komposisi rasa atau detail produk..." style="width:100%; height:100px; padding:10px 14px; border:1px solid #CBD5E1; border-radius:8px; outline:none; font-size:14px; resize:none; font-family:inherit;"></textarea>
            </div>

            <button type="submit" name="save_product" style="width:100%; background:#4F46E5; color:white; border:none; padding:12px; border-radius:10px; font-weight:600; cursor:pointer; font-size:15px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);">Simpan ke Katalog</button>
        </form>
    </div>
</div>