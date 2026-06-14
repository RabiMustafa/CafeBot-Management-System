<?php
include 'db.php';

// THE BOUNCER: Kick out anyone who isn't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// THE STAFF BOUNCER: If a waiter tries to access the menu, send them back to the kitchen!
if ($_SESSION['role'] === 'waiter') {
    header("Location: waiter_dashboard.php");
    exit();
}
if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$success_msg = '';

// CART LOGIC: If they click "Add to Cart"
// CART LOGIC: If they click "Add to Cart"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $name = $_POST['item_name'];
    $price = $_POST['item_price'];

    // Force the input to be an integer so they can't send decimals or text
    $qty = (int) $_POST['quantity'];

    // THE EXPLOIT FIX: Server-side validation
    if ($qty < 1) {
        // Catch hackers trying to order negative food
        $success_msg = "<div class='alert alert-danger text-center fw-bold'>Nice try! You must order at least 1 item.</div>";
    } else {
        // Safe to process!
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$item_id] = [
                'name' => $name,
                'price' => $price,
                'qty' => $qty
            ];
        }
        $success_msg = "<div class='alert alert-success text-center fw-bold'>Added $qty x $name to your cart!</div>";
    }
}

include 'header.php';
?>

<style>
    .menu-wrapper {
        max-width: 1050px;
        margin: 0 auto;
    }

    .page-header {
        font-family: 'Playfair Display', serif;
        color: var(--accent);
        font-weight: 900;
        margin: 50px 0 20px;
        text-align: center;
    }

    .menu-card {
        background-color: var(--cream);
        border: 1px solid rgba(124, 74, 30, 0.15);
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(124, 74, 30, 0.04);
        transition: all 0.3s ease;
        height: 100%;
        padding: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .menu-card-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .menu-card-body {
        padding: 20px 24px 28px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(124, 74, 30, 0.12);
        border-color: var(--accent);
    }

    .menu-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        position: relative;
    }

    .menu-card-link:hover {
        color: inherit;
    }

    .menu-card-img-wrap {
        position: relative;
        overflow: hidden;
    }

    .menu-card-img-wrap::after {
        content: 'View Details →';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(0deg, rgba(62, 39, 35, 0.85) 0%, transparent 100%);
        color: #fff;
        font-family: 'Nunito', sans-serif;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 30px 20px 14px;
        text-align: center;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .menu-card:hover .menu-card-img-wrap::after {
        opacity: 1;
        transform: translateY(0);
    }

    .menu-card:hover .menu-card-img {
        transform: scale(1.05);
    }

    .menu-card-img {
        transition: transform 0.4s ease;
    }

    .item-name {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 1.35rem;
        color: var(--text);
        margin-bottom: 6px;
    }

    .item-price {
        font-family: 'Nunito', sans-serif;
        font-weight: 800;
        font-size: 1.15rem;
        color: var(--accent2);
        margin-bottom: 24px;
    }

    .input-group-custom {
        display: flex;
        border: 1px solid rgba(124, 74, 30, 0.2);
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }

    .qty-label {
        background: rgba(124, 74, 30, 0.05);
        color: var(--muted);
        font-size: 0.9rem;
        font-weight: 700;
        padding: 8px 12px;
        border-right: 1px solid rgba(124, 74, 30, 0.2);
        display: flex;
        align-items: center;
    }

    .qty-input {
        border: none;
        background: transparent;
        text-align: center;
        color: var(--text);
        font-weight: 700;
        width: 60px;
    }

    .qty-input:focus {
        outline: none;
    }

    .btn-order {
        background-color: transparent;
        color: var(--accent);
        font-weight: 700;
        border: none;
        border-left: 1px solid rgba(124, 74, 30, 0.2);
        transition: 0.2s;
        padding: 8px 16px;
        flex-grow: 1;
    }

    .btn-order:hover {
        background-color: var(--accent);
        color: #fff;
    }
</style>

<div class="menu-wrapper">
    <h1 class="page-header">Our Artisanal Menu</h1>
    <?= $success_msg ?>

    <div class="row g-4 mb-5">
        <?php
        $result = $conn->query("SELECT * FROM MenuItems");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card menu-card">
                        <a href="item.php?id=<?= $row['id'] ?>" class="menu-card-link">
                            <div class="menu-card-img-wrap">
                                <img src="images/<?= $row['id'] ?>.jpg" alt="<?= htmlspecialchars($row['name']) ?>" class="menu-card-img">
                            </div>
                            <div class="menu-card-body">
                                <h5 class="item-name"><?= htmlspecialchars($row['name']) ?></h5>
                                <div class="item-price">Rs. <?= number_format($row['price'], 2) ?></div>
                            </div>
                        </a>
                        <div class="menu-card-body" style="padding-top: 0;">
                            <!-- Now submits to itself to add to the session cart -->
                            <form action="menu.php" method="POST" class="mt-auto">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="item_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="item_name" value="<?= htmlspecialchars($row['name']) ?>">
                                <input type="hidden" name="item_price" value="<?= $row['price'] ?>">

                                <div class="input-group-custom">
                                    <span class="qty-label">Qty</span>
                                    <input type="number" name="quantity" class="qty-input" value="1" min="1" required>
                                    <button type="submit" class="btn-order">+ Add to Cart</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>