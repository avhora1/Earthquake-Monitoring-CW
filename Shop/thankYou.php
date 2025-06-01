<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Thank You - Quake</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- If not already imported, Roboto font -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
<?php include '../headerNew.php'; ?>
<style>
body {
    background: radial-gradient(#000525 0%, #000 100%);
    font-family: 'Roboto', Arial, sans-serif;
    margin: 0;
    min-height: 100vh;
}
.thankyou-bg-blur {
    min-height: 100vh;
    width: 100vw;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    position: relative;
}
.glass-box {
    background: linear-gradient(153deg, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0.03) 100%);
    backdrop-filter: blur(1.5vh);
    border-radius: 2.4rem;
    min-height: 54vh;
    min-width: 610px;
    max-width: 98vw;
    box-shadow: 0 8px 48px 0 rgb(0 0 0 / 20%);
    border: 2.5px solid rgba(255, 255, 255, 0.13);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 4rem 3rem 3.5rem 3rem;
}
.glass-box h1 {
    padding: 0;
    font-size: 3.7rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 16px;
    margin-top: 0;
}
.glass-box .highlight {
    color: #ff7400;
}

.glass-box p {
    margin: 0 0 32px 0;
    color: rgba(255, 255, 255, 0.85);
    font-size: 1.35rem;
    font-weight: 400;
    line-height: 1.6;
    text-align: center;
    max-width: 530px;
}

.big-icon {
    margin: 38px 0 32px 0;
    width: 220px;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.thankyou-actions {
    display: flex;
    gap: 32px;
    margin-top: 22px;
}
.btn {
    font-family: 'Roboto', Arial, sans-serif;
    font-size: 1.13rem;
    border: none;
    outline: none;
    padding: 16px 48px;
    border-radius: 13px;
    cursor: pointer;
    font-weight: 500;
    box-shadow: 0 3px 18px 0 rgba(0,0,0,0.11);
    transition: box-shadow 0.2s, background 0.2s, color 0.2s;
}
.btn-download {
    background: linear-gradient(90deg, #ff9100 0%, #ffbe3d 100%);
    color: #fff;
}
.btn-download:hover {
    background: linear-gradient(90deg, #ffbe3d 0%, #ff9100 100%);
    box-shadow: 0 0 28px #fa8c16;
    text-decoration: none;
}
.btn-shop {
    background: #fff;
    color: #191919;
    border: 2.5px solid transparent;
    transition: border 0.2s;
    font-size: 1.12rem;
}
.btn-shop:hover {
    border: 2.5px solid #ff9100;
    color: #ff7400;
}
@media (max-width: 900px) {
    .glass-box {
        min-width: 85vw;
        padding: 3vw 3vw 5vw 3vw;
    }
    .glass-box h1 { font-size: 2.2rem; }
    .glass-box p { font-size: 1.07rem; }
    .big-icon { width: 28vw; height: 28vw; }
}

@media (max-width: 600px) {
    .glass-box {
        min-width: unset;
        padding: 7vw 2vw 7vw 2vw;
    }
    .glass-box h1 { font-size: 1.5rem; }
    .big-icon { width: 23vw; height: 23vw; }
    .thankyou-actions { gap: 8px; flex-direction: column; align-items: stretch; }
    .btn { width: 100%; min-width: 0; }
}
</style>
</head>
<body>
<div class="thankyou-bg-blur">
    <div class="glass-box">
        <h1>Thank you <span class="highlight"><?php echo $_SESSION["firstname"];?></span>!</h1>
        <p>We are getting your order ready. Please press the button below to download your receipt.</p>
        <div class="big-icon">
        <img src="../assets/icons/ThankYouBox.svg" alt="Icon" width="220" height="220" class="big-icon-svg">
        </div>
        <div class="thankyou-actions">
            <button class="btn btn-download">Receipt download</button>
            <button class="btn btn-shop">Continue shopping</button>
        </div>
    </div>
</div>
</body>
</html>
<!--Add script that uses AJAX that produces a receipt from querying the orders database-->