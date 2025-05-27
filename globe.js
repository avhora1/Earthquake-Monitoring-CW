console.clear();

import * as THREE from "https://cdn.skypack.dev/three@0.136.0";
import OrbitControls from "https://cdn.skypack.dev/three@0.136.0/examples/jsm/controls/OrbitControls.js";


let scene = new THREE.Scene();
let camera = new THREE.PerspectiveCamera(60, (innerWidth / 2) / innerHeight, 1, 1000);
camera.position.set(-1, 0.5, 1).setLength(30);
let renderer = new THREE.WebGLRenderer({
    canvas: document.querySelector('#globe'),
    antialias: true,
    alpha: true // Enable transparency
});
renderer.setSize((innerWidth / 2), (innerHeight));


let controls = new OrbitControls(camera, renderer.domElement);
controls.target.set(0, 12, 0);
controls.update();
controls.enablePan = false;
controls.enableZoom = false;

let light1 = new THREE.DirectionalLight(0xF7CE00, 0.5);
let light2 = new THREE.DirectionalLight(0xffffff, 0.5);
let light3 = new THREE.DirectionalLight(0x20202, 0.5);

light1.position.set(0.5, 1, 1);
light2.position.set(0, -10, 10);
light3.position.set(0, 10, 10);

scene.add(light1, light2, light3, new THREE.AmbientLight(0xffffff, 1));
// scene.add(new THREE.PolarGridHelper(40));

// text ring
let cnv = document.createElement("canvas");
cnv.width = 1024;
cnv.height = 128;
let ctx = cnv.getContext("2d");


ctx.fillRect(0, 0, cnv.width, cnv.height);
ctx.clearRect(0, 10, cnv.width, cnv.height - 20);
ctx.textBaseline = "middle";
ctx.textAlign = "center";
ctx.fillStyle = "black";
ctx.font = "bold 80px Oswald";
ctx.fillText("Earthquakes • Artifacts •", cnv.width * 0.5, cnv.height * 0.5);

let cnvTexture = new THREE.CanvasTexture(cnv);
cnvTexture.wrapS = THREE.RepeatWrapping;
cnvTexture.wrapT = THREE.RepeatWrapping;
cnvTexture.repeat.set(3, 1);

let gc = new THREE.CylinderGeometry(12, 12, 5, 72, 1, true);
let mc = new THREE.MeshBasicMaterial({ map: cnvTexture, alphaTest: 0.5, side: THREE.DoubleSide, opacity: 1 });
let c = new THREE.Mesh(gc, mc);

// Add sphere

const earthTexture = new THREE.TextureLoader().load('assets/earth/earthMap.jpg')
const earthMap = new THREE.TextureLoader().load('assets/earth/earthSpec.jpg')

const geometry = new THREE.SphereGeometry(10, 64, 64);
const material = new THREE.MeshStandardMaterial({ map: earthTexture, bumpMap: earthMap, bumpScale: -100, opacity: 1 });
const sphere = new THREE.Mesh(geometry, material);


c.position.y = 10;
sphere.position.y = 10;

const axialTilt = 23.5 * Math.PI / 180;
sphere.rotation.y = -2;
c.rotation.y = -2;
sphere.rotation.z = -axialTilt;
c.rotation.z = -axialTilt;


const group = new THREE.Group();
group.add(c);
group.add(sphere);

scene.add(group);

// Create a quaternion for rotation
const axis = new THREE.Vector3(0, 1, 0).normalize(); // Rotate around the y-axis
const quaternion = new THREE.Quaternion().setFromAxisAngle(axis, 0.004); // Small rotation step

scene.fog = new THREE.Fog(0xcccccc, 1, 100);

let raycaster = new THREE.Raycaster();
let mouse = new THREE.Vector2();
let intersects;

window.addEventListener("dblclick", event => {
    mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
    mouse.y = - (event.clientY / window.innerHeight) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);
    intersects = raycaster.intersectObjects(clickables, false);
    if (intersects.length > 0) {
        let obj = intersects[0].object;
        alert(obj.userData.message);
    }
})

window.addEventListener("resize", onWindowResize);

let clock = new THREE.Clock();

animate();

function animate() {


    requestAnimationFrame(animate);
    let t = clock.getDelta();
    cnvTexture.offset.x -= t * 0.1;
    sphere.quaternion.multiply(quaternion); // Apply the quaternion rotation

    renderer.render(scene, camera);

}

function onWindowResize() {

    camera.aspect = innerWidth / innerHeight;
    camera.updateProjectionMatrix();

    renderer.setSize(innerWidth, innerHeight);

}
