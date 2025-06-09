<?php include $_SERVER['DOCUMENT_ROOT'].'/session.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quake</title>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include '../headerNew.php'; ?>
    <style>
    body {
        background: radial-gradient(#000540 0%, #000 100%);
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
        grid-template-rows: repeat(3, 35vh);
        gap: 3vw 2vw;
        justify-content: start;
        width: 65vw;
        padding-bottom: 5vh;
    }

    .glass-card {
        perspective: 1000px;
        width: 13vw;
        min-width: 140px;
        height: 35vh;
        min-height: 150px;
    }

    .glass-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.65s cubic-bezier(.23, .91, .36, 1.21);
        transform-style: preserve-3d;
    }

    .glass-card.flip-added .glass-card-inner {
        transform: rotateY(180deg);
    }

    .glass-card-front,
    .glass-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 2rem;
        box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
        border: 2px solid rgba(255, 255, 255, 0.09);
        backface-visibility: hidden;
    }

    /* Front = looks like old glass-card */
    .glass-card-front {
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
        backdrop-filter: blur(1vh);
        color: #fff;
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

    .glass-card-back {
        background: #fff;
        color: #111;
        box-shadow: 0 8px 36px #0004;
        transform: rotateY(180deg);
    }

    .glass-card-back .card-type,
    .glass-card-back .card-price,
    .glass-card-back .glass-card-content {
        color: #222 !important;
        text-shadow: none !important;
    }

    .glass-card-back .shop-buy-btn {
        background: #06dc55;
        pointer-events: none;
        font-size: 1.18em;
        font-weight: 700;
        color: #fff !important;
        cursor: not-allowed;
        box-shadow: 0 0 25px #fa8c1645;
        border-radius: 1rem;
        border: none;
    }

    .glass-card-back .cart-plus-icon {
        display: none;
    }

    .glass-card-back .remove-from-basket-btn {
        width: 100%;
        min-width: 10vh;
        min-height: 48px;
        border-radius: 1rem;
        background: #e03744;
        color: #fff;
        box-shadow: 0 0 18px #df243050;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        outline: none;
        border: none;
        transition: background 0.18s, box-shadow 0.18s, opacity 0.16s;
        padding: 0;
        margin: 0 auto 10px auto;
        font-size: 1em;
        font-weight: 700;
        gap: 0;
    }

    .glass-card-back .remove-from-basket-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .glass-card-back .remove-from-basket-btn:hover,
    .glass-card-back .remove-from-basket-btn:focus {
        background: #b70317;
        box-shadow: 0 0 26px #f5424280;
    }

    .remove-icon {
        height: 1.7rem;
        width: auto;
        display: block;
        pointer-events: none;
        user-select: none;
        margin: 0;
        filter: drop-shadow(0 2px 6px #a82e3480);
    }

    .glass-card-front .shop-buy-btn {
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
        font-size: 1em;
        border: none;
        cursor: pointer;
        box-shadow: 0 0 25px #fa8c1645;
        outline: none;
        transition: background 0.16s, box-shadow 0.20s, transform 0.12s;
        padding: 0;
        margin: 0 auto 10px auto;
        bottom: 0;
        gap: 0;
    }

    .glass-card-front .shop-buy-btn:hover,
    .glass-card-front .shop-buy-btn:focus {
        background: linear-gradient(90deg, #ffc63f, #ff9100 95%);
        box-shadow: 0 0 38px #ff910088;
        color: #fff;
        transform: scale(1.045);
    }

    .shop-card-actions {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .shop-tooltip {
        position: absolute;
        top: 28px;
        right: 110%;
        min-width: 160px;
        max-width: 250px;
        background: #fff;
        color: #222;
        border-radius: 1em;
        box-shadow: 0 4px 18px #0002;
        font-size: 1rem;
        opacity: 0;
        pointer-events: none;
        padding: 18px 22px;
        transform: translateY(-8px) scale(0.98);
        transition: opacity 0.18s, transform 0.16s;
        border: 1.5px solid #ececec;
        display: block;
        font-family: 'Roboto', Arial, sans-serif;
        line-height: 1.45em;
    }

    .glass-card-front:hover .shop-tooltip,
    .glass-card-back:hover .shop-tooltip {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0) scale(1.02);
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center" style="min-height: 92vh;">
            <div class="shop-intro-wrapper">
                <div class="shop-header-big">
                    Welcome <span class="shop-username"><?=htmlspecialchars($_SESSION['firstname'] ?? "User")?></span>,
                    to the shop.
                </div>
                <div class="shop-header-desc">
                    This is the shop, where you can buy any artefacts that the scientists have deemed as not required
                    for scientific research. Add to your basket to buy a piece of explosive nature!
                    All these artefacts are real, get one while stock lasts!
                </div>
                <hr class="shop-header-sep">
            </div>
            <div class="glass-grid">
                <?php
include '../connection.php';

function sentenceCase($string) {
    // Normalize spacing between sentences.
    $string = preg_replace('/\s+([.?!])/', '$1', $string);
    // Ensure a single space after punctuation.
    $string = preg_replace('/([.?!])(\s*)/', '$1 ', $string);

    // Capitalize the first letter of every sentence.
    return preg_replace_callback('/([.?!]\s*|^)([a-z])/', function ($matches) {
        return $matches[1] . strtoupper($matches[2]);
    }, strtolower($string));
}

$sql = "SELECT s.id, a.type, s.price, a.description, a.earthquake_id
        FROM stock_list s
        JOIN artefacts a ON s.artifact_id = a.id
        WHERE s.availability = 'Yes'
        ORDER BY s.id ASC
        OFFSET 0 ROWS FETCH NEXT 16 ROWS ONLY";
$result = sqlsrv_query($conn, $sql);
if ($result === false) {
    echo '<div style="color:#f66;font-size:1.3em;">' . print_r(sqlsrv_errors(), true) . '</div>';
} elseif (sqlsrv_has_rows($result)) {
    // get basket items already added (for reload/persistent button state)
    $basket = $_SESSION['basket'] ?? [];
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $id = htmlspecialchars($row['id'] ?? 'N/A');
        $type = htmlspecialchars($row['type'] ?? 'Unknown Type');
        switch (strtolower($type)) {
            case 'solidified lava': $img = "../assets/images/Rock1.png"; break;
            case 'foreign debris':  $img = "../assets/images/Rock2.png"; break;
            case 'ash sample':      $img = "../assets/images/Ash.png";   break;
            case 'ground soil':     $img = "../assets/images/Soil.png";  break;
            default:                $img = "../assets/images/default.png";
        }
        $type = sentenceCase($type); // Capitalize every sentence.
        $price = isset($row['price']) ? "£" . number_format($row['price'], 2) : "£??";
        $isAdded = isset($basket[$id]);
        $description = htmlspecialchars($row['description'] ?? '');
        $description = sentenceCase($description); // Capitalize every sentence.
        $eqid = htmlspecialchars($row['earthquake_id'] ?? '');
        ?>
                <div class="glass-card<?= $isAdded ? ' flip-added' : '' ?>" id="shop-card-<?= $id ?>">
                    <div class="glass-card-inner">
                        <!-- Front Face -->
                        <div class="glass-card-front">
                            <img src="<?= $img ?>" alt="<?= $type ?>"
                                style="height:150px;width:auto;margin-bottom:5vh;margin-top:7vh;display:block;object-fit:contain;filter:drop-shadow(0 5px 24px #2229);">
                            <div class="card-row">
                                <span class="card-type"><?= $type ?></span>
                                <span class="card-price"><?= $price ?></span>
                            </div>
                            <div class="shop-card-actions" style="margin-top: auto;">
                                <div class="shop-tooltip">
                                    <?php if ($description): ?>
                                    <span
                                        style="font-size:1.08em; font-weight: 400; color: #2b2c2e;"><?= $description ?></span><br>
                                    <?php endif; ?>
                                    <?php if ($eqid): ?>
                                    <span style="font-size:0.98em; color:#777; font-style:italic;">Earthquake ID:
                                        <?= $eqid ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="shop-buy-btn<?= $isAdded ? ' added-to-basket' : '' ?>"
                                    data-id="<?= $id ?>" <?= $isAdded ? 'disabled' : '' ?> type="button"
                                    style="display:flex;align-items:center;justify-content:center;">
                                    <img src="../assets/icons/basket.svg" alt="Add to basket" class="cart-plus-icon">
                                </button>
                            </div>
                        </div>
                        <!-- Back Face -->
                        <div class="glass-card-back">
                            <img src="<?= $img ?>" alt="<?= $type ?>"
                                style="height:150px;width:auto;margin-bottom:5vh;margin-top:7vh;display:block;object-fit:contain;filter:drop-shadow(0 5px 24px #2229);">
                            <div class="card-row">
                                <span class="card-type"><?= $type ?></span>
                                <span class="card-price"><?= $price ?></span>
                            </div>
                            <div class="shop-card-actions" style="margin-top: auto; gap: 7px;">
                                <div class="shop-tooltip">
                                    <?php if ($description): ?>
                                    <span
                                        style="font-size:1.08em; font-weight: 400; color: #2b2c2e;"><?= $description ?></span><br>
                                    <?php endif; ?>
                                    <?php if ($eqid): ?>
                                    <span style="font-size:0.98em; color:#777; font-style:italic;">Earthquake ID:
                                        <?= $eqid ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="remove-from-basket-btn" type="button" data-id="<?= $id ?>"
                                    aria-label="Remove from basket">
                                    <img src="../assets/icons/rubbish.svg" alt="Remove" class="remove-icon">
                                </button>
                            </div>
                        </div>
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
    <!-- AJAX for Add to Basket -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.shop-buy-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                const button = this;
                const artefactId = button.dataset.id;
                if (button.disabled || button.classList.contains('added-to-basket')) return;
                button.disabled = true;
                button.style.opacity = 0.7;
                fetch(`../Basket/add_to_basket.php?id=${artefactId}`, {
                        method: 'GET',
                        credentials: 'same-origin'
                    })
                    .then(resp => resp.text())
                    .then(text => {
                        if (text.trim() === "OK") {
                            button.classList.add('added-to-basket');
                            // Flip the card!
                            const card = button.closest('.glass-card');
                            if (card) card.classList.add('flip-added');
                            // --- Update basket badge dynamically ---
                            fetch('/basket/basket_count.php')
                                .then(res => res.json())
                                .then(data => {
                                    let badge = document.getElementById(
                                        'basket-badge-count');
                                    if (badge) {
                                        badge.textContent = data.count;
                                        badge.style.display = (parseInt(data.count) >
                                            0) ? '' : 'none';
                                    }
                                });
                        } else {
                            throw new Error(text);
                        }
                    })
                    .catch(err => {
                        button.disabled = false;
                        button.style.opacity = 1;
                        alert('Could not add to basket. Please try again.');
                    });
            });
        });

        // Remove from basket functionality
        document.querySelectorAll('.remove-from-basket-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                const button = this;
                const id = button.dataset.id;
                button.disabled = true;
                fetch(`../Basket/remove_from_basket.php?id=${id}`, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(resp => resp.text())
                    .then(text => {
                        if (text.trim() === "OK") {
                            // Visually flip back
                            const card = button.closest('.glass-card');
                            if (card) card.classList.remove('flip-added');

                            // Update basket badge
                            fetch('/basket/basket_count.php')
                                .then(res => res.json())
                                .then(data => {
                                    let badge = document.getElementById(
                                        'basket-badge-count');
                                    if (badge) {
                                        badge.textContent = data.count;
                                        badge.style.display = (parseInt(data.count) >
                                            0) ? '' : 'none';
                                    }
                                });

                            setTimeout(() => {
                                if (card) {
                                    const addBtn = card.querySelector(
                                        '.glass-card-front .shop-buy-btn');
                                    if (addBtn) {
                                        addBtn.innerHTML =
                                            '<img src="../assets/icons/basket.svg" alt="Add to basket" class="cart-plus-icon">';
                                        addBtn.classList.remove('added-to-basket');
                                        addBtn.disabled = false;
                                        addBtn.style.opacity = 1;
                                    }
                                    const removeBtn = card.querySelector(
                                        '.glass-card-back .remove-from-basket-btn'
                                    );
                                    if (removeBtn) {
                                        // Only reset disabled, DO NOT change innerHTML/textContent!
                                        removeBtn.disabled = false;
                                        // (No need to reset text)
                                    }
                                }
                            }, 350);
                        } else {
                            button.disabled = false;
                            alert('Could not remove from basket.');
                        }
                    })
                    .catch(() => {
                        button.disabled = false;
                        alert('Could not remove from basket.');
                    });
            });
        });
    });
    </script>
</body>

</html>