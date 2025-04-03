import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';


// Setup
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 10000);
const renderer = new THREE.WebGLRenderer({
  canvas: document.querySelector('#bg'),
  alpha: true // Enable transparency
});

renderer.setPixelRatio(window.devicePixelRatio);
renderer.setSize(window.innerWidth, window.innerHeight);
camera.position.setZ(400); // Move the camera further back for visibility

// Set the background color of the scene to be transparent
scene.background = null;

// // Add orbit controls with rotation only
const controls = new OrbitControls(camera, renderer.domElement);
controls.enablePan = false; // Disable panning
controls.enableZoom = false; // Disable zooming
controls.enableRotate = true; // Enable rotation

// // Add grid helper
const gridHelper = new THREE.GridHelper(1000, 1000); // Adjust size and divisions as needed
// scene.add(gridHelper);

// Add sphere

const earthTexture = new THREE.TextureLoader().load('earthNight.jpg')
const earthMap = new THREE.TextureLoader().load('earthSpec.jpg')

const geometry = new THREE.SphereGeometry(200, 64, 64);
const material = new THREE.MeshStandardMaterial({ map: earthTexture, bumpMap: earthMap });
const sphere = new THREE.Mesh(geometry, material);
scene.add(sphere);

const axialTilt = 23.5 * Math.PI / 180;
sphere.rotation.y = -2;
sphere.rotation.z = -axialTilt;

// Create a quaternion for rotation
const axis = new THREE.Vector3(0, 1, 0).normalize(); // Rotate around the y-axis
const quaternion = new THREE.Quaternion().setFromAxisAngle(axis, 0.002); // Small rotation step

// Add point light
const pointLight1 = new THREE.PointLight(0xffffff, 1, 0, 0); // White light, intensity 1, distance 1000
pointLight1.position.set(0, 200, -100); // Position up and to the left
const pointLight2 = new THREE.PointLight(0xffffff, 1, 0, 0); // White light, intensity 1, distance 1000
pointLight2.position.set(0, -10, -200); // Position up and to the left
const pointLight3 = new THREE.PointLight(0xffffff, 1, 0, 0); // White light, intensity 1, distance 1000
pointLight3.position.set(-50, 50, 50); // Position up and to the left
const pointLight4 = new THREE.PointLight(0xffffff, 1, 0, 0); // White light, intensity 1, distance 1000
pointLight4.position.set(50, 50, 50); // Position up and to the left
// scene.add(pointLight1);
// scene.add(pointLight2);
// scene.add(pointLight3);
// scene.add(pointLight4);


const dLight = new THREE.DirectionalLight(0xffffff, 0.8);
dLight.position.set(-800, 100, 400);
scene.add(dLight);

const dLight1 = new THREE.DirectionalLight(0x7982f6, 1);
dLight1.position.set(-200, 500, 200);
scene.add(dLight1);

const dLight2 = new THREE.PointLight(0x8566cc, 1);
dLight2.position.set(-200, 500, 200);
scene.add(dLight2);

// const lightHelper = new THREE.PointLightHelper(pointLight1)
// scene.add(lightHelper)

// Add ambient light
const ambientLight = new THREE.AmbientLight(0xbbbbbb, 0.2); // White light, intensity 0.5
scene.add(ambientLight);

scene.fog = new THREE.Fog(0x535ef3, 40, 2000)



// Animation loop
function animate() {
  requestAnimationFrame(animate);
  sphere.quaternion.multiply(quaternion); // Apply the quaternion rotation
  renderer.render(scene, camera);
}
animate();