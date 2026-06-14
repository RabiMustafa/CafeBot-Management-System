<?php
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';
$date_filter = isset($_GET['date_range']) ? $_GET['date_range'] : 'All Time';

$where = [];
$params = [];
$types = "";

if ($status_filter !== 'All') {
    $where[] = "o.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($date_filter === 'Today') {
    $where[] = "DATE(o.order_date) = CURDATE()";
} elseif ($date_filter === 'This Week') {
    $where[] = "YEARWEEK(o.order_date, 1) = YEARWEEK(CURDATE(), 1)";
}

$where_sql = "";
if (count($where) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}

$sql = "SELECT o.id, o.total_amount, o.order_date, o.status, c.full_name AS customer_name, w.full_name AS waiter_name 
        FROM Orders o 
        JOIN users c ON o.user_id = c.id 
        LEFT JOIN users w ON o.waiter_id = w.id
        $where_sql 
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    // Dynamic binding
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include 'header.php';
?>

<div class="container py-5">
    <h2 class="mb-4" style="font-family: 'Playfair Display', serif; font-weight: 700; color: #3e2723;">
        📋 Order History
    </h2>

    <!-- Filter Form -->
    <div class="card shadow-sm border-0 mb-4" style="background-color: rgba(253, 246, 236, 0.9); border-radius: 12px;">
        <div class="card-body">
            <form method="GET" action="admin_orders.php" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="All" <?= $status_filter === 'All' ? 'selected' : '' ?>>All</option>
                        <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Ready" <?= $status_filter === 'Ready' ? 'selected' : '' ?>>Ready</option>
                        <option value="Completed" <?= $status_filter === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Cancelled" <?= $status_filter === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Date Range</label>
                    <select name="date_range" class="form-select">
                        <option value="All Time" <?= $date_filter === 'All Time' ? 'selected' : '' ?>>All Time</option>
                        <option value="Today" <?= $date_filter === 'Today' ? 'selected' : '' ?>>Today</option>
                        <option value="This Week" <?= $date_filter === 'This Week' ? 'selected' : '' ?>>This Week</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary fw-bold px-4">Apply Filters</button>
                    <a href="admin_orders.php" class="btn btn-outline-secondary ms-2">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm border-0" style="background-color: rgba(253, 246, 236, 0.9); border-radius: 12px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: #3e2723; color: #fdf6ec;">
                        <tr>
                            <th scope="col">Order ID</th>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Served By</th>
                            <th scope="col">Total Price</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>
                                        <?php if ($order['waiter_name']): ?>
                                            👨‍🍳 <?php echo htmlspecialchars($order['waiter_name']); ?>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-success fw-bold">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo date('M j, Y, g:i a', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'bg-secondary';
                                            if ($order['status'] === 'Pending') $badgeClass = 'bg-warning text-dark';
                                            if ($order['status'] === 'Ready') $badgeClass = 'bg-info text-dark';
                                            if ($order['status'] === 'Completed') $badgeClass = 'bg-success';
                                            if ($order['status'] === 'Cancelled') $badgeClass = 'bg-danger';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3 py-2">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No orders match the selected filters.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
