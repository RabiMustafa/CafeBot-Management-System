<?php
include 'db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Kick waiters out of checkout
if ($_SESSION['role'] === 'waiter') {
    header("Location: waiter_dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $total_amount = $_POST['total_amount'];
    $payment_method = $conn->real_escape_string($_POST['payment_method']);

    // 1. Insert into Orders table (Now includes user_id and payment_method!)
    $sql_order = "INSERT INTO Orders (user_id, total_amount, payment_method) VALUES ($user_id, $total_amount, '$payment_method')";

    if ($conn->query($sql_order) === TRUE) {
        $order_id = $conn->insert_id;

        // 2. Loop through the session cart and insert into OrderDetails
        foreach ($_SESSION['cart'] as $item_id => $details) {
            $qty = $details['qty'];
            $sql_details = "INSERT INTO OrderDetails (order_id, item_id, quantity) VALUES ($order_id, $item_id, $qty)";
            $conn->query($sql_details);
        }

        // 3. Clear the cart memory since the order is placed
        unset($_SESSION['cart']);

        // 4. Show the beautiful success screen
        include 'header.php';
        ?>
        <div class="container text-center my-5"
            style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
            <div style="background: #d4edda; border: 2px solid #28a745; border-radius: 12px; padding: 40px; max-width: 500px;">
                <div style="font-size: 3rem; margin-bottom: 10px;">✅</div>
                <h2 style="font-family: 'Playfair Display', serif; color: #155724; font-weight: 800;">Order Confirmed!</h2>
                <p class="lead mt-3" style="color: #155724;">Your order <strong>#<?= $order_id ?></strong> has been sent to the
                    kitchen.</p>
                <p style="color: #155724;">Total Paid: Rs. <?= number_format($total_amount, 2) ?> via <?= $payment_method ?></p>
                <a href="menu.php" class="btn mt-3 fw-bold"
                    style="background-color: #28a745; color: white; padding: 10px 20px; border-radius: 8px;">Order More</a>
            </div>
        </div>
        <?php
        include 'footer.php';
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>