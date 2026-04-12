function renderMap(data) {
    const map = document.getElementById('map');
    map.innerHTML = '';

    addPin(map, data.kingdom.capital.x, data.kingdom.capital.y, 'kingdom', 'Capital');

    data.clans.forEach(c => {
        addPin(map, c.capital_x, c.capital_y, 'clan', c.clan_name);
    });

    data.castles.forEach(c => {
        addPin(map, c.x, c.y, 'castle', c.name || 'Castle');
    });
}

function addPin(map, x, y, type, label) {
    const el = document.createElement('div');

    el.className = `pin pin-${type}`;
    el.style.left = (x / 992 * 100) + '%';
    el.style.top = (y / 989 * 100) + '%';

    el.innerText = label;

    el.onclick = () => {
        alert(`${type}: ${label}`);
    };

    map.appendChild(el);
}