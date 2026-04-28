<div style="height: 100%; display: flex; flex-direction: column;">
    
    <div class="scroll-area">
        <div style="height: 40vh; min-height: 300px; position: relative;">
            <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800" style="width: 100%; height: 100%; object-fit: cover;">
            <a href="index.php?page=home" style="position: absolute; top: 20px; left: 20px; width: 45px; height: 45px; background: #fff; border-radius: 50%; display: grid; place-items: center; text-decoration: none; color: #000; font-weight: bold;">&lt;</a>
        </div>

        <div style="padding: 24px;">
            <h2 style="margin: 0; font-size: 26px; color: var(--dark);">Burger Bistro</h2>
            <p style="color: var(--gray); margin: 8px 0 20px 0;">🍔 Rose Garden</p>
            
            <div style="display: flex; gap: 25px; font-weight: 600; margin-bottom: 20px; font-size: 15px;">
                <span>⭐ 4.7</span> <span style="color:var(--primary)">🚚 Free</span> <span>🕒 20 min</span>
            </div>

            <p style="color: var(--gray); font-size: 14px; line-height: 1.6; margin-bottom: 25px;">
                Maecenas sed diam eget risus varius blandit sit amet non magna. Integer posuere erat a ante venenatis dapibus posuere velit aliquet.
            </p>

            <h4 style="font-size: 12px; text-transform: uppercase; margin-bottom: 15px;">Size:</h4>
            <div style="display: flex; gap: 15px; margin-bottom: 30px;">
                <div style="width: 55px; height: 55px; background: #F0F4F7; border-radius: 50%; display: grid; place-items: center;">10"</div>
                <div style="width: 55px; height: 55px; background: var(--primary); color: #fff; border-radius: 50%; display: grid; place-items: center; font-weight: 700;">14"</div>
                <div style="width: 55px; height: 55px; background: #F0F4F7; border-radius: 50%; display: grid; place-items: center;">16"</div>
            </div>

            <h4 style="font-size: 12px; text-transform: uppercase; margin-bottom: 15px;">Ingredients:</h4>
            <div style="display: flex; gap: 12px; overflow-x: auto;" class="scroll-hide">
                <?php foreach(['🧂','🍗','🧅','🧄','🌶️'] as $ing): ?>
                    <div style="min-width: 60px; height: 60px; background: #FFF1E9; border-radius: 50%; display: grid; place-items: center; font-size: 24px;"><?php echo $ing; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div style="background: #fff; padding: 25px; border-radius: 30px 30px 0 0; box-shadow: 0 -10px 40px rgba(0,0,0,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 28px; color: var(--dark);">$32</h2>
            <div style="background: var(--dark); color: white; padding: 12px 20px; border-radius: 30px; display: flex; align-items: center; gap: 20px;">
                <button style="background:none; border:none; color:white; font-size:20px; cursor:pointer;">-</button>
                <span style="font-weight:bold; font-size: 18px;">1</span>
                <button style="background:none; border:none; color:white; font-size:20px; cursor:pointer;">+</button>
            </div>
        </div>
        <button class="btn-orange" onclick="window.location.href='index.php?page=cart'">Add to Cart</button>
    </div>
</div>