<?php include $_SERVER['DOCUMENT_ROOT'].'/session.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quake</title>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php include '../headerNew.php'; ?>
<style>
body {
    background: radial-gradient(#000525 0%, #000 100%);
}

header {
    position: relative;
}

.shop-intro-wrapper {
    max-width: 70vw;
}

.shop-header-big {
    font-size: 6vh;
    font-weight: 900;
    font-family: 'Roboto', Arial, sans-serif;
    color: #fff;
}

.shop-username {
    color: #ff9100;
}

.shop-header-desc {
    color: rgba(255, 255, 255, 0.5);
    font-size: 2vh;
    font-weight: 400;
    line-height: 1.32;
    margin-bottom: 21px;
    width: 100%;
}

.shop-header-sep {
    height: 1.5px;
    background: #fff;
    opacity: 0.13;
    border: none;
    margin-bottom: 10vh;
    margin-top: 7px;
    width: 100%;
}

.glass-grid {
    display: grid;
    grid-template-columns: repeat(4, 15vw);
    grid-template-rows: repeat(2, 35vh);
    gap: 3vw 2vw;
    /* row-gap column-gap - tune as needed */
    justify-content: start;
    width: 65vw;
}

.glass-card {
    background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
    backdrop-filter: blur(1vh);
    border-radius: 2rem;
    box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
    border: 2px solid rgba(255, 255, 255, 0.09);
    width: 13vw;
    min-width: 140px;
    height: 35vh;
    min-height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: box-shadow .18s, transform .13s;
}

.glass-card:hover {
    box-shadow: 0 18px 38px #0009;
    transform: scale(1.025);
}

.glass-card-content {
    color: #fff;
    font-family: 'Roboto', Arial, sans-serif;
    font-size: 2rem;
    text-shadow: 0 2px 10px #101325aa;
    font-weight: bold;
    letter-spacing: 0.02em;
    text-align: center;
    user-select: none;
}

.shop-buy-btn {
    width: 100%;
    min-width: 10vh;
    min-height: 48px;
    border-radius: 1rem;
    background: linear-gradient(90deg, #ff9100 2%, #ffc63f 92%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #fff;
    font-size: 2.2em;
    border: none;
    cursor: pointer;
    box-shadow: 0 0 25px #fa8c1645;
    outline: none;
    transition: background 0.16s, box-shadow 0.20s, transform 0.12s;
    padding: 0;
    margin: 0 auto;
    margin-bottom: 10px;
    bottom: 0;
    gap: 0;
}

.shop-buy-btn:hover,
.shop-buy-btn:focus {
    background: linear-gradient(90deg, #ffc63f, #ff9100 95%);
    box-shadow: 0 0 38px #ff910088;
    color: #fff;
    transform: scale(1.045);
}

.cart-plus-icon {
    height: 2rem;
    width: auto;
    margin: 0;
    padding: 0;
    filter: drop-shadow(0 1.5px 8px #c46e0051);
    user-select: none;
    pointer-events: none;
    display: block;
}


.card-row {
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    justify-content: space-between;
    bottom: 0;
    padding: 0 8px;
}

.card-type {
    font-family: 'Roboto', Arial, sans-serif;
    font-weight: 900;
    font-size: 1rem;
    color: #fff;
    letter-spacing: 0.01em;
    line-height: 1.12;
}

.card-price {
    font-family: 'Roboto', Arial, sans-serif;
    font-weight: 200;
    font-size: 1rem;
    color: #dfe6f0;
    letter-spacing: 0.01em;
    line-height: 1.12;
    margin-left: 14px;
}
</style>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center" style="min-height: 92vh;">
            <div class="shop-intro-wrapper">
                <div class="shop-header-big">
                    Welcome <span class="shop-username"><?=htmlspecialchars($_SESSION['firstname'] ?? "User")?></span>,
                    to the shop.
                </div>
                <div class="shop-header-desc">
                    This is the shop, where you can buy any artefacts that the scientists have deemed as not required for scientific research. Add to your basket to buy a piece of explosive nature! 
                    All these artefacts are real, get one while stock lasts!
                </div>
                <hr class="shop-header-sep">
            </div>
            <div class="glass-grid">
                <?php
include '../connection.php';
$sql = "SELECT s.id, a.type, s.price
        FROM stock_list s
        JOIN artefacts a ON s.artifact_id = a.id
        WHERE s.availability = 'Yes'
        ORDER BY s.id ASC
        OFFSET 0 ROWS FETCH NEXT 8 ROWS ONLY";
$result = sqlsrv_query($conn, $sql);
if ($result === false) {
    echo '<div style="color:#f66;font-size:1.3em;">' . print_r(sqlsrv_errors(), true) . '</div>';
} elseif (sqlsrv_has_rows($result)) {
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $id = htmlspecialchars($row['id'] ?? 'N/A');
        $type = htmlspecialchars($row['type'] ?? 'Unknown Type');
switch (strtolower($type)) {
    case 'solidified lava':
        $img = "../assets/images/Rock1.png";
        break;
    case 'foreign debris':
        $img = "../assets/images/Rock2.png";
        break;
    case 'ash sample':
        $img = "../assets/images/Ash.png";
        break;
    case 'ground soil':
        $img = "../assets/images/Soil.png";
        break;
    default:
        $img = "../assets/images/default.png"; // fallback if desired, or leave blank
}
        $price = isset($row['price']) ? "£" . number_format($row['price'], 2) : "£??";
        ?>
                <div class="glass-card">
                    <img src="<?= $img ?>" alt="<?= $type ?>"
                        style="height:150px;width:auto;margin-bottom:5vh;margin-top:7vh;display:block;object-fit:contain;filter:drop-shadow(0 5px 24px #2229);">
                    <div class="card-row">
                        <span class="card-type"><?= $type ?></span>
                        <span class="card-price"><?= $price ?></span>
                    </div>
                    <div class="shop-card-actions" style="margin-top: auto;">
                        <a href="../Basket/add_to_basket.php?id=<?= $id ?>" class="shop-buy-btn"
                            style="display:flex;align-items:center;justify-content:center;">
                            <img src="../assets/icons/basket.svg" alt="Add to basket" class="cart-plus-icon">
                        </a>
                    </div>
                </div>
                <?php
    }
} else {
    for ($i=1; $i<=8; $i++) {
       echo '<div class="glass-card"><div class="glass-card-content" style="font-size:1.11em;">(Empty)</div></div>';
    }
}
sqlsrv_close($conn);
?>
            </div>
        </div>
    </div>

</body>

</html>