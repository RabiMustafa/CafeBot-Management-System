<?php
include 'db.php';

// THE BOUNCER: Kick out anyone who isn't a logged-in waiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'waiter') {
    header("Location: index.php");
    exit();
}

// HANDLE STATUS UPDATES
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);

    // Update the database
    $conn->query("UPDATE Orders SET status = '$new_status' WHERE id = $order_id");

    // Refresh the page to show the new status
    header("Location: waiter_dashboard.php");
    exit();
}

// HANDLE ORDER CLAIMING
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_order'])) {
    $order_id = intval($_POST['order_id']);
    $waiter_id = $_SESSION['user_id'];
    
    $conn->query("UPDATE Orders SET waiter_id = $waiter_id, status = 'In Progress' WHERE id = $order_id");
    
    header("Location: waiter_dashboard.php");
    exit();
}

include 'header.php';

// Function to render order cards
function render_order_cards($conn, $sql_orders, $is_claimable = false) {
    $orders_result = $conn->query($sql_orders);

    if ($orders_result->num_rows > 0) {
        while ($order = $orders_result->fetch_assoc()) {
            $order_id = $order['id'];
            $status = $order['status'];

            // Determine badge color
            $badge_class = 'status-pending';
            if ($status === 'In Progress') $badge_class = 'bg-primary text-white';
            if ($status === 'Ready') $badge_class = 'status-ready';

            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="ticket-card h-100">
                    <div class="ticket-header">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="mb-0 fw-bold" style="color: var(--accent);">Order #<?= $order_id ?></h4>
                            <span class="status-badge <?= $badge_class ?>">⏳ <?= $status ?></span>
                        </div>
                        <div class="text-muted fw-bold">👤 <?= htmlspecialchars($order['full_name']) ?></div>
                        <div class="text-muted fw-bold">💳 <?= $order['payment_method'] ?> (Rs.
                            <?= number_format($order['total_amount'], 2) ?>)</div>
                        <div class="text-muted" style="font-size: 0.85rem;">🕒
                            <?= date('h:i A', strtotime($order['order_date'])) ?></div>
                    </div>

                    <div class="p-3 flex-grow-1">
                        <?php
                        $sql_items = "SELECT od.quantity, m.name 
                                      FROM OrderDetails od 
                                      JOIN MenuItems m ON od.item_id = m.id 
                                      WHERE od.order_id = $order_id";
                        $items_result = $conn->query($sql_items);

                        while ($item = $items_result->fetch_assoc()) {
                            echo "<div class='item-row d-flex justify-content-between'>";
                            echo "<span>" . htmlspecialchars($item['name']) . "</span>";
                            echo "<span style='color: var(--accent2);'>x" . $item['quantity'] . "</span>";
                            echo "</div>";
                        }
                        ?>
                    </div>

                    <!-- Status Action Buttons -->
                    <div class="p-3 pt-0 mt-auto">
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?= $order_id ?>">

                            <?php if ($is_claimable): ?>
                                <input type="hidden" name="accept_order" value="1">
                                <button type="submit" class="btn btn-primary btn-update">👨‍🍳 Accept Order</button>
                            <?php else: ?>
                                <input type="hidden" name="update_status" value="1">
                                <?php if ($status === 'Pending' || $status === 'In Progress'): ?>
                                    <input type="hidden" name="new_status" value="Ready">
                                    <button type="submit" class="btn btn-success btn-update">🔔 Mark as Ready</button>
                                <?php elseif ($status === 'Ready'): ?>
                                    <input type="hidden" name="new_status" value="Completed">
                                    <button type="submit" class="btn btn-dark btn-update">✅ Order Picked Up</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    </div>

                </div>
            </div>
            <?php
        }
    } else {
        echo "<div class='col-12 text-center py-4'>
                <h5 class='text-muted'>No orders in this category.</h5>
              </div>";
    }
}
?>

<style>
    .dashboard-wrapper { max-width: 1200px; margin: 0 auto; }
    .ticket-card { background: #fff; border: 2px solid rgba(124, 74, 30, 0.2); border-radius: 12px; border-top: 6px solid var(--accent); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); display: flex; flex-direction: column; }
    .ticket-header { background: var(--bg); padding: 15px; border-bottom: 2px dashed rgba(124, 74, 30, 0.2); }
    .item-row { padding: 8px 0; border-bottom: 1px solid rgba(124, 74, 30, 0.1); font-weight: 600; }
    .item-row:last-child { border-bottom: none; }
    .status-badge { padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; }
    .status-pending { background-color: #ffeeba; color: #856404; }
    .status-ready { background-color: #d4edda; color: #155724; }
    .btn-update { width: 100%; border-radius: 8px; font-weight: bold; padding: 10px; transition: 0.2s; }
</style>

<div class="dashboard-wrapper my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 900;">
            👨‍🍳 Live Kitchen Dashboard
        </h2>
        <button onclick="window.location.reload();" class="btn btn-outline-secondary fw-bold">↻ Refresh Orders</button>
    </div>

    <!-- My Active Orders Section -->
    <h3 class="mb-3" style="font-family: 'Playfair Display', serif; color: #3e2723;">My Active Orders</h3>
    <div class="row g-4 mb-5">
        <?php
        $waiter_id = $_SESSION['user_id'];
        $sql_my_orders = "SELECT o.id, o.total_amount, o.payment_method, o.order_date, o.status, u.full_name 
                          FROM Orders o 
                          JOIN users u ON o.user_id = u.id 
                          WHERE o.waiter_id = $waiter_id AND o.status != 'Completed'
                          ORDER BY o.order_date ASC";
        render_order_cards($conn, $sql_my_orders, false);
        ?>
    </div>

    <hr style="border-top: 2px dashed rgba(124, 74, 30, 0.2);" class="my-5">

    <!-- Pending Orders Section -->
    <h3 class="mb-3" style="font-family: 'Playfair Display', serif; color: #3e2723;">Pending Orders (Unassigned)</h3>
    <div class="row g-4">
        <?php
        $sql_pending_orders = "SELECT o.id, o.total_amount, o.payment_method, o.order_date, o.status, u.full_name 
                               FROM Orders o 
                               JOIN users u ON o.user_id = u.id 
                               WHERE o.waiter_id IS NULL AND o.status != 'Completed'
                               ORDER BY o.order_date ASC";
        render_order_cards($conn, $sql_pending_orders, true);
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>