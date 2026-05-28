<?php

// =======================
// AMBIL SEMUA PRODUCT
// =======================
$stmt = $pdo->prepare("
    SELECT *
    FROM products
    ORDER BY product_id DESC
");

$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =======================
// HITUNG CART
// =======================
$cartCount = 0;

if (isset($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $item) {

        $cartCount += $item['qty'];
    }
}

?>

<style>
    /* Paksa .app-viewport agar melar penuh jika dibuka di desktop */
    .app-viewport {
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        min-height: 100vh !important;
        position: static !important;
        box-shadow: none !important;
        background: #F8FAFC !important;
        display: block !important;
    }

    /* Container utama pembungkus layout */
    .home-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 20px 120px 20px;
        box-sizing: border-box;
    }

    /* Pengaturan Kategori Scroll */
    .category-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        margin-bottom: 32px;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
    }
    .category-scroll::-webkit-scrollbar {
        display: none; /* Sembunyikan scrollbar bawaan browser */
    }
    .category-item {
        min-width: 105px;
        padding: 12px 18px;
        border-radius: 24px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #E2E8F0;
    }
    .category-item.active {
        background: #FFB830 !important; /* Warna primary warm */
        color: #0F172A !important;
        border-color: #FFB830;
        box-shadow: 0 4px 12px rgba(255, 184, 48, 0.3);
    }
    .category-item:not(.active):hover {
        background: #F1F5F9;
        border-color: #CBD5E1;
    }

    /* Pengaturan Grid Produk: Otomatis adaptif PC & HP */
    .product-grid {
        display: grid;
        grid-template-columns: 1fr; /* Default HP: 1 Kolom besar sesuai desain aslimu */
        gap: 24px;
    }

    /* Gaya Kartu Produk Modern */
    .product-card {
        background: white; 
        border-radius: 24px; 
        overflow: hidden; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        border: 1px solid #E2E8F0;
        height: 100%; 
        display: flex; 
        flex-direction: column; 
        justify-content: space-between;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
    }

    /* Gaya bottom navbar agar tetap mengambang rapi di bawah layar komputer maupun HP */
    .fixed-bottom-nav {
        position: fixed !important;
        bottom: 0;
        left: 0;
        right: 0;
        height: 84px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 32px 32px 0 0;
        display: flex;
        justify-content: space-around;
        align-items: center;
        box-shadow: 0 -10px 25px -5px rgba(0, 0, 0, 0.06);
        z-index: 999;
        border-top: 1px solid #F1F5F9;
    }
    .nav-link {
        text-decoration: none;
        font-size: 26px;
        transition: transform 0.2s ease;
        padding: 10px;
    }
    .nav-link:hover {
        transform: scale(1.15);
    }

    /* Saat dibuka di layar lebar (Desktop / Tablet) */
    @media (min-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(3, 1fr); /* Menjadi 3 Kolom sejajar di PC */
        }
        .fixed-bottom-nav {
            max-width: 450px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            bottom: 16px !important; /* Membuat navbar melayang estetik di PC */
            border-radius: 24px !important;
            border: 1px solid #E2E8F0;
        }
    }
    @media (min-width: 1024px) {
        .product-grid {
            grid-template-columns: repeat(4, 1fr); /* Layar monitor besar muat 4 kolom */
        }
    }
</style>

<div style="height:100%; display:flex; flex-direction:column; background:#F8FAFC;">

    <div class="scroll-area home-container">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:32px;">

            <div style="width:46px; height:46px; border-radius:14px; background:white; border:1px solid #E2E8F0; display:grid; place-items:center; font-size:18px; color:#475569; cursor:pointer; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                ☰
            </div>

            <div style="text-align:center;">
                <small style="color:#FFB830; font-weight:800; letter-spacing: 1px; font-size: 11px;">DELIVER TO</small>
                <div style="margin-top:4px; color:#0F172A; font-weight: 600; font-size:14px;">
                    📍 Surakarta, Indonesia
                </div>
            </div>

            <a href="index.php?page=cart" style="text-decoration:none;">
                <div style="width:48px; height:48px; background:#0F172A; border-radius:14px; display:grid; place-items:center; position:relative; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <span style="font-size:20px;">🛒</span>
                    <?php if($cartCount > 0): ?>
                        <div style="position:absolute; top:-6px; right:-6px; width:22px; height:22px; border-radius:50%; background:#FFB830; color:#0F172A; font-size:11px; display:grid; place-items:center; font-weight:800; border:2px solid white;">
                            <?= $cartCount ?>
                        </div>
                    <?php endif; ?>
                </div>
            </a>

        </div>

        <div style="margin-bottom:32px;">
            <h1 style="margin:0; font-size:28px; font-weight: 800; color:#0F172A; letter-spacing: -0.5px;">
                Hello, <?= htmlspecialchars($_SESSION['customer_name'] ?? 'Pelanggan') ?> 👋
            </h1>
            <p style="color:#64748B; margin-top:8px; font-size: 15px; font-weight: 500;">
                What do you want to eat today?
            </p>
        </div>

        <div style="background:white; border:1px solid #E2E8F0; border-radius:16px; padding:14px 20px; display:flex; align-items:center; gap:12px; margin-bottom:32px; box-shadow:0 2px 4px rgba(0,0,0,0.01);">
            <span style="font-size:18px; color:#94A3B8;">🔍</span>
            <input type="text" placeholder="Search delicious food..." style="border:none; background:none; outline:none; width:100%; font-size:15px; color:#0F172A; font-weight:500;">
        </div>

        <div class="category-scroll">
            <div class="category-item active" style="background:white; color:#475569;">🍽️ All</div>
            <div class="category-item" style="background:white; color:#475569;">🍔 Burger</div>
            <div class="category-item" style="background:white; color:#475569;">🍕 Pizza</div>
            <div class="category-item" style="background:white; color:#475569;">🍜 Noodles</div>
        </div>

        <h2 style="margin-bottom:24px; font-size: 22px; font-weight:700; color:#0F172A;">Popular Foods</h2>

        <div class="product-grid">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                    <a href="index.php?page=info&id=<?= $product['product_id'] ?>" style="text-decoration:none; color:inherit;">
                        <div class="product-card">
                            
                            <div>
                                <div style="position:relative; overflow:hidden;">
                                    <img src="<?= htmlspecialchars($product['image_url']) ?>" style="width:100%; height:200px; object-fit:cover;">
                                </div>

                                <div style="padding:20px 20px 10px 20px;">
                                    <div style="display:flex; justify-content:space-between; align-items:start; gap: 10px;">
                                        <div>
                                            <h3 style="margin:0; font-size:17px; font-weight:700; color:#0F172A; line-height:1.4;"><?= htmlspecialchars($product['name']) ?></h3>
                                            <p style="color:#64748B; margin-top:6px; font-size:13px; font-weight:500;">Delicious premium choices</p>
                                        </div>
                                        <div style="background:#FFF8EC; color:#FFB830; padding:6px 10px; border-radius:10px; font-weight:700; font-size:13px; white-space: nowrap; display: flex; align-items: center; gap:4px;">
                                            ⭐ 4.8
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="padding: 0 20px 20px 20px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding-top:12px; border-top:1px dashed #E2E8F0;">
                                    <h2 style="margin:0; color:#0F172A; font-size:18px; font-weight:800;">
                                        Rp <?= number_format($product['price']) ?>
                                    </h2>
                                    <div style="background:#FFB830; color:#0F172A; width:38px; height:38px; border-radius:12px; display:grid; place-items:center; font-size:16px; font-weight:bold; transition: background 0.2s;">
                                        ➔
                                    </div>
                                </div>
                            </div>

                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; background:white; border-radius:24px; padding:60px 20px; text-align:center; border:1px solid #E2E8F0;">
                    <div style="font-size:64px; margin-bottom:16px;">🍽️</div>
                    <h3 style="margin:0; color:#0F172A; font-size:18px;">No Products Yet</h3>
                    <p style="color:#64748B; margin-top:8px; font-size:14px;">Admin has not added foods yet.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <nav class="fixed-bottom-nav">
        <a href="index.php?page=home" class="nav-link" style="color:#FFB830;">🏠</a>
        <a href="index.php?page=history" class="nav-link" style="color:#94A3B8;">📦</a>
        <a href="index.php?page=profile" class="nav-link" style="color:#94A3B8;">👤</a>
    </nav>

</div>