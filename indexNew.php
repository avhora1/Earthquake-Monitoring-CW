<?php include $_SERVER['DOCUMENT_ROOT'].'/session.php'; ?>

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
    right: 23vw;
    z-index: -2;
    height: 93vh;

}

.glass-box {
    background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
    backdrop-filter: blur(1vh);
    border-radius: 2rem;
    min-height: 33vh;
    top: 20vh;
    box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
    border: 2px solid rgba(255, 255, 255, 0.09);

}

.glass-box h1 {
    padding-top: 2vh;
    padding-left: 3vh;
    font-size: 6vh;
    font-weight: 900;
    color: #fff;
    margin-bottom: 20px;
    font-family: 'Roboto', Arial, sans-serif;
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
</body>

</html>