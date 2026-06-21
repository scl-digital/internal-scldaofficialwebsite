(function () {
    console.log("Procedural Forest Script Initializing...");

    let scene, camera, renderer;
    let trees = new Map();
    const GRID_SIZE = 15;
    const RENDER_RADIUS = 80;
    const TREE_PROBABILITY = 0.15;

    // Deterministic pseudo-random function based on grid coordinates
    function random(x, z) {
        const h = Math.sin(x * 12.9898 + z * 78.233) * 43758.5453123;
        return h - Math.floor(h);
    }

    function createPalmTree() {
        const group = new THREE.Group();

        // Trunk - slightly curved for a tropical look
        const trunkHeight = 4 + Math.random() * 2;
        const trunkGeom = new THREE.CylinderGeometry(0.15, 0.3, trunkHeight, 6);
        const trunkMat = new THREE.MeshStandardMaterial({ color: 0x5C4033 }); // Brownish
        const trunk = new THREE.Mesh(trunkGeom, trunkMat);
        trunk.position.y = trunkHeight / 2;
        group.add(trunk);

        // Leaves
        const leafColor = 0x2E8B57; // Sea Green
        const leafMat = new THREE.MeshStandardMaterial({ color: leafColor, side: THREE.DoubleSide });

        const numLeaves = 8 + Math.floor(Math.random() * 4);
        for (let i = 0; i < numLeaves; i++) {
            const leafGroup = new THREE.Group();
            const leafGeom = new THREE.ConeGeometry(0.5, 3, 3);
            const leaf = new THREE.Mesh(leafGeom, leafMat);

            leaf.position.y = 1.5;
            leaf.rotation.x = -Math.PI / 3;

            leafGroup.add(leaf);
            leafGroup.position.y = trunkHeight;
            leafGroup.rotation.y = (i / numLeaves) * Math.PI * 2;
            leafGroup.rotation.z = 0.5 + Math.random() * 0.5;

            group.add(leafGroup);
            group.castShadow = true;
            group.receiveShadow = true;
        }

        return group;
    }

    function update() {
        if (!scene || !camera) {
            findThreeJS();
            return;
        }

        const camX = Math.round(camera.position.x / GRID_SIZE);
        const camZ = Math.round(camera.position.z / GRID_SIZE);
        const range = Math.ceil(RENDER_RADIUS / GRID_SIZE);

        const currentKeys = new Set();

        for (let x = camX - range; x <= camX + range; x++) {
            for (let z = camZ - range; z <= camZ + range; z++) {
                // Distance check for circular range
                const dx = x * GRID_SIZE - camera.position.x;
                const dz = z * GRID_SIZE - camera.position.z;
                if (dx * dx + dz * dz > RENDER_RADIUS * RENDER_RADIUS) continue;

                const key = `${x},${z}`;
                currentKeys.add(key);

                if (!trees.has(key)) {
                    if (random(x, z) < TREE_PROBABILITY) {
                        const tree = createPalmTree();
                        const offsetX = (random(x, z + 1) - 0.5) * GRID_SIZE * 0.8;
                        const offsetZ = (random(x + 1, z) - 0.5) * GRID_SIZE * 0.8;

                        tree.position.set(x * GRID_SIZE + offsetX, 0, z * GRID_SIZE + offsetZ);

                        // Add some random rotation and scale
                        tree.rotation.y = random(x, z) * Math.PI * 2;
                        const s = 0.8 + random(z, x) * 0.5;
                        tree.scale.set(s, s, s);

                        scene.add(tree);
                        trees.set(key, tree);
                    }
                }
            }
        }

        // Remove old trees
        for (const [key, tree] of trees.entries()) {
            if (!currentKeys.has(key)) {
                scene.remove(tree);
                trees.delete(key);
            }
        }
    }

    function findThreeJS() {
        const canvas = document.querySelector('canvas');
        if (canvas && canvas.__r3f) {
            const state = canvas.__r3f.getState();
            scene = state.scene;
            camera = state.camera;
            renderer = state.gl;

            if (scene && camera) {
                console.log("Successfully hooked into Three.js/R3F scene.");
                // Inject our own update loop if R3F doesn't have one we can easily hook
                // or just use requestAnimationFrame
                startLoop();
            }
        }
    }

    let loopStarted = false;
    function startLoop() {
        if (loopStarted) return;
        loopStarted = true;

        function animate() {
            update();
            requestAnimationFrame(animate);
        }
        animate();
    }

    // Initial delay to let React/Three load
    setTimeout(findThreeJS, 2000);
})();
