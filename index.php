<?php include $_SERVER['DOCUMENT_ROOT'].'/session.php';
$show_logout_toast = isset($_GET['logout']) && $_GET['logout'] == '1';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quake</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">



</head>
<?php include 'headerNew.php'; ?>
<style>
body {
    background: radial-gradient(#000525 0%, #000 100%);
}

.earth img {
    position: absolute;
    top: 20vh;
    right: 10vw;
    z-index: -1;
    width: 80vh;
    height: 80vh;
}

.crack img {
    position: absolute;
    top: 0;
    right: 26vw;
    z-index: -2;
    height: 93vh;

}

.glass-box {
    background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
    backdrop-filter: blur(1vh);
    border-radius: 2rem;
    box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
    border: 2px solid rgba(255, 255, 255, 0.09);
    min-height: 33vh;
    top: 20vh;

}

.glass-box h1 {
    padding-top: 2vh;
    padding-left: 3vh;
    font-size: 6vh;
    font-weight: 900;
    font-family: 'Roboto', Arial, sans-serif;
    color: #fff;
    margin-bottom: 20px;
}

.glass-box .highlight {
    color: #ff7400;
}


.glass-box p {
    padding-left: 3vh;
    padding-right: 3vh;
    color: rgba(255, 255, 255, 0.5);
    font-size: 2vh;
    padding-bottom: 1vh;
    font-weight: 400;
    line-height: 1.32;
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
</style>

<body>

    <div class="earth">
        <img src="/assets/images/earth.png" alt="">
    </div>
    <div class="crack">
        <img src="/assets/images/crack.png" alt="">
    </div>


    <div class="container-fluid">
        <div class="row justify-content-start align-items-center" style="min-height: 92vh;">
            <div class="col-1"></div>
            <div class="col-6">
                <div class="glass-box">
                    <h1>
                        Seismic Activity<br>
                        <span class="highlight">Earthquake</span><span> Monitoring</span>
                    </h1>
                    <p>
                        This is a website to monitor earthquakes, buy artifacts and lorum ipsum deez nuts. Lorem ipsum
                        dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                        dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                        aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur.
                    </p>
                </div>
            </div>
            <div class="col-5"></div>

        </div>
    </div>
    <?php if ($show_logout_toast): ?>
    <div class="quake-toast-container" aria-live="assertive" aria-atomic="true">
        <div class="quake-toast" id="quakeLogoutToast" role="alert">
            <span class="quake-toast__icon">
                <svg width="28" height="28" fill="none" viewBox="0 0 28 28">
                    <circle cx="14" cy="14" r="14" fill="#ffbe3d" fill-opacity="0.22" />
                    <path d="M8 15.5l4.1 3.2c.28.22.68.18.91-.09l6-7" stroke="#ff9100" stroke-width="2.1"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <span class="quake-toast__msg">
                Logged out successfully!
            </span>
            <button class="quake-toast__close" onclick="dismissToast()" aria-label="Dismiss">&times;</button>
        </div>
    </div>
    <script>
    function dismissToast() {
        document.getElementById('quakeLogoutToast').style.display = 'none';
    }
    setTimeout(dismissToast, 3000);
    </script>
    <style>
    .quake-toast-container {
        position: fixed;
        top: 36px;
        right: 38px;
        z-index: 9999;
        pointer-events: none;
    }

    .quake-toast {
        display: flex;
        align-items: center;
        min-width: 290px;
        max-width: 360px;
        padding: 18px 28px 18px 18px;
        font-size: 1.11em;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(113deg, rgba(40, 44, 64, .93) 55%, rgba(22, 26, 38, .99) 100%);
        border-radius: 18px;
        box-shadow: 0px 6px 38px #000a, 0 0 8px #ff910034;
        backdrop-filter: blur(12px);
        border: 1.3px solid #312e2255;
        pointer-events: auto;
        animation: quake-toast-in .5s cubic-bezier(.88, .12, .48, .98);
    }

    @keyframes quake-toast-in {
        from {
            opacity: 0;
            transform: translateY(-24px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .quake-toast__icon {
        color: #ffbe3d;
        margin-right: 14px;
        font-size: 2em;
        display: flex;
        align-items: center;
    }

    .quake-toast__msg strong {
        font-weight: 900;
        color: #ff9100;
    }

    .quake-toast__close {
        cursor: pointer;
        margin-left: 18px;
        opacity: .7;
        background: none;
        border: none;
        font-size: 1.4em;
        color: #fff;
        transition: opacity.15s;
    }

    .quake-toast__close:hover {
        opacity: 1;
    }

    @media (max-width:600px) {
        .quake-toast-container {
            right: 4vw;
            top: 14px;
        }

        .quake-toast {
            min-width: 180px;
            max-width: 95vw;
            font-size: 1em;
            padding: 13px 13px 13px 16px;
        }
    }
    </style>
    <?php endif; ?>
</body>

</html>