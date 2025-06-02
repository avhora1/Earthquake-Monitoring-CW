<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/sidebar.php';
include '../headerNew.php';
include '../queryLibrary.php';
$shelf_caps = [];
for ($i = 0; $i < 12; $i++) {
    $letter = chr(65 + $i); // A-L
    $shelf_caps[$letter] = get_shelf_capacity($conn, $letter);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Warehouse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/quake.css">
    <style>
    /* [Unchanged styles] */
    body {
        background: #191a2a;
        margin: 0;
    }

    .carousel-outer {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        min-height: 100vh;
        width: calc(100% - 285px);
        margin-left: 285px;
    }

    .carousel-header {
        width: 900px;
        max-width: 78vw;
        margin: 85px 0 36px 0;
        display: flex;
        justify-content: center;
        z-index: 2;
    }

    .carousel-input {
        width: 100%;
        max-width: 380px;
        padding: 14px 20px;
        font-size: 1.15rem;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 18px 0 #15153233;
        outline: none;
        color: #222336;
        background: #fff9f6;
        font-family: inherit;
        transition: box-shadow 0.2s, border 0.2s, background 0.2s;
    }

    .carousel-input:focus {
        box-shadow: 0 2px 22px 0 #d3b794;
    }

    .carousel-input.invalid {
        background: #fff6f9;
        border: 1px solid #b90030;
    }

    .carousel-wrap {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        min-height: 480px;
        width: 100vw;
        position: relative;
        gap: 14px;
        /* Space between arrow and shelves container */
    }

    .carousel-arrow {
        background: #292c45;
        color: #fff;
        font-size: 2.2rem;
        border: none;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        cursor: pointer;
        margin: 0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.16);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.17s;
    }

    .carousel-arrow:disabled {
        opacity: 0.45;
        cursor: default;
        background: #18192b;
    }

    .shelves-viewport {
        background: linear-gradient(141deg, rgba(255, 255, 255, 0.13) 0%, rgba(26, 26, 44, 0.18) 100%);
        border-radius: 36px;
        width: 860px;
        max-width: 76vw;
        height: 410px;
        min-height: 300px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 0 30px 0 #13143066;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-bottom: 56px;
        margin: 0;
    }

    .shelves {
        display: flex;
        transition: transform 0.66s cubic-bezier(.66, -0.01, .2, 1.14);
        will-change: transform;
        height: 275px;
        align-items: flex-start;
    }

    .shelf {
        min-width: 220px;
        max-width: 220px;
        margin: 0 16px 0 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-shrink: 0;
    }

    .shelf h2 {
        color: #fff;
        font-size: 1.44rem;
        font-weight: 700;
        margin: 20px 0 18px 0;
        text-align: center;
        text-shadow: 0 2px 8px rgba(30, 20, 0, 0.09);
        font-family: inherit;
    }

    .shelf-grid {
        display: grid;
        grid-template-columns: repeat(3, 54px);
        grid-template-rows: repeat(3, 54px);
        gap: 17px;
    }

    .slot {
        border-radius: 12px;
        background: linear-gradient(to bottom, #ffe9c0 45%, #c98231 95%);
        width: 54px;
        height: 54px;
        border: none;
        box-shadow: 0 0 18px #ffe7b399;
        transition: background 0.23s, box-shadow 0.23s;
    }

    .slot.full {
        background: linear-gradient(to bottom, #ff6262 40%, #c32c2c 100%);
        box-shadow: 0 2px 14px 0 #e0313137;
        border: 1.5px solid #c32c2c;
    }

    .shelf-slider {
        position: absolute;
        left: 50%;
        bottom: 23px;
        transform: translateX(-50%);
        width: 285px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 35px;
        pointer-events: auto;
        user-select: none;
        z-index: 5;
    }

    .slider-bar {
        flex: 1;
        height: 12px;
        background: #fff;
        border-radius: 7px;
        margin: 0 8px;
        position: relative;
        box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.07);
        cursor: pointer;
        transition: background 0.2s;
    }

    .slider-bar.active {
        background: #ffe6cc;
    }

    .dot {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #111;
        margin-right: 8px;
        display: inline-block;
        position: absolute;
        left: 0;
        top: 50%;
        z-index: 2;
        transition: left 0.23s linear;
        cursor: grab;
        box-shadow: 0 2px 8px #0002;
        pointer-events: auto;
    }

    .shelf-slider .dot:active {
        cursor: grabbing;
    }

    .label-a,
    .label-z {
        color: #fff;
        font-weight: 700;
        font-family: 'Montserrat', 'Arial', sans-serif;
        font-size: 1.03rem;
    }

    .label-a {
        margin-left: 3px;
    }

    .label-z {
        margin-right: 3px;
    }

    @media (max-width:1100px) {

        .carousel-header,
        .shelves-viewport {
            width: 99vw;
            min-width: 0;
            max-width: 100vw;
        }
    }

    @media (max-width:900px) {
        .shelves-viewport {
            width: 97vw;
            max-width: 97vw;
            padding-bottom: 40px;
        }

        .shelf {
            min-width: 118px;
            max-width: 118px;
        }

        .shelf-grid {
            grid-template-columns: repeat(3, 32px);
            grid-template-rows: repeat(3, 32px);
            gap: 8px;
        }

        .slot {
            width: 32px;
            height: 32px;
        }

        .carousel-arrow {
            width: 28px;
            height: 28px;
        }
    }

    @media (max-width:700px) {
        .carousel-header {
            width: 97vw;
            max-width: 97vw;
            margin: 50px 0 14px 0;
        }

        .shelves-viewport {
            width: 99vw;
            min-width: 0;
            height: 220px;
            padding-bottom: 22px;
        }

        .shelf {
            min-width: 80px;
            max-width: 80px;
        }

        .shelf-grid {
            grid-template-columns: repeat(3, 20px);
            grid-template-rows: repeat(3, 20px);
            gap: 4px;
        }

        .slot {
            width: 20px;
            height: 20px;
        }

        .carousel-arrow {
            width: 18px;
            height: 18px;
            font-size: 1.2rem;
        }

        .shelf-slider {
            width: 65px;
            height: 10px;
            bottom: 3px;
        }

        .label-a,
        .label-z {
            font-size: 0.9rem;
        }

        .dot {
            width: 7px;
            height: 7px;
        }
    }
    </style>
    <script>
    const shelfCaps = <?php echo json_encode($shelf_caps); ?>;
    </script>
</head>

<body>
    <div class="carousel-outer">
        <div class="carousel-header">
            <input type="text" class="carousel-input" id="shelf-jump-input" maxlength="1"
                placeholder="Type a letter (A-L)..." />
        </div>
        <div class="carousel-wrap">
            <button class="carousel-arrow left" aria-label="Previous shelf">&#8592;</button>
            <div class="shelves-viewport">
                <div class="shelves"></div>
                <div class="shelf-slider">
                    <span class="label-a">A</span>
                    <div class="slider-bar"></div>
                    <span class="dot"></span>
                    <span class="label-z">L</span>
                </div>
            </div>
            <button class="carousel-arrow right" aria-label="Next shelf">&#8594;</button>
        </div>
    </div>
    <script>
    // ===== Carousel JS =====
    const shelvesContainer = document.querySelector('.shelves');
    const leftBtn = document.querySelector('.carousel-arrow.left');
    const rightBtn = document.querySelector('.carousel-arrow.right');
    const TOTAL_SHELVES = 12;
    const MAX_CAPACITY = 9;

    // Moved these from global scope to DOMContentLoaded
    // let shelfElems = [];

    function renderShelfGrid(letter) {
        const cap = shelfCaps[letter] === null ? 0 : shelfCaps[letter];
        let slotsHtml = '';
        for (let i = 0; i < MAX_CAPACITY; i++) {
            slotsHtml += `<div class="slot${i >= cap ? ' full' : ''}"></div>`;
        }
        return `<div class="shelf-grid">${slotsHtml}</div>`;
    }

    // Carousel state moved to closure scope
    let shelfElems = [];
    let currentShelf = 0; // always starts at A

    // Used everywhere, needs visibility:
    function getShelfCardWidth() {
        const shelf = shelfElems[0];
        const style = getComputedStyle(shelf);
        const marginLeft = parseFloat(style.marginLeft) || 0;
        const marginRight = parseFloat(style.marginRight) || 0;
        return shelf.offsetWidth + marginLeft + marginRight;
    }

    function centerShelf(idx, transition = 'bounce') {
        // WRAP LOGIC: Going before 0 (A) wraps to L, after 11 (L) wraps to A
        if (idx < 0) idx = TOTAL_SHELVES - 1;
        if (idx >= TOTAL_SHELVES) idx = 0;
        currentShelf = idx;
        shelvesContainer.style.transition = (transition === 'none') ?
            'none' :
            'transform ' + (transition === 'bounce' ? '0.6s cubic-bezier(.66,-0.01,.2,1.14)' : '0.23s linear');
        const viewportWidth = document.querySelector('.shelves-viewport').offsetWidth;
        const shelfWidth = getShelfCardWidth();
        const totalWidth = shelfElems.length * shelfWidth;
        let offset = idx * shelfWidth - (viewportWidth + shelfWidth);
        // ------------- FIX: remove clamping so A and L can be centered at endpoints
        // offset = Math.max(0, Math.min(offset, totalWidth - viewportWidth));
        shelvesContainer.style.transform = `translateX(${-offset}px)`;
        afterShelfChange();
    }

    // Event handlers and logic below do not change
    leftBtn.onclick = () => centerShelf(currentShelf - 1, 'bounce');
    rightBtn.onclick = () => centerShelf(currentShelf + 1, 'bounce');
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') leftBtn.click();
        else if (e.key === 'ArrowRight') rightBtn.click();
    });
    window.addEventListener('resize', () => centerShelf(currentShelf, 'none'));

    // Input
    const input = document.getElementById('shelf-jump-input');
    input.addEventListener('input', function() {
        let val = this.value.toUpperCase();
        if (!/^[A-L]$/.test(val)) {
            this.classList.add('invalid');
            this.value = "";
            return;
        }
        this.classList.remove('invalid');
        this.value = val;
        let idx = val.charCodeAt(0) - 65;
        // WRAP not necessary, but clamp anyway
        if (idx < 0) idx = 0;
        if (idx > TOTAL_SHELVES - 1) idx = TOTAL_SHELVES - 1;
        centerShelf(idx, 'bounce');
    });

    // Slider bar
    const sliderBar = document.querySelector('.slider-bar');
    const sliderDot = document.querySelector('.dot');

    function updateSliderDot() {
        const sliderMax = sliderBar.offsetWidth;
        let pos = sliderMax * (currentShelf / (TOTAL_SHELVES - 1));
        // Clamp so dot fully inside
        pos = Math.max(sliderDot.offsetWidth / 2, Math.min(sliderMax - sliderDot.offsetWidth / 2, pos));
        sliderDot.style.left = `${pos + sliderDot.offsetWidth}px`;
        sliderDot.style.top = `${sliderBar.offsetTop + sliderBar.offsetHeight / 2 - sliderDot.offsetHeight / 2}px`;
    }

    function jumpToSliderPosition(pageX, smooth = true) {
        let rect = sliderBar.getBoundingClientRect();
        let x = pageX - rect.left;
        x = Math.max(0, Math.min(rect.width, x));
        let percent = x / rect.width;
        let shelfIdx = Math.round(percent * (TOTAL_SHELVES - 1));
        centerShelf(shelfIdx, smooth ? 'linear' : 'none');
    }

    function afterShelfChange() {
        updateSliderDot();
    }

    // Always create shelves and start at A, after layout
    document.addEventListener('DOMContentLoaded', function() {
        shelfElems = [];
        for (let i = 0; i < TOTAL_SHELVES; i++) {
            const letter = String.fromCharCode(65 + i);
            const shelf = document.createElement('div');
            shelf.className = 'shelf';
            shelf.innerHTML = `<h2>Shelf ${letter}</h2>${renderShelfGrid(letter)}`;
            shelfElems.push(shelf);
        }
        shelvesContainer.innerHTML = '';
        shelfElems.forEach(s => shelvesContainer.appendChild(s));
        setTimeout(() => {
            centerShelf(0, 'none');
            afterShelfChange();
            setTimeout(updateSliderDot, 10);
        }, 5);
    });

    // Slider dragging
    let dragging = false;

    function startScrub(e) {
        dragging = true;
        sliderBar.classList.add('active');
        sliderDot.classList.add('active');
        let pageX = e.type.startsWith('touch') ? e.touches[0].pageX : e.pageX;
        jumpToSliderPosition(pageX, true);
        e.preventDefault();
    }

    function scrubMove(e) {
        if (!dragging) return;
        let pageX = e.type.startsWith('touch') ? (e.touches[0] ? e.touches[0].pageX : null) : e.pageX;
        if (pageX == null) return;
        jumpToSliderPosition(pageX, true);
    }

    function stopScrub() {
        dragging = false;
        sliderBar.classList.remove('active');
        sliderDot.classList.remove('active');
    }
    sliderBar.addEventListener('mousedown', startScrub);
    sliderDot.addEventListener('mousedown', startScrub);
    document.addEventListener('mousemove', scrubMove);
    document.addEventListener('mouseup', stopScrub);
    sliderBar.addEventListener('touchstart', startScrub, {
        passive: false
    });
    sliderDot.addEventListener('touchstart', startScrub, {
        passive: false
    });
    document.addEventListener('touchmove', scrubMove, {
        passive: false
    });
    document.addEventListener('touchend', stopScrub);
    sliderBar.addEventListener('click', function(e) {
        if (dragging) return;
        jumpToSliderPosition(e.pageX, true);
    });
    </script>
</body>

</html>