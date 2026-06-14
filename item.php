<?php
include 'db.php';

// THE BOUNCER: Kick out anyone who isn't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// THE STAFF BOUNCER: If a waiter tries to access, send them back
if ($_SESSION['role'] === 'waiter') {
    header("Location: waiter_dashboard.php");
    exit();
}
if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Validate the item ID from the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: menu.php");
    exit();
}

$item_id = (int) $_GET['id'];

// Fetch the item from the database
$stmt = $conn->prepare("SELECT * FROM MenuItems WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: menu.php");
    exit();
}

$item = $result->fetch_assoc();

$success_msg = '';

// CART LOGIC: If they click "Add to Cart"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $name = $_POST['item_name'];
    $price = $_POST['item_price'];
    $qty = (int) $_POST['quantity'];

    if ($qty < 1) {
        $success_msg = "<div class='alert alert-danger text-center fw-bold'>Nice try! You must order at least 1 item.</div>";
    } else {
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
    .item-detail-wrapper {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px 0 60px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'Nunito', sans-serif;
        font-weight: 700;
        font-size: 1rem;
        color: var(--accent);
        text-decoration: none;
        margin-bottom: 28px;
        transition: color 0.2s ease, gap 0.2s ease;
    }

    .back-link:hover {
        color: var(--accent2);
        gap: 10px;
    }

    .detail-card {
        background-color: var(--cream);
        border: 1px solid rgba(124, 74, 30, 0.15);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(124, 74, 30, 0.08);
        overflow: hidden;
    }

    .detail-img {
        width: 100%;
        height: 380px;
        object-fit: cover;
        display: block;
    }

    .detail-body {
        padding: 36px 40px 44px;
    }

    .detail-name {
        font-family: 'Playfair Display', serif;
        font-weight: 900;
        font-size: 2rem;
        color: var(--text);
        margin-bottom: 8px;
    }

    .detail-divider {
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        border: none;
        border-radius: 2px;
        margin: 16px 0 20px;
    }

    .detail-price {
        font-family: 'Nunito', sans-serif;
        font-weight: 800;
        font-size: 1.5rem;
        color: var(--accent2);
        margin-bottom: 32px;
    }

    .detail-form {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .detail-input-group {
        display: flex;
        border: 1px solid rgba(124, 74, 30, 0.2);
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .detail-qty-label {
        background: rgba(124, 74, 30, 0.05);
        color: var(--muted);
        font-size: 0.95rem;
        font-weight: 700;
        padding: 12px 16px;
        border-right: 1px solid rgba(124, 74, 30, 0.2);
        display: flex;
        align-items: center;
    }

    .detail-qty-input {
        border: none;
        background: transparent;
        text-align: center;
        color: var(--text);
        font-weight: 700;
        font-size: 1.05rem;
        width: 70px;
        padding: 12px 8px;
    }

    .detail-qty-input:focus {
        outline: none;
    }

    .btn-add-cart {
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        color: #fff;
        font-family: 'Nunito', sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
        border: none;
        border-radius: 10px;
        padding: 12px 32px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(124, 74, 30, 0.2);
    }

    .btn-add-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(124, 74, 30, 0.3);
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .detail-img {
            height: 260px;
        }

        .detail-body {
            padding: 24px 20px 32px;
        }

        .detail-name {
            font-size: 1.5rem;
        }

        .detail-price {
            font-size: 1.25rem;
        }

        .detail-form {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-add-cart {
            text-align: center;
        }
    }
</style>

<div class="item-detail-wrapper">
    <a href="menu.php" class="back-link">← Back to Menu</a>

    <?= $success_msg ?>

    <div class="detail-card">
        <img src="images/<?= $item['id'] ?>.jpg" alt="<?= htmlspecialchars($item['name']) ?>" class="detail-img">
        <div class="detail-body">
            <h1 class="detail-name"><?= htmlspecialchars($item['name']) ?></h1>
            <hr class="detail-divider">
            <div class="detail-price">Rs. <?= number_format($item['price'], 2) ?></div>

            <form action="item.php?id=<?= $item['id'] ?>" method="POST" class="detail-form">
                <input type="hidden" name="add_to_cart" value="1">
                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['name']) ?>">
                <input type="hidden" name="item_price" value="<?= $item['price'] ?>">

                <div class="detail-input-group">
                    <span class="detail-qty-label">Qty</span>
                    <input type="number" name="quantity" class="detail-qty-input" value="1" min="1" required>
                </div>
                <button type="submit" class="btn-add-cart">🛒 Add to Cart</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
