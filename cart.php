<?php
// Turn on the error lights!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kick waiters out of the cart
if ($_SESSION['role'] === 'waiter') {
    header("Location: waiter_dashboard.php");
    exit();
}
if ($_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// ITEM REMOVAL LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_item'])) {
    $remove_id = $_POST['remove_id'];

    // If the item exists in the cart, delete it
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }

    // Refresh the page instantly so the item disappears
    header("Location: cart.php");
    exit();
}

include 'header.php';
?>

<style>
    .cart-wrapper {
        max-width: 800px;
        margin: 0 auto;
        background: var(--cream);
        border: 1px solid rgba(124, 74, 30, 0.15);
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 8px 25px rgba(124, 74, 30, 0.05);
    }

    .table th {
        color: var(--muted);
        font-weight: 700;
        border-bottom: 2px solid rgba(124, 74, 30, 0.2);
    }

    .table td {
        vertical-align: middle;
        font-weight: 600;
        color: var(--text);
        border-bottom: 1px solid rgba(124, 74, 30, 0.1);
    }

    .btn-checkout {
        background: var(--accent);
        color: white;
        font-weight: bold;
        padding: 12px 30px;
        border-radius: 8px;
        border: none;
        transition: 0.3s;
        width: 100%;
    }

    .btn-checkout:hover {
        background: var(--accent2);
        color: white;
        transform: translateY(-2px);
    }
</style>

<div class="container my-5">
    <div class="cart-wrapper">
        <h2 class="mb-4 text-center"
            style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 800;">Your Cart</h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table mb-4" style="background-color: transparent;">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-center" style="width: 50px;"></th> <!-- The missing 5th header! -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0;
                    foreach ($_SESSION['cart'] as $id => $details):
                        $subtotal = $details['price'] * $details['qty'];
                        $grand_total += $subtotal;
                        ?>
                        <tr>
                            <td style="font-family: 'Playfair Display', serif; font-size: 1.1rem; vertical-align: middle;">
                                <?= htmlspecialchars($details['name']) ?></td>
                            <td class="text-center" style="vertical-align: middle;">Rs.
                                <?= number_format($details['price'], 2) ?></td>
                            <td class="text-center" style="vertical-align: middle;"><?= $details['qty'] ?></td>
                            <td class="text-end" style="color: var(--accent2); vertical-align: middle;">Rs.
                                <?= number_format($subtotal, 2) ?></td>

                            <!-- THE REMOVE BUTTON -->
                            <td class="text-center" style="vertical-align: middle;">
                                <form method="POST" action="cart.php" style="margin: 0;">
                                    <input type="hidden" name="remove_id" value="<?= $id ?>">
                                    <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger"
                                        style="border-radius: 50%; width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center; margin: auto; transition: 0.2s;"
                                        title="Remove from Cart">
                                        <span style="font-size: 0.8rem; font-weight: bold;">✖</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end fs-5 align-middle" style="border-bottom: none;">Grand Total:</th>
                        <th class="text-end fs-5 align-middle" style="color: var(--accent); border-bottom: none;">Rs.
                            <?= number_format($grand_total, 2) ?></th>
                        <th style="border-bottom: none;"></th> <!-- The missing 5th footer! -->
                    </tr>
                </tfoot>
            </table>

            <!-- Checkout Form -->
            <form action="checkout.php" method="POST" class="mt-4 p-4"
                style="background: var(--bg); border-radius: 8px; border: 1px solid rgba(124, 74, 30, 0.1);">
                <h5 class="mb-3" style="color: var(--text); font-weight: 700;">Payment Method</h5>
                <select name="payment_method" class="form-select mb-4" required style="border-color: rgba(124,74,30,0.2);">
                    <option value="" disabled selected>Select how you want to pay...</option>
                    <option value="Cash">Cash at Counter</option>
                    <option value="Easypaisa">Easypaisa</option>
                    <option value="JazzCash">JazzCash</option>
                </select>

                <input type="hidden" name="total_amount" value="<?= $grand_total ?>">
                <button type="submit" class="btn-checkout">Confirm Order & Pay Rs.
                    <?= number_format($grand_total, 2) ?></button>
            </form>

        <?php else: ?>
            <div class="text-center py-5">
                <h4 class="text-muted mb-3">Your cart is empty.</h4>
                <a href="menu.php" class="btn btn-outline-secondary">Go to Menu</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>