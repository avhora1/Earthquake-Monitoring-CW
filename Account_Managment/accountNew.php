<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
// Only logged in accounts allowed
if (!isset($_SESSION['account_loggedin']) || !$_SESSION['account_loggedin']) {
    header('Location: /Sign-in/signin.php');
    exit();
}
$account_name = $_SESSION['account_name'];
$account_type = $_SESSION['account_type'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../headerNew.php';?>
    <meta charset="utf-8">
    <title>Manage Account | Quake</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link to your quake-admin.css (from previous steps, for header/sidebar/buttons/forms/tables) -->
    <link rel="stylesheet" href="/assets/css/quake.css">
    <style>
        /* Page-specific adjustments */
        .account-panel {
            flex: 1.6;
            min-width: 550px;
            background: linear-gradient(113deg,rgba(40,44,64,.91),rgba(22,26,38,.95) 90%);
            border-radius: 20px;
            box-shadow: 0 0 34px #090e206e;
            padding: 40px 45px 36px 45px;
            margin: 0 16px 0 0;
        }
        .account-panel h2 {
            font-size: 2.1rem;
            font-weight: 800;
            margin-bottom: 3px;
        }
        .account-panel .section-desc {
            color: #bfc7df;
            margin-bottom: 26px;
            font-size: 1.07em;
            line-height: 1.5;
        }

        .account-panel dl {
            margin-bottom: 13px;
            margin-top: 0;
            font-size: 1.1em;
            display: grid;
            grid-template-columns: 170px 1fr;
        }
        .account-panel dt { color: #e5e7ed; font-weight: 500;}
        .account-panel dd { margin: 0 0 0 0; color: #fff; }
        /* Form style */
        .account-actions-form {
            display: flex;
            flex-direction: column;
            gap: 21px;
        }
        .account-action-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 14px;
        }
        .account-action-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.07em;
        }
        .account-action-label svg, .account-action-label img {
            height: 1.32em;
            width: 1.32em;
            vertical-align: middle;
            margin-right: 3px;
        }
        .account-actions-form input[type="text"],
        .account-actions-form input[type="password"] {
            background: transparent;
            border: none;
            border-bottom: 1.5px solid #3d414d;
            color: #fff;
            font-size: 1.06em;
            padding: 6px 10px 6px 10px;
            border-radius: 0;
            outline: none;
            min-width: 140px;
            transition: border-color .18s;
        }
        .account-actions-form input[type="text"]:focus,
        .account-actions-form input[type="password"]:focus {
            border-color: #ff9100;
        }
        .account-actions-form .delete-btn {
            margin-left: 10px;
            min-width: 38px;
            min-height: 38px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .account-action-row .delete-btn img {
            width: 1.25em;
            vertical-align: middle;
        }
        /* Orders button row */
        .account-action-row.orders-row {
            margin-top: 20px;
        }
        .account-panel .view-orders-btn {
            background: #fff;
            color: #212124;
            border-radius: 9px;
            font-family: inherit;
            font-weight: 600;
            font-size: 1.01em;
            border: none;
            padding: 7px 24px;
            margin-right: 10px;
            cursor: pointer;
            box-shadow: 0 0 7px #191d30c5;
            transition: background .18s, color .18s;
            display: flex;
            align-items: center;
        }
        .account-panel .view-orders-btn:hover {
            background: #ff9100;
            color: #fff;
        }
        .account-panel .view-orders-btn svg, 
        .account-panel .view-orders-btn img {
            width: 1.1em;
            margin-right: 6px;
            vertical-align: middle;
        }
        @media (max-width:1100px) {
            .account-panel { padding: 18px 6vw; }
        }
        @media (max-width:700px) {
            .account-panel { min-width: none; padding: 9px 1vw 18px 2vw;}
        }
    </style>
</head>
<body>
<!-- HEADER & SIDEBAR (use your PHP includes or just use same HTML markup as elsewhere) -->
<div class="sidebar">
    <ul class="sidebar-nav">
        <li><a href="/Earthquake/manage_earthquakesNew.php"><img src="/assets/icons/quake.svg">Earthquakes</a></li>
        <li><a href="#"><img src="/assets/icons/observatory.svg">Observatories</a></li>
        <li><a href="#"><img src="/assets/icons/warehouse.svg">Warehouse</a></li>
        <li><a href="#"><img src="/assets/icons/box.svg">Pallets</a></li>
        <li><a href="../Artefact/manage_artefactsNew.php"><img src="/assets/icons/artifact.svg">Artifacts</a></li>
        <li><a href="/shop/shop.php"><img src="/assets/icons/shop.svg">Shop</a></li>
        <li><a href="#"><img src="/assets/icons/team.svg">Team</a></li>
        <li class="active"><a href="#"><img src="/assets/icons/account.svg">Account</a></li>
    </ul>
    <div class="sidebar-logout">
        <a href="/sign-in/logout.php"><img src="/assets/icons/logout.svg">Log out</a>
    </div>
</div>

<!-- MAIN CONTENT AREA -->
<div class="main-content">
    <div class="account-panel">
        <h2>Manage Account</h2>
        <div class="section-desc">
            Manage your Observatory account. Change your username/password, view your account type and orders.
        </div>
        <dl>
            <dt>Username:</dt>
            <dd><?= htmlspecialchars($account_name) ?></dd>
            <dt>Account Type:</dt>
            <dd><?= str_replace("_", " ", htmlspecialchars(ucfirst($account_type))) ?></dd>
        </dl>

        <form class="account-actions-form" autocomplete="off" method