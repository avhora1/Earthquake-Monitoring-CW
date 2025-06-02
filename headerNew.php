<?php
include 'session.php';
include 'connection.php';
$acct = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : 'guest';
// Always at the top, after session_start(), on every page that uses the basket!
if (isset($_SESSION['basket']) && count($_SESSION['basket']) > 0) {
    $ids = array_keys($_SESSION['basket']);
    $ids_int = array_map('intval', $ids);

    // Fetch only IDs that still exist in stock_list table
    if (!empty($ids_int)) {
        $in = implode(',', $ids_int);
        $sql = "SELECT id FROM stock_list WHERE id IN ($in)";
        $result = sqlsrv_query($conn, $sql);
        $live_ids = [];
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $live_ids[] = $row['id'];
        }
        // Remove any IDs not in $live_ids
        $_SESSION['basket'] = array_intersect_key($_SESSION['basket'], array_flip($live_ids));
    }
}
?>

<?php
$current_path = $_SERVER['REQUEST_URI'];
$shop_active = strpos($current_path, '/shop/shop.php') !== false ? 'active' : '';
$earthquakes_active = strpos($current_path, '/earthquakes/earthquakes.php') !== false ? 'active' : '';
$dashboard_active = strpos($current_path, '/dashboard/dashboard.php') !== false ? 'active' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quake</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <style>
    body {
        margin: 0;
        font-family: 'Roboto', Arial, sans-serif;
        background: #000;
    }

    header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.0);
        /* FULLY TRANSPARENT */
        z-index: 999;
        box-shadow: none;
    }

    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1300px;
        margin: 0 auto;
        padding: 18px 40px;
    }

    .logo {
        display: flex;
        align-items: center;
    }

    .logo-img {
        height: 45px;
        width: auto;
    }

    /* Nav Links */
    nav ul {
        list-style: none;
        display: flex;
        gap: 32px;
        margin: 0;
        padding: 0;
    }

    nav a {
        text-decoration: none;
        color: #fff;
        font-family: 'Roboto', Arial, sans-serif;
        font-size: 1.11rem;
        font-weight: 400;
        transition: color 0.2s;
        border: none;
    }

    nav a:hover {
        color: #fa8c16;
    }

    .nav-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .login-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.05rem;
        cursor: pointer;
        transition: color 0.2s;
        font-family: 'Roboto', Arial, sans-serif;
        text-decoration: none;
        font-weight: 400;
    }

    .login-btn:hover {
        color: #fa8c16;
        text-decoration: none;
    }

    .signup-btn {
        padding: 7px 24px;
        background: linear-gradient(90deg, #ff9100, #ffbe3d);
        color: #fff;
        border: none;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 0 16px #fa8c16;
        margin-left: 8px;
        cursor: pointer;
        font-size: 1rem;
        transition: background 0.25s, box-shadow 0.25s;
        font-family: 'Roboto', Arial, sans-serif;
        text-decoration: none;
    }

    .signup-btn:hover {
        background: linear-gradient(90deg, #ffbe3d, #ff9100);
        box-shadow: 0 0 28px #fa8c16;
        text-decoration: none;
    }

    .basket-link {
        margin-left: 18px;
        display: flex;
        align-items: center;
        height: 38px;
    }

    .basket-icon {
        height: 30px;
        width: 30px;
        object-fit: contain;
        filter: drop-shadow(0 0 2px #191919);
        transition: filter 0.15s;
    }

    .basket-link:hover .basket-icon {
        filter: brightness(1.3) drop-shadow(0 0 10px #ff9100);
    }

    /* Responsive tweaks */
    @media (max-width: 900px) {
        .navbar {
            flex-direction: column;
            gap: 20px;
            padding: 12px;
        }

        nav ul {
            gap: 15px;
        }

        .logo-img {
            height: 34px;
        }
    }

    @media (max-width: 600px) {
        .navbar {
            flex-direction: column;
            align-items: stretch;
            padding: 7px 4vw;
        }

        .nav-actions {
            margin-top: 7px;
        }

        nav ul {
            justify-content: center;
            gap: 8vw;
        }

        .basket-link {
            margin-top: 5px;
            margin-left: 0;
            align-self: flex-end;
        }
    }

    nav a.active {
        color: #fa8c16;
    }

    navbar.active {
        max-width: 1300px;
    }
    </style>
</head>

<body>
    <header>
        <div class="navbar">
            <a href="/" class="logo">
                <img src="/assets/brand/Quake Logo.png" alt="Quake Logo" class="logo-img">
            </a>
            <nav>
                <ul>
                    <li><a href="/shop/shop.php" class="<?php echo $shop_active; ?>">Shop</a></li>
                    <li><a href="/earthquakes/earthquakes.php"
                            class="<?php echo $earthquakes_active; ?>">Earthquakes</a></li>
                    <li><a href="/Earthquake/manage_earthquakesNew.php" class="<?php echo $dashboard_active; ?>">Dashboard</a></li>
                </ul>
            </nav>
            <div class="nav-actions">
                <a href="/sign-in/signin.php" class="login-btn">Login</a>
                <a href="/sign-in/register.php" class="signup-btn">Sign up</a>
                <a href="/basket/basket.php" class="basket-link" title="Basket">
                    <img src="/assets/icons/basket.png" alt="Basket" class="basket-icon">
                </a>
            </div>
        </div>
    </header>