<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kasir') {
    header("Location: index.php?page=login");
    exit;
}

// Mengambil data nama kasir yang sedang login berdasarkan session
// Asumsi: Saat login kasir, Anda menyimpan $_SESSION['username'] atau $_SESSION['kasir_id']
$kasirName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Kasir Foodel';

// Handler pembaruan status pesanan langsung di tempat (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];
    
    $stmtUpdate = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmtUpdate->execute([$newStatus, $orderId]);
    $successMsg = "Status pesanan #$orderId berhasil diubah menjadi: $newStatus";
}

// Tarik data statistik dari SQLite
$totalPending = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
$totalProcessing = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Processing'")->fetchColumn();
$totalEarnings = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status = 'Completed'")->fetchColumn() ?? 0;

// Ambil seluruh daftar transaksi
$orders = $pdo->query("
    SELECT o.*, c.name as customer_name 
    FROM orders o 
    JOIN customers c ON o.customer_id = c.customer_id 
    ORDER BY o.order_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #E2E8F0; padding-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: #E0E7FF; display: flex; align-items: center; justify-content: center; border: 2px solid #4F46E5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <span style="color: #4F46E5; font-weight: 700; font-size: 20px;"><?= strtoupper(substr($kasirName, 0, 1)) ?></span>
        </div>
        <div>
            <h1 style="margin: 0; color: #0F172A; font-size: 24px; font-weight: 700;">Selamat Datang, Kasir <span style="color: #4F46E5;"><?= htmlspecialchars($kasirName) ?></span>! 👋</h1>
            <p style="margin: 3px 0 0 0; color: #64748B; font-size: 14px;">Monitor pesanan masuk dan kelola status pembayaran Foodel hari ini</p>
        </div>
    </div>
    <a href="logout.php" style="background: #FEE2E2; color: #DC2626; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; transition: 0.2s;">Keluar Sistem</a>
</div>

<?php if (isset($successMsg)): ?>
    <div style="background: #DCFCE7; color: #15803D; padding: 15px; border-radius: 12px; margin-bottom: 25px; font-weight: 500; font-size: 14px;">
        ✓ <?= htmlspecialchars($successMsg) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid #E2E8F0; border-left: 5px solid #D97706;">
        <h3 style="margin: 0; font-size: 13px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Antrean Baru</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: 700; color: #0F172A;"><?= $totalPending ?> Pesanan</p>
    </div>
    <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid #E2E8F0; border-left: 5px solid #4F46E5;">
        <h3 style="margin: 0; font-size: 13px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Sedang Diproses</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: 700; color: #0F172A;"><?= $totalProcessing ?> Pesanan</p>
    </div>
    <div style="background: white; padding: 24px; border-radius: 16px; border: 1px solid #E2E8F0; border-left: 5px solid #16A34A;">
        <h3 style="margin: 0; font-size: 13px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px;">Pendapatan Sukses</h3>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: 700; color: #16A34A;">Rp <?= number_format($totalEarnings) ?></p>
    </div>
</div>

<div style="background: white; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
    <div style="padding: 20px; border-bottom: 1px solid #E2E8F0; background: #FFF;">
        <h2 style="margin:0; font-size: 18px; color: #1E293B;">Daftar Transaksi Masuk</h2>
    </div>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
            <thead>
                <tr style="background: #F8FAFC;">
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">ID Order</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">Tanggal</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">Pelanggan</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">Alamat</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">Metode</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">Total Bayar</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0;">Status</th>
                    <th style="padding: 16px; color: #475569; font-weight: 600; border-bottom: 1px solid #E2E8F0; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #94A3B8; padding: 40px;">Belum ada pesanan masuk di database fdl.db.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($orders as $o): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 16px; font-weight: bold; color: #4F46E5;">#<?= $o['order_id'] ?></td>
                        <td style="padding: 16px; color: #64748B;"><?= date('d M Y, H:i', strtotime($o['order_date'])) ?></td>
                        <td style="padding: 16px; font-weight: 600; color: #1E293B;"><?= htmlspecialchars($o['customer_name']) ?></td>
                        <td style="padding: 16px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($o['address']) ?>">
                            <?= htmlspecialchars($o['address']) ?>
                        </td>
                        <td style="padding: 16px;"><span style="background: #EDF2F7; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;"><?= htmlspecialchars($o['payment_method']) ?></span></td>
                        <td style="padding: 16px; font-weight: 700; color: #0F172A;">Rp <?= number_format($o['total_price']) ?></td>
                        <td style="padding: 16px;">
                            <?php 
                            $bg = '#FEF3C7'; $cl = '#D97706';
                            if ($o['status'] === 'Processing') { $bg = '#E0E7FF'; $cl = '#4F46E5'; }
                            if ($o['status'] === 'Completed') { $bg = '#DCFCE7'; $cl = '#16A34A'; }
                            if ($o['status'] === 'Cancelled') { $bg = '#FEE2E2'; $cl = '#DC2626'; }
                            ?>
                            <span style="background: <?= $bg ?>; color: <?= $cl ?>; padding: 6px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; display: inline-block;">
                                <?= $o['status'] ?>
                            </span>
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <form method="POST" style="display: flex; gap: 6px; justify-content: center; margin: 0;">
                                <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                                <select name="status" style="padding: 6px 10px; border-radius: 8px; border: 1px solid #CBD5E1; background: white; font-size: 13px; outline: none; cursor: pointer;">
                                    <option value="Pending" <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Processing" <?= $o['status'] === 'Processing' ? 'selected' : '' ?>>Dimasak</option>
                                    <option value="Completed" <?= $o['status'] === 'Completed' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="Cancelled" <?= $o['status'] === 'Cancelled' ? 'selected' : '' ?>>Batal</option>
                                </select>
                                <button type="submit" name="update_status" style="background: #0EA5E9; color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px;">Set</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>