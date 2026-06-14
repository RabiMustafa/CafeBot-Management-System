<?php
include 'db.php';

// Redirect to login if they shouldn't be here
if (!isset($_SESSION['pending_user_id'])) {
    header("Location: login.php");
    exit();
}

$pending_user_id = $_SESSION['pending_user_id'];
$error = '';

// Fetch the OTP for the portfolio illusion
$result = $conn->query("SELECT otp_code FROM users WHERE id = $pending_user_id");
$user = $result->fetch_assoc();
$actual_otp = $user['otp_code'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_code = $_POST['otp_code'];

    if ($entered_code === $actual_otp) {
        // Success! Mark as verified
        $conn->query("UPDATE users SET is_verified = 1, otp_code = NULL WHERE id = $pending_user_id");

        // Now fully log them in
        $full_user_result = $conn->query("SELECT * FROM users WHERE id = $pending_user_id");
        $full_user = $full_user_result->fetch_assoc();

        $_SESSION['user_id'] = $full_user['id'];
        $_SESSION['user_name'] = $full_user['full_name'];
        $_SESSION['role'] = $full_user['role'];

        // Clean up pending session variable
        unset($_SESSION['pending_user_id']);

        // Redirect based on role or to index.php as requested
        if ($_SESSION['role'] === 'waiter') {
            header("Location: waiter_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Incorrect verification code. Please try again.";
    }
}

include 'header.php';
?>

<style>
    .auth-card {
        background: var(--cream);
        border: 1px solid rgba(124, 74, 30, 0.15);
        border-radius: 16px;
        padding: 40px 36px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 8px 25px rgba(124, 74, 30, 0.08);
        margin: 0 auto;
    }

    .auth-logo {
        width: 50px;
        height: 50px;
        background: var(--accent);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 16px;
    }

    .form-control {
        background: var(--bg);
        border: 1px solid rgba(124, 74, 30, 0.2);
        text-align: center;
        letter-spacing: 5px;
        font-size: 1.5rem;
    }

    .form-control:focus {
        border-color: var(--accent);
        box-shadow: none;
        background: white;
    }

    .btn-cafe {
        background-color: var(--accent);
        color: white;
        border-radius: 8px;
        font-weight: bold;
        width: 100%;
        padding: 10px;
        transition: 0.2s;
    }

    .btn-cafe:hover {
        background-color: var(--accent2);
        color: white;
    }

    .dev-banner {
        background-color: #fff3cd;
        border: 1px solid #ffe69c;
        color: #664d03;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-size: 0.95rem;
    }
</style>

<div class="container mt-4">
    <div class="dev-banner">
        <strong>Developer Mode:</strong> Email delivery is bypassed for portfolio demonstration.<br>
        Your Verification Code is: <strong><?= htmlspecialchars($actual_otp) ?></strong>
    </div>

    <div class="d-flex align-items-center" style="min-height: 60vh;">
        <div class="auth-card">
            <div class="auth-logo">✉️</div>
            <h3 class="text-center mb-1"
                style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 800;">Verify Email</h3>
            <p class="text-center text-muted mb-4">Enter the 6-digit code sent to your email.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <input type="text" name="otp_code" class="form-control" maxlength="6" required pattern="\d{6}" title="Please enter 6 digits">
                </div>
                <button type="submit" class="btn btn-cafe mb-3">Verify Account</button>
                <div class="text-center" style="font-size: 0.9rem;">
                    <a href="login.php" style="color: var(--accent); font-weight: bold; text-decoration: none;">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
