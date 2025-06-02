<?php include $_SERVER['DOCUMENT_ROOT'].'/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earthquakes</title>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: radial-gradient(78.82% 50% at 50% 50%, #000525 0%, #000 100%);
    }

    .glass-box {
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
        backdrop-filter: blur(1vh);
        border-radius: 2rem;
        border: 2px solid rgba(255, 255, 255, 0.09);
        box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
        min-height: 80vh;
        transform: translateY(5vh);
        padding: 2vh;
        overflow: hidden;
    }

    .filters-title {
        color: #fff;
        font-family: 'Roboto', Arial, sans-serif;
        font-weight: 700;
        font-size: 2rem;
        text-align: center;

    }

    .dual-slider {
        width: 100%;
        margin-top: 7px;
        margin-bottom: 10px;
    }

    .slider-track {
        position: relative;
        width: 100%;
        height: 32px;
    }

    .slider {
        position: absolute;
        left: 0;
        right: 0;
        top: 7px;
        width: 100%;
        pointer-events: none;
        appearance: none;
        -webkit-appearance: none;
        background: none;
        z-index: 3;
    }

    .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        background: #18f545;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        box-shadow: 0 2.5px 13px #0a0a0a60;
        border: 2.5px solid #fff;
        cursor: pointer;
        pointer-events: all;
        transition: background .19s;
        z-index: 10;
        position: relative;
    }

    .slider::-moz-range-thumb,
    .slider::-ms-thumb {
        background: #18f545;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        border: 2.5px solid #fff;
        cursor: pointer;
        pointer-events: all;
        transition: background .19s;
        z-index: 10;
        position: relative;
    }

    .slider:focus {
        outline: none;
    }

    .slider::-webkit-slider-runnable-track {
        height: 5px;
        background: transparent;
        border-radius: 1.4vw;
    }

    .slider::-moz-range-track {
        height: 5px;
        background: transparent;
    }

    .slider::-ms-fill-lower,
    .slider::-ms-fill-upper {
        background: transparent;
    }

    .slider-range {
        position: absolute;
        left: 0;
        top: 21px;
        height: 5px;
        background: linear-gradient(90deg, #09ff00, #00b144);
        z-index: 2;
        border-radius: 3px;
        pointer-events: none;
    }

    .slider-label-row {
        font-size: 1.5rem;
        color: #fff;
        margin-top: 8px;
        display: flex;
        justify-content: space-between;
        font-family: 'Roboto', Arial, sans-serif;
        font-weight: 300;
        letter-spacing: 0.02em;
    }

    .slider-track::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 21px;
        height: 5px;
        background: #fff;
        border-radius: 1.4vw;
        z-index: 1;
        pointer-events: none;
    }

    .filter-label {
        font-size: 1.5rem;
        color: #fff;
        font-family: 'Roboto', Arial, sans-serif;
        font-weight: 200;
    }

    .switch-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        color: #fff;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 38px;
        height: 20px;
        margin-left: 8px;
        margin-right: 4px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, #ff9100, #ffbe3d 71%);
        border-radius: 20px;
        transition: background .23s;
    }

    .switch input:not(:checked)+.slider {
        background: #222;
    }

    .switch .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background: #fff;
        border-radius: 50%;
        transition: transform .23s cubic-bezier(.8, -0.1, .8, 1.2), background .13s;
    }

    .switch input:checked+.slider:before {
        transform: translateX(18px);
        background: #fff;
    }

    .switch input:not(:checked)+.slider:before {
        transform: translateX(0);
    }

    .obs-dropdown-container {
        position: relative;
        margin-top: 18px;
        font-family: 'Roboto', Arial, sans-serif;
    }

    .obs-dropdown-selected {
        background: #fff;
        border-radius: 12px;
        border: 2px solid #e4e6f1;
        box-shadow: 0 3px 16px #0001;
        padding: 14px 20px 14px 20px;
        font-size: 1.35rem;
        color: #21222a;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: border .16s;
    }

    .obs-dropdown-selected:focus,
    .obs-dropdown-selected.active {
        border: 2.5px solid #ff9100;
    }

    .obs-dropdown-arrow {
        font-size: 1.52rem;
        color: #222;
        transition: transform .18s;
    }

    .obs-dropdown-selected.active .obs-dropdown-arrow {
        transform: rotate(180deg);
    }

    .obs-dropdown-list {
        background: #fff;
        position: absolute;
        left: 0;
        right: 0;
        top: 110%;
        border-radius: 12px;
        box-shadow: 0 6px 32px #0003;
        border: 2px solid #e4e6f1;
        padding: 13px 14px 14px 14px;
        max-height: 235px;
        overflow-y: auto;
        z-index: 99999;
        display: none;
    }

    .obs-dropdown-list.active {
        display: block;
    }

    .obs-search {
        width: 97%;
        font-size: 1.12em;
        padding: 8px 10px;
        border-radius: 7px;
        border: 1.5px solid #d6d1f9;
        margin-bottom: 11px;
        outline: none;
    }

    .obs-options {
        max-height: 180px;
        overflow-y: auto;
    }

    .obs-option-row {
        display: flex;
        align-items: center;
        font-size: 1.12em;
        min-height: 32px;
        margin-bottom: 1px;
        padding: 5px 2px 4px 0;
    }

    .obs-option-checkbox {
        appearance: none;
        border-radius: 5px;
        margin-right: 11px;
        width: 17px;
        height: 17px;
        border: 2.2px solid #ccc;
        outline: none;
        background: #f7f7fc;
        transition: border .18s;
        cursor: pointer;
    }

    .obs-option-checkbox:checked {
        border-color: #ff9100;
        background: linear-gradient(90deg, #ff9100 0%, #ffbe3d 100%);
    }

    .obs-option-label {
        user-select: none;
        color: #232441;
    }

    .obs-dropdown-container {
        margin-bottom: 8px;
    }


    #globe-canvas-container {
        width: 100%;
        height: 75vh;
        min-height: 440px;
        max-height: 86vh;
        position: relative;
        z-index: 2;
    }

    #globe-canvas-container canvas {
        display: block;
        border-radius: 2.5vw;
        margin: 0 auto;
        background: none;
        transform: translateY(5vh);
    }

    #quake-hover-card {
        position: absolute;
        min-width: 250px;
        background: #fff;
        border-radius: 10px;

        z-index: 9999;
        left: 0;
        top: 0;
        display: none;
        pointer-events: none;
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
        backdrop-filter: blur(1vh);
        border-radius: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.09);
        box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
        color: #fff;
        padding-left: 10px;
        padding-right: 10px;
        transition:
            opacity 0.2s cubic-bezier(.6, -0.01, .6, 1.05),
            transform 0.35s cubic-bezier(.41, 1.43, .57, 1.11);
    }

    #quake-hover-card.active {
        display: block;
    }

    #quake-hover-card .quake-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    #quake-location {
        font-family: 'Roboto', Arial, sans-serif;
        font-size: 2.2rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        line-height: 1.09;
        white-space: nowrap;
        flex: 1 1 auto;
    }

    #quake-mag {
        font-family: 'Roboto', Arial, sans-serif;
        font-size: 2.05rem;
        font-weight: 200;
        margin-left: 100px;
        flex: 0 0 auto;
    }

    #quake-desc-date {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        width: 100%;
        margin-top: 10px;
    }

    #quake-desc {
        font-size: 1.45rem;
        font-weight: 300;
        letter-spacing: 0.01em;
        color: #e5eafd;
        line-height: 1.13;
        flex: 1 1 auto;
        /* allow multi-line wrap */
        word-break: break-word;
        margin-right: 12px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #quake-date {
        color: #f5f7fd;
        font-size: 1.58rem;
        font-weight: 200;
        line-height: 1.08;
        white-space: nowrap;
        flex: 0 0 auto;
        margin-left: 22px;
    }
    </style>
    <script type="importmap">
        {
            "imports": {
                "three": "https://cdn.jsdelivr.net/npm/three@v0.177.0/build/three.module.js",
                "three/addons/": "https://cdn.jsdelivr.net/npm/three@v0.177.0/examples/jsm/"
            }
        }
    </script>
</head>
<?php include '../headerNew.php'; ?>
<body>
<div class="container-fluid">
<div class="row justify-content-start align-items-center" style="min-height: 92vh;">
    <div class="col-1"></div>
    <div class="col-3">
        <div class="glass-box">
            <div class="filters-title">Filters</div>
            <!-- Year Slider -->
            <div class="filter-group">
                <label class="filter-label" for="slider-year-min">Year:</label>
                <div class="dual-slider">
                    <div class="slider-track">
                        <div class="slider-range" id="year-slider-range"></div>
                        <input id="slider-year-min" type="range" min="1950" max="2025" value="1950" class="slider" />
                        <input id="slider-year-max" type="range" min="1950" max="2025" value="2025" class="slider" />
                    </div>
                    <div class="slider-label-row">
                        <span id="year-min-label">1950</span>
                        <span id="year-max-label" style="float:right">2025</span>
                    </div>
                </div>
            </div>
            <!-- Magnitude Slider -->
            <div class="filter-group">
                <label class="filter-label" for="slider-mag-min">Magnitude:</label>
                <div class="dual-slider">
                    <div class="slider-track">
                        <div class="slider-range" id="mag-slider-range"></div>
                        <input id="slider-mag-min" type="range" min="0" max="9" step="0.1" value="0" class="slider" />
                        <input id="slider-mag-max" type="range" min="0" max="9" step="0.1" value="9" class="slider" />
                    </div>
                    <div class="slider-label-row">
                        <span id="mag-min-label">0</span>
                        <span id="mag-max-label" style="float:right">9</span>
                    </div>
                </div>
            </div>
            <div class="filter-group">
                <label class="filter-label" style="margin-bottom:12px;">Types:</label>
                <div class="switch-row"><span style="margin-left: 1vh;">Tectonic</span>
                    <label class="switch">
                        <input type="checkbox" class="eq-type" value="tectonic" checked><span class="slider"></span>
                    </label>
                </div>
                <div class="switch-row"><span style="margin-left: 1vh;">Volcanic</span>
                    <label class="switch">
                        <input type="checkbox" class="eq-type" value="volcanic" checked><span class="slider"></span>
                    </label>
                </div>
                <div class="switch-row"><span style="margin-left: 1vh;">Collapse</span>
                    <label class="switch">
                        <input type="checkbox" class="eq-type" value="collapse" checked><span class="slider"></span>
                    </label>
                </div>
                <div class="switch-row"><span style="margin-left: 1vh;">Explosion</span>
                    <label class="switch">
                        <input type="checkbox" class="eq-type" value="explosion" checked><span class="slider"></span>
                    </label>
                </div>
            </div>
            <div class="switch-row" style="margin-top:15px;">
                <span style="font-size:1.11em; color: #fff;">Observatories</span>
            </div>
            <div class="obs-dropdown-container" style="margin-top:7px;">
                <div class="obs-dropdown-selected" tabindex="0" style="background:#fff;padding:8px 16px;border-radius:8px;cursor:pointer;">
                    <span class="obs-placeholder">Observatories</span>
                    <span class="obs-dropdown-arrow">&#9662;</span>
                </div>
                <div class="obs-dropdown-list" style="min-width:220px;padding:12px;">
                    <input type="text" class="obs-search" placeholder="Search observatories..." style="width:95%;margin-bottom:8px;" />
                    <div class="obs-options">
                        <!-- JS will render checkboxes here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-8" id="globe-col">
        <div id="globe-canvas-container" style="width:100%;height:75vh;min-height:440px;">
            <div id="quake-hover-card" style="display:none;position:absolute;">
                <div class="quake-row">
                    <div id="quake-location"></div>
                    <div id="quake-mag"></div>
                </div>
                <div id="quake-desc-date">
                    <span id="quake-desc"></span>
                    <span id="quake-date"></span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Scripts -->
<script type="module">
import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

// === THREE.js Globe setup ===
const container = document.getElementById('globe-canvas-container');
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(36, container.offsetWidth / container.offsetHeight, 0.1, 1000);
camera.position.set(0, 0, 5.3);

const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
renderer.setSize(container.offsetWidth, container.offsetHeight);
renderer.setClearColor(0x000000, 0);
container.appendChild(renderer.domElement);
renderer.setPixelRatio(window.devicePixelRatio);

scene.add(new THREE.AmbientLight(0xffffff, 0.08));
const hemiLight = new THREE.HemisphereLight(0xb8e6ff, 0x444444, 0.41);
hemiLight.position.set(0, 7, 0);
scene.add(hemiLight);
const sun = new THREE.DirectionalLight(0xf4ffff, 2.8);
sun.position.set(-8, 13, -6);
scene.add(sun);
const rimLight = new THREE.DirectionalLight(0x73b8ff, 1.5);
rimLight.position.set(-10, 13, 8);
scene.add(rimLight);

const globe = new THREE.Mesh(
    new THREE.SphereGeometry(1.5, 64, 64),
    new THREE.MeshPhongMaterial({
        map: new THREE.TextureLoader().load('../assets/earth/earthmap.jpg'),
        shininess: 12,
        specular: new THREE.Color('rgb(108,179,240)'),
        reflectivity: 0.19
    })
);
globe.rotation.y = Math.PI / -2;
scene.add(globe);

const controls = new OrbitControls(camera, renderer.domElement);
controls.enablePan = false;
controls.enableZoom = true;
controls.minDistance = 3.1; controls.maxDistance = 7;
controls.enableDamping = true; controls.dampingFactor = 0.12;
controls.rotateSpeed = 0.53;
controls.minPolarAngle = Math.PI * 0.14;
controls.maxPolarAngle = Math.PI * 0.86;

// === Marker management ===
let quakeMarkers = [];
function getMarkerColor(mag) {
    if (mag < 3) return 0x1ED75D;
    else if (mag < 4) return 0xCBE800;
    else if (mag < 5) return 0xFFF500;
    else if (mag < 6) return 0xFF9900;
    else if (mag < 7) return 0xFF5400;
    else return 0xFF2222;
}
function createEarthquakeMarker(lat, lon, size, mag) {
    const phi = (90 - lat) * Math.PI / 180;
    const theta = ((lon + 180) * Math.PI / 180) + (globe.rotation.y = Math.PI / -2);
    const radius = 1.515;
    const x = -radius * Math.sin(phi) * Math.cos(theta);
    const y = radius * Math.cos(phi);
    const z = radius * Math.sin(phi) * Math.sin(theta);
    const geom = new THREE.SphereGeometry(size, 24, 24);
    const color = getMarkerColor(mag);
    const mat = new THREE.MeshPhongMaterial({ color: color, emissive: color });
    const mesh = new THREE.Mesh(geom, mat);
    mesh.position.set(x, y, z);
    mesh.lookAt(0, 0, 0);
    return mesh;
}
function renderEarthquakes(eqArr) {
    quakeMarkers.forEach(marker => scene.remove(marker));
    quakeMarkers = [];
    eqArr.forEach(eq => {
        if (isNaN(eq.lat) || isNaN(eq.lon)) return;
        const marker = createEarthquakeMarker(eq.lat, eq.lon, 0.003 * eq.mag, eq.mag);
        marker.userData = { ...eq };
        scene.add(marker);
        quakeMarkers.push(marker);
    });
}
// === Raycasting for popover ===
const raycaster = new THREE.Raycaster(), mouse = new THREE.Vector2();
const card = document.getElementById('quake-hover-card');
const locDiv = document.getElementById('quake-location');
const magDiv = document.getElementById('quake-mag');
const dateDiv = document.getElementById('quake-date');
const descDiv = document.getElementById('quake-desc');
let showingMarker = null;
renderer.domElement.addEventListener('pointermove', (event) => {
    const rect = renderer.domElement.getBoundingClientRect();
    mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);
    const intersects = raycaster.intersectObjects(quakeMarkers, false);
    if (intersects.length > 0) {
        const m = intersects[0].object;
        locDiv.textContent = `${m.userData.city}, ${m.userData.country}`;
        magDiv.textContent = `${m.userData.mag}`;
        dateDiv.textContent = `${m.userData.date}`;
        descDiv.textContent = `${m.userData.desc}`;
        card.classList.add('active');
        card.style.display = "block";
        // Project to screen
        const markerScreenPos = m.position.clone().project(camera);
        const halfW = container.offsetWidth / 2;
        const halfH = container.offsetHeight / 2;
        const cardX = halfW + markerScreenPos.x * halfW;
        const cardY = halfH - markerScreenPos.y * halfH - 70; // a little above the dot
        card.style.left = cardX + 'px';
        card.style.top = cardY + 'px';
        showingMarker = m;
    } else {
        card.classList.remove('active');
        card.style.display = "none";
        showingMarker = null;
    }
});
renderer.domElement.addEventListener('mouseleave', () => {
    card.classList.remove('active');
    card.style.display = "none";
    showingMarker = null;
});
function updateHoverCard() {
    if (showingMarker) {
        const markerScreenPos = showingMarker.position.clone().project(camera);
        const halfW = container.offsetWidth / 2;
        const halfH = container.offsetHeight / 2;
        const cardX = halfW + markerScreenPos.x * halfW;
        const cardY = halfH - markerScreenPos.y * halfH - 70;
        card.style.left = (cardX) + 'px';
        card.style.top = (cardY) + 'px';
    }
}
renderer.setAnimationLoop(() => {
    controls.update();
    renderer.render(scene, camera);
    updateHoverCard();
});
window.addEventListener('resize', () => {
    const w = container.offsetWidth, h = container.offsetHeight;
    renderer.setSize(w, h);
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
});

// === Year/Mag Dual Sliders ===
function makeDualSlider(minId, maxId, barId, minLblId, maxLblId, min, max, padding, step) {
    const minEl = document.getElementById(minId);
    const maxEl = document.getElementById(maxId);
    const barEl = document.getElementById(barId);
    const minLbl = document.getElementById(minLblId);
    const maxLbl = document.getElementById(maxLblId);
    function toStep(val) {
        val = parseFloat(val);
        return step ? parseFloat(val.toFixed(String(step).split('.')[1]?.length || 1)) : val;
    }
    minEl.addEventListener('input', function() {
        if (toStep(minEl.value) > toStep(maxEl.value) - padding) minEl.value = toStep(maxEl.value) - padding;
        minLbl.textContent = minEl.value; updateBar();
    });
    maxEl.addEventListener('input', function() {
        if (toStep(maxEl.value) < toStep(minEl.value) + padding) maxEl.value = toStep(minEl.value) + padding;
        maxLbl.textContent = maxEl.value; updateBar();
    });
    function updateBar() {
        const percentMin = 100 * (minEl.value - min) / (max - min);
        const percentMax = 100 * (maxEl.value - min) / (max - min);
        barEl.style.left = percentMin + "%";
        barEl.style.width = (percentMax - percentMin) + "%";
    }
    updateBar();
    minLbl.textContent = minEl.value;
    maxLbl.textContent = maxEl.value;
}
makeDualSlider("slider-year-min","slider-year-max","year-slider-range","year-min-label","year-max-label",1950,2025,1,1);
makeDualSlider("slider-mag-min","slider-mag-max","mag-slider-range","mag-min-label","mag-max-label",0,9,0.1,0.1);

// === Observatories Dropdown/Ajax ===
const obsDropdown = document.querySelector('.obs-dropdown-container');
const obsSelected = obsDropdown.querySelector('.obs-dropdown-selected');
const obsList = obsDropdown.querySelector('.obs-dropdown-list');
const obsOptionsCont = obsDropdown.querySelector('.obs-options');
const obsSearch = obsDropdown.querySelector('.obs-search');
const obsPlaceholder = obsDropdown.querySelector('.obs-placeholder');
let observatoriesData = [];
function fetchAndRenderObservatories(thenCallUpdateGlobe = false) {
    fetch('observatories_api.php')
        .then(resp => resp.json())
        .then(obsArr => {
            observatoriesData = obsArr;
            renderObservatoryCheckboxes();
            if (thenCallUpdateGlobe) updateGlobe();
        });
}
function renderObservatoryCheckboxes(filter = '') {
    obsOptionsCont.innerHTML = '';
    observatoriesData.forEach(obs => {
        if (!filter || obs.name.toLowerCase().includes(filter.toLowerCase())) {
            const row = document.createElement('div');
            row.className = 'obs-option-row';
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'obs-option-checkbox';
            checkbox.value = obs.id;
            checkbox.checked = false;
            const label = document.createElement('span');
            label.className = 'obs-option-label';
            label.textContent = obs.name;
            checkbox.addEventListener('change', updateObsSelectedText);
            row.appendChild(checkbox);
            row.appendChild(label);
            obsOptionsCont.appendChild(row);
        }
    });
}
function updateObsSelectedText() {
    const checked = [...obsOptionsCont.querySelectorAll('input[type=checkbox]:checked')];
    if (!checked.length) {
        obsPlaceholder.textContent = "Observatories";
    } else if (checked.length === 1) {
        const id = checked[0].value;
        const name = observatoriesData.find(o => o.id == id)?.name || '';
        obsPlaceholder.textContent = name;
    } else {
        obsPlaceholder.textContent = `${checked.length} Observatories`;
    }
}
obsSelected.addEventListener('click', function () {
    obsSelected.classList.toggle('active');
    obsList.classList.toggle('active');
    obsSearch.value = '';
    renderObservatoryCheckboxes();
    setTimeout(() => obsSearch.focus(), 50);
});
document.addEventListener('mousedown', e => {
    if (!obsDropdown.contains(e.target)) {
        obsSelected.classList.remove('active');
        obsList.classList.remove('active');
    }
});
obsSearch.addEventListener('input', function() {
    renderObservatoryCheckboxes(this.value.trim());
});
fetchAndRenderObservatories(true);

// === Filtering and AJAX ===
function collectFilters() {
    const minYear = parseInt(document.getElementById('slider-year-min').value);
    const maxYear = parseInt(document.getElementById('slider-year-max').value);
    const minMag = parseFloat(document.getElementById('slider-mag-min').value);
    const maxMag = parseFloat(document.getElementById('slider-mag-max').value);
    const types = Array.from(document.querySelectorAll('.eq-type:checked')).map(cb => cb.value);
    const obs = Array.from(document.querySelectorAll('.obs-option-checkbox:checked')).map(cb => parseInt(cb.value));
    return { min_year: minYear, max_year: maxYear, min_mag: minMag, max_mag: maxMag, types: types, observatories: obs };
}
async function fetchEarthquakesAjax(filters = {}) {
    const params = new URLSearchParams();
    if (filters.min_year !== undefined) params.append('min_year', filters.min_year);
    if (filters.max_year !== undefined) params.append('max_year', filters.max_year);
    if (filters.min_mag !== undefined) params.append('min_mag', filters.min_mag);
    if (filters.max_mag !== undefined) params.append('max_mag', filters.max_mag);
    if (filters.types && filters.types.length) params.append('types', filters.types.join(','));
    if (filters.observatories && filters.observatories.length) params.append('observatories', filters.observatories.join(','));
    const resp = await fetch('earthquakes_api.php?' + params.toString());
    if (!resp.ok) return [];
    return resp.json();
}
async function updateGlobe() {
    const filters = collectFilters();
    const earthquakes = await fetchEarthquakesAjax(filters);
    renderEarthquakes(earthquakes);
}
// Event listeners for filtering
['slider-year-min','slider-year-max','slider-mag-min','slider-mag-max']
.forEach(id => document.getElementById(id).addEventListener('input', updateGlobe));
document.querySelectorAll('.eq-type').forEach(cb => {
    cb.addEventListener('change', updateGlobe);
});
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('obs-option-checkbox')) {
        updateGlobe();
    }
});
</script>
</body>
</html>