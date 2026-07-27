<?php
// Secure the page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'kasir') {
    header("Location: index.php?page=login");
    exit;
}

$kasirName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Kasir Foodel';

// Handle in-place order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];
    
    // Safely update using backticks around Transaction
    $stmtUpdate = $pdo->prepare("UPDATE `Transaction` SET status = ? WHERE id_transaksi = ?");
    $stmtUpdate->execute([$newStatus, $orderId]);
    $successMsg = "Status pesanan #$orderId berhasil diubah menjadi: $newStatus";
}

// Pull stats from database
$totalPending = $pdo->query("SELECT COUNT(*) FROM `Transaction` WHERE status = 'Pending'")->fetchColumn();
$totalProcessing = $pdo->query("SELECT COUNT(*) FROM `Transaction` WHERE status = 'Processing'")->fetchColumn();
$totalEarnings = $pdo->query("SELECT SUM(total_amount) FROM `Transaction` WHERE status = 'Completed'")->fetchColumn() ?? 0;

// Fetch all transactions with customer details
$orders = $pdo->query("
    SELECT t.*, c.name as customer_name 
    FROM `Transaction` t 
    JOIN customers c ON t.id_user = c.id_user 
    ORDER BY t.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .kasir-shell {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #F8F9FA;
        overflow: hidden;
        min-height: 0;
    }

    .kasir-scroll {
        flex: 1 1 auto;
        overflow-y: auto;
        padding: 20px 16px 28px;
        min-height: 0;
        -webkit-overflow-scrolling: touch;
    }

    .kasir-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        border-bottom: 2px dashed #eee;
        padding-bottom: 16px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .kasir-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .kasir-card {
        background: #fff;
        padding: 18px;
        border-radius: 18px;
        border: 1px solid #eee;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }

    .kasir-table-wrap {
        display: block;
    }

    .kasir-mobile-list {
        display: none;
    }

    @media (max-width: 768px) {
        .kasir-scroll {
            padding: 16px 12px 24px;
        }

        .kasir-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .kasir-header .logout-link {
            width: 100%;
            text-align: center;
        }

        .kasir-stats {
            grid-template-columns: 1fr;
        }

        .kasir-card {
            padding: 16px;
        }

        .kasir-table-wrap {
            display: none;
        }

        .kasir-mobile-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .kasir-mobile-item {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }

        .kasir-mobile-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            margin-bottom: 8px;
        }

        .kasir-mobile-label {
            font-size: 11px;
            color: var(--gray);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kasir-mobile-value {
            font-size: 14px;
            color: var(--dark);
            font-weight: 600;
            text-align: right;
        }

        .kasir-mobile-actions {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .kasir-mobile-actions select,
        .kasir-mobile-actions button {
            width: 100%;
        }
    }
</style>

<div class="kasir-shell">
    <div class="kasir-scroll">
    <!-- HEADER -->
    <div class="kasir-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: #FFF9F5; display: flex; align-items: center; justify-content: center; border: 2px solid var(--primary);">
                <span style="color: var(--primary); font-weight: 900; font-size: 24px;"><?= strtoupper(substr($kasirName, 0, 1)) ?></span>
            </div>
            <div>
                <h1 style="margin: 0; color: var(--dark); font-size: 24px; font-weight: 700;">Welcome, <span style="color: var(--primary);"><?= htmlspecialchars($kasirName) ?></span>! 👋</h1>
                <p style="margin: 5px 0 0 0; color: var(--gray); font-size: 14px;">Monitor incoming orders and manage payments</p>
            </div>
        </div>
        <a href="logout.php" class="logout-link" style="background: #FFF1F1; color: #FF4B4B; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 14px; border: 1px solid #FFE0E0;">Keluar Sistem</a>
    </div>

    <?php if (isset($successMsg)): ?>
        <div style="background: #E5F6DF; color: #1E4620; padding: 15px; border-radius: 15px; margin-bottom: 25px; font-weight: bold; font-size: 14px; border: 1px solid #c3e6cb;">
            ✓ <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>

    <!-- STATS GRID -->
    <div class="kasir-stats">
        <div class="kasir-card">
            <h3 style="margin: 0; font-size: 13px; color: var(--gray); font-weight: bold; letter-spacing: 0.5px;">ANTREAN BARU</h3>
            <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: 900; color: var(--dark);"><?= $totalPending ?> Pesanan</p>
        </div>
        <div class="kasir-card">
            <h3 style="margin: 0; font-size: 13px; color: var(--gray); font-weight: bold; letter-spacing: 0.5px;">SEDANG DIPROSES</h3>
            <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: 900; color: var(--primary);"><?= $totalProcessing ?> Pesanan</p>
        </div>
        <div class="kasir-card">
            <h3 style="margin: 0; font-size: 13px; color: var(--gray); font-weight: bold; letter-spacing: 0.5px;">PENDAPATAN SUKSES</h3>
            <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: 900; color: #16A34A;">Rp <?= number_format($totalEarnings, 0, ',', '.') ?></p>
        </div>
    </div>

    <!-- TRANSACTION TABLE -->
    <div style="background: #fff; border-radius: 20px; border: 2px solid #eee; box-shadow: 0 4px 10px rgba(0,0,0,0.02); overflow: hidden;">
        <div style="padding: 20px; border-bottom: 2px solid #eee; background: #fff;">
            <h2 style="margin:0; font-size: 18px; color: var(--dark);">Daftar Transaksi Masuk</h2>
        </div>

        <div class="kasir-table-wrap" style="overflow-x: auto; padding: 10px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; min-width: 760px;">
                <thead>
                    <tr style="background: #F6F6F6;">
                        <th style="padding: 16px; color: var(--gray); font-weight: bold; border-radius: 10px 0 0 10px;">ID Order</th>
                        <th style="padding: 16px; color: var(--gray); font-weight: bold;">Tanggal</th>
                        <th style="padding: 16px; color: var(--gray); font-weight: bold;">Pelanggan</th>
                        <th style="padding: 16px; color: var(--gray); font-weight: bold;">Alamat</th>
                        <th style="padding: 16px; color: var(--gray); font-weight: bold;">Metode</th>
                        <th style="padding: 16px; color: var(--gray); font-weight: bold;">Total Bayar</th>
                        <th style="padding: 16px; color: var(--gray); font-weight: bold;">Status</th>
                        <th style="padding: 16px; color: var(--gray); font-weight: bold; text-align: center; border-radius: 0 10px 10px 0;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--gray); padding: 40px; font-weight: bold;">Belum ada pesanan masuk.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($orders as $o): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 16px; font-weight: 900; color: var(--primary);">#<?= $o['id_transaksi'] ?></td>
                            <td style="padding: 16px; color: var(--gray); font-weight: 500;"><?= date('d M Y, H:i', strtotime($o['created_at'] ?? 'now')) ?></td>
                            <td style="padding: 16px; font-weight: bold; color: var(--dark);"><?= htmlspecialchars($o['customer_name']) ?></td>
                            <td style="padding: 16px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--gray);" title="<?= htmlspecialchars($o['address'] ?? '') ?>">
                                <?= htmlspecialchars($o['address'] ?? 'Belum ada alamat') ?>
                            </td>
                            <td style="padding: 16px;">
                                <span style="background: #F6F6F6; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: bold; color: var(--dark); border: 1px solid #eee;">
                                    <?= htmlspecialchars($o['payment_method'] ?? 'Cash') ?>
                                </span>
                            </td>
                            <td style="padding: 16px; font-weight: 900; color: var(--dark);">Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></td>
                            <td style="padding: 16px;">
                                <?php 
                                $bg = '#FFF9F5'; $cl = 'var(--primary)';
                                if ($o['status'] === 'Processing') { $bg = '#E0E7FF'; $cl = '#4F46E5'; }
                                if ($o['status'] === 'Completed') { $bg = '#E5F6DF'; $cl = '#16A34A'; }
                                if ($o['status'] === 'Cancelled') { $bg = '#FFF1F1'; $cl = '#FF4B4B'; }
                                ?>
                                <span style="background: <?= $bg ?>; color: <?= $cl ?>; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block;">
                                    <?= $o['status'] ?>
                                </span>
                            </td>
                            <td style="padding: 16px; text-align: center;">
                                <form method="POST" style="display: flex; gap: 6px; justify-content: center; margin: 0;">
                                    <input type="hidden" name="order_id" value="<?= $o['id_transaksi'] ?>">
                                    <select name="status" style="padding: 8px 12px; border-radius: 10px; border: 2px solid #eee; background: #fff; font-size: 13px; font-weight: bold; outline: none; cursor: pointer; color: var(--dark);">
                                        <option value="Pending" <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Processing" <?= $o['status'] === 'Processing' ? 'selected' : '' ?>>Dimasak</option>
                                        <option value="Completed" <?= $o['status'] === 'Completed' ? 'selected' : '' ?>>Selesai</option>
                                        <option value="Cancelled" <?= $o['status'] === 'Cancelled' ? 'selected' : '' ?>>Batal</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-orange" style="padding: 8px 15px; border-radius: 10px; font-size: 13px; width: auto; margin: 0;">Set</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="kasir-mobile-list">
            <?php if (empty($orders)): ?>
                <div style="text-align: center; color: var(--gray); padding: 24px 12px; font-weight: bold;">Belum ada pesanan masuk.</div>
            <?php endif; ?>
            <?php foreach ($orders as $o): ?>
                <?php 
                $bg = '#FFF9F5'; $cl = 'var(--primary)';
                if ($o['status'] === 'Processing') { $bg = '#E0E7FF'; $cl = '#4F46E5'; }
                if ($o['status'] === 'Completed') { $bg = '#E5F6DF'; $cl = '#16A34A'; }
                if ($o['status'] === 'Cancelled') { $bg = '#FFF1F1'; $cl = '#FF4B4B'; }
                ?>
                <div class="kasir-mobile-item">
                    <div class="kasir-mobile-row">
                        <span class="kasir-mobile-label">Order</span>
                        <span class="kasir-mobile-value" style="color: var(--primary);">#<?= $o['id_transaksi'] ?></span>
                    </div>
                    <div class="kasir-mobile-row">
                        <span class="kasir-mobile-label">Pelanggan</span>
                        <span class="kasir-mobile-value"><?= htmlspecialchars($o['customer_name']) ?></span>
                    </div>
                    <div class="kasir-mobile-row">
                        <span class="kasir-mobile-label">Alamat</span>
                        <span class="kasir-mobile-value" style="font-size: 12px; color: var(--gray); text-align: left;"><?= htmlspecialchars($o['address'] ?? 'Belum ada alamat') ?></span>
                    </div>
                    <div class="kasir-mobile-row">
                        <span class="kasir-mobile-label">Total</span>
                        <span class="kasir-mobile-value">Rp <?= number_format($o['total_amount'], 0, ',', '.') ?></span>
                    </div>
                    <div class="kasir-mobile-row">
                        <span class="kasir-mobile-label">Status</span>
                        <span style="background: <?= $bg ?>; color: <?= $cl ?>; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: bold;"><?= $o['status'] ?></span>
                    </div>
                    <div class="kasir-mobile-actions">
                        <form method="POST" style="display: flex; flex-direction: column; gap: 8px; margin: 0;">
                            <input type="hidden" name="order_id" value="<?= $o['id_transaksi'] ?>">
                            <select name="status" style="padding: 10px 12px; border-radius: 10px; border: 2px solid #eee; background: #fff; font-size: 13px; font-weight: bold; outline: none; cursor: pointer; color: var(--dark);">
                                <option value="Pending" <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Processing" <?= $o['status'] === 'Processing' ? 'selected' : '' ?>>Dimasak</option>
                                <option value="Completed" <?= $o['status'] === 'Completed' ? 'selected' : '' ?>>Selesai</option>
                                <option value="Cancelled" <?= $o['status'] === 'Cancelled' ? 'selected' : '' ?>>Batal</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-orange" style="padding: 10px 15px; border-radius: 10px; font-size: 13px; width: 100%; margin: 0;">Update Status</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</div>