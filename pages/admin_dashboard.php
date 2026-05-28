<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

// Mengambil data nama admin yang sedang login berdasarkan session
$adminName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin Foodel';

// Rekap metrik admin
$totalMenu = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();

// Ambil list katalog menu kuliner
$products = $pdo->query("SELECT * FROM products ORDER BY product_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #E2E8F0; padding-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: #FFEAD6; display: flex; align-items: center; justify-content: center; border: 2px solid #FF7A00; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <span style="color: #FF7A00; font-weight: 700; font-size: 20px;"><?= strtoupper(substr($adminName, 0, 1)) ?></span>
        </div>
        <div>
            <h1 style="margin: 0; color: #0F172A; font-size: 24px; font-weight: 700;">Selamat Datang, Admin <span style="color: #FF7A00;"><?= htmlspecialchars($adminName) ?></span>! 👑</h1>
            <p style="margin: 3px 0 0 0; color: #64748B; font-size: 14px;">Kontrol penuh katalog menu, harga, dan operasional Foodel</p>
        </div>
    </div>
    <div style="display: flex; gap: 12px; align-items: center;">
        <a href="index.php?page=add_product" style="background: #4F46E5; color: white; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);">+ Tambah Menu Baru</a>
        <a href="logout.php" style="background: #FEE2E2; color: #DC2626; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px;">Keluar</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid #E2E8F0; border-left: 5px solid #4F46E5;">
        <h3 style="margin: 0; font-size: 13px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Varian Menu Aktif</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: 700; color: #0F172A;"><?= $totalMenu ?> Item</p>
    </div>
    <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid #E2E8F0; border-left: 5px solid #0EA5E9;">
        <h3 style="margin: 0; font-size: 13px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Total Pelanggan</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: 700; color: #0F172A;"><?= $totalCustomers ?> Pengguna</p>
    </div>
</div>

<div style="background: white; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
    <div style="padding: 20px; border-bottom: 1px solid #E2E8F0; background: white;">
        <h2 style="margin:0; font-size: 18px; color: #1E293B;">Katalog Menu Kuliner</h2>
    </div>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background: #F8FAFC;">
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0; width: 60px;">ID</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0; width: 80px;">Foto</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">Nama Menu</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0; width: 120px;">Kategori</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0; width: 150px;">Harga Jual</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">Deskripsi Rasa</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0; text-align: center; width: 150px;">Opsi Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94A3B8; padding: 40px;">Katalog kosong. Silakan klik tombol Tambah Menu Baru.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($products as $p): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 16px; font-weight: bold; color: #64748B;">#<?= $p['product_id'] ?></td>
                        <td style="padding: 16px;">
                            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="Menu" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 1px solid #E2E8F0; display: block;">
                        </td>
                        <td style="padding: 16px; font-weight: 600; color: #0F172A;"><?= htmlspecialchars($p['name']) ?></td>
                        <td style="padding: 16px;"><span style="background: #F1F5F9; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 500; color: #475569;"><?= htmlspecialchars($p['type']) ?></span></td>
                        <td style="padding: 16px; font-weight: 600; color: #16A34A;">Rp <?= number_format($p['price']) ?></td>
                        <td style="padding: 16px; max-width: 250px; color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= htmlspecialchars($p['description']) ?>
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <a href="index.php?page=edit_product&id=<?= $p['product_id'] ?>" style="color: #4F46E5; text-decoration: none; font-weight: 600; margin-right: 15px; font-size: 13px;">Edit</a>
                            <a href="index.php?page=delete_product&id=<?= $p['product_id'] ?>" style="color: #EF4444; text-decoration: none; font-weight: 600; font-size: 13px;" onclick="return confirm('Hapus hidangan ini dari menu?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>