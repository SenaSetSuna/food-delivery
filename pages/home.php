<?php
// 1. Fetch current user data for the top-left avatar and greeting
$customerId = $_SESSION['customer_id'] ?? null;
$customerName = 'Guest';
$userProfilePic = '';

if ($customerId) {
    $stmtUser = $pdo->prepare("SELECT name, user_profile FROM customers WHERE id_user = ?");
    $stmtUser->execute([$customerId]);
    $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
    if ($userData) {
        $customerName = $userData['name'];
        $userProfilePic = $userData['user_profile'];
    }
}

// 2. FETCH REAL CATEGORIES FROM DATABASE
$catStmt = $pdo->query("SELECT * FROM Category");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. FETCH REAL PRODUCTS & SELLER INFO FROM DATABASE
$prodStmt = $pdo->query("
    SELECT p.*, s.shopname, c.categoryname 
    FROM product p
    JOIN User_Seller s ON p.id_seller = s.id_seller
    JOIN Category c ON p.category_id = c.category_id
    WHERE p.is_available = 1
");
$products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .category-chip {
        background: #fff;
        min-width: 120px;
        padding: 12px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #eee;
        cursor: pointer;
        transition: transform .15s ease, background .2s ease, border-color .2s ease, color .2s ease;
        white-space: nowrap;
    }
    .category-chip.active {
        background: #FFD27C;
        border-color: #FFD27C;
        color: #1E1E2E;
    }
    .category-chip:hover {
        transform: translateY(-1px);
    }
    .category-chip img {
        width: 25px;
        height: 25px;
        border-radius: 50%;
    }
    .product-card {
        margin-bottom: 22px;
        transition: transform .18s ease, box-shadow .18s ease;
        border-radius: 20px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06);
        display: flex;
        flex-direction: column;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
    }
    .product-card img {
        width: 100%;
        aspect-ratio: 14 / 9;
        object-fit: cover;
        display: block;
    }
    .product-info {
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .product-title {
        margin: 0;
        color: #111827;
        font-size: 15px;
        font-weight: 700;
    }
    .product-subtitle {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.4;
    }
    .product-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .product-chip {
        background: #f8fafc;
        color: #374151;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
    }
    .product-price {
        color: var(--primary);
        font-weight: 700;
        font-size: 14px;
    }
</style>

<div style="height: 100%; display: flex; flex-direction: column;">
    <div class="scroll-area" id="main-scroll" style="padding: 0 20px 100px 20px; position: relative;">
        
        <div style="padding-top: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                
                <a href="index.php?page=profile" style="text-decoration: none;">
                    <?php if (!empty($userProfilePic)): ?>
                        <img src="<?= htmlspecialchars($userProfilePic) ?>" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <?php else: ?>
                        <div style="width: 45px; height: 45px; background: #FFD27C; border-radius: 50%; display: grid; place-items: center; font-size: 20px; font-weight: bold; color: var(--primary); box-shadow: 0 4px 10px rgba(255, 118, 34, 0.2);">
                            <?= strtoupper(substr($customerName, 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </a>

                <div style="text-align: center;">
                    <small style="color: var(--primary); font-weight: 700;">DELIVER TO</small><br>
                    <span style="color: var(--gray); font-size: 14px;">Home ▼</span>
                </div>
                
                <a href="index.php?page=cart" style="text-decoration:none;">
                    <div style="background: var(--dark); width: 45px; height: 45px; border-radius: 15px; display: grid; place-items: center; position: relative;">
                        <span style="font-size: 20px;">👜</span>
                    </div>
                </a>
            </div>
            
            <h2 style="font-weight: 400; color: #1E1E2E; font-size: 22px; margin-bottom: 20px;">
                Hey <?= htmlspecialchars($customerName) ?>, <b>Hungry?</b>
            </h2>
        </div>

        <div id="sticky-header" style="position: sticky; top: 0; z-index: 50; background: #fff; padding-top: 10px; margin: 0 -20px; padding-left: 20px; padding-right: 20px;">
            <div style="background: #F6F6F6; padding: 15px; border-radius: 15px; margin-bottom: 15px; display: flex; align-items: center;">
                <span style="margin-right: 10px;">🔍</span>
                <input id="search-input" type="text" placeholder="Search dishes..." style="border: none; background: transparent; outline: none; width: 100%;">
            </div>
            
            <div id="category-wrapper" style="transition: all 0.3s ease; overflow: hidden; max-height: 100px; opacity: 1;">
                <div style="display: flex; gap: 15px; overflow-x: auto; padding-bottom: 15px;" class="scroll-hide">
                    
                    <div class="category-chip active" role="button" tabindex="0" data-category="all">
                        <span style="font-weight: 600; font-size: 14px;">All Food</span>
                    </div>

                    <?php foreach($categories as $c): ?>
                        <div class="category-chip" role="button" tabindex="0" data-category="<?= htmlspecialchars(strtolower($c['categoryname'])) ?>">
                            <img src="<?= htmlspecialchars($c['imgurl']) ?>" onerror="this.style.display='none'">
                            <span style="font-weight: 600; font-size: 14px;"><?= htmlspecialchars($c['categoryname']) ?></span>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
            <div id="sticky-shadow" style="height: 1px; background: transparent; transition: background 0.3s;"></div>
        </div>

        <h3 style="margin: 15px 0;">Available Food</h3>
        
        <?php if(empty($products)): ?>
            <p style="color: var(--gray); text-align: center; margin-top: 30px;">No food available right now.</p>
        <?php else: ?>
            <div id="product-list">
                <?php foreach($products as $res): ?>
                <a href="index.php?page=info&id=<?= $res['id_product'] ?>" class="product-link" style="text-decoration: none; color: inherit; display: block;" data-name="<?= htmlspecialchars(strtolower($res['name'])) ?>" data-shop="<?= htmlspecialchars(strtolower($res['shopname'])) ?>" data-category="<?= htmlspecialchars(strtolower($res['categoryname'])) ?>">
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($res['product_img']) ?>" alt="<?= htmlspecialchars($res['name']) ?>">
                        <div class="product-info">
                            <h4 class="product-title"><?= htmlspecialchars($res['name']) ?></h4>
                            <p class="product-subtitle"><?= htmlspecialchars($res['shopname']) ?> • <?= htmlspecialchars($res['categoryname']) ?></p>
                            <div class="product-stats">
                                <div class="product-chip">⭐ <?= number_format($res['rating'], 1) ?></div>
                                <div class="product-price">Rp <?= number_format($res['price'], 0, ',', '.') ?></div>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <p id="no-results" style="display:none; color: var(--gray); text-align: center; margin-top: 30px;">No food found for your search or selected filter.</p>
        <?php endif; ?>
    </div>

    <nav style="height: 85px; background: #fff; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #f0f0f0; border-radius: 25px 25px 0 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.03); position: absolute; bottom: 0; width: 100%;">
        <div onclick="window.location.href='index.php?page=home'" style="color: var(--primary); font-size: 26px; cursor: pointer;">🏠</div>
        <div onclick="window.location.href='index.php?page=app_info'" style="color: #98A8B8; font-size: 26px; cursor: pointer;">ℹ️</div>
        <div onclick="window.location.href='index.php?page=profile'" style="color: #98A8B8; font-size: 26px; cursor: pointer;">👤</div>
    </nav>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const scrollContainer = document.getElementById('main-scroll');
    const catWrapper = document.getElementById('category-wrapper');
    const stickyShadow = document.getElementById('sticky-shadow');
    const searchInput = document.getElementById('search-input');
    const categoryChips = document.querySelectorAll('.category-chip');
    const productLinks = document.querySelectorAll('.product-link');
    const noResults = document.getElementById('no-results');
    let lastScroll = 0;
    let selectedCategory = 'all';

    function filterProducts() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        productLinks.forEach(link => {
            const name = link.dataset.name || '';
            const shop = link.dataset.shop || '';
            const category = link.dataset.category || '';
            const matchesSearch = query === '' || name.includes(query) || shop.includes(query) || category.includes(query);
            const matchesCategory = selectedCategory === 'all' || category === selectedCategory;
            const shouldShow = matchesSearch && matchesCategory;
            link.style.display = shouldShow ? 'block' : 'none';
            if (shouldShow) {
                visibleCount++;
            }
        });

        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function selectCategory(category, selectedChip) {
        selectedCategory = category;
        categoryChips.forEach(chip => chip.classList.toggle('active', chip === selectedChip));
        filterProducts();
    }

    categoryChips.forEach(chip => {
        chip.addEventListener('click', () => selectCategory(chip.dataset.category, chip));
        chip.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                selectCategory(chip.dataset.category, chip);
            }
        });
    });

    searchInput.addEventListener('input', filterProducts);

    if (categoryChips.length > 0) {
        selectCategory(categoryChips[0].dataset.category, categoryChips[0]);
    }

    scrollContainer.addEventListener('scroll', () => {
        let currentScroll = scrollContainer.scrollTop;
        if (currentScroll > 120) {
            stickyShadow.style.background = '#f0f0f0';
            if (currentScroll > lastScroll) {
                catWrapper.style.maxHeight = '0px';
                catWrapper.style.opacity = '0';
            } else {
                catWrapper.style.maxHeight = '100px';
                catWrapper.style.opacity = '1';
            }
        } else {
            stickyShadow.style.background = 'transparent';
            catWrapper.style.maxHeight = '100px';
            catWrapper.style.opacity = '1';
        }
        lastScroll = currentScroll <= 0 ? 0 : currentScroll;
    });
});
</script>