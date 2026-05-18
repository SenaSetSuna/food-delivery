<?php
$customerName = isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : 'Guest';

$categories = [
    ['name' => 'All', 'act' => true, 'icon' => '🍽️'],
    ['name' => 'Hot Dog', 'act' => false, 'icon' => '🌭'],
    ['name' => 'Burger', 'act' => false, 'icon' => '🍔'],
    ['name' => 'Pizza', 'act' => false, 'icon' => '🍕'],
];

$restaurants = [
    ['name' => 'Rose Garden Restaurant', 'tags' => 'Burger - Chicken - Riche', 'rating' => '4.7', 'delivery' => 'Free', 'time' => '20 min', 'img' => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=500'],
    ['name' => 'Pizzahub Surakarta', 'tags' => 'Pizza - Italian - Pasta', 'rating' => '4.9', 'delivery' => '$2.00', 'time' => '35 min', 'img' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=500'],
    ['name' => 'Solo Chicken Express', 'tags' => 'Fried Chicken - Spicy - Local', 'rating' => '4.5', 'delivery' => 'Free', 'time' => '15 min', 'img' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=500'],
    ['name' => 'Sunrise Beverages', 'tags' => 'Coffee - Tea - Boba', 'rating' => '4.8', 'delivery' => '$1.00', 'time' => '10 min', 'img' => 'https://images.unsplash.com/photo-1544145945-f904253d0c7b?w=500']
];
?>

<div style="height: 100%; display: flex; flex-direction: column;">
    
    <div class="scroll-area" id="main-scroll" style="padding: 0 20px 100px 20px; position: relative;">
        
        <div style="padding-top: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <div style="background: #ECF0F4; width: 45px; height: 45px; border-radius: 50%; display: grid; place-items: center; cursor: pointer;">☰</div>
                <div style="text-align: center;">
                    <small style="color: var(--primary); font-weight: 700;">DELIVER TO</small><br>
                    <span style="color: var(--gray); font-size: 14px;">Halal Lab office ▼</span>
                </div>
                <a href="index.php?page=cart" style="text-decoration:none;">
                    <div style="background: var(--dark); width: 45px; height: 45px; border-radius: 15px; display: grid; place-items: center; position: relative;">
                        <span style="font-size: 20px;">👜</span>
                        <span style="position: absolute; top: -5px; right: -5px; background: var(--primary); color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: grid; place-items: center; border: 2px solid white;">2</span>
                    </div>
                </a>
            </div>

            <h2 style="font-weight: 400; color: #1E1E2E; font-size: 22px; margin-bottom: 20px;">
                Hey <?= htmlspecialchars($customerName) ?>, <b>Good Afternoon!</b>
            </h2>
        </div>

        <div id="sticky-header" style="position: sticky; top: 0; z-index: 50; background: #fff; padding-top: 10px; margin: 0 -20px; padding-left: 20px; padding-right: 20px;">
            
            <div style="background: #F6F6F6; padding: 15px; border-radius: 15px; margin-bottom: 15px; display: flex; align-items: center;">
                <span style="margin-right: 10px;">🔍</span>
                <input type="text" placeholder="Search dishes, restaurants" style="border: none; background: transparent; outline: none; width: 100%;">
            </div>

            <div id="category-wrapper" style="transition: all 0.3s ease; overflow: hidden; max-height: 100px; opacity: 1;">
                <div style="display: flex; gap: 15px; overflow-x: auto; padding-bottom: 15px;" class="scroll-hide">
                    <?php foreach($categories as $c): ?>
                        <div style="background: <?= $c['act'] ? '#FFD27C' : '#fff' ?>; min-width: 120px; padding: 12px; border-radius: 30px; display: flex; align-items: center; gap: 10px; border: 1px solid #eee; cursor: pointer;">
                            <div style="width: 35px; height: 35px; background: #98A8B8; border-radius: 50%; display: grid; place-items: center;"><?= $c['icon'] ?></div>
                            <span style="font-weight: 600; font-size: 14px;"><?= $c['name'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div id="sticky-shadow" style="height: 1px; background: transparent; transition: background 0.3s;"></div>
        </div>

        <h3 style="margin: 15px 0;">Open Restaurants</h3>
        
        <?php foreach($restaurants as $res): ?>
        <a href="index.php?page=info" style="text-decoration: none; color: inherit;">
            <div style="margin-bottom: 30px;">
                <img src="<?= $res['img'] ?>" style="width:100%; height:180px; border-radius:20px; object-fit: cover;">
                <h4 style="margin: 10px 0 5px 0;"><?= $res['name'] ?></h4>
                <p style="color: var(--gray); font-size: 13px; margin: 0;"><?= $res['tags'] ?></p>
                <div style="display: flex; gap: 20px; margin-top: 10px; font-weight: 600; font-size: 14px;">
                    <span>⭐ <?= $res['rating'] ?></span> 
                    <span style="color: var(--primary);">🚚 <?= $res['delivery'] ?></span> 
                    <span style="color: var(--gray);">🕒 <?= $res['time'] ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <nav style="height: 85px; background: #fff; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #f0f0f0; border-radius: 25px 25px 0 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.03); position: absolute; bottom: 0; width: 100%;">
        <div onclick="window.location.href='index.php?page=home'" style="color: var(--primary); font-size: 26px; cursor: pointer;">🏠</div>
        <div style="color: #98A8B8; font-size: 26px; cursor: pointer;">🔍</div>
        <div style="color: #98A8B8; font-size: 26px; cursor: pointer;">📄</div>
        <div onclick="window.location.href='index.php?page=profile'" style="color: #98A8B8; font-size: 26px; cursor: pointer;">👤</div>
    </nav>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const scrollContainer = document.getElementById('main-scroll');
    const catWrapper = document.getElementById('category-wrapper');
    const stickyShadow = document.getElementById('sticky-shadow');
    let lastScroll = 0;

    scrollContainer.addEventListener('scroll', () => {
        let currentScroll = scrollContainer.scrollTop;
        
        // Only trigger the hide/show logic if they have scrolled past the top greeting
        if (currentScroll > 120) {
            stickyShadow.style.background = '#f0f0f0'; // Turns on the bottom line
            
            if (currentScroll > lastScroll) {
                // Scrolling DOWN: Smoothly collapse the category height to 0
                catWrapper.style.maxHeight = '0px';
                catWrapper.style.opacity = '0';
            } else {
                // Scrolling UP: Expand the category back to full height
                catWrapper.style.maxHeight = '100px';
                catWrapper.style.opacity = '1';
            }
        } else {
            // Reached the absolute top: reset everything to default
            stickyShadow.style.background = 'transparent';
            catWrapper.style.maxHeight = '100px';
            catWrapper.style.opacity = '1';
        }
        
        lastScroll = currentScroll <= 0 ? 0 : currentScroll; // For Mobile or negative scrolling
    });
});
</script>