<?php
include 'db.php';

// THE REVERSE BOUNCER: If they ARE already logged in, send them to the Home Page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
// If the user submits the login form:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Find the user by email
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password matches the hash in the database
        // Verify the password matches the hash in the database
        if (password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 0) {
                // Not verified yet, send to verification
                $_SESSION['pending_user_id'] = $user['id'];
                header("Location: verify.php");
                exit();
            } else {
                // Login Success! Save their info to the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role']; // <-- We now memorize their role!
    
                // Redirect based on role
                if ($_SESSION['role'] === 'waiter') {
                    header("Location: waiter_dashboard.php");
                } else {
                    header("Location: menu.php");
                }
                exit();
            }
        } else {
            $error = "Incorrect password.";
        }
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
</style>

<div class="d-flex align-items-center" style="min-height: 70vh;">
    <div class="auth-card">
        <div class="auth-logo">☕</div>
        <h3 class="text-center mb-1"
            style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 800;">Welcome Back</h3>
        <p class="text-center text-muted mb-4">Log in to place your order</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-muted fw-bold" style="font-size: 0.85rem;">EMAIL ADDRESS</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted fw-bold" style="font-size: 0.85rem;">PASSWORD</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-cafe mb-3">Log In</button>
            <div class="text-center" style="font-size: 0.9rem;">
                Don't have an account? <a href="signup.php"
                    style="color: var(--accent); font-weight: bold; text-decoration: none;">Sign Up</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>