<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CafeBot</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --accent: #7c4a1e;
            --accent2: #a0622b;
            --cream: #fdf6ec;
            --bg: #f5ede0;
            --text: #2e1a0e;
            --muted: #9e7d62;
        }

        body {
            background-color: var(--bg);
            font-family: 'Nunito', sans-serif;
            color: var(--text);
            padding-bottom: 40px;
            /* Premium Parchment Background Aesthetic */
            background-image:
                radial-gradient(circle at 20% 80%, rgba(200, 131, 74, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(124, 74, 30, 0.06) 0%, transparent 50%);
        }

        /* Custom Premium Navbar Styles */
        .custom-navbar {
            background-color: #3e2723;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .custom-navbar .navbar-brand {
            color: #f4eee0 !important;
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .custom-navbar .nav-link {
            color: #f4eee0 !important;
            font-family: 'Nunito', sans-serif;
            font-weight: 600;
            font-size: 1.1rem;
            margin-left: 20px;
            position: relative;
            transition: color 0.3s ease;
            padding: 8px 0;
        }

        /* Elegant Hover Underline Effect */
        .custom-navbar .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #d4a373;
            transition: width 0.3s ease;
        }

        .custom-navbar .nav-link:hover {
            color: #d4a373 !important;
        }

        .custom-navbar .nav-link:hover::after {
            width: 100%;
        }

        /* Mobile Hamburger Icon Customization */
        .custom-navbar .navbar-toggler {
            border-color: rgba(244, 238, 224, 0.5);
        }

        .custom-navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(244, 238, 224, 1)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">☕ CafeBot</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cafeNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="cafeNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <!-- Check the role! -->
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link fw-bold" href="admin_dashboard.php"
                                    style="color: #dc3545 !important;">🛡️ Admin Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" href="admin_orders.php"
                                    style="color: #ffc107 !important;">📋 Order History</a></li>
                        <?php elseif ($_SESSION['role'] === 'waiter'): ?>
                            <li class="nav-item"><a class="nav-link fw-bold" href="waiter_dashboard.php"
                                    style="color: #28a745 !important;">👨‍🍳 Live Orders</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="menu.php">Full Menu</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" href="my_orders.php"
                                    style="color: #17a2b8 !important;">🧾 My Orders</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold" href="cart.php"
                                    style="color: var(--accent2) !important;">🛒 My Cart</a></li>
                        <?php endif; ?>

                        <!-- Logout Button (Everyone sees this) -->
                        <li class="nav-item ms-lg-4 mt-3 mt-lg-0">
                            <a href="logout.php" class="btn fw-bold px-4 py-2"
                                style="background-color: #d4a373; color: #3e2723; border-radius: 8px; transition: 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Logged-out visitors -->
                        <li class="nav-item"><a class="nav-link" href="menu.php">Full Menu</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Container Starts Here -->
    <div class="container mt-4"></div>