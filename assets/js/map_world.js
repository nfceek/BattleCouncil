document.addEventListener("DOMContentLoaded", () => {

    const map = document.getElementById("map");
    if (!map) return; // prevent crash on other pages

    let kingdomData = []; // local safe store

    fetch(`${BASE_URL}/public/api/map_world.php`)
        .then(res => res.json())
        .then(data => {

            if (!data.kingdoms) return;

            kingdomData = data.kingdoms;
            window.kingdomData = kingdomData; // optional global

            renderKingdoms(kingdomData);
        })
        .catch(err => {
            console.error('Map load error:', err);
        });

    function renderKingdoms(kingdoms) {
        kingdoms.forEach(k => addPin(k));
    }

    const imageCache = {};

    function setCapitalImage(el, building) {

        const imgPath = `/images/capitals/${building}.png`;
        const fallback = `/images/capitals/default.png`;

        if (imageCache[imgPath] !== undefined) {
            el.style.backgroundImage = `url('${imageCache[imgPath] ? imgPath : fallback}')`;
            return;
        }

        const testImg = new Image();

        testImg.onload = function () {
            imageCache[imgPath] = true;
            el.style.backgroundImage = `url('${imgPath}')`;
        };

        testImg.onerror = function () {
            imageCache[imgPath] = false;
            el.style.backgroundImage = `url('${fallback}')`;
        };

        testImg.src = imgPath;
    }

    function addPin(k) {
        const el = document.createElement('div');

        el.className = 'kingdom-pin';

        el.style.width = "120px";
        el.style.height = "120px";
        el.style.backgroundSize = "contain";
        el.style.backgroundRepeat = "no-repeat";

        setCapitalImage(el, k.capital_bldg);

        el.style.left = (k.capital_x / 992 * 100) + '%';
        el.style.top  = (k.capital_y / 989 * 100) + '%';

        el.title = `K${k.kingdomID} - ${k.capital_bldg}`;

        map.appendChild(el);
    }

    map.addEventListener('click', (e) => {

        if (!kingdomData.length) return; // prevent early click crash

        const rect = map.getBoundingClientRect();

        const x = (e.clientX - rect.left) / rect.width * 992;
        const y = (e.clientY - rect.top) / rect.height * 989;

        const nearest = findNearest(x, y, kingdomData);

        console.log('Nearest:', nearest);
    });

    function findNearest(x, y, kingdoms) {
        let closest = null;
        let minDist = Infinity;

        kingdoms.forEach(k => {
            const dx = k.capital_x - x;
            const dy = k.capital_y - y;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < minDist) {
                minDist = dist;
                closest = k;
            }
        });

        return closest;
    }

});