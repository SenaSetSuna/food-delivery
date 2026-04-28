<div style="height: 100%; display: flex; flex-direction: column; padding: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <a href="index.php?page=home" style="background:#ECF0F4; width:45px; height:45px; border-radius:50%; display:grid; place-items:center; text-decoration:none; color:#000; font-weight:bold;">&lt;</a>
        <h3 style="margin:0; color:var(--dark);">Cart</h3>
        <span style="color:#059669; font-weight:bold;">DONE</span>
    </div>

    <div class="scroll-area">
        <div style="display:flex; align-items:center; margin-bottom:25px;">
            <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?w=200" style="width:100px; height:100px; border-radius:20px; object-fit: cover;">
            <div style="flex-grow:1; margin-left:15px;">
                <h4 style="margin:0; color:var(--dark);">Pizza Calzone</h4>
                <p style="margin:5px 0; font-weight:bold; color:var(--dark);">$64</p>
                <small style="color:var(--gray)">14"</small>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                <span style="color: #FF4B4B; cursor: pointer;">✕</span>
                <div style="background:var(--dark); padding:8px 12px; border-radius:25px; display:flex; gap:12px; color:white;">
                    <span>-</span> <b>1</b> <span>+</span>
                </div>
            </div>
        </div>
    </div>

    <div style="background: #fff; padding: 25px; border-radius: 30px 30px 0 0; box-shadow: 0 -10px 30px rgba(0,0,0,0.05); margin: 0 -20px -20px -20px;">
        <p style="color:var(--gray); font-size:12px; font-weight:bold; text-transform:uppercase;">Delivery Address</p>
        <div style="background:#F6F6F6; padding:15px; border-radius:12px; margin: 10px 0 20px 0; font-size:14px; border:1px solid #eee;">2118 Thornridge Cir. Syracuse</div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <span style="color:var(--dark);">TOTAL: <b style="font-size:26px;">$64</b></span>
            <span style="color:var(--primary); font-weight:bold;">Breakdown ></span>
        </div>
        <button class="btn-orange">Place Order</button>
    </div>
</div>