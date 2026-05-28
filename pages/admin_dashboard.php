<?php
// Only allow authenticated admin access
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$admin_msg = '';
if (isset($_SESSION['admin_msg'])) {
    $admin_msg = $_SESSION['admin_msg'];
    unset($_SESSION['admin_msg']);
}
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_customer' && !empty($_POST['customer_id'])) {
        $customerId = (int) $_POST['customer_id'];
        try {
            $stmt = $pdo->prepare('DELETE FROM customers WHERE id_user = ?');
            $stmt->execute([$customerId]);
            $_SESSION['admin_msg'] = 'Customer account deleted successfully.';
            header('Location: index.php?page=admin_dashboard');
            exit;
        } catch (PDOException $e) {
            $error = 'Unable to delete customer: ' . $e->getMessage();
        }
    }

    if ($_POST['action'] === 'delete_seller' && !empty($_POST['seller_id'])) {
        $sellerId = (int) $_POST['seller_id'];
        try {
            $stmt = $pdo->prepare('DELETE FROM User_Seller WHERE id_seller = ?');
            $stmt->execute([$sellerId]);
            $_SESSION['admin_msg'] = 'Seller account deleted successfully.';
            header('Location: index.php?page=admin_dashboard');
            exit;
        } catch (PDOException $e) {
            $error = 'Unable to delete seller. It may still have active orders or linked products.';
        }
    }
}

$customerCount = $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();
$sellerCount = $pdo->query('SELECT COUNT(*) FROM User_Seller')->fetchColumn();
$productCount = $pdo->query('SELECT COUNT(*) FROM product')->fetchColumn();

$customers = $pdo->query('SELECT id_user, name, username, email, phone, address FROM customers ORDER BY id_user DESC')->fetchAll(PDO::FETCH_ASSOC);
$sellers = $pdo->query('SELECT id_seller, shopname, email, phone FROM User_Seller ORDER BY id_seller DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="height: 100%; display: flex; flex-direction: column; background: #F4F5F7; position: relative;">
    <div style="padding: 20px; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.06); z-index: 10;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap;">
            <div>
                <small style="color: var(--primary); font-weight: 700; font-size: 11px; letter-spacing: 0.8px;">ADMIN PANEL</small>
                <h1 style="margin: 8px 0 0 0; font-size: 24px; color: #111827;">Welcome, <?= htmlspecialchars($adminName) ?></h1>
                <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 14px;">Manage all customers and sellers from one place.</p>
            </div>
            <a href="logout.php" style="text-decoration: none;">
                <button class="btn-orange" style="background: #FFF1F1; color: #FF4B4B; border: 1px solid #FFE0E0; padding: 14px 22px;">Log Out</button>
            </a>
        </div>
    </div>

    <div class="scroll-area" style="padding: 20px;">
        <?php if ($admin_msg): ?>
            <div style="background: #E5F6DF; color: #1E4620; padding: 14px 16px; border-radius: 14px; margin-bottom: 20px; font-weight: 600;"><?= htmlspecialchars($admin_msg) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #FFE5E5; color: #B91C1C; padding: 14px 16px; border-radius: 14px; margin-bottom: 20px; font-weight: 600;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div style="display: flex; gap: 16px; margin-bottom: 25px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 180px; background: #fff; padding: 18px; border-radius: 18px; box-shadow: 0 10px 20px rgba(15,23,42,0.06); border: 1px solid #eef2ff;">
                <div style="color: #6b7280; font-size: 12px; font-weight: bold;">CUSTOMERS</div>
                <div style="font-size: 28px; font-weight: 700; color: #111827; margin-top: 8px;"><?= $customerCount ?></div>
            </div>
            <div style="flex: 1; min-width: 180px; background: #fff; padding: 18px; border-radius: 18px; box-shadow: 0 10px 20px rgba(15,23,42,0.06); border: 1px solid #eef2ff;">
                <div style="color: #6b7280; font-size: 12px; font-weight: bold;">SELLERS</div>
                <div style="font-size: 28px; font-weight: 700; color: #111827; margin-top: 8px;"><?= $sellerCount ?></div>
            </div>
            <div style="flex: 1; min-width: 180px; background: #fff; padding: 18px; border-radius: 18px; box-shadow: 0 10px 20px rgba(15,23,42,0.06); border: 1px solid #eef2ff;">
                <div style="color: #6b7280; font-size: 12px; font-weight: bold;">PRODUCTS</div>
                <div style="font-size: 28px; font-weight: 700; color: #111827; margin-top: 8px;"><?= $productCount ?></div>
            </div>
        </div>

        <div style="background: #fff; padding: 18px 18px 12px 18px; border-radius: 18px; margin-bottom: 20px; border: 1px solid #eee; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 20px;">&#x1F50D;</span>
            <input id="admin-search" type="text" placeholder="Search customers or sellers" style="width: 100%; border: none; background: transparent; outline: none; font-size: 14px; color: #111827;">
        </div>

        <div style="display: grid; gap: 20px;">
            <section style="background: #fff; padding: 18px; border-radius: 18px; border: 1px solid #eee;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; gap: 12px; flex-wrap: wrap;">
                    <div>
                        <h2 style="margin: 0; font-size: 18px; color: #111827;">Customers</h2>
                        <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 13px;">Manage every registered customer account.</p>
                    </div>
                </div>
                <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <colgroup>
                            <col style="width: 18%;">
                            <col style="width: 14%;">
                            <col style="width: 24%;">
                            <col style="width: 14%;">
                            <col style="width: 20%;">
                            <col style="width: 10%;">
                        </colgroup>
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
                                <th style="padding: 12px 10px;">Name</th>
                                <th style="padding: 12px 10px;">Username</th>
                                <th style="padding: 12px 10px;">Email</th>
                                <th style="padding: 12px 10px;">Phone</th>
                                <th style="padding: 12px 10px;">Address</th>
                                <th style="padding: 12px 10px; width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="customer_rows">
                            <?php foreach ($customers as $customer): ?>
                                <tr data-search="<?= htmlspecialchars(strtolower($customer['name'] . ' ' . $customer['username'] . ' ' . $customer['email'] . ' ' . $customer['phone'] . ' ' . $customer['address'])) ?>">
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #111827; font-weight: 600; word-break: break-word; white-space: normal;"><?= htmlspecialchars($customer['name']) ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #4b5563; word-break: break-word; white-space: normal;"><?= htmlspecialchars($customer['username']) ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #4b5563; word-break: break-word; white-space: normal;"><?= htmlspecialchars($customer['email']) ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #4b5563; word-break: break-word; white-space: normal;"><?= htmlspecialchars($customer['phone']) ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #4b5563; word-break: break-word; white-space: normal;"><?= htmlspecialchars($customer['address']) ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6;">
                                        <form method="POST" onsubmit="return confirm('Delete this customer account permanently?');">
                                            <input type="hidden" name="action" value="delete_customer">
                                            <input type="hidden" name="customer_id" value="<?= $customer['id_user'] ?>">
                                            <button type="submit" aria-label="Delete seller" style="background: #FFE5E5; border: none; color: #b91c1c; padding: 10px; border-radius: 12px; cursor: pointer; font-size: 16px; width: auto; min-width: 0; display: inline-flex; align-items: center; justify-content: center;">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section style="background: #fff; padding: 18px; border-radius: 18px; border: 1px solid #eee;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; gap: 12px; flex-wrap: wrap;">
                    <div>
                        <h2 style="margin: 0; font-size: 18px; color: #111827;">Sellers</h2>
                        <p style="margin: 6px 0 0 0; color: #6b7280; font-size: 13px;">Manage every registered seller account.</p>
                    </div>
                </div>
                <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <colgroup>
                            <col style="width: 26%;">
                            <col style="width: 26%;">
                            <col style="width: 18%;">
                            <col style="width: 20%;">
                            <col style="width: 10%;">
                        </colgroup>
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">
                                <th style="padding: 12px 10px;">Shop</th>
                                <th style="padding: 12px 10px;">Email</th>
                                <th style="padding: 12px 10px;">Phone</th>
                                <th style="padding: 12px 10px;">Products</th>
                                <th style="padding: 12px 10px; width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="seller_rows">
                            <?php foreach ($sellers as $seller): ?>
                                <?php
                                    $productStmt = $pdo->prepare('SELECT COUNT(*) FROM product WHERE id_seller = ?');
                                    $productStmt->execute([$seller['id_seller']]);
                                    $productCountBySeller = $productStmt->fetchColumn();
                                ?>
                                <tr data-search="<?= htmlspecialchars(strtolower($seller['shopname'] . ' ' . $seller['email'] . ' ' . $seller['phone'])) ?>">
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #111827; font-weight: 600; word-break: break-word; white-space: normal;"><?= htmlspecialchars($seller['shopname']) ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #4b5563; word-break: break-word; white-space: normal;"><?= htmlspecialchars($seller['email']) ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #4b5563; word-break: break-word; white-space: normal;"><?= htmlspecialchars($seller['phone']) ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6; color: #4b5563;"><?= $productCountBySeller ?></td>
                                    <td style="padding: 14px 10px; border-bottom: 1px solid #f3f4f6;">
                                        <form method="POST" onsubmit="return confirm('Delete this seller account permanently?');">
                                            <input type="hidden" name="action" value="delete_seller">
                                            <input type="hidden" name="seller_id" value="<?= $seller['id_seller'] ?>">
                                            <button type="submit" aria-label="Delete seller" style="background: #FFE5E5; border: none; color: #b91c1c; padding: 10px; border-radius: 12px; cursor: pointer; font-size: 16px; width: auto; min-width: 0; display: inline-flex; align-items: center; justify-content: center;">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    document.getElementById('admin-search').addEventListener('input', function() {
        const searchValue = this.value.trim().toLowerCase();
        const customerRows = document.querySelectorAll('#customer_rows tr');
        const sellerRows = document.querySelectorAll('#seller_rows tr');

        customerRows.forEach(row => {
            const value = row.dataset.search;
            row.style.display = value.includes(searchValue) ? 'table-row' : 'none';
        });

        sellerRows.forEach(row => {
            const value = row.dataset.search;
            row.style.display = value.includes(searchValue) ? 'table-row' : 'none';
        });
    });
</script>




