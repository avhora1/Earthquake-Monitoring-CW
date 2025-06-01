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
        z-index: -3;
        padding: 2vh;
    }

    .year-slider-outer {
        width: 100%;
        margin-top: 7px;
        margin-bottom: 10px;
    }

    .year-slider-track {
        position: relative;
        width: 100%;
        height: 32px;
        margin-bottom: 0;
    }

    .year-slider {
        position: absolute;
        left: 0;
        right: 0;
        top: 7px;
        width: 100%;
        pointer-events: none;
        -webkit-appearance: none;
        background: none;
    }

    .year-slider::-webkit-slider-thumb {
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
        z-index: 99;
    }

    .year-slider::-moz-range-thumb {
        background: #18f545;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        border: 2.5px solid #fff;
        cursor: pointer;
        pointer-events: all;
        transition: background .19s;
        z-index: 99;
    }

    .year-slider::-ms-thumb {
        background: #18f545;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        border: 2.5px solid #fff;
        cursor: pointer;
        pointer-events: all;
        transition: background .19s;
        z-index: 99;
    }

    .year-slider:focus {
        outline: none;
    }

    .year-slider::-webkit-slider-runnable-track {
        height: 5px;
        background: transparent;
        border-radius: 1.4vw;
    }

    .year-slider::-moz-range-track {
        height: 5px;
        background: transparent;
    }

    .year-slider::-ms-fill-lower,
    .year-slider::-ms-fill-upper {
        background: transparent;
    }

    .year-slider-range {
        position: absolute;
        left: 0;
        top: 21px;
        height: 5px;
        background: linear-gradient(90deg, #09ff00, #00b144);
        z-index: 5;
        border-radius: 3px;
    }

    .year-slider-label-row {
        font-size: 1.5rem;
        color: #fff;
        margin-top: 8px;
        display: flex;
        justify-content: space-between;
        font-family: 'Roboto', Arial, sans-serif;
        font-weight: 200;
        letter-spacing: 0.02em;
    }

    .year-slider-track::before,
    .year-slider-track::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: 21px;
        height: 5px;
        background: #fff;
        border-radius: 1.4vw;
        z-index: 0;
    }

    .filter-label {
        font-size: 1.5rem;
        color: #fff;
        font-family: 'Roboto', Arial, sans-serif;
        font-weight: 200;

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
                    <div class="filter-group">
                        <label class="filter-label">Year:</label>
                        <div class="year-slider-outer">
                            <div class="year-slider-track">
                                <div class="year-slider-range" id="year-slider-range"></div>
                                <input id="year-min" type="range" min="1950" max="2025" value="1950"
                                    class="year-slider" />
                                <input id="year-max" type="range" min="1950" max="2025" value="2025"
                                    class="year-slider" />
                            </div>
                            <div class="year-slider-label-row">
                                <span id="year-min-label">1950</span>
                                <span id="year-max-label" style="float:right">2025</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-8" id="globe-col">
                <div id="globe-canvas-container">
                    <div id="quake-hover-card">
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


    <script type="module">
    import * as THREE from 'three';
    import {
        OrbitControls
    } from 'three/addons/controls/OrbitControls.js';

    // Container
    const container = document.getElementById('globe-canvas-container');
    const width = container.offsetWidth;
    const height = container.offsetHeight;

    // Scene, camera, renderer
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(36, width / height, 0.1, 1000);
    camera.position.set(0, 0, 5.3);
    const renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true
    });
    renderer.setSize(width, height);
    renderer.setClearColor(0x000000, 0);
    container.appendChild(renderer.domElement);
    renderer.setPixelRatio(window.devicePixelRatio);

    // Lighting
    // FOG for atmospheric depth
    // scene.fog = new THREE.FogExp2(0x071025, 0.74); // color, density

    // 1. Ambient Light — just a touch of overall fill
    scene.add(new THREE.AmbientLight(0xffffff, 0.08));

    // 2. Hemisphere Light — stunning blue sky effect, supports rim glow
    const hemiLight = new THREE.HemisphereLight(0xb8e6ff, 0x444444, 0.41);
    hemiLight.position.set(0, 7, 0);
    scene.add(hemiLight);

    // 3. The "Sun" — a powerful, blue-white directional
    const sunColor = 0xf4ffff; // crisp blue-white
    const sunIntensity = 2.8; // VERY bright
    const sun = new THREE.DirectionalLight(sunColor, sunIntensity);
    sun.position.set(-8, 13, -6); // HIGH top-left
    sun.castShadow = false;
    scene.add(sun);

    // (Optional) a subtle "halo" support for even more dramatic rim effect
    const rimLight = new THREE.DirectionalLight(0x73b8ff, 1.5);
    rimLight.position.set(-10, 13, 8); // farther to the rear/side
    scene.add(rimLight);

    // 4. Point-light "burst" for hot highlight
    const hotSpot = new THREE.PointLight(0xcff5ff, 3.5, 7, 3.6);
    hotSpot.position.set(-8.2, 15, -7.5); // exactly at/above the sun: tweak for max effect
    scene.add(hotSpot);

    // 5. Softer fill from "bottom right" (helps soften shadow fill)
    const fill = new THREE.DirectionalLight(0x4660b8, 0.23);
    fill.position.set(2.7, -5, 6.5);
    scene.add(fill);

    // Globe
    const loader = new THREE.TextureLoader();
    const earthMap = loader.load('../assets/earth/earthmap.jpg');
    const sphereGeometry = new THREE.SphereGeometry(1.5, 64, 64);
    const sphereMaterial = new THREE.MeshPhongMaterial({
        map: earthMap,
        shininess: 12,
        specular: new THREE.Color('rgb(108,179,240)'),
        reflectivity: 0.19
    });
    const globe = new THREE.Mesh(sphereGeometry, sphereMaterial);
    globe.rotation.y = Math.PI / -2; // quarter turn right (adjust to taste)
    scene.add(globe);

    // Earthquake markers
    const earthquakes = [{
            lat: 35.6895,
            lon: 139.6917,
            mag: 6.9,
            city: "Tokyo",
            country: "Japan",
            date: "27.08.2011",
            desc: "Major roads cracked, minor structural damage, power outages in several districts."
        },
        {
            lat: 37.7749,
            lon: -122.4194,
            mag: 4.3,
            city: "San Francisco",
            country: "USA",
            date: "30.03.2014",
            desc: "Light shaking, picture frames fell, no major damage."
        },
        {
            lat: 34.0522,
            lon: -118.2437,
            mag: 5.7,
            city: "Los Angeles",
            country: "USA",
            date: "13.06.2019",
            desc: "Felt throughout the city, some windows shattered downtown."
        },
        {
            lat: -33.4489,
            lon: -70.6693,
            mag: 8.2,
            city: "Santiago",
            country: "Chile",
            date: "01.04.2014",
            desc: "Severe quake, buildings collapsed, tsunami warning issued."
        },
        {
            lat: 19.4326,
            lon: -99.1332,
            mag: 7.1,
            city: "Mexico City",
            country: "Mexico",
            date: "19.09.2017",
            desc: "Multiple collapsed buildings, rescue operations, thousands evacuated."
        },
        {
            lat: 55.7558,
            lon: 37.6176,
            mag: 3.2,
            city: "Moscow",
            country: "Russia",
            date: "11.02.2006",
            desc: "Light tremors felt, little to no impact."
        },
        {
            lat: 41.0082,
            lon: 28.9784,
            mag: 4.5,
            city: "Istanbul",
            country: "Turkey",
            date: "18.07.2023",
            desc: "People rushed outdoors, minor cracks in older buildings."
        },
        {
            lat: 31.2304,
            lon: 121.4737,
            mag: 5.3,
            city: "Shanghai",
            country: "China",
            date: "03.04.2022",
            desc: "Moderate shaking, subway services temporarily suspended."
        },
        {
            lat: 48.8566,
            lon: 2.3522,
            mag: 3.6,
            city: "Paris",
            country: "France",
            date: "25.12.2018",
            desc: "Residents reported rattling windows, no known damage."
        },
        {
            lat: -36.8485,
            lon: 174.7633,
            mag: 6.2,
            city: "Auckland",
            country: "New Zealand",
            date: "20.08.2020",
            desc: "Shaking felt across city, minor landslides outside city."
        },
        {
            lat: 51.5074,
            lon: -0.1278,
            mag: 2.9,
            city: "London",
            country: "UK",
            date: "14.11.2010",
            desc: "Rare quake, light tremor felt only in high rises."
        },
        {
            lat: 35.6762,
            lon: 139.6503,
            mag: 5.0,
            city: "Yokohama",
            country: "Japan",
            date: "10.10.2011",
            desc: "Some chimneys toppled, railway inspections carried out."
        },
        {
            lat: 40.7128,
            lon: -74.0060,
            mag: 4.2,
            city: "New York",
            country: "USA",
            date: "29.08.2016",
            desc: "Unexpected quake, subway delays reported."
        },
        {
            lat: -23.5505,
            lon: -46.6333,
            mag: 6.6,
            city: "São Paulo",
            country: "Brazil",
            date: "15.05.2017",
            desc: "Moderate property damage, especially on outskirts."
        },
        {
            lat: 39.9042,
            lon: 116.4074,
            mag: 7.3,
            city: "Beijing",
            country: "China",
            date: "12.07.2014",
            desc: "Strong quake, several old structures collapsed."
        },
        {
            lat: 28.6139,
            lon: 77.2090,
            mag: 4.7,
            city: "New Delhi",
            country: "India",
            date: "02.02.2012",
            desc: "Office buildings briefly evacuated, minor panic."
        },
        {
            lat: 43.6532,
            lon: -79.3832,
            mag: 5.8,
            city: "Toronto",
            country: "Canada",
            date: "24.03.2013",
            desc: "Shaking rattled windows and furniture, few reports of damage."
        },
        {
            lat: 1.3521,
            lon: 103.8198,
            mag: 4.2,
            city: "Singapore",
            country: "Singapore",
            date: "09.06.2021",
            desc: "Quake felt in tall buildings, no damage reported."
        },
        {
            lat: 34.0522,
            lon: -118.2437,
            mag: 6.9,
            city: "Los Angeles",
            country: "USA",
            date: "06.07.2019",
            desc: "Strongest in decades, some freeway sections closed."
        },
        {
            lat: -22.9068,
            lon: -43.1729,
            mag: 5.1,
            city: "Rio de Janeiro",
            country: "Brazil",
            date: "28.08.2019",
            desc: "Seaside promenade damaged, minor injuries reported."
        },
        {
            lat: 55.9533,
            lon: -3.1883,
            mag: 2.7,
            city: "Edinburgh",
            country: "UK",
            date: "13.07.2020",
            desc: "Small quake, some reports of faint rumbling."
        },
        {
            lat: 41.9028,
            lon: 12.4964,
            mag: 3.3,
            city: "Rome",
            country: "Italy",
            date: "22.12.2008",
            desc: "Short tremor, no damage."
        },
        {
            lat: 49.2827,
            lon: -123.1207,
            mag: 7.1,
            city: "Vancouver",
            country: "Canada",
            date: "01.06.2005",
            desc: "Heavy shaking, landslide on mountainside, several injuries."
        },
        {
            lat: -34.6037,
            lon: -58.3816,
            mag: 3.8,
            city: "Buenos Aires",
            country: "Argentina",
            date: "18.02.2011",
            desc: "Quake felt by residents, minor panic, no reported damage."
        },
        {
            lat: 35.6895,
            lon: 51.3890,
            mag: 6.4,
            city: "Tehran",
            country: "Iran",
            date: "30.03.2006",
            desc: "Multiple aftershocks followed, some buildings damaged."
        },
        {
            lat: 59.9343,
            lon: 30.3351,
            mag: 4.9,
            city: "Saint Petersburg",
            country: "Russia",
            date: "09.09.2018",
            desc: "Shaking felt on upper floors, no damage."
        },
        {
            lat: 6.5244,
            lon: 3.3792,
            mag: 5.0,
            city: "Lagos",
            country: "Nigeria",
            date: "07.01.2019",
            desc: "Quake felt strongly, several pipelines briefly shut off."
        },
        {
            lat: -33.9249,
            lon: 18.4241,
            mag: 2.5,
            city: "Cape Town",
            country: "South Africa",
            date: "17.10.2017",
            desc: "No damage, light tremor only."
        },
        {
            lat: 30.0444,
            lon: 31.2357,
            mag: 4.1,
            city: "Cairo",
            country: "Egypt",
            date: "06.08.2015",
            desc: "Felt throughout the region, small cracks in older walls."
        },
        {
            lat: 39.7392,
            lon: -104.9903,
            mag: 3.4,
            city: "Denver",
            country: "USA",
            date: "14.04.2021",
            desc: "Woken by weak shaking, no impact."
        },
        {
            lat: -12.0464,
            lon: -77.0428,
            mag: 7.0,
            city: "Lima",
            country: "Peru",
            date: "14.09.2018",
            desc: "Destruction in suburbs, hundreds left homeless."
        },
    ];

    const quakeMarkers = [];
    earthquakes.forEach(eq => {
        const marker = createEarthquakeMarker(eq.lat, eq.lon, 0.003 * eq.mag, eq.mag);
        marker.userData = {
            city: eq.city,
            country: eq.country,
            mag: eq.mag,
            date: eq.date,
            desc: eq.desc
        };
        scene.add(marker);
        quakeMarkers.push(marker);
    });

    function getMarkerColor(mag) {
        if (mag < 3) return 0x1ED75D; // green
        else if (mag < 4) return 0xCBE800; // yellow-green
        else if (mag < 5) return 0xFFF500; // yellow
        else if (mag < 6) return 0xFF9900; // orange
        else if (mag < 7) return 0xFF5400; // red-orange
        else return 0xFF2222; // bright red
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
        const mat = new THREE.MeshPhongMaterial({
            color: color,
            emissive: color
        });
        const mesh = new THREE.Mesh(geom, mat);
        mesh.position.set(x, y, z);
        mesh.lookAt(0, 0, 0);
        return mesh;
    }

    // Controls (define BEFORE using controls in animation)
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enablePan = false;
    controls.enableZoom = true;
    controls.minDistance = 3.1;
    controls.maxDistance = 7;
    controls.enableDamping = true;
    controls.dampingFactor = 0.12;
    controls.rotateSpeed = 0.53;
    controls.minPolarAngle = Math.PI * 0.14;
    controls.maxPolarAngle = Math.PI * 0.86;

    // Raycasting for popover
    const raycaster = new THREE.Raycaster(),
        mouse = new THREE.Vector2();
    const card = document.getElementById('quake-hover-card');
    let showingMarker = null;

    renderer.domElement.addEventListener('pointermove', onPointerMove);
    renderer.domElement.addEventListener('mouseleave', onPointerLeave);

    function onPointerMove(event) {
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

                // UPDATE BOX TEXT HERE:
                locDiv.textContent = `${m.userData.city}, ${m.userData.country}`;
                magDiv.textContent = `${m.userData.mag}`;
                dateDiv.textContent = `${m.userData.date}`;
                descDiv.textContent = `${m.userData.desc}`;

                card.classList.add('active');
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
                showingMarker = null;
            }
        });
        renderer.domElement.addEventListener('mouseleave', () => {
            card.classList.remove('active');
            showingMarker = null;
        });
    }

    function onPointerLeave() {
        card.classList.remove('active');
        card.style.opacity = 0;
        card.style.display = 'none';
        showingMarker = null;
    }

    function updateHoverCard() {
        if (showingMarker) {
            const markerScreenPos = showingMarker.position.clone().project(camera);
            const halfW = container.offsetWidth / 2;
            const halfH = container.offsetHeight / 2;
            const cardX = halfW + markerScreenPos.x * halfW;
            const cardY = halfH - markerScreenPos.y * halfH;
            card.style.left = (cardX) + 'px';
            card.style.top = (cardY) + 'px';
        }
    }

    renderer.setAnimationLoop(() => {
        controls.update();
        renderer.render(scene, camera);
        updateHoverCard();
    });

    // Responsive resize
    window.addEventListener('resize', () => {
        const w = container.offsetWidth,
            h = container.offsetHeight;
        renderer.setSize(w, h);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
    });
    </script>

    <script>
    // Say you put this at the end of your <body>
    const yearMin = document.getElementById('year-min');
    const yearMax = document.getElementById('year-max');
    const yearMinL = document.getElementById('year-min-label');
    const yearMaxL = document.getElementById('year-max-label');
    const rngBar = document.getElementById('year-slider-range');

    const minYear = parseInt(yearMin.min);
    const maxYear = parseInt(yearMax.max);
    const padding = 1; // Minimum distance in years between the sliders

    yearMin.addEventListener('input', function() {
        if (parseInt(yearMin.value) > parseInt(yearMax.value) - padding) {
            yearMin.value = parseInt(yearMax.value) - padding;
        }
        yearMinL.textContent = yearMin.value;
        updateRangeBar();
    });
    yearMax.addEventListener('input', function() {
        if (parseInt(yearMax.value) < parseInt(yearMin.value) + padding) {
            yearMax.value = parseInt(yearMin.value) + padding;
        }
        yearMaxL.textContent = yearMax.value;
        updateRangeBar();
    });

    function updateRangeBar() {
        const percentMin = 100 * (yearMin.value - minYear) / (maxYear - minYear);
        const percentMax = 100 * (yearMax.value - minYear) / (maxYear - minYear);
        rngBar.style.left = percentMin + "%";
        rngBar.style.width = (percentMax - percentMin) + "%";
    }
    updateRangeBar();
    yearMinL.textContent = yearMin.value;
    yearMaxL.textContent = yearMax.value;
    </script>
</body>

</html>