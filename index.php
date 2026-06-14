<?php
include 'db.php';     // Starts the session and memory!
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}
include 'header.php'; // Loads the navbar
?>

<style>
    /* Home Page Specific Styles */
    .hero-section {
        text-align: center;
        padding: 80px 20px 60px;
    }

    .hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 20px;
        color: #3e2723;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto 40px auto;
        color: #4a4a4a;
        line-height: 1.6;
    }

    .btn-primary-custom {
        background-color: var(--accent);
        color: #fff;
        border: 2px solid var(--accent);
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-primary-custom:hover {
        background-color: var(--accent2);
        color: #fff;
        border-color: var(--accent2);
        transform: translateY(-2px);
    }

    .btn-secondary-custom {
        background-color: transparent;
        color: #3e2723;
        border: 2px solid #3e2723;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-secondary-custom:hover {
        background-color: #3e2723;
        color: #fdf6ec;
        transform: translateY(-2px);
    }

    .step-card {
        background-color: rgba(253, 246, 236, 0.7);
        border: 1px solid rgba(124, 74, 30, 0.15);
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        height: 100%;
        transition: 0.3s;
    }

    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(124, 74, 30, 0.12);
    }
</style>

<div class="hero-section">
    <h1 class="hero-title">☕ Welcome to CafeBot</h1>

    <p class="hero-subtitle">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'waiter'): ?>
            Kitchen Staff Portal. Access the live dashboard to manage and update customer orders in real-time.
        <?php else: ?>
            Experience the future of cafe dining. Browse our artisanal menu, customize your options, and place your
            order with ease.
        <?php endif; ?>
    </p>

    <div class="d-flex justify-content-center gap-3 flex-wrap">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'waiter'): ?>
            <a href="waiter_dashboard.php" class="btn-primary-custom" style="text-decoration: none;">👨‍🍳 View Live
                Orders</a>
        <?php else: ?>
            <a href="menu.php" class="btn-primary-custom">View Full Menu</a>
        <?php endif; ?>
    </div>
</div>

<div class="container py-5">
    <h2 class="text-center mb-5" style="font-family: 'Playfair Display', serif; font-weight: 700; color: #3e2723;">How
        It Works</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="step-card">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">📜</div>
                <h3 style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 700;">Browse the
                    Menu</h3>
                <p>Explore our carefully curated artisanal categories, from perfectly roasted coffees to gourmet
                    sandwiches.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="step-card">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">🛒</div>
                <h3 style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 700;">Place Your
                    Order</h3>
                <p>Easily select your favorite items, customize your options, and securely place your order online.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="step-card">
                <div style="font-size: 2.5rem; margin-bottom: 15px;">✨</div>
                <h3 style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 700;">Enjoy Your
                    Order</h3>
                <p>Your order is prepared fast and fresh. Pick it up from the counter and enjoy a seamless dining
                    experience.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>