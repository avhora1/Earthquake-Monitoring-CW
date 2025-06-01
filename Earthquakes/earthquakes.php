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
        border-radius: 1rem;
        min-height: 80vh;
        transform: translateY(5vh);
        box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
        border: 2px solid rgba(255, 255, 255, 0.09);
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
        min-width: 210px;
        max-width: 340px;
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.18) 0%, rgba(32, 35, 53, 0.09) 100%);
        backdrop-filter: blur(1.4vh);
        -webkit-backdrop-filter: blur(1.4vh);
        border-radius: 0.7em;
        border: 1.5px solid rgba(255, 255, 255, 0.12);
        color: #fff;
        padding: 17px 22px 13px 22px;
        z-index: 888;
        box-shadow: 0 4px 32px #0007;
        font-family: 'Roboto', Arial, sans-serif;
        pointer-events: none;
        left: 0;
        top: 0;
        transform: translate(-50%, -125%) scale(1);
        transition: opacity .19s, filter .23s;
        opacity: 0;
        display: none;
    }

    #quake-hover-card.active {
        display: block;
        opacity: 1;
        filter: drop-shadow(0 2px 12px #1117);
    }

    .quake-card-city {
        font-size: 1.12em;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .quake-card-mag {
        font-size: 1.02em;
        opacity: .93;
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
                    <!-- Your left glass content goes here if needed -->
                </div>
            </div>
            <div class="col-8" id="globe-col">
                <div id="globe-canvas-container">
                    <!-- Floating Quake Card -->
                    <div id="quake-hover-card">
                        <div class="quake-card-city"></div>
                        <div class="quake-card-mag"></div>
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
    scene.add(new THREE.AmbientLight(0xffffff, 0.55));
    const dLight = new THREE.DirectionalLight(0xffffff, 0.55);
    dLight.position.set(7, 6, 9);
    scene.add(dLight);

    // Globe
    const loader = new THREE.TextureLoader();
    const earthMap = loader.load('../assets/earth/earthmap.jpg');
    const sphereGeometry = new THREE.SphereGeometry(1.5, 64, 64);
    const sphereMaterial = new THREE.MeshPhongMaterial({
        map: earthMap,
        shininess: 5,
        specular: new THREE.Color('rgb(45,85,130)')
    });
    const globe = new THREE.Mesh(sphereGeometry, sphereMaterial);
    scene.add(globe);

    // Earthquake markers
    const earthquakes = [{
            lat: 51.5074,
            lon: -1.9,
            size: 0.05,
            city: "Bournemouth",
            country: "UK",
            mag: 7.9
        },
        {
            lat: 35.7796,
            lon: -5.8137,
            size: 0.045,
            city: "Tangier",
            country: "Morocco",
            mag: 3.9
        },
        {
            lat: 37.9838,
            lon: 23.7275,
            size: 0.04,
            city: "Athens",
            country: "Greece",
            mag: 4.6
        },
        {
            lat: 40.7128,
            lon: -74.006,
            size: 0.037,
            city: "New York",
            country: "USA",
            mag: 5.1
        }
    ];
    const quakeMarkers = [];
    earthquakes.forEach(eq => {
        const marker = createEarthquakeMarker(eq.lat, eq.lon, eq.size || 0.037);
        marker.userData = {
            city: eq.city,
            country: eq.country,
            mag: eq.mag
        };
        scene.add(marker);
        quakeMarkers.push(marker);
    });

    function createEarthquakeMarker(lat, lon, size) {
        const phi = (90 - lat) * Math.PI / 180;
        const theta = (lon + 180) * Math.PI / 180;
        const radius = 1.515;
        const x = -radius * Math.sin(phi) * Math.cos(theta);
        const y = radius * Math.cos(phi);
        const z = radius * Math.sin(phi) * Math.sin(theta);
        const geom = new THREE.SphereGeometry(size, 24, 24);
        const mat = new THREE.MeshPhongMaterial({
            color: 0xff3137,
            emissive: 0xfd2020
        });
        const mesh = new THREE.Mesh(geom, mat);
        mesh.position.set(x, y, z);
        mesh.lookAt(0, 0, 0);
        return mesh;
    }

    // Controls (define BEFORE using controls in animation)
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enablePan = false;
    controls.enableZoom = false;
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
    const cityEl = card.querySelector('.quake-card-city');
    const magEl = card.querySelector('.quake-card-mag');
    let showingMarker = null;

    renderer.domElement.addEventListener('pointermove', onPointerMove);
    renderer.domElement.addEventListener('mouseleave', onPointerLeave);

    function onPointerMove(event) {
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(quakeMarkers, false);
        if (intersects.length > 0) {
            const m = intersects[0].object;
            cityEl.textContent = `${m.userData.city}, ${m.userData.country}`;
            magEl.textContent = `Magnitude: ${m.userData.mag}`;
            card.classList.add('active');
            const markerScreenPos = m.position.clone().project(camera);
            const halfW = container.offsetWidth / 2;
            const halfH = container.offsetHeight / 2;
            const cardX = halfW + markerScreenPos.x * halfW;
            const cardY = halfH - markerScreenPos.y * halfH;
            card.style.left = (cardX) + 'px';
            card.style.top = (cardY) + 'px';
            card.style.display = 'block';
            showingMarker = m;
        } else {
            card.classList.remove('active');
            card.style.opacity = 0;
            card.style.display = 'none';
            showingMarker = null;
        }
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
</body>

</html>