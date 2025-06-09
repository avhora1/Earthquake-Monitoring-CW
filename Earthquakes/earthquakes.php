<?php include $_SERVER['DOCUMENT_ROOT'].'/session.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META & TITLE -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earthquakes</title>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <?php include '../headerNew.php'; ?>

    <style>
    body {
        background: radial-gradient(#000540 0%, #000 100%);
    }

    .glass-box {
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
        backdrop-filter: blur(1vh);
        border-radius: 2rem;
        border: 2px solid rgba(255, 255, 255, 0.09);
        box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
        min-height: 80vh;
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
    }

    #quake-hover-card {
        position: absolute;
        min-width: 250px;
        background: #fff;
        border-radius: 10px;
        max-width: 600px;
        /* Or whatever fits for your globe */
        width: auto;
        white-space: normal;
        /* Allow wrapping */
        word-break: break-word;
        /* Wrap long words */
        overflow-wrap: break-word;
        /* For extra compatibility */
        z-index: 9999;
        left: 0;
        top: 0;
        display: none;
        pointer-events: none;
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.00) 100%);
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
        white-space: wrap;
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

    <!-- MODULE IMPORT MAP FOR THREE.JS -->
    <script type="importmap">
        {
            "imports": {
                "three": "https://cdn.jsdelivr.net/npm/three@0.177.0/build/three.module.js",
                "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.177.0/examples/jsm/"
            }
        }
    </script>
</head>


<body>
    <div class="container-fluid">
        <div class="row justify-content-start align-items-center" style="min-height: 92vh;transform: translateY(5vh);
">
            <div class="col-1"></div>
            <div class="col-3">
                <div class="glass-box">
                    <div class="filters-title">Filters</div>

                    <!-- YEAR SLIDER -->
                    <div class="filter-group">
                        <label class="filter-label" for="slider-year-min">Year:</label>
                        <div class="dual-slider">
                            <div class="slider-track">
                                <div class="slider-range" id="year-slider-range"></div>
                                <input id="slider-year-min" type="range" min="1950" max="2025" value="1950"
                                    class="slider" />
                                <input id="slider-year-max" type="range" min="1950" max="2025" value="2025"
                                    class="slider" />
                            </div>
                            <div class="slider-label-row">
                                <span id="year-min-label">1950</span>
                                <span id="year-max-label" style="float:right">2025</span>
                            </div>
                        </div>
                    </div>
                    <!-- MAGNITUDE SLIDER -->
                    <div class="filter-group">
                        <label class="filter-label" for="slider-mag-min">Magnitude:</label>
                        <div class="dual-slider">
                            <div class="slider-track">
                                <div class="slider-range" id="mag-slider-range"></div>
                                <input id="slider-mag-min" type="range" min="0" max="9" step="0.1" value="0"
                                    class="slider" />
                                <input id="slider-mag-max" type="range" min="0" max="9" step="0.1" value="9"
                                    class="slider" />
                            </div>
                            <div class="slider-label-row">
                                <span id="mag-min-label">0</span>
                                <span id="mag-max-label" style="float:right">9</span>
                            </div>
                        </div>
                    </div>

                    <!-- EARTHQUAKE TYPES FILTER -->
                    <div class="filter-group">
                        <label class="filter-label" style="margin-bottom:12px;">Types:</label>
                        <div class="switch-row" style="margin-top:0;">
                            <span style="font-size:1.11em; color: #fff;">Earthquakes</span>
                        </div>
                        <div class="switch-row"><span style="margin-left: 1vh;">Tectonic</span>
                            <label class="switch">
                                <input type="checkbox" class="eq-type" value="tectonic" checked><span
                                    class="slider"></span>
                            </label>
                        </div>
                        <div class="switch-row"><span style="margin-left: 1vh;">Volcanic</span>
                            <label class="switch">
                                <input type="checkbox" class="eq-type" value="volcanic" checked><span
                                    class="slider"></span>
                            </label>
                        </div>
                        <div class="switch-row"><span style="margin-left: 1vh;">Collapse</span>
                            <label class="switch">
                                <input type="checkbox" class="eq-type" value="collapse" checked><span
                                    class="slider"></span>
                            </label>
                        </div>
                        <div class="switch-row"><span style="margin-left: 1vh;">Explosion</span>
                            <label class="switch">
                                <input type="checkbox" class="eq-type" value="explosion" checked><span
                                    class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- OBSERVATORIES TOGGLE AND DROPDOWN -->
                    <div class="switch-row" style="margin-top:15px;">
                        <span style="font-size:1.11em; color: #fff;">Observatories</span>
                        <label class="switch">
                            <input type="checkbox" id="toggle-observatories" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="obs-dropdown-container" style="margin-top:7px;">
                        <div class="obs-dropdown-selected" tabindex="0"
                            style="background:#fff;padding:8px 16px;border-radius:8px;cursor:pointer;">
                            <span class="obs-placeholder">Observatories</span>
                            <span class="obs-dropdown-arrow">&#9662;</span>
                        </div>
                        <div class="obs-dropdown-list" style="min-width:220px;padding:12px;">
                            <input type="text" class="obs-search" placeholder="Search observatories..."
                                style="width:95%;margin-bottom:8px;" />
                            <div class="obs-options">
                                <!-- JS will insert checkboxes here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GLOBE AND POPUP -->
            <div class="col-7" id="globe-col">
                <div id="globe-canvas-container"
                    style="width:100%;height:80vh;min-height:440px;border-radius:2rem;border: 2px solid rgba(255, 255, 255, 0.09);">
                    <!-- Hover popup for marker info -->
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

    <!-- SCRIPTS -->
    <script type="module">
    /* ===============================
     * 1. MODULE IMPORTS AND HELPERS
     * =============================== */
    import * as THREE from 'three';
    import {
        OrbitControls
    } from 'three/addons/controls/OrbitControls.js';
    import getStarfield from "./getStarfield.js";
    import {
        getFresnelMat
    } from "./getFresnelMat.js";

    /* ===============================
     * 2. DOM ELEMENT REFERENCES
     * =============================== */
    const container = document.getElementById('globe-canvas-container');
    const quakeHoverCard = document.getElementById('quake-hover-card');
    const locationDiv = document.getElementById('quake-location');
    const magnitudeDiv = document.getElementById('quake-mag');
    const dateDiv = document.getElementById('quake-date');
    const descDiv = document.getElementById('quake-desc');

    /* ===============================
     * 3. THREE.JS GLOBE/SCENE SETUP
     * =============================== */
    // --- Scene/Camera/Renderer Setup ---
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(36, container.offsetWidth / container.offsetHeight, 0.1, 1000);
    camera.position.set(0, 0, 6);

    const renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true
    });
    renderer.setSize(container.offsetWidth, container.offsetHeight);
    renderer.setClearColor(0x000000, 0);
    renderer.setPixelRatio(window.devicePixelRatio);
    container.appendChild(renderer.domElement);

    // --- Globe Group: (all 3d globe-related objects go here) ---
    const globeGroup = new THREE.Group();
    scene.add(globeGroup);

    // --- Lighting (some lights can be part of globeGroup if you want them to "move" with the globe) ---
    scene.add(new THREE.AmbientLight(0xffffff, 0.08));
    const hemiLight = new THREE.HemisphereLight(0xb8e6ff, 0x444444, 0.41);
    hemiLight.position.set(3, 3, 0);
    scene.add(hemiLight);

    // Directional lights simulate sun/rim lighting. Put in globeGroup if you want to "rotate" with globe.
    const sun = new THREE.DirectionalLight(0xffffff);
    sun.position.set(-8, 4, 0);
    globeGroup.add(sun);

    const rimLight = new THREE.DirectionalLight(0xffffff, 1);
    rimLight.position.set(-10, 13, 8);
    globeGroup.add(rimLight);

    const rimLight2 = new THREE.DirectionalLight(0xffffff, 1);
    rimLight2.position.set(10, 13, -8);
    globeGroup.add(rimLight2);

    // --- Globe Base Spheres ---
    const texLoader = new THREE.TextureLoader();

    // Fresnel Glow
    const fresnelMat = getFresnelMat();
    const glowMesh = new THREE.Mesh(
        new THREE.SphereGeometry(1.5, 64, 64),
        fresnelMat
    );
    glowMesh.scale.setScalar(1.01);
    globeGroup.add(glowMesh);

    // Earth surface
    const globe = new THREE.Mesh(
        new THREE.SphereGeometry(1.5, 64, 64),
        new THREE.MeshPhongMaterial({
            map: texLoader.load('../assets/earth/earthmap2.jpg'),
            bumpMap: texLoader.load('../assets/earth/bumpmap.jpg'),
            bumpScale: 7,
            specularMap: texLoader.load('../assets/earth/specmap.jpg'),
            shininess: 2,
            specular: new THREE.Color('rgb(108,179,240)'),
            reflectivity: 0.01,
        })
    );
    globe.rotation.y = Math.PI / -2;
    globeGroup.add(globe);

    // Clouds
    const cloudsMesh = new THREE.Mesh(
        new THREE.SphereGeometry(1.5, 64, 64),
        new THREE.MeshStandardMaterial({
            map: texLoader.load('../assets/earth/clouds.jpg'),
            blending: THREE.AdditiveBlending,
        })
    );
    cloudsMesh.scale.setScalar(1.003);
    globeGroup.add(cloudsMesh);

    // Fun: "Stuart" box (can remove if you prefer)
    const stuartTexture = texLoader.load('../assets/images/stuart.jpg');
    const stuart = new THREE.Mesh(
        new THREE.BoxGeometry(0.3, 0.3, 0.3),
        new THREE.MeshBasicMaterial({
            map: stuartTexture
        })
    );
    globeGroup.add(stuart);

    // --- Stars (background, not part of globeGroup so they don't rotate with globe) ---
    const stars = getStarfield({
        numStars: 20000
    });
    scene.add(stars);

    // --- Orbit Controls for camera interactivity ---
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enablePan = false;
    controls.enableZoom = true;
    controls.minDistance = 1;
    controls.maxDistance = 10;
    controls.enableDamping = true;
    controls.dampingFactor = 0.12;
    controls.rotateSpeed = 0.53;
    controls.minPolarAngle = Math.PI * 0.14;
    controls.maxPolarAngle = Math.PI * 0.86;

    /* ===============================
     * 4. MARKERS: EARTHQUAKES & OBSERVATORIES
     * =============================== */
    // --- Observatory Markers ---
    let observatoryMarkers = [];

    function createObservatoryMarker(lat, lon) {
        const phi = (90 - lat) * Math.PI / 180;
        const theta = ((lon + 180) * Math.PI / 180) + globe.rotation.y;
        const r = 1.512;
        const x = -r * Math.sin(phi) * Math.cos(theta);
        const y = r * Math.cos(phi);
        const z = r * Math.sin(phi) * Math.sin(theta);
        const geom = new THREE.SphereGeometry(0.015, 20, 20);
        const mat = new THREE.MeshPhongMaterial({
            color: 0xffffff,
            emissive: 0xffffff,
            shininess: 80
        });
        const mesh = new THREE.Mesh(geom, mat);
        mesh.position.set(x, y, z);
        mesh.lookAt(0, 0, 0);
        return mesh;
    }
    async function updateObservatoryMarkers() {
        // Remove existing markers
        observatoryMarkers.forEach(marker => globeGroup.remove(marker));
        observatoryMarkers = [];
        let obsArr = await fetch('observatories_api.php').then(r => r.json());
        obsArr.forEach(obs => {
            const marker = createObservatoryMarker(parseFloat(obs.latitude), parseFloat(obs.longitude));
            marker.userData = {
                name: obs.name,
                est: obs.est_date,
                country: obs.country,
                state: 'observatory'
            };
            marker.visible = document.getElementById('toggle-observatories').checked;
            globeGroup.add(marker);
            observatoryMarkers.push(marker);
        });
    }

    // --- Earthquake Markers ---
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
        const theta = ((lon + 180) * Math.PI / 180) + globe.rotation.y;
        const r = 1.515;
        const x = -r * Math.sin(phi) * Math.cos(theta);
        const y = r * Math.cos(phi);
        const z = r * Math.sin(phi) * Math.sin(theta);
        const geom = new THREE.SphereGeometry(size, 24, 24);
        const color = getMarkerColor(mag);
        const mat = new THREE.MeshPhongMaterial({
            color: color,
            emissive: color
        });
        const mesh = new THREE.Mesh(geom, mat);
        mesh.position.set(x, y, z);
        mesh.lookAt(0, 0, 0);
        return mesh;
    }

    function renderEarthquakes(eqArr) {
        quakeMarkers.forEach(marker => globeGroup.remove(marker));
        quakeMarkers = [];
        eqArr.forEach(eq => {
            if (isNaN(eq.lat) || isNaN(eq.lon)) return;
            const marker = createEarthquakeMarker(eq.lat, eq.lon, 0.003 * eq.mag, eq.mag);
            marker.userData = {
                ...eq,
                state: 'earthquake'
            };
            globeGroup.add(marker);
            quakeMarkers.push(marker);
        });
    }

    /* ===============================
     * 5. HOVER CARD LOGIC
     * =============================== */
    // --- For caching geocode lookups ---
    const quakeHoverCache = {};

    // --- Popup event logic ---
    const raycaster = new THREE.Raycaster(),
        mouse = new THREE.Vector2();
    let showingMarker = null;

    renderer.domElement.addEventListener('pointermove', async (event) => {
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects([...quakeMarkers, ...observatoryMarkers], false);
        if (intersects.length > 0) {
            const m = intersects[0].object;
            // For Earthquake
            if (m.userData.state === 'earthquake') {
                let {
                    city,
                    country
                } = await getCityCountryForMarker(m);
                locationDiv.textContent = (city ? (city + ', ') : '') + (country ? country : m.userData
                    .country);
                magnitudeDiv.textContent = `${m.userData.mag}`;
                dateDiv.textContent = `${m.userData.date}`;
                descDiv.textContent = m.userData.desc || "";
            }
            // For Observatory
            else if (m.userData.state === 'observatory') {
                locationDiv.textContent = m.userData.name || '';
                magnitudeDiv.textContent = '';
                dateDiv.textContent = m.userData.est ? 'Est. ' + m.userData.est : '';
                descDiv.textContent = m.userData.country || '';
            }
            quakeHoverCard.classList.add('active');
            quakeHoverCard.style.display = "block";
            // Project marker position to 2D screen
            const markerScreenPos = m.position.clone().project(camera);
            const halfW = container.offsetWidth / 2;
            const halfH = container.offsetHeight / 2;
            quakeHoverCard.style.left = (halfW + markerScreenPos.x * halfW) + 'px';
            quakeHoverCard.style.top = (halfH - markerScreenPos.y * halfH - 70) + 'px';
            showingMarker = m;
        } else {
            quakeHoverCard.classList.remove('active');
            quakeHoverCard.style.display = "none";
            showingMarker = null;
        }
    });
    renderer.domElement.addEventListener('mouseleave', () => {
        quakeHoverCard.classList.remove('active');
        quakeHoverCard.style.display = "none";
        showingMarker = null;
    });

    function updateHoverCardPosition() {
        if (showingMarker) {
            const markerScreenPos = showingMarker.position.clone().project(camera);
            const halfW = container.offsetWidth / 2;
            const halfH = container.offsetHeight / 2;
            quakeHoverCard.style.left = (halfW + markerScreenPos.x * halfW) + 'px';
            quakeHoverCard.style.top = (halfH - markerScreenPos.y * halfH - 70) + 'px';
        }
    }

    async function getCityCountryForMarker(marker) {
        if (marker.userData.city && marker.userData.country) {
            return {
                city: marker.userData.city,
                country: marker.userData.country
            };
        }
        const key = `${marker.userData.lat},${marker.userData.lon}`;
        if (quakeHoverCache[key]) return quakeHoverCache[key];
        const resp = await fetch("geocode_api.php?lat=" + encodeURIComponent(marker.userData.lat) + "&lon=" +
            encodeURIComponent(marker.userData.lon));
        const result = await resp.json();
        quakeHoverCache[key] = result || {
            city: "",
            country: ""
        };
        return result;
    }

    /* ===============================
     * 6. RENDER LOOP / HANDLE RESIZE
     * =============================== */
    renderer.setAnimationLoop(() => {
        controls.update();
        renderer.render(scene, camera);
        updateHoverCardPosition();
    });
    window.addEventListener('resize', () => {
        const w = container.offsetWidth,
            h = container.offsetHeight;
        renderer.setSize(w, h);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
    });

    /* ===============================
     * 7. SLIDERS, DROPDOWN, AND FILTER UI
     * =============================== */

    // Dual sliders (year, mag) UI logic
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
            if (toStep(minEl.value) > toStep(maxEl.value) - padding) minEl.value = toStep(maxEl.value) -
                padding;
            minLbl.textContent = minEl.value;
            updateBar();
        });
        maxEl.addEventListener('input', function() {
            if (toStep(maxEl.value) < toStep(minEl.value) + padding) maxEl.value = toStep(minEl.value) +
                padding;
            maxLbl.textContent = maxEl.value;
            updateBar();
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
    makeDualSlider("slider-year-min", "slider-year-max", "year-slider-range", "year-min-label", "year-max-label", 1950,
        2025, 1, 1);
    makeDualSlider("slider-mag-min", "slider-mag-max", "mag-slider-range", "mag-min-label", "mag-max-label", 0, 9, 0.1,
        0.1);

    // Observatory dropdown logic
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
    obsSelected.addEventListener('click', function() {
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

    /* ===============================
     * 8. DATA FETCH/FILTERING LOGIC
     * =============================== */
    // Random news snippets for quake hover descriptions
    const randomQuakeSnippets = [
        "Local media report minor injuries.",
        "Quick response from emergency services.",
        "Residents described shaking as moderate.",
        "Earthquake felt over a wide region.",
        "People were woken from their sleep.",
        "Electricity temporarily disrupted.",
        "Follow-up tremors were weak.",
        "No tsunami warning issued.",
        "Buildings withstood the shock.",
        "Schools closed for inspection."
    ];

    function collectFilters() {
        const minYear = parseInt(document.getElementById('slider-year-min').value);
        const maxYear = parseInt(document.getElementById('slider-year-max').value);
        const minMag = parseFloat(document.getElementById('slider-mag-min').value);
        const maxMag = parseFloat(document.getElementById('slider-mag-max').value);
        const types = Array.from(document.querySelectorAll('.eq-type:checked')).map(cb => cb.value);
        const obs = Array.from(document.querySelectorAll('.obs-option-checkbox:checked')).map(cb => parseInt(cb.value));
        return {
            min_year: minYear,
            max_year: maxYear,
            min_mag: minMag,
            max_mag: maxMag,
            types: types,
            observatories: obs
        };
    }
    async function fetchEarthquakesAjax(filters = {}) {
        const params = new URLSearchParams();
        if (filters.min_year !== undefined) params.append('min_year', filters.min_year);
        if (filters.max_year !== undefined) params.append('max_year', filters.max_year);
        if (filters.min_mag !== undefined) params.append('min_mag', filters.min_mag);
        if (filters.max_mag !== undefined) params.append('max_mag', filters.max_mag);
        if (filters.types?.length) params.append('types', filters.types.join(','));
        if (filters.observatories?.length) params.append('observatories', filters.observatories.join(','));
        const resp = await fetch('earthquakes_api.php?' + params.toString());
        if (!resp.ok) return [];
        let quakes = await resp.json();
        // Add a random description to each quake
        quakes.forEach(eq => {
            eq.desc = randomQuakeSnippets[Math.floor(Math.random() * randomQuakeSnippets.length)];
        });
        return quakes;
    }

    async function updateGlobe() {
        await updateObservatoryMarkers();
        const filters = collectFilters();
        const earthquakes = await fetchEarthquakesAjax(filters);
        renderEarthquakes(earthquakes);
    }

    /* ===============================
     * 9. EVENT HOOKUP (FILTERS ⇒ GLOBE)
     * =============================== */
    ['slider-year-min', 'slider-year-max', 'slider-mag-min', 'slider-mag-max']
    .forEach(id => document.getElementById(id).addEventListener('input', updateGlobe));
    document.querySelectorAll('.eq-type').forEach(cb => {
        cb.addEventListener('change', updateGlobe);
    });
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('obs-option-checkbox')) {
            updateGlobe();
        }
    });
    const observatoriesToggle = document.getElementById('toggle-observatories');
    observatoriesToggle.addEventListener('change', updateGlobe);

    // Initial load
    updateObservatoryMarkers();
    fetchAndRenderObservatories(true);
    </script>
</body>

</html>