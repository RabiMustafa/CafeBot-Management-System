<?php
include 'db.php';

// THE REVERSE BOUNCER: If they ARE already logged in, send them to the Home Page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = '';
// If the form was submitted:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $otp_code = sprintf("%06d", mt_rand(1, 999999));

    try {
        $sql = "INSERT INTO users (full_name, email, password, is_verified, otp_code) VALUES ('$name', '$email', '$password', 0, '$otp_code')";

        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Account created successfully! You can now <a href='login.php'>Login</a>.</div>";
        }
    } catch (mysqli_sql_exception $e) {
        // Error 1062 is MySQL's specific code for "Duplicate Entry"
        if ($e->getCode() == 1062) {
            $message = "<div class='alert alert-danger'>Error: That email is already registered! Please log in.</div>";
        } else {
            // Catch any other weird database errors gracefully
            $message = "<div class='alert alert-danger'>Oops! Something went wrong on our end. Please try again.</div>";
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
            style="font-family: 'Playfair Display', serif; color: var(--accent); font-weight: 800;">Create Account</h3>
        <p class="text-center text-muted mb-4">Join CafeBot today</p>

        <?= $message ?> <!-- Displays success or error message here -->

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-muted fw-bold" style="font-size: 0.85rem;">FULL NAME</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted fw-bold" style="font-size: 0.85rem;">EMAIL ADDRESS</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted fw-bold" style="font-size: 0.85rem;">PASSWORD</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn btn-cafe mb-3">Sign Up</button>
            <div class="text-center" style="font-size: 0.9rem;">
                Already have an account? <a href="login.php"
                    style="color: var(--accent); font-weight: bold; text-decoration: none;">Log In</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>