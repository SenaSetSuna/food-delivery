<div style="height: 100%; display: flex; flex-direction: column;">
    
    <div class="scroll-area" style="padding: 30px 20px 20px 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div style="background: #ECF0F4; width: 45px; height: 45px; border-radius: 50%; display: grid; place-items: center;">☰</div>
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

        <h2 style="font-weight: 400; color: #1E1E2E; font-size: 22px; margin-bottom: 20px;">Hey Halal, <b>Good Afternoon!</b></h2>

        <div style="background: #F6F6F6; padding: 15px; border-radius: 15px; margin-bottom: 30px; display: flex; align-items: center;">
            <span style="margin-right: 10px;">🔍</span>
            <input type="text" placeholder="Search dishes, restaurants" style="border: none; background: transparent; outline: none; width: 100%;">
        </div>

        <div style="display: flex; gap: 15px; overflow-x: auto; padding-bottom: 10px;" class="scroll-hide">
            <?php 
            $cats = [['name'=>'All', 'act'=>true, 'icon'=>'🍽️'], ['name'=>'Hot Dog', 'act'=>false, 'icon'=>'🌭'], ['name'=>'Burger', 'act'=>false, 'icon'=>'🍔']];
            foreach($cats as $c): ?>
                <div style="background: <?= $c['act'] ? '#FFD27C' : '#fff' ?>; min-width: 120px; padding: 12px; border-radius: 30px; display: flex; align-items: center; gap: 10px; border: 1px solid #eee;">
                    <div style="width: 35px; height: 35px; background: #98A8B8; border-radius: 50%; display: grid; place-items: center;"><?= $c['icon'] ?></div>
                    <span style="font-weight: 600; font-size: 14px;"><?= $c['name'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <h3 style="margin: 30px 0 15px 0;">Open Restaurants</h3>
        <?php for($i=0; $i<3; $i++): ?>
        <a href="index.php?page=info" style="text-decoration: none; color: inherit;">
            <div style="margin-bottom: 30px;">
                <img src="https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=500" style="width:100%; height:180px; border-radius:20px; object-fit: cover;">
                <h4 style="margin: 10px 0 5px 0;">Rose Garden Restaurant</h4>
                <p style="color: var(--gray); font-size: 13px; margin: 0;">Burger - Chicken - Riche - Wings</p>
                <div style="display: flex; gap: 20px; margin-top: 10px; font-weight: 600; font-size: 14px;">
                    <span>⭐ 4.7</span> <span style="color: var(--primary);">🚚 Free</span> <span>🕒 20 min</span>
                </div>
            </div>
        </a>
        <?php endfor; ?>
    </div>

    <nav style="height: 85px; background: #fff; display: flex; justify-content: space-around; align-items: center; border-top: 1px solid #f0f0f0; border-radius: 25px 25px 0 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.03);">
        <div style="color: var(--primary); font-size: 26px;">🏠</div>
        <div style="color: #98A8B8; font-size: 26px;">🔍</div>
        <div style="color: #98A8B8; font-size: 26px;">📄</div>
        <div style="color: #98A8B8; font-size: 26px;">👤</div>
    </nav>
</div>