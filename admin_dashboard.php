<?php
include 'db.php';

// Strict session check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle role switching POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['new_role'])) {
    $target_user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    // Prevent admin from changing their own role, and validate role
    if ($target_user_id != $_SESSION['user_id'] && in_array($new_role, ['customer', 'waiter'])) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $target_user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect to prevent form resubmission
    header("Location: admin_dashboard.php");
    exit();
}

include 'header.php';

// Fetch all users except the current admin
$stmt = $conn->prepare("SELECT id, full_name, email, role FROM users WHERE id != ? ORDER BY full_name ASC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container py-5">
    <h2 class="mb-4" style="font-family: 'Playfair Display', serif; font-weight: 700; color: #3e2723;">
        🛡️ Admin Dashboard: User Management
    </h2>

    <div class="card shadow-sm border-0" style="background-color: rgba(253, 246, 236, 0.9); border-radius: 12px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: #3e2723; color: #fdf6ec;">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Current Role</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $user['role'] === 'waiter' ? 'bg-success' : ($user['role'] === 'admin' ? 'bg-danger' : 'bg-primary'); ?> rounded-pill px-3 py-2">
                                            <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Role Toggle Form -->
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <form method="POST" action="admin_dashboard.php" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <?php if ($user['role'] === 'customer'): ?>
                                                    <input type="hidden" name="new_role" value="waiter">
                                                    <button type="submit" class="btn btn-sm btn-outline-success fw-bold" style="border-radius: 50px;">
                                                        Make Waiter
                                                    </button>
                                                <?php else: ?>
                                                    <input type="hidden" name="new_role" value="customer">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary fw-bold" style="border-radius: 50px;">
                                                        Make Customer
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Admin</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No other users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
