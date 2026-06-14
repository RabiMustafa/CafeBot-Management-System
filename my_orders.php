<?php
include 'db.php';

// THE BOUNCER: Kick out guests
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// STAFF BOUNCER: Waiters don't need to track personal orders here
if ($_SESSION['role'] === 'waiter') {
    header("Location: waiter_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

include 'header.php';
?>

<!-- Auto-refresh the page every 30 seconds so the status updates live! -->
<meta http-equiv="refresh" content="30">

<style>
    .tracker-wrapper {
        max-width: 900px;
        margin: 0 auto;
    }

    .order-card {
        background: #fff;
        border: 1px solid rgba(124, 74, 30, 0.15);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .order-header {
        background: var(--cream);
        padding: 15px 20px;
        border-bottom: 1px solid rgba(124, 74, 30, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
    }

    /* Status Colors */
    .status-Pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .status-Ready {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        animation: pulse 2s infinite;
    }

    .status-Completed {
        background-color: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }

    /* Make the "Ready" badge pulse so it grabs their attention! */
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
        }

        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }
</style>

<div class="tracker-wrapper my-5">
    <h2 class="mb-4 text-center"
        style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 900;">
        🧾 My Order History
    </h2>

    <?php
    // Fetch ONLY this specific user's orders
    $sql_orders = "SELECT o.id, o.total_amount, o.payment_method, o.order_date, o.status, w.full_name as waiter_name 
                   FROM Orders o 
                   LEFT JOIN users w ON o.waiter_id = w.id
                   WHERE o.user_id = $user_id 
                   ORDER BY o.order_date DESC";
    $orders_result = $conn->query($sql_orders);

    if ($orders_result->num_rows > 0) {
        while ($order = $orders_result->fetch_assoc()) {
            $order_id = $order['id'];
            $status = $order['status'];
            ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: var(--accent);">Order #
                            <?= $order_id ?>
                        </h5>
                        <small class="text-muted">
                            <?= date('F j, Y, g:i a', strtotime($order['order_date'])) ?>
                        </small>
                        <small class="text-muted d-block mt-1">
                            <?php 
                            if ($order['waiter_name']) {
                                echo "👨‍🍳 Served by: " . htmlspecialchars($order['waiter_name']);
                            } else {
                                echo "⏳ Waiting for a server...";
                            }
                            ?>
                        </small>
                    </div>
                    <div>
                        <!-- Dynamic Status Badge -->
                        <span class="status-badge status-<?= $status ?>">
                            <?php
                            if ($status === 'Pending')
                                echo "⏳ Pending";
                            elseif ($status === 'In Progress')
                                echo "👨‍🍳 Preparing";
                            elseif ($status === 'Ready')
                                echo "🔔 Ready for Pickup!";
                            else
                                echo "✅ Completed";
                            ?>
                        </span>
                    </div>
                </div>

                <div class="p-3">
                    <ul class="list-unstyled mb-0">
                        <?php
                        // Fetch the items for this specific order
                        $sql_items = "SELECT od.quantity, m.name 
                                      FROM OrderDetails od 
                                      JOIN MenuItems m ON od.item_id = m.id 
                                      WHERE od.order_id = $order_id";
                        $items_result = $conn->query($sql_items);

                        while ($item = $items_result->fetch_assoc()) {
                            echo "<li class='mb-1 text-muted fw-bold'>{$item['quantity']}x " . htmlspecialchars($item['name']) . "</li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted fw-bold">Payment:
                        <?= $order['payment_method'] ?>
                    </span>
                    <h5 class="mb-0 fw-bold" style="color: var(--accent2);">Total: Rs.
                        <?= number_format($order['total_amount'], 2) ?>
                    </h5>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<div class='text-center py-5'>
                <div style='font-size: 3rem; margin-bottom: 15px;'>🛒</div>
                <h4 class='text-muted'>You haven't placed any orders yet.</h4>
                <a href='menu.php' class='btn btn-outline-secondary mt-2'>Browse Menu</a>
              </div>";
    }
    ?>
</div>

<?php include 'footer.php'; ?>